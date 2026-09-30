@extends('user.directorates._layout')

{{-- This view renders its own tab bar and its own Preview panel/actions,
     so the shared layout skips both. --}}
@section('directorate-tabs', '1')
@section('directorate-preview', '1')

@section('directorate-sections')

@include('user.states.sections.border')

{{-- Attachments upload: kept here in the standalone wrapper (not in the shared
     partial) so the combined state form can use its own shared attachments[]
     input instead. --}}
<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-paperclip"></i>
            </div>
            Attachments
            <small style="font-weight:400;color:var(--gray-500);">(PDFs, images, Word — max 10MB each)</small>
        </div>
    </div>
    <div class="card-body">
        <div class="fg">
            <div class="attach-zone" id="attachZone">
                <i class="fas fa-cloud-upload-alt"></i>
                <div style="font-size:.85rem;color:var(--gray-500);margin-bottom:4px;">Drag &amp; drop files here or click to browse</div>
                <div style="font-size:.74rem;color:var(--gray-400);">Nominal roll, pictures, supporting documents</div>
                <input type="file" name="attachments[]" id="attachInput" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display:none;">
            </div>
            <div class="attach-list" id="attachList"></div>
        </div>
    </div>
</div>

<!-- ======================================================
    PREVIEW
======================================================= -->

<div class="tab-panel" id="tab-preview">

    <div class="redas-card" style="margin-bottom:14px;">

        <div class="card-head">

            <div class="card-head-title">

                <div class="card-head-icon" style="background:#f0fdf4;color:#15803d;">
                    <i class="fas fa-eye"></i>
                </div>

                8. Preview

            </div>

        </div>

        <div class="card-body">

            <p style="font-size:.82rem;color:var(--gray-600);margin-bottom:14px;">Review the generated report below before submitting. Use “Back to Edit” to make corrections.</p>

            <!-- ==========================================
                 DYNAMIC PREVIEW CONTENT
            =========================================== -->

            <div id="previewContent">

                <div style="
                    background:#ffffff;
                    border:1px solid #dbe3ec;
                    border-radius:8px;
                    padding:50px 20px;
                    text-align:center;
                    color:#64748b;
                ">

                    <i class="fas fa-file-alt"
                       style="
                        font-size:40px;
                        margin-bottom:15px;
                        opacity:.5;
                       "></i>

                    <h5 style="
                        margin:0 0 7px;
                        color:#334155;
                    ">
                        Preview not generated
                    </h5>

                    <p style="
                        margin:0;
                        font-size:13px;
                    ">
                        Click the Preview tab to generate the report preview.
                    </p>

                </div>

            </div>

        </div>

    </div>

    <div class="border-actions">
        <button type="button" class="btn-nis btn-ghost border-edit-btn"><i class="fas fa-arrow-left"></i> Back to Edit</button>
        <div class="border-actions-center">
            <button type="button" class="btn-nis btn-outline-nis border-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
        </div>
        <button type="button" class="btn-nis btn-primary-nis border-submit-return-btn"><i class="fas fa-paper-plane"></i> Submit Return</button>
    </div>

</div>

<script>
/*
 * The shared partial (user.states.sections.border) renders the tab bar without
 * a Preview tab (the combined state form must not have one). Re-add it here for
 * the standalone page. This runs synchronously while the form markup is being
 * parsed — before the layout/footer scripts collect and bind .entry-tab
 * elements — so the Preview tab behaves exactly like the others.
 */
(function () {
    var bar = document.getElementById('entryTabs');
    if (!bar || bar.querySelector('.entry-tab[data-tab="preview"]')) return;
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'entry-tab';
    btn.setAttribute('data-tab', 'preview');
    btn.innerHTML = '<i class="fas fa-eye"></i><span>Preview</span>';
    bar.appendChild(btn);
})();
</script>

<script>
/**************************************************************************
 * REDAS REPORT PREVIEW
 *
 * This module only handles generating and displaying the preview.
 * The tab panels and their calculators live in the shared partial
 * (user.states.sections.border), which exposes window.borderFormRecalc().
 **************************************************************************/

(function () {

    "use strict";

    /**********************************************************************
     * HELPER: safely get an element
     **********************************************************************/
    function byId(id) {
        return document.getElementById(id);
    }


    /**********************************************************************
     * HELPER: safely read an input/select/textarea
     **********************************************************************/
    function getFieldValue(selectors) {

        for (let i = 0; i < selectors.length; i++) {

            const element = document.querySelector(selectors[i]);

            if (!element) {
                continue;
            }

            const value =
                typeof element.value !== "undefined"
                    ? element.value
                    : element.textContent;

            if (value !== null && String(value).trim() !== "") {
                return String(value).trim();
            }
        }

        return "";
    }


    /**********************************************************************
     * HELPER: number
     **********************************************************************/
    function valueNumber(element) {

        if (!element) {
            return 0;
        }

        return Number(element.value || 0) || 0;
    }


    /**********************************************************************
     * HELPER: HTML escaping
     **********************************************************************/
    function escapeHtml(value) {

        return String(value ?? "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }


    /**********************************************************************
     * HELPER: format file size
     **********************************************************************/
    function formatFileSize(bytes) {

        if (!bytes) {
            return "0 Bytes";
        }

        const units = ["Bytes", "KB", "MB", "GB"];

        const index = Math.min(
            Math.floor(Math.log(bytes) / Math.log(1024)),
            units.length - 1
        );

        return (
            (bytes / Math.pow(1024, index)).toFixed(
                index === 0 ? 0 : 2
            ) +
            " " +
            units[index]
        );
    }


    /**********************************************************************
     * HELPER: get visible table title
     **********************************************************************/
    function getCardTitle(element, fallback) {

        const card = element.closest(".redas-card");

        if (!card) {
            return fallback;
        }

        const title = card.querySelector(".card-head-title");

        if (!title) {
            return fallback;
        }

        return title.textContent
            .replace(/\s+/g, " ")
            .trim() || fallback;
    }


    /**********************************************************************
     * PREVIEW CSS
     **********************************************************************/
    function previewStyles() {

        return `
            <style>

                .redas-preview {
                    width:100%;
                    box-sizing:border-box;
                    color:#1f2937;
                    font-family:Arial,Helvetica,sans-serif;
                    font-size:14px;
                }

                .redas-preview * {
                    box-sizing:border-box;
                }

                .redas-preview-header {
                    border-bottom:3px solid #006633;
                    padding:4px 0 18px;
                    margin-bottom:18px;
                }

                .redas-preview-header h2 {
                    margin:0;
                    color:#111827;
                    font-size:23px;
                    line-height:1.25;
                    text-transform:uppercase;
                }

                .redas-preview-header h3 {
                    margin:6px 0 0;
                    font-size:17px;
                    font-weight:600;
                    color:#374151;
                }

                .redas-preview-subtitle {
                    margin-top:4px;
                    color:#4b5563;
                }

                .redas-preview-meta {
                    display:grid;
                    grid-template-columns:repeat(3,minmax(0,1fr));
                    gap:10px;
                    margin-top:16px;
                }

                .redas-preview-meta-box {
                    padding:10px 12px;
                    border:1px solid #dbe3ea;
                    border-radius:7px;
                    background:#f8fafc;
                }

                .redas-preview-label {
                    display:block;
                    margin-bottom:4px;
                    color:#64748b;
                    font-size:11px;
                    font-weight:700;
                    text-transform:uppercase;
                }

                .redas-preview-value {
                    color:#111827;
                    font-weight:600;
                    word-break:break-word;
                }

                .redas-preview-section {
                    margin-bottom:18px;
                    border:1px solid #dbe3ea;
                    border-radius:8px;
                    overflow:hidden;
                    background:#fff;
                }

                .redas-preview-section-title {
                    padding:10px 13px;
                    background:#006633;
                    color:#fff;
                    font-weight:700;
                    font-size:15px;
                }

                .redas-preview-section-body {
                    padding:13px;
                }

                .redas-preview-table-wrap {
                    width:100%;
                    overflow-x:auto;
                }

                .redas-preview-table {
                    width:100%;
                    min-width:650px;
                    border-collapse:collapse;
                }

                .redas-preview-table th,
                .redas-preview-table td {
                    border:1px solid #dbe3ea;
                    padding:7px 8px;
                    vertical-align:middle;
                }

                .redas-preview-table th {
                    background:#f1f5f9;
                    color:#334155;
                    font-weight:700;
                    text-align:left;
                }

                .redas-preview-table td.num,
                .redas-preview-table th.num {
                    text-align:center;
                }

                .redas-preview-total {
                    background:#ecfdf5 !important;
                    font-weight:700;
                }

                .redas-preview-empty {
                    padding:14px;
                    border:1px dashed #cbd5e1;
                    border-radius:7px;
                    background:#f8fafc;
                    color:#64748b;
                }

                .redas-preview-note {
                    padding:12px;
                    border:1px solid #fde68a;
                    border-radius:7px;
                    background:#fffbeb;
                    color:#92400e;
                    line-height:1.5;
                }

                .redas-preview-text {
                    white-space:pre-wrap;
                    line-height:1.6;
                    padding:10px 12px;
                    border:1px solid #e2e8f0;
                    border-radius:6px;
                    background:#f8fafc;
                }

                .redas-preview-narrative {
                    margin-bottom:14px;
                }

                .redas-preview-narrative:last-child {
                    margin-bottom:0;
                }

                .redas-preview-narrative-title {
                    margin-bottom:5px;
                    font-weight:700;
                    color:#334155;
                }

                @media(max-width:768px) {
                    .redas-preview-meta {
                        grid-template-columns:1fr;
                    }

                    .redas-preview-header h2 {
                        font-size:19px;
                    }
                }

            </style>
        `;
    }


    /**********************************************************************
     * HEADER
     **********************************************************************/
    function buildPreviewHeader() {

        const period = getFieldValue([
            '[name="report_period"]',
            '[name="reportPeriod"]',
            '[name="return_period"]',
            '[name="returnPeriod"]',
            '#reportPeriod',
            '#returnPeriod'
        ]) || "Not specified";


        const command = getFieldValue([
            '[name="formation"]',
            '[name="command"]',
            '[name="state"]',
            '#formation',
            '#command',
            '#state'
        ]) || "Not specified";


        const officer = getFieldValue([
            '[name="reporting_officer"]',
            '[name="reportingOfficer"]',
            '#reportingOfficer'
        ]) || "Not specified";


        const preparedDate = new Date().toLocaleDateString(
            "en-NG",
            {
                day:"2-digit",
                month:"long",
                year:"numeric"
            }
        );


        return `
            <div class="redas-preview-header">

                <h2>Nigeria Immigration Service</h2>

                <h3>
                    Border Management Directorate Returns
                </h3>

                <div class="redas-preview-subtitle">
                    REDAS Report
                </div>

                <div class="redas-preview-meta">

                    <div class="redas-preview-meta-box">
                        <span class="redas-preview-label">
                            Reporting Period
                        </span>
                        <span class="redas-preview-value">
                            ${escapeHtml(period)}
                        </span>
                    </div>

                    <div class="redas-preview-meta-box">
                        <span class="redas-preview-label">
                            Formation / Command
                        </span>
                        <span class="redas-preview-value">
                            ${escapeHtml(command)}
                        </span>
                    </div>

                    <div class="redas-preview-meta-box">
                        <span class="redas-preview-label">
                            Reporting Officer
                        </span>
                        <span class="redas-preview-value">
                            ${escapeHtml(officer)}
                        </span>
                    </div>

                </div>

                <div style="
                    margin-top:10px;
                    color:#64748b;
                    font-size:12px;
                ">
                    Date Prepared:
                    <strong>${escapeHtml(preparedDate)}</strong>
                </div>

            </div>
        `;
    }


    /**********************************************************************
     * PERSONNEL
     **********************************************************************/
    function buildPersonnelPreview() {

        const tables = document.querySelectorAll(".staff-table");

        if (!tables.length) {
            return "";
        }

        let html = "";
        let hasAnyData = false;


        tables.forEach(function (table) {

            let rows = "";

            table.querySelectorAll("tbody tr").forEach(function (row) {

                if (row.classList.contains("total-row")) {
                    return;
                }

                const rankCell = row.querySelector("td");

                const maleInput = row.querySelector(".male");
                const femaleInput = row.querySelector(".female");
                const totalInput = row.querySelector(".total");

                if (!maleInput || !femaleInput || !totalInput) {
                    return;
                }

                const male = valueNumber(maleInput);
                const female = valueNumber(femaleInput);
                const total = valueNumber(totalInput);


                if (male === 0 && female === 0) {
                    return;
                }

                hasAnyData = true;


                rows += `
                    <tr>
                        <td>${escapeHtml(
                            rankCell ? rankCell.textContent.trim() : ""
                        )}</td>
                        <td class="num">${male}</td>
                        <td class="num">${female}</td>
                        <td class="num">${total}</td>
                    </tr>
                `;
            });


            if (!rows) {
                return;
            }


            const grandMale =
                byId("grandMale")?.value || "0";

            const grandFemale =
                byId("grandFemale")?.value || "0";

            const grandTotal =
                byId("grandTotal")?.value || "0";


            html += `
                <div class="redas-preview-section">

                    <div class="redas-preview-section-title">
                        <i class="fas fa-users"></i>
                        Personnel / Staff Strength
                    </div>

                    <div class="redas-preview-section-body">

                        <div class="redas-preview-table-wrap">

                            <table class="redas-preview-table">

                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th class="num">Male</th>
                                        <th class="num">Female</th>
                                        <th class="num">Total</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    ${rows}
                                </tbody>

                                <tfoot>
                                    <tr class="redas-preview-total">
                                        <th>TOTAL</th>
                                        <td class="num">${escapeHtml(grandMale)}</td>
                                        <td class="num">${escapeHtml(grandFemale)}</td>
                                        <td class="num">${escapeHtml(grandTotal)}</td>
                                    </tr>
                                </tfoot>

                            </table>

                        </div>

                    </div>

                </div>
            `;
        });


        return hasAnyData ? html : "";
    }


    /**********************************************************************
     * LAND BORDER
     **********************************************************************/
    function buildLandBorderPreview() {

        const tables = document.querySelectorAll(".land-border-table");

        if (!tables.length) {
            return "";
        }

        let html = "";


        tables.forEach(function (table) {

            let rows = "";
            let hasData = false;


            table.querySelectorAll("tbody tr").forEach(function (row) {

                if (row.classList.contains("total-row")) {
                    return;
                }


                const arrivalMale =
                    row.querySelector(".arrival-male");

                const arrivalFemale =
                    row.querySelector(".arrival-female");

                const arrivalTotal =
                    row.querySelector(".arrival-total");

                const departureMale =
                    row.querySelector(".departure-male");

                const departureFemale =
                    row.querySelector(".departure-female");

                const departureTotal =
                    row.querySelector(".departure-total");


                if (
                    !arrivalMale ||
                    !arrivalFemale ||
                    !arrivalTotal ||
                    !departureMale ||
                    !departureFemale ||
                    !departureTotal
                ) {
                    return;
                }


                const am = valueNumber(arrivalMale);
                const af = valueNumber(arrivalFemale);
                const at = valueNumber(arrivalTotal);

                const dm = valueNumber(departureMale);
                const df = valueNumber(departureFemale);
                const dt = valueNumber(departureTotal);


                if (am === 0 && af === 0 && dm === 0 && df === 0) {
                    return;
                }


                hasData = true;


                /*
                 * The second cell in the existing table is used
                 * as the control-post label.
                 */
                const cells = row.querySelectorAll("td");

                const post =
                    cells.length > 1
                        ? cells[1].textContent.trim()
                        : cells[0]?.textContent.trim() || "";


                rows += `
                    <tr>
                        <td>${escapeHtml(post)}</td>
                        <td class="num">${am}</td>
                        <td class="num">${af}</td>
                        <td class="num">${at}</td>
                        <td class="num">${dm}</td>
                        <td class="num">${df}</td>
                        <td class="num">${dt}</td>
                    </tr>
                `;
            });


            if (!hasData) {
                return;
            }


            const totalRow = table.querySelector(".total-row");
            const totals = totalRow
                ? totalRow.querySelectorAll("input")
                : [];


            const title = getCardTitle(
                table,
                "Land Border Returns"
            );


            html += `
                <div class="redas-preview-section">

                    <div class="redas-preview-section-title">
                        <i class="fas fa-road"></i>
                        ${escapeHtml(title)}
                    </div>

                    <div class="redas-preview-section-body">

                        <div class="redas-preview-table-wrap">

                            <table class="redas-preview-table">

                                <thead>

                                    <tr>
                                        <th rowspan="2">
                                            Control Post
                                        </th>

                                        <th colspan="3" class="num">
                                            Arrival
                                        </th>

                                        <th colspan="3" class="num">
                                            Departure
                                        </th>
                                    </tr>

                                    <tr>
                                        <th class="num">Male</th>
                                        <th class="num">Female</th>
                                        <th class="num">Total</th>

                                        <th class="num">Male</th>
                                        <th class="num">Female</th>
                                        <th class="num">Total</th>
                                    </tr>

                                </thead>

                                <tbody>
                                    ${rows}
                                </tbody>

                                <tfoot>
                                    <tr class="redas-preview-total">

                                        <th>TOTAL</th>

                                        <td class="num">
                                            ${escapeHtml(totals[0]?.value || "0")}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(totals[1]?.value || "0")}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(totals[2]?.value || "0")}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(totals[3]?.value || "0")}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(totals[4]?.value || "0")}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(totals[5]?.value || "0")}
                                        </td>

                                    </tr>
                                </tfoot>

                            </table>

                        </div>

                    </div>

                </div>
            `;
        });


        return html;
    }


    /**********************************************************************
     * NATIONALITY
     **********************************************************************/
    function buildNationalityPreview() {

        const cards =
            document.querySelectorAll(".nationality-card");

        if (!cards.length) {
            return "";
        }

        let html = "";


        cards.forEach(function (card) {

            let rows = "";
            let hasData = false;


            card.querySelectorAll("tbody tr").forEach(function (row) {

                const nationality =
                    row.querySelector(".nationality-select");

                const arrivalMale =
                    row.querySelector(".arrivalMale");

                const arrivalFemale =
                    row.querySelector(".arrivalFemale");

                const arrivalTotal =
                    row.querySelector(".arrivalTotal");

                const departureMale =
                    row.querySelector(".departureMale");

                const departureFemale =
                    row.querySelector(".departureFemale");

                const departureTotal =
                    row.querySelector(".departureTotal");

                const grandTotal =
                    row.querySelector(".grandTotal");


                if (
                    !nationality ||
                    !arrivalMale ||
                    !arrivalFemale ||
                    !arrivalTotal ||
                    !departureMale ||
                    !departureFemale ||
                    !departureTotal ||
                    !grandTotal
                ) {
                    return;
                }


                const country = nationality.value;

                const am = valueNumber(arrivalMale);
                const af = valueNumber(arrivalFemale);
                const at = valueNumber(arrivalTotal);

                const dm = valueNumber(departureMale);
                const df = valueNumber(departureFemale);
                const dt = valueNumber(departureTotal);

                const gt = valueNumber(grandTotal);


                if (
                    !country &&
                    am === 0 &&
                    af === 0 &&
                    dm === 0 &&
                    df === 0
                ) {
                    return;
                }


                hasData = true;


                rows += `
                    <tr>

                        <td>
                            ${escapeHtml(country || "-")}
                        </td>

                        <td class="num">${am}</td>
                        <td class="num">${af}</td>
                        <td class="num">${at}</td>

                        <td class="num">${dm}</td>
                        <td class="num">${df}</td>
                        <td class="num">${dt}</td>

                        <td class="num">${gt}</td>

                    </tr>
                `;
            });


            if (!hasData) {
                return;
            }


            const state =
                card.querySelector(".command-state")?.value || "";

            const post =
                card.querySelector(".control-post")?.value || "";


            html += `
                <div class="redas-preview-section">

                    <div class="redas-preview-section-title">

                        <i class="fas fa-passport"></i>

                        Land Border Returns by Nationality

                        ${
                            state
                                ? ` - ${escapeHtml(state)}`
                                : ""
                        }

                        ${
                            post
                                ? `
                                    <span style="
                                        font-weight:400;
                                        margin-left:5px;
                                    ">
                                        (${escapeHtml(post)})
                                    </span>
                                  `
                                : ""
                        }

                    </div>

                    <div class="redas-preview-section-body">

                        <div class="redas-preview-table-wrap">

                            <table class="redas-preview-table">

                                <thead>

                                    <tr>

                                        <th rowspan="2">
                                            Nationality
                                        </th>

                                        <th colspan="3" class="num">
                                            Arrival
                                        </th>

                                        <th colspan="3" class="num">
                                            Departure
                                        </th>

                                        <th rowspan="2" class="num">
                                            Grand Total
                                        </th>

                                    </tr>

                                    <tr>

                                        <th class="num">M</th>
                                        <th class="num">F</th>
                                        <th class="num">Total</th>

                                        <th class="num">M</th>
                                        <th class="num">F</th>
                                        <th class="num">Total</th>

                                    </tr>

                                </thead>

                                <tbody>
                                    ${rows}
                                </tbody>

                                <tfoot>

                                    <tr class="redas-preview-total">

                                        <th>TOTAL</th>

                                        <td class="num">
                                            ${escapeHtml(
                                                card.querySelector(".totalArrivalMale")?.value || "0"
                                            )}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(
                                                card.querySelector(".totalArrivalFemale")?.value || "0"
                                            )}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(
                                                card.querySelector(".totalArrival")?.value || "0"
                                            )}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(
                                                card.querySelector(".totalDepartureMale")?.value || "0"
                                            )}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(
                                                card.querySelector(".totalDepartureFemale")?.value || "0"
                                            )}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(
                                                card.querySelector(".totalDeparture")?.value || "0"
                                            )}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(
                                                card.querySelector(".totalGrand")?.value || "0"
                                            )}
                                        </td>

                                    </tr>

                                </tfoot>

                            </table>

                        </div>

                    </div>

                </div>
            `;
        });


        return html;
    }


    /**********************************************************************
     * SEAPORT
     **********************************************************************/
    function buildSeaportPreview() {

        const tables =
            document.querySelectorAll(".seaport-table");

        if (!tables.length) {
            return "";
        }

        let html = "";


        tables.forEach(function (table) {

            let rows = "";
            let hasData = false;


            table.querySelectorAll("tbody tr").forEach(function (row) {

                if (row.classList.contains("state-total")) {
                    return;
                }


                const inputs = [
                    ".passenger-arrival-male",
                    ".passenger-arrival-female",
                    ".passenger-arrival-total",
                    ".passenger-departure-male",
                    ".passenger-departure-female",
                    ".passenger-departure-total",
                    ".crew-arrival-male",
                    ".crew-arrival-female",
                    ".crew-arrival-total",
                    ".crew-departure-male",
                    ".crew-departure-female",
                    ".crew-departure-total",
                    ".boat-arrival",
                    ".boat-departure"
                ];


                const values = inputs.map(function (selector) {
                    return valueNumber(row.querySelector(selector));
                });


                const hasMovement =
                    values.some(function (value) {
                        return value > 0;
                    });


                if (!hasMovement) {
                    return;
                }


                hasData = true;


                const cells = row.querySelectorAll("td");

                const location =
                    cells.length > 1
                        ? cells[1].textContent.trim()
                        : cells[0]?.textContent.trim() || "";


                rows += `
                    <tr>

                        <td>${escapeHtml(location)}</td>

                        <td class="num">${values[0]}</td>
                        <td class="num">${values[1]}</td>
                        <td class="num">${values[2]}</td>

                        <td class="num">${values[3]}</td>
                        <td class="num">${values[4]}</td>
                        <td class="num">${values[5]}</td>

                        <td class="num">${values[6]}</td>
                        <td class="num">${values[7]}</td>
                        <td class="num">${values[8]}</td>

                        <td class="num">${values[9]}</td>
                        <td class="num">${values[10]}</td>
                        <td class="num">${values[11]}</td>

                        <td class="num">${values[12]}</td>
                        <td class="num">${values[13]}</td>

                    </tr>
                `;
            });


            if (!hasData) {
                return;
            }


            const title =
                getCardTitle(
                    table,
                    "Activities of the Service at the Seaport & Marine Base"
                );


            html += `
                <div class="redas-preview-section">

                    <div class="redas-preview-section-title">
                        <i class="fas fa-ship"></i>
                        ${escapeHtml(title)}
                    </div>

                    <div class="redas-preview-section-body">

                        <div class="redas-preview-table-wrap">

                            <table class="redas-preview-table">

                                <thead>

                                    <tr>

                                        <th rowspan="2">
                                            Seaport / Marine Base
                                        </th>

                                        <th colspan="3" class="num">
                                            Passenger Arrival
                                        </th>

                                        <th colspan="3" class="num">
                                            Passenger Departure
                                        </th>

                                        <th colspan="3" class="num">
                                            Crew Arrival
                                        </th>

                                        <th colspan="3" class="num">
                                            Crew Departure
                                        </th>

                                        <th colspan="2" class="num">
                                            Boat
                                        </th>

                                    </tr>

                                    <tr>

                                        <th class="num">M</th>
                                        <th class="num">F</th>
                                        <th class="num">Total</th>

                                        <th class="num">M</th>
                                        <th class="num">F</th>
                                        <th class="num">Total</th>

                                        <th class="num">M</th>
                                        <th class="num">F</th>
                                        <th class="num">Total</th>

                                        <th class="num">M</th>
                                        <th class="num">F</th>
                                        <th class="num">Total</th>

                                        <th class="num">Arrival</th>
                                        <th class="num">Departure</th>

                                    </tr>

                                </thead>

                                <tbody>
                                    ${rows}
                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>
            `;
        });


        return html;
    }


    /**********************************************************************
     * AIRPORT
     **********************************************************************/
    function buildAirportPreview() {

        const tables =
            document.querySelectorAll(".airport-table");

        if (!tables.length) {
            return "";
        }

        let html = "";


        tables.forEach(function (table) {

            let rows = "";
            let hasData = false;


            table.querySelectorAll("tbody tr").forEach(function (row) {

                if (row.classList.contains("airport-total")) {
                    return;
                }


                const amInput =
                    row.querySelector(".airport-arrival-male");

                const afInput =
                    row.querySelector(".airport-arrival-female");

                const dmInput =
                    row.querySelector(".airport-departure-male");

                const dfInput =
                    row.querySelector(".airport-departure-female");

                const totalInput =
                    row.querySelector(".airport-row-total");


                if (
                    !amInput ||
                    !afInput ||
                    !dmInput ||
                    !dfInput ||
                    !totalInput
                ) {
                    return;
                }


                const am = valueNumber(amInput);
                const af = valueNumber(afInput);
                const dm = valueNumber(dmInput);
                const df = valueNumber(dfInput);
                const total = valueNumber(totalInput);


                if (am === 0 && af === 0 && dm === 0 && df === 0) {
                    return;
                }


                hasData = true;


                const cells = row.querySelectorAll("td");

                const category =
                    cells.length > 1
                        ? cells[1].textContent.trim()
                        : cells[0]?.textContent.trim() || "";


                rows += `
                    <tr>

                        <td>${escapeHtml(category)}</td>

                        <td class="num">${am}</td>
                        <td class="num">${af}</td>

                        <td class="num">${dm}</td>
                        <td class="num">${df}</td>

                        <td class="num">${total}</td>

                    </tr>
                `;
            });


            if (!hasData) {
                return;
            }


            const totalRow =
                table.querySelector(".airport-total");


            const title =
                getCardTitle(
                    table,
                    "Passenger Movement Across International Airports"
                );


            html += `
                <div class="redas-preview-section">

                    <div class="redas-preview-section-title">
                        <i class="fas fa-plane"></i>
                        ${escapeHtml(title)}
                    </div>

                    <div class="redas-preview-section-body">

                        <div class="redas-preview-table-wrap">

                            <table class="redas-preview-table">

                                <thead>

                                    <tr>

                                        <th>
                                            Movement Category
                                        </th>

                                        <th colspan="2" class="num">
                                            Arrival
                                        </th>

                                        <th colspan="2" class="num">
                                            Departure
                                        </th>

                                        <th class="num">
                                            Total
                                        </th>

                                    </tr>

                                    <tr>

                                        <th></th>

                                        <th class="num">Male</th>
                                        <th class="num">Female</th>

                                        <th class="num">Male</th>
                                        <th class="num">Female</th>

                                        <th></th>

                                    </tr>

                                </thead>

                                <tbody>
                                    ${rows}
                                </tbody>

                                <tfoot>

                                    <tr class="redas-preview-total">

                                        <th>AIRPORT TOTAL</th>

                                        <td class="num">
                                            ${escapeHtml(
                                                totalRow?.querySelector(
                                                    ".total-airport-arrival-male"
                                                )?.value || "0"
                                            )}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(
                                                totalRow?.querySelector(
                                                    ".total-airport-arrival-female"
                                                )?.value || "0"
                                            )}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(
                                                totalRow?.querySelector(
                                                    ".total-airport-departure-male"
                                                )?.value || "0"
                                            )}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(
                                                totalRow?.querySelector(
                                                    ".total-airport-departure-female"
                                                )?.value || "0"
                                            )}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(
                                                totalRow?.querySelector(
                                                    ".total-airport-grand"
                                                )?.value || "0"
                                            )}
                                        </td>

                                    </tr>

                                </tfoot>

                            </table>

                        </div>

                    </div>

                </div>
            `;
        });


        return html;
    }


    /**********************************************************************
     * OFFSHORE
     **********************************************************************/
    function buildOffshorePreview() {

        const cards =
            document.querySelectorAll(".offshore-card");

        if (!cards.length) {
            return "";
        }

        let html = "";


        cards.forEach(function (card) {

            const state =
                card.querySelector(".offshore-state")?.value || "";


            let rows = "";


            card.querySelectorAll("tbody tr").forEach(function (row) {

                const terminal =
                    row.querySelector(".terminalName")?.value?.trim() || "";

                const tankers =
                    valueNumber(
                        row.querySelector(".tankerCount")
                    );

                const crew =
                    valueNumber(
                        row.querySelector(".crewCount")
                    );


                if (!terminal && tankers === 0 && crew === 0) {
                    return;
                }


                rows += `
                    <tr>

                        <td>
                            ${escapeHtml(terminal || "-")}
                        </td>

                        <td class="num">
                            ${tankers}
                        </td>

                        <td class="num">
                            ${crew}
                        </td>

                    </tr>
                `;
            });


            if (!rows) {
                return;
            }


            html += `
                <div class="redas-preview-section">

                    <div class="redas-preview-section-title">
                        <i class="fas fa-anchor"></i>
                        Offshore Activities
                        ${state ? " - " + escapeHtml(state) : ""}
                    </div>

                    <div class="redas-preview-section-body">

                        <div class="redas-preview-table-wrap">

                            <table class="redas-preview-table">

                                <thead>

                                    <tr>

                                        <th>Terminal Name</th>
                                        <th class="num">Tankers</th>
                                        <th class="num">Crew</th>

                                    </tr>

                                </thead>

                                <tbody>
                                    ${rows}
                                </tbody>

                                <tfoot>

                                    <tr class="redas-preview-total">

                                        <th>STATE TOTAL</th>

                                        <td class="num">
                                            ${escapeHtml(
                                                card.querySelector(
                                                    ".totalTankers"
                                                )?.value || "0"
                                            )}
                                        </td>

                                        <td class="num">
                                            ${escapeHtml(
                                                card.querySelector(
                                                    ".totalCrew"
                                                )?.value || "0"
                                            )}
                                        </td>

                                    </tr>

                                </tfoot>

                            </table>

                        </div>

                    </div>

                </div>
            `;
        });


        return html;
    }


    /**********************************************************************
     * GENERAL COMMENTS
     *
     * This function is deliberately flexible; it only reads fields if they
     * actually exist on the page.
     **********************************************************************/
    function buildGeneralReportPreview() {

        const fieldGroups = [
            {
                title:"Security Report",
                selectors:[
                    '[name="border[general][security]"]',
                    '[name="security_report"]',
                    '#securityReport'
                ]
            },
            {
                title:"Other Reports",
                selectors:[
                    '[name="border[general][other]"]',
                    '[name="other_report"]',
                    '#otherReport'
                ]
            },
            {
                title:"Challenges",
                selectors:[
                    '[name="border[general][challenges]"]',
                    '[name="challenges"]',
                    '#challenges'
                ]
            },
            {
                title:"Recommendations / Way Forward",
                selectors:[
                    '[name="border[general][recommendations]"]',
                    '[name="recommendations"]',
                    '#recommendations'
                ]
            },
            {
                title:"Conclusion",
                selectors:[
                    '[name="border[general][conclusion]"]',
                    '[name="conclusion"]',
                    '#conclusion'
                ]
            }
        ];


        let html = "";
        let hasData = false;


        fieldGroups.forEach(function (group) {

            const value = getFieldValue(group.selectors);

            if (!value) {
                return;
            }

            hasData = true;


            html += `
                <div class="redas-preview-narrative">

                    <div class="redas-preview-narrative-title">
                        ${escapeHtml(group.title)}
                    </div>

                    <div class="redas-preview-text">
                        ${escapeHtml(value)}
                    </div>

                </div>
            `;
        });


        /*
         * Attachment input is only added if it exists.
         */
        const attachmentInput =
            byId("attachInput");


        if (
            attachmentInput &&
            attachmentInput.files &&
            attachmentInput.files.length
        ) {

            hasData = true;


            html += `
                <div class="redas-preview-narrative">

                    <div class="redas-preview-narrative-title">
                        <i class="fas fa-paperclip"></i>
                        Attachments
                    </div>

                    <ul style="
                        margin:0;
                        padding-left:20px;
                    ">

                        ${
                            Array.from(
                                attachmentInput.files
                            )
                            .map(function (file) {

                                return `
                                    <li style="margin-bottom:5px;">
                                        <strong>
                                            ${escapeHtml(file.name)}
                                        </strong>

                                        <span style="
                                            color:#64748b;
                                            margin-left:5px;
                                        ">
                                            (${formatFileSize(file.size)})
                                        </span>
                                    </li>
                                `;

                            })
                            .join("")
                        }

                    </ul>

                </div>
            `;
        }


        if (!hasData) {
            return "";
        }


        return `
            <div class="redas-preview-section">

                <div class="redas-preview-section-title">
                    <i class="fas fa-comments"></i>
                    General Report / Other Comments
                </div>

                <div class="redas-preview-section-body">
                    ${html}
                </div>

            </div>
        `;
    }


    /**********************************************************************
     * GENERATE PREVIEW
     **********************************************************************/
    function generatePreview() {

        const container =
            byId("previewContent");


        if (!container) {
            console.error(
                "REDAS Preview: #previewContent was not found."
            );
            return;
        }


        /*
         * Recalculate everything first. The calculators live in the shared
         * partial (user.states.sections.border) behind window.borderFormRecalc.
         */
        try {

            if (typeof window.borderFormRecalc === "function") {
                window.borderFormRecalc();
            }

        } catch (error) {

            console.error(
                "REDAS Preview calculation error:",
                error
            );

        }


        let html =
            previewStyles() +
            '<div class="redas-preview">' +
            buildPreviewHeader();


        let sectionCount = 0;


        const sections = [
            buildPersonnelPreview(),
            buildLandBorderPreview(),
            buildNationalityPreview(),
            buildSeaportPreview(),
            buildAirportPreview(),
            buildOffshorePreview(),
            buildGeneralReportPreview()
        ];


        sections.forEach(function (section) {

            if (section) {

                html += section;
                sectionCount++;

            }

        });


        if (sectionCount === 0) {

            html += `
                <div class="redas-preview-section">

                    <div class="redas-preview-section-title">
                        <i class="fas fa-file-circle-exclamation"></i>
                        Preview
                    </div>

                    <div class="redas-preview-section-body">

                        <div class="redas-preview-empty">
                            No information has been entered yet.
                            Please complete the applicable sections
                            before reviewing the report.
                        </div>

                    </div>

                </div>
            `;
        }


        html += `
            <div class="redas-preview-section">

                <div class="redas-preview-section-title">
                    <i class="fas fa-check-circle"></i>
                    Review Before Submission
                </div>

                <div class="redas-preview-section-body">

                    <div class="redas-preview-note">

                        <strong>
                            Please carefully verify the report.
                        </strong>

                        <div style="margin-top:5px;">
                            Check all figures, personnel strength,
                            border movements, nationality returns,
                            marine activities, airport movements,
                            offshore activities and comments before
                            submission.
                        </div>

                    </div>

                </div>

            </div>
        `;


        html += "</div>";


        container.innerHTML = html;
    }


    /**********************************************************************
     * INITIALIZE PREVIEW
     *
     * Back to Edit / Submit Return are wired by the directorate layout's
     * global script (.border-edit-btn / .border-submit-return-btn).
     **********************************************************************/
    function initializePreview() {

        /*
         * Preview tab
         */
        const previewTab =
            document.querySelector(
                '.entry-tab[data-tab="preview"]'
            );


        if (previewTab) {

            previewTab.addEventListener(
                "click",
                function () {

                    /*
                     * Small delay allows the existing tab-switching code
                     * to activate the Preview panel first.
                     */
                    window.setTimeout(
                        generatePreview,
                        0
                    );

                }
            );

        }

    }


    /*
     * Run after DOM is ready.
     */
    if (document.readyState === "loading") {

        document.addEventListener(
            "DOMContentLoaded",
            initializePreview
        );

    } else {

        initializePreview();

    }

})();

</script>

@endsection
