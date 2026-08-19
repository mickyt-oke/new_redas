{{-- ============================================================
    8. INCIDENT: (Communication)
============================================================= --}}

<div id="incidentsCommunicationCard" class="visa-card animate-fade-up">

    <!-- Section Header -->
    <button type="button" id="incidentsCommunicationToggle" class="visa-accordion-header">
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-satellite-dish"></i>
            </div>
            <div style="text-align: left;">
                <h5>Incident (Communication)</h5>
                <small>Track VHF radios, satellite links, inter-office links and VoIP telephone system failures</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span class="section-status status-progress" id="incidentsCommunicationStatus">In Progress</span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <!-- Section Body -->
    <div class="visa-card-body">
        <div class="table-toolbar">
            <div class="table-toolbar-left">
                <strong>Communication Link Log</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" class="btn-nis btn-primary-nis btn-sm"
                        data-ict-target="incidents_communication_tbody"
                        data-ict-prefix="incidents_communication"
                        data-ict-fields='[{"name":"incident","type":"text"},{"name":"datetime","type":"datetime-local"},{"name":"systems_affected","type":"text"},{"name":"cause","type":"text"},{"name":"action_taken","type":"text"},{"name":"impact_assessment","type":"text"},{"name":"recommendation","type":"text"}]'>
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="visa-table">
                <thead>
                    <tr>
                        <th style="width:50px;">S/N</th>
                        <th>Incident</th>
                        <th style="width:160px;">Date/Time</th>
                        <th>Systems Affected</th>
                        <th>Cause</th>
                        <th>Action Taken</th>
                        <th>Impact Assessment</th>
                        <th>Recommendation</th>
                        <th style="width:60px;">Action</th>
                    </tr>
                </thead>
                <tbody id="incidents_communication_tbody">
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">1</td>
                        <td><input type="text" name="incidents_communication[0][incident]" class="ni"></td>
                        <td><input type="datetime-local" name="incidents_communication[0][datetime]" class="ni"></td>
                        <td><input type="text" name="incidents_communication[0][systems_affected]" class="ni"></td>
                        <td><input type="text" name="incidents_communication[0][cause]" class="ni"></td>
                        <td><input type="text" name="incidents_communication[0][action_taken]" class="ni"></td>
                        <td><input type="text" name="incidents_communication[0][impact_assessment]" class="ni"></td>
                        <td><input type="text" name="incidents_communication[0][recommendation]" class="ni"></td>
                        <td class="text-center">
                            <button type="button" class="btn-nis btn-ghost btn-sm btn-delete-row" style="color:var(--color-danger);padding:4px 8px;">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="visa-total-row">
                        <th colspan="4" style="text-align:left;">TOTAL</th>
                        <th colspan="5"><input readonly id="incidentsCommunicationTotal" class="ni total-input" value="0" style="width:100px;"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total Communication Incidents : <strong id="incidentsCommunicationSummary">0 Incidents</strong>
            </div>
            <div class="section-buttons">
                <button
                    type="submit"
                    name="action"
                    value="draft"
                    class="btn-nis btn-success">
                    <i class="fas fa-save"></i>
                    Save Section
                </button>
                <button type="button" class="btn-nis btn-ghost" id="resetIncidentsCommunication">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
            </div>
        </div>
    </div>

</div>
