
    // PROVOST/SECURITY UNIT JAVASCRIPT LOGIC

    // Section 2 and 3 on Staff Strength

    const unitsBody = document.getElementById("provostUnitsBody");
    const strengthBody = document.getElementById("staffStrengthBody");
    const totalStrengthInput = document.getElementById("staffStrengthGrandTotal");

    /*
     * PROVOST/SECURITY UNIT page only — no-op when its distinctive
     * elements are absent (e.g. on other directorate pages).
     */
    if (unitsBody && strengthBody && totalStrengthInput && document.getElementById("addProvostUnit")) {

    // STAFF STRENGTH

    function calculateStaffStrengthTotal() {

        let total = 0;

        document.querySelectorAll(".staff-strength-input").forEach(input => {

            total += Number(input.value) || 0;

        });

        totalStrengthInput.value = total;

    }

    function bindStrengthEvents() {

        document.querySelectorAll(".staff-strength-input").forEach(input => {

            input.oninput = calculateStaffStrengthTotal;

        });

    }

    function renderStaffStrengthTable() {

        const existingStrengths = [];

        document.querySelectorAll(".staff-strength-input").forEach(input => {

            existingStrengths.push(input.value);

        });

        strengthBody.innerHTML = "";

        document.querySelectorAll(".provost-unit-name").forEach((input, index) => {

            const row = document.createElement("tr");

            row.innerHTML = `

            <td>${index + 1}</td>

            <td>

                <input
                    type="hidden"
                    name="staff_strength[${index}][unit]"
                    value="${input.value}"
                >

                ${input.value || "-"}

            </td>

            <td>

                <input
                    type="number"
                    min="0"
                    class="ni staff-strength-input"
                    name="staff_strength[${index}][strength]"
                    value="${existingStrengths[index] || ''}"
                    placeholder="0"
                >

            </td>

        `;

            strengthBody.appendChild(row);

        });

        bindStrengthEvents();

        calculateStaffStrengthTotal();

    }

    // ADD SUB UNIT

    document
        .getElementById("addProvostUnit")
        .addEventListener("click", function () {

            const index = unitsBody.querySelectorAll("tr").length;

            const row = document.createElement("tr");

            row.classList.add("data-row");

            row.innerHTML = `

        <td>${index + 1}</td>

        <td>

            <input
                type="text"
                class="ni provost-unit-name"
                name="provost_units[${index}][name]"
                placeholder="Enter Sub Unit Name"
            >

        </td>

        <td>

            <button
                type="button"
                class="remove-row-btn removeProvostUnit"
            >

                <i class="fas fa-trash"></i>

            </button>

        </td>

    `;

            unitsBody.appendChild(row);

            renderStaffStrengthTable();

        });


    // UPDATE STAFF TABLE WHEN USER TYPES

    document.addEventListener("input", function (e) {

        if (!e.target.classList.contains("provost-unit-name")) return;

        renderStaffStrengthTable();

    });


    // DELETE SUB UNIT

    document.addEventListener("click", function (e) {

        const button = e.target.closest(".removeProvostUnit");

        if (!button) return;

        if (unitsBody.querySelectorAll("tr").length === 1) {

            alert("At least one Sub Unit is required.");

            return;

        }

        button.closest("tr").remove();

        document.querySelectorAll("#provostUnitsBody tr")
            .forEach((row, index) => {

                row.querySelector("td").textContent = index + 1;

                row.querySelector(".provost-unit-name")
                    .name = `provost_units[${index}][name]`;

            });

        renderStaffStrengthTable();

    });

    renderStaffStrengthTable();

    } // end PROVOST/SECURITY UNIT page guard

    /*
    | FIREARMS TABLES
    |
    | Handles:
    | - Add Firearm Row
    | - Auto Numbering
    | - Total Firearm Types
    | - Total Ammunition
    | - Delete Row
    |
    */

    function initialiseFirearmsTable(config) {

        const tbody = document.getElementById(config.body);

        const countInput = document.getElementById(config.count);

        const ammoTotalInput = document.getElementById(config.ammo);

        if (!tbody) return;

        function updateTotals() {

            let ammoTotal = 0;

            const rows = tbody.querySelectorAll("tr");

            rows.forEach((row, index) => {

                row.querySelector("td:first-child").textContent = index + 1;

                row.querySelector(".firearm-type").name =
                    `${config.prefix}[${index}][type]`;

                row.querySelector(".firearm-ammo").name =
                    `${config.prefix}[${index}][ammunition]`;

                ammoTotal += Number(
                    row.querySelector(".firearm-ammo").value
                ) || 0;

            });

            countInput.value = rows.length;

            ammoTotalInput.value = ammoTotal;

        }

        tbody.addEventListener("input", function (e) {

            if (e.target.classList.contains("firearm-ammo")) {

                updateTotals();

            }

        });

        tbody.addEventListener("click", function (e) {

            const button = e.target.closest(".remove-firearm-row");

            if (!button) return;

            button.closest("tr").remove();

            updateTotals();

        });

        document.querySelector(`[data-body="${config.body}"]`)
            .addEventListener("click", function () {

                const index = tbody.querySelectorAll("tr").length;

                const row = document.createElement("tr");

                row.classList.add("data-row");

                row.innerHTML = `
                    <td>${index + 1}</td>

                    <td>
                        <input
                            type="text"
                            class="ni firearm-type"
                            name="${config.prefix}[${index}][type]"
                        >
                    </td>

                    <td>
                        <input
                            type="number"
                            min="0"
                            class="ni firearm-ammo"
                            name="${config.prefix}[${index}][ammunition]"
                        >
                    </td>

                    <td>

                        <button
                            type="button"
                            class="btn-icon btn-danger remove-firearm-row"
                        >
                            <i class="fas fa-trash"></i>
                        </button>

                    </td>
                    `;

                tbody.appendChild(row);

                updateTotals();

            });

        updateTotals();

    }


    initialiseFirearmsTable({

        body: "provostFirearmsBody",

        prefix: "provost_firearms",

        count: "provostFirearmCount",

        ammo: "provostAmmoTotal"

    });

    initialiseFirearmsTable({

        body: "rrsFirearmsBody",

        prefix: "rrs_firearms",

        count: "rrsFirearmCount",

        ammo: "rrsAmmoTotal"

    });

    initialiseFirearmsTable({

        body: "jtfFirearmsBody",

        prefix: "jtf_firearms",

        count: "jtfFirearmCount",

        ammo: "jtfAmmoTotal"

    });


    // Generic Dynamic Single Column Tables

    function initialiseSimpleDynamicTable(config) {

        const tbody = document.getElementById(config.body);

        if (!tbody) return;

        const addButton = document.querySelector(
            `[data-body="${config.body}"]`
        );

        function refreshRows() {

            tbody.querySelectorAll("tr").forEach((row, index) => {

                row.querySelector("td:first-child").textContent = index + 1;

                row.querySelector("input").name =
                    `${config.prefix}[${index}][description]`;

            });

        }

        addButton.addEventListener("click", function () {

            const index = tbody.querySelectorAll("tr").length;

            const row = document.createElement("tr");

            row.classList.add("data-row");

            row.innerHTML = `
            <td>${index + 1}</td>

            <td>

                <input
                    type="text"
                    class="ni"
                    name="${config.prefix}[${index}][description]"
                    placeholder="${config.placeholder}"
                >

                    </td>

                    <td>

                        <button
                            type="button"
                            class="btn-icon btn-danger remove-simple-row"
                        >
                            <i class="fas fa-trash"></i>
                        </button>

                    </td>
                `;

            tbody.appendChild(row);

        });

        tbody.addEventListener("click", function (e) {

            const button = e.target.closest(".remove-simple-row");

            if (!button) return;

            button.closest("tr").remove();

            refreshRows();

        });

        refreshRows();

    }


    initialiseSimpleDynamicTable({

        body: "responsibilityBody",

        prefix: "responsibilities",

        placeholder: "Enter Area of Responsibility"

    });

    initialiseSimpleDynamicTable({

        body: "activitiesBody",

        prefix: "activities",

        placeholder: "Enter Activity"

    });

    initialiseSimpleDynamicTable({

        body: "challengesBody",

        prefix: "challenges",

        placeholder: "Enter Challenge"

    });



