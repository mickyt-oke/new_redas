
const landBorderStates = @json($states);

/**************************************************************************
 * REDAS BORDER MANAGEMENT CALCULATIONS
 *
 * NOTE: Tab switching is handled centrally in partials/footer.blade.php
 * (it also scrolls the tab bar), so it is intentionally NOT duplicated here.
 **************************************************************************/

document.addEventListener("DOMContentLoaded", function () {

    calculateStaffStrength();
    calculateLandBorder();
    calculateSeaport();
    calculateAirportMovement();

});

/**************************************************************************
 * Auto Calculate on Input (consolidated)
 **************************************************************************/

document.addEventListener("input", function (e) {

    if (e.target.closest(".staff-table")) {
        calculateStaffStrength();
    }

    if (e.target.closest(".land-border-table")) {
        calculateLandBorder();
    }

    if (e.target.closest(".seaport-table")) {
        calculateSeaport();
    }

    if (e.target.closest(".airport-table")) {
        calculateAirportMovement();
    }

    if (
        e.target.classList.contains("tankerCount") ||
        e.target.classList.contains("crewCount")
    ) {
        const offshoreCard = e.target.closest(".offshore-card");
        calculateStateTotals(offshoreCard);
        calculateGrandTotals();
    }

    /*
     * Nationality rows — scoped to rows inside .nationality-table so
     * keystrokes in other tables no longer throw on unguarded lookups.
     */
    const nationalityRow = e.target.closest(".nationality-table tbody tr");
    if (nationalityRow) {
        calculateNationalityRow(nationalityRow);
        updateCommandTotals(
            nationalityRow.closest(".nationality-card")
        );
    }

});

/**************************************************************************
 * Staff Strenght
 **************************************************************************/
function calculateStaffStrength() {

    document.querySelectorAll(".staff-table").forEach(function(table){

        let male=0;
        let female=0;

        table.querySelectorAll("tbody tr").forEach(function(row){

            if(row.classList.contains("total-row")) return;

            let m=parseInt(row.querySelector(".male").value)||0;
            let f=parseInt(row.querySelector(".female").value)||0;

            row.querySelector(".total").value=m+f;

            male+=m;
            female+=f;

        });

        document.getElementById("grandMale").value=male;
        document.getElementById("grandFemale").value=female;
        document.getElementById("grandTotal").value=male+female;

    });

}
/**************************************************************************
 * Land Border
 **************************************************************************/
function calculateLandBorder(){

document.querySelectorAll(".land-border-table").forEach(function(table){

let arrivalMale=0;
let arrivalFemale=0;

let departureMale=0;
let departureFemale=0;

table.querySelectorAll("tbody tr").forEach(function(row){

if(row.classList.contains("total-row")) return;

let am=parseInt(row.querySelector(".arrival-male").value)||0;
let af=parseInt(row.querySelector(".arrival-female").value)||0;

let dm=parseInt(row.querySelector(".departure-male").value)||0;
let df=parseInt(row.querySelector(".departure-female").value)||0;

row.querySelector(".arrival-total").value=am+af;
row.querySelector(".departure-total").value=dm+df;

arrivalMale+=am;
arrivalFemale+=af;

departureMale+=dm;
departureFemale+=df;

});

const total=table.querySelector(".total-row").querySelectorAll("input");

total[0].value=arrivalMale;
total[1].value=arrivalFemale;
total[2].value=arrivalMale+arrivalFemale;

total[3].value=departureMale;
total[4].value=departureFemale;
total[5].value=departureMale+departureFemale;

});

}
/**************************************************************************
 * ACTIVITIES OF THE SERVICE AT THE SEAPORT & MARINE BASE
 **************************************************************************/
function num(input){
    return parseInt(input?.value || 0) || 0;
}

function calculateSeaport(){

    document.querySelectorAll(".seaport-table").forEach(function(table){

        let totals = {

            passengerArrivalMale:0,
            passengerArrivalFemale:0,

            passengerDepartureMale:0,
            passengerDepartureFemale:0,

            crewArrivalMale:0,
            crewArrivalFemale:0,

            crewDepartureMale:0,
            crewDepartureFemale:0,

            boatArrival:0,
            boatDeparture:0

        };

        table.querySelectorAll("tbody tr").forEach(function(row){

            if(row.classList.contains("state-total")) return;

            //-------------------------------------
            // Passenger Arrival
            //-------------------------------------

            let paMale = num(row.querySelector(".passenger-arrival-male"));
            let paFemale = num(row.querySelector(".passenger-arrival-female"));

            row.querySelector(".passenger-arrival-total").value =
                paMale + paFemale;

            totals.passengerArrivalMale += paMale;
            totals.passengerArrivalFemale += paFemale;


            //-------------------------------------
            // Passenger Departure
            //-------------------------------------

            let pdMale = num(row.querySelector(".passenger-departure-male"));
            let pdFemale = num(row.querySelector(".passenger-departure-female"));

            row.querySelector(".passenger-departure-total").value =
                pdMale + pdFemale;

            totals.passengerDepartureMale += pdMale;
            totals.passengerDepartureFemale += pdFemale;


            //-------------------------------------
            // Crew Arrival
            //-------------------------------------

            let caMale = num(row.querySelector(".crew-arrival-male"));
            let caFemale = num(row.querySelector(".crew-arrival-female"));

            row.querySelector(".crew-arrival-total").value =
                caMale + caFemale;

            totals.crewArrivalMale += caMale;
            totals.crewArrivalFemale += caFemale;


            //-------------------------------------
            // Crew Departure
            //-------------------------------------

            let cdMale = num(row.querySelector(".crew-departure-male"));
            let cdFemale = num(row.querySelector(".crew-departure-female"));

            row.querySelector(".crew-departure-total").value =
                cdMale + cdFemale;

            totals.crewDepartureMale += cdMale;
            totals.crewDepartureFemale += cdFemale;


            //-------------------------------------
            // Boat
            //-------------------------------------

            totals.boatArrival +=
                num(row.querySelector(".boat-arrival"));

            totals.boatDeparture +=
                num(row.querySelector(".boat-departure"));

        });

        //-----------------------------------------
        // STATE TOTAL ROW
        //-----------------------------------------

        const totalRow = table.querySelector(".state-total");

        totalRow.querySelector(".total-arrival-male").value =
            totals.passengerArrivalMale;

        totalRow.querySelector(".total-arrival-female").value =
            totals.passengerArrivalFemale;

        totalRow.querySelector(".total-arrival").value =
            totals.passengerArrivalMale +
            totals.passengerArrivalFemale;


        totalRow.querySelector(".total-departure-male").value =
            totals.passengerDepartureMale;

        totalRow.querySelector(".total-departure-female").value =
            totals.passengerDepartureFemale;

        totalRow.querySelector(".total-departure").value =
            totals.passengerDepartureMale +
            totals.passengerDepartureFemale;


        totalRow.querySelector(".total-crew-arrival-male").value =
            totals.crewArrivalMale;

        totalRow.querySelector(".total-crew-arrival-female").value =
            totals.crewArrivalFemale;

        totalRow.querySelector(".total-crew-arrival").value =
            totals.crewArrivalMale +
            totals.crewArrivalFemale;


        totalRow.querySelector(".total-crew-departure-male").value =
            totals.crewDepartureMale;

        totalRow.querySelector(".total-crew-departure-female").value =
            totals.crewDepartureFemale;

        totalRow.querySelector(".total-crew-departure").value =
            totals.crewDepartureMale +
            totals.crewDepartureFemale;


        totalRow.querySelector(".total-boat-arrival").value =
            totals.boatArrival;

        totalRow.querySelector(".total-boat-departure").value =
            totals.boatDeparture;


        //-----------------------------------------
        // SUMMARY CARDS
        //-----------------------------------------

        const card = table.closest(".redas-card");

        card.querySelector(".summary-arrival").textContent =
            totals.passengerArrivalMale +
            totals.passengerArrivalFemale;

        card.querySelector(".summary-departure").textContent =
            totals.passengerDepartureMale +
            totals.passengerDepartureFemale;

        card.querySelector(".summary-crew-arrival").textContent =
            totals.crewArrivalMale +
            totals.crewArrivalFemale;

        card.querySelector(".summary-crew-departure").textContent =
            totals.crewDepartureMale +
            totals.crewDepartureFemale;

        card.querySelector(".summary-boat-arrival").textContent =
            totals.boatArrival;

        card.querySelector(".summary-boat-departure").textContent =
            totals.boatDeparture;

    });

}


/**************************************************************
 * PASSENGER MOVEMENT ACROSS INTERNATIONAL AIRPORTS
 **************************************************************/

function numberValue(input){
    return parseInt(input?.value || 0) || 0;
}

function calculateAirportMovement(){

    document.querySelectorAll(".airport-table").forEach(function(table){

        let arrivalMale = 0;
        let arrivalFemale = 0;

        let departureMale = 0;
        let departureFemale = 0;

        table.querySelectorAll("tbody tr").forEach(function(row){

            if(row.classList.contains("airport-total"))
                return;

            let arrivalMaleInput =
                row.querySelector(".airport-arrival-male");

            if(!arrivalMaleInput)
                return;

            let arrivalFemaleInput =
                row.querySelector(".airport-arrival-female");

            let departureMaleInput =
                row.querySelector(".airport-departure-male");

            let departureFemaleInput =
                row.querySelector(".airport-departure-female");

            let totalInput =
                row.querySelector(".airport-row-total");

            let am = numberValue(arrivalMaleInput);
            let af = numberValue(arrivalFemaleInput);

            let dm = numberValue(departureMaleInput);
            let df = numberValue(departureFemaleInput);

            //---------------------------------------
            // Row Total
            //---------------------------------------

            totalInput.value = am + af + dm + df;

            //---------------------------------------
            // Running Totals
            //---------------------------------------

            arrivalMale += am;
            arrivalFemale += af;

            departureMale += dm;
            departureFemale += df;

        });

        //---------------------------------------
        // Airport Total Row
        //---------------------------------------

        const totalRow = table.querySelector(".airport-total");

        totalRow.querySelector(".total-airport-arrival-male").value =
            arrivalMale;

        totalRow.querySelector(".total-airport-arrival-female").value =
            arrivalFemale;

        totalRow.querySelector(".total-airport-departure-male").value =
            departureMale;

        totalRow.querySelector(".total-airport-departure-female").value =
            departureFemale;

        totalRow.querySelector(".total-airport-grand").value =
            arrivalMale +
            arrivalFemale +
            departureMale +
            departureFemale;

        //---------------------------------------
        // Airport Summary
        //---------------------------------------

        const card = table.closest(".redas-card");

        if(card){

            card.querySelector(".airport-summary-arrival").textContent =
                arrivalMale + arrivalFemale;

            card.querySelector(".airport-summary-departure").textContent =
                departureMale + departureFemale;

            card.querySelector(".airport-summary-grand").textContent =
                arrivalMale +
                arrivalFemale +
                departureMale +
                departureFemale;

        }

    });

}


//  * Live calculation & initial load are handled by the consolidated
//  * listeners at the top of this script.

/**************************************************************************
 * OFFSHORE ACTIVITIES
 **************************************************************************/
const offshoreData = {

    "Akwa Ibom": [
        "BOP (QIT)",
        "YOHO",
        "ODUDU",
        "EBOK",
        "ATAN"
    ],

    "Bayelsa": [
        "AGBAMI",
        "BONGA/EA",
        "TULJA",
        "PENNINGTON",
        "BRASS"
    ],

    "Delta": [
        "Escravos",
        "Forcados"
    ],

    "Rivers": [
        "Bonny",
        "Onne",
        "Otakikpo",
        "Okrika Jetty",
        "Akpo Field",
        "Egina Field",
        "Agbami Field"
    ],

    "Lagos": [
        "LADOL",
        "EA Terminal",
        "Abo Terminal",
        "Bonga Terminal",
        "Erha Terminal"
    ]

};

document.addEventListener("DOMContentLoaded", function () {

    const btnAddState = document.getElementById("btnAddState");
    const offshoreStates = document.getElementById("offshoreStates");
    const stateTemplate = document.getElementById("offshoreStateTemplate");

    if (!btnAddState || !offshoreStates || !stateTemplate) {
        console.error("Offshore elements not found.");
        return;
    }

    btnAddState.addEventListener("click", function () {

        const clone = stateTemplate.content.cloneNode(true);

        const select = clone.querySelector(".offshore-state");

        Object.keys(offshoreData).forEach(function(state){

            const option = document.createElement("option");

            option.value = state;
            option.textContent = state;

            select.appendChild(option);

        });

        offshoreStates.appendChild(clone);

    });

});

// offshoreData
function createTerminalRow(terminalName = "") {

    const template = document.getElementById("terminalTemplate");

    const row = template.content.cloneNode(true);

    row.querySelector(".terminalName").value = terminalName;

    return row;

}

// LOAD STATE

document.addEventListener("change", function (e) {

    if (!e.target.classList.contains("offshore-state")) return;

    const select = e.target;

    const state = select.value;

    if (state === "") return;

    // Check if this state already exists
    let duplicate = false;

    document.querySelectorAll(".offshore-state").forEach(function(item){

        if(item !== select && item.value === state){

            duplicate = true;

        }

    });

    if(duplicate){

        alert(state + " has already been added.");

        select.value = "";

        return;

    }

    const card = select.closest(".offshore-card");

    const tbody = card.querySelector("tbody");

    tbody.innerHTML = "";

    offshoreData[state].forEach(function(terminal){

        tbody.appendChild(createTerminalRow(terminal));

    });

    updateSerialNumbers(card);

    calculateStateTotals(card);

    calculateGrandTotals();

});

// Serial Number Function

function updateSerialNumbers(card){

    card.querySelectorAll("tbody tr").forEach(function(row,index){

        row.querySelector(".sn").textContent = index + 1;

    });

}


// ADD TERMINAL

document.addEventListener("click", function (e) {

    if (!e.target.closest(".btnAddTerminal")) return;

    const card = e.target.closest(".offshore-card");

    const tbody = card.querySelector("tbody");

    tbody.appendChild(createTerminalRow());

    updateSerialNumbers(card);

});



//  * REMOVE TERMINAL

document.addEventListener("click", function (e) {

    if (!e.target.closest(".btnRemoveTerminal")) return;

    const row = e.target.closest("tr");

    const card = e.target.closest(".offshore-card");

    row.remove();

    updateSerialNumbers(card);

    calculateStateTotals(card);

    calculateGrandTotals();

});

//  * REMOVE STATE


document.addEventListener("click", function (e) {

    if (!e.target.closest(".btnRemoveState")) return;

    if (!confirm("Remove this state?")) return;

    e.target.closest(".offshore-card").remove();

    calculateGrandTotals();

});

//  * STATE TOTALS

function calculateStateTotals(card){

    let tankers = 0;

    let crew = 0;

    card.querySelectorAll("tbody tr").forEach(function(row){

        tankers += Number(row.querySelector(".tankerCount").value) || 0;

        crew += Number(row.querySelector(".crewCount").value) || 0;

    });

    card.querySelector(".totalTankers").value = tankers;

    card.querySelector(".totalCrew").value = crew;

}


//  * GRAND TOTAL

function calculateGrandTotals(){

    let tankers = 0;

    let crew = 0;

    document.querySelectorAll(".offshore-card").forEach(function(card){

        tankers += Number(card.querySelector(".totalTankers").value) || 0;

        crew += Number(card.querySelector(".totalCrew").value) || 0;

    });

    document.getElementById("grandTankers").value = tankers;

    document.getElementById("grandCrew").value = crew;

}

//  * Live calculation is handled by the consolidated input listener
//  * at the top of this script.


/*****************************************************************
 * LAND BORDER RETURNS BY NATIONALITY
 *****************************************************************/

// =========================================
// NATIONALITY LIST
// =========================================

const nationalityList = [
    "Nigeria", "Benin", "Burkina Faso", "Cameroon", "Cape Verde",
    "Central African Republic", "Chad", "Congo", "Côte d'Ivoire",
    "DR Congo", "Egypt", "Equatorial Guinea", "Eritrea",
    "Eswatini", "Ethiopia", "Gabon", "Gambia", "Ghana",
    "Guinea", "Guinea-Bissau", "Kenya", "Liberia",
    "Libya", "Mali", "Morocco", "Niger", "Senegal",
    "Sierra Leone", "South Africa", "Sudan", "Togo",
    "Tunisia", "United Kingdom", "United States",
    "Canada", "France", "Germany", "Italy", "Spain",
    "Turkey", "China", "India", "Pakistan",
    "Japan", "South Korea", "Brazil", "Russia"
];

// =========================================
// INITIALIZE MODULE
// =========================================

document.addEventListener("DOMContentLoaded", function () {

    const btnAddCommand = document.getElementById("btnAddCommand");
    const commandContainer = document.getElementById("commandContainer");
    const commandTemplate = document.getElementById("commandTemplate");

    if (!btnAddCommand || !commandContainer || !commandTemplate) return;

    btnAddCommand.addEventListener("click", function () {

    const clone = commandTemplate.content.cloneNode(true);

    const stateSelect = clone.querySelector(".command-state");

    Object.keys(landBorderStates).forEach(function(state){

        const option = document.createElement("option");

        option.value = state;
        option.textContent = state;

        stateSelect.appendChild(option);

    });

    commandContainer.appendChild(clone);
    updateOverallTotals();

    });

});

// =========================================
// LOAD CONTROL POSTS (+ duplicate state guard)
// =========================================

document.addEventListener("change", function(e){

    if(!e.target.classList.contains("command-state")) return;

    const select = e.target;
    const state = select.value;

    const card = select.closest(".nationality-card");

    const controlPost = card.querySelector(".control-post");

    controlPost.innerHTML =
        '<option value="">Select Control Post</option>';

    if(state === "") return;

    // Prevent duplicate states
    let duplicate = false;

    document.querySelectorAll(".command-state").forEach(function(item){

        if(item !== select && item.value === state){

            duplicate = true;

        }

    });

    if(duplicate){

        alert(state + " has already been added.");

        select.value = "";

        return;

    }

    if(!landBorderStates[state]) return;

    landBorderStates[state].forEach(function(post){

        const option = document.createElement("option");

        option.value = post;
        option.textContent = post;

        controlPost.appendChild(option);

    });

});

// =========================================
// ADD NATIONALITY ROW
// =========================================

document.addEventListener("click", function(e){

    const button = e.target.closest(".btnAddNationalityRow");

    if(!button) return;

    const card = button.closest(".nationality-card");

    const tbody = card.querySelector("tbody");

    const template =
        document.getElementById("nationalityRowTemplate");

    const clone =
        template.content.cloneNode(true);

    loadNationalityOptions(
        clone.querySelector(".nationality-select")
    );

    tbody.appendChild(clone);

    updateNationalitySerial(card);

    updateCommandTotals(card);

});

// =========================================
// DELETE ROW
// =========================================

document.addEventListener("click", function(e){

    const button = e.target.closest(".btnRemoveNationality");

    if(!button) return;

    const card = button.closest(".nationality-card");

    button.closest("tr").remove();

    updateNationalitySerial(card);

    updateCommandTotals(card);

});

// =========================================
// DELETE COMMAND
// =========================================

document.addEventListener("click", function(e){

    const button = e.target.closest(".btnRemoveCommand");

    if(!button) return;

    const card = button.closest(".nationality-card");

    card.remove();

    updateOverallTotals();

});

// =========================================
// LIVE CALCULATION is handled by the consolidated input listener
// at the top of this script (scoped to .nationality-table rows).
// =========================================

// =========================================
// SERIAL NUMBERS
// =========================================

function updateNationalitySerial(card){

    card.querySelectorAll("tbody tr").forEach(function(row,index){

        row.querySelector(".sn").textContent = index + 1;

    });

}

// =========================================
// LOAD NATIONALITIES
// =========================================

function loadNationalityOptions(select){

    select.innerHTML =
        '<option value="">Select Nationality</option>';

    nationalityList.forEach(function(country){

        const option =
            document.createElement("option");

        option.value = country;
        option.textContent = country;

        select.appendChild(option);

    });

}

// =========================================
// ROW TOTALS
// =========================================

function calculateNationalityRow(row){

    const arrivalMale =
        Number(row.querySelector(".arrivalMale").value);

    const arrivalFemale =
        Number(row.querySelector(".arrivalFemale").value);

    const departureMale =
        Number(row.querySelector(".departureMale").value);

    const departureFemale =
        Number(row.querySelector(".departureFemale").value);

    const arrivalTotal =
        arrivalMale + arrivalFemale;

    const departureTotal =
        departureMale + departureFemale;

    const grandTotal =
        arrivalTotal + departureTotal;

    row.querySelector(".arrivalTotal").value =
        arrivalTotal;

    row.querySelector(".departureTotal").value =
        departureTotal;

    row.querySelector(".grandTotal").value =
        grandTotal;

}

// =========================================
// FOOTER TOTALS
// =========================================

function updateCommandTotals(card){

    let arrivalMale = 0;
    let arrivalFemale = 0;

    let departureMale = 0;
    let departureFemale = 0;

    card.querySelectorAll("tbody tr").forEach(function(row){

        arrivalMale +=
            Number(row.querySelector(".arrivalMale").value);

        arrivalFemale +=
            Number(row.querySelector(".arrivalFemale").value);

        departureMale +=
            Number(row.querySelector(".departureMale").value);

        departureFemale +=
            Number(row.querySelector(".departureFemale").value);

    });

    const arrivalTotal =
        arrivalMale + arrivalFemale;

    const departureTotal =
        departureMale + departureFemale;

    const grandTotal =
        arrivalTotal + departureTotal;

    card.querySelector(".totalArrivalMale").value =
        arrivalMale;

    card.querySelector(".totalArrivalFemale").value =
        arrivalFemale;

    card.querySelector(".totalArrival").value =
        arrivalTotal;

    card.querySelector(".totalDepartureMale").value =
        departureMale;

    card.querySelector(".totalDepartureFemale").value =
        departureFemale;

    card.querySelector(".totalDeparture").value =
        departureTotal;

    card.querySelector(".totalGrand").value =
        grandTotal;

}


// =========================================
// PREVENT DUPLICATE NATIONALITIES
// =========================================

document.addEventListener("change", function(e){

    if(!e.target.classList.contains("nationality-select")) return;

    const select = e.target;

    if(select.value === "") return;

    const card = select.closest(".nationality-card");

    let duplicate = false;

    card.querySelectorAll(".nationality-select").forEach(function(item){

        if(item !== select && item.value === select.value){

            duplicate = true;

        }

    });

    if(duplicate){

        alert(select.value + " has already been selected.");

        select.value = "";

    }

});
// =========================================
// OVERALL TOTALS
// =========================================

function updateOverallTotals(){

    let arrivalMale = 0;
    let arrivalFemale = 0;

    let departureMale = 0;
    let departureFemale = 0;

    document.querySelectorAll(".nationality-card").forEach(function(card){

        arrivalMale += Number(card.querySelector(".totalArrivalMale")?.value || 0);

        arrivalFemale += Number(card.querySelector(".totalArrivalFemale")?.value || 0);

        departureMale += Number(card.querySelector(".totalDepartureMale")?.value || 0);

        departureFemale += Number(card.querySelector(".totalDepartureFemale")?.value || 0);

    });

    const arrivalTotal = arrivalMale + arrivalFemale;

    const departureTotal = departureMale + departureFemale;

    const grandTotal = arrivalTotal + departureTotal;

    const arrivalMaleInput = document.getElementById("overallArrivalMale");
    const arrivalFemaleInput = document.getElementById("overallArrivalFemale");
    const arrivalTotalInput = document.getElementById("overallArrivalTotal");

    const departureMaleInput = document.getElementById("overallDepartureMale");
    const departureFemaleInput = document.getElementById("overallDepartureFemale");
    const departureTotalInput = document.getElementById("overallDepartureTotal");

    const grandTotalInput = document.getElementById("overallGrandTotal");

    if(arrivalMaleInput) arrivalMaleInput.value = arrivalMale;
    if(arrivalFemaleInput) arrivalFemaleInput.value = arrivalFemale;
    if(arrivalTotalInput) arrivalTotalInput.value = arrivalTotal;

    if(departureMaleInput) departureMaleInput.value = departureMale;
    if(departureFemaleInput) departureFemaleInput.value = departureFemale;
    if(departureTotalInput) departureTotalInput.value = departureTotal;

    if(grandTotalInput) grandTotalInput.value = grandTotal;

}

/**************************************************************************
 * REDAS REPORT PREVIEW
 *
 * IMPORTANT:
 * The Preview HTML is outside the <script> tag.
 * This module only handles generating and displaying the preview.
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
     * This function is deliberately flexible because the uploaded script
     * does not contain the HTML/name attributes for the General Reports
     * fields. It only reads fields if they actually exist on the page.
     **********************************************************************/
    function buildGeneralReportPreview() {

        const fieldGroups = [
            {
                title:"Security Report",
                selectors:[
                    '[name="general[security]"]',
                    '[name="security_report"]',
                    '#securityReport'
                ]
            },
            {
                title:"Other Reports",
                selectors:[
                    '[name="general[other]"]',
                    '[name="other_report"]',
                    '#otherReport'
                ]
            },
            {
                title:"Challenges",
                selectors:[
                    '[name="general[challenges]"]',
                    '[name="challenges"]',
                    '#challenges'
                ]
            },
            {
                title:"Recommendations / Way Forward",
                selectors:[
                    '[name="general[recommendations]"]',
                    '[name="recommendations"]',
                    '#recommendations'
                ]
            },
            {
                title:"Conclusion",
                selectors:[
                    '[name="general[conclusion]"]',
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
         * Recalculate everything first.
         */
        try {

            if (typeof calculateStaffStrength === "function") {
                calculateStaffStrength();
            }

            if (typeof calculateLandBorder === "function") {
                calculateLandBorder();
            }

            if (typeof calculateSeaport === "function") {
                calculateSeaport();
            }

            if (typeof calculateAirportMovement === "function") {
                calculateAirportMovement();
            }

            if (typeof calculateGrandTotals === "function") {
                calculateGrandTotals();
            }

            if (typeof updateOverallTotals === "function") {
                updateOverallTotals();
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
     * Back to Edit / Submit Return are wired in the navigation IIFE at
     * the top of this script (.border-edit-btn / .border-submit-return-btn).
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


