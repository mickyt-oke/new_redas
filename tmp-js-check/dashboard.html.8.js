

    // ACTU JAVASCRIPT LOGIC

    document.addEventListener("DOMContentLoaded", function () {

        /*
         * ACTU page only — requires its own staff inputs/totals so this
         * never touches look-alike ids (e.g. #grandTotal) on other pages.
         */
        if (
            !document.querySelector(".staff-male") ||
            !document.getElementById("maleTotal") ||
            !document.getElementById("femaleTotal")
        ) return;

        function calculateStaffStrength() {

            let maleTotal = 0;
            let femaleTotal = 0;

            document.querySelectorAll(".staff-male").forEach(function (maleInput) {

                const row = maleInput.dataset.row;

                const femaleInput = document.querySelector(
                    `.staff-female[data-row="${row}"]`
                );

                const male = Number(maleInput.value) || 0;
                const female = Number(femaleInput.value) || 0;

                document.getElementById(`staff-total-${row}`).value = male + female;

                maleTotal += male;
                femaleTotal += female;
            });

            document.getElementById("maleTotal").value = maleTotal;
            document.getElementById("femaleTotal").value = femaleTotal;
            document.getElementById("grandTotal").value = maleTotal + femaleTotal;
        }

        document.addEventListener("input", function (e) {

            if (
                e.target.classList.contains("staff-male") ||
                e.target.classList.contains("staff-female")
            ) {
                calculateStaffStrength();
            }

        });

        calculateStaffStrength();

    });


    //    ACTU CASE MATRIX


    function calculateCasesMatrix() {

        /* ACTU cases matrix page only — no-op when its grid is absent. */
        if (!document.querySelector('.case-input') || !document.getElementById('row-total-0')) return;

        // Row totals

        for (let row = 0; row < 4; row++) {

            let rowTotal = 0;

            for (let col = 0; col < 12; col++) {

                const input = document.querySelector(
                    `.case-input[data-row="${row}"][data-col="${col}"]`
                );

                rowTotal += Number(input?.value || 0);

            }

            document.getElementById(`row-total-${row}`).value = rowTotal;

        }

        // Column totals

        let grandTotal = 0;

        for (let col = 0; col < 12; col++) {

            let columnTotal = 0;

            for (let row = 0; row < 4; row++) {

                const input = document.querySelector(
                    `.case-input[data-row="${row}"][data-col="${col}"]`
                );

                columnTotal += Number(input?.value || 0);

            }

            document.getElementById(`col-total-${col}`).value = columnTotal;

            grandTotal += columnTotal;

        }

        document.getElementById('grand-total').value = grandTotal;

    }

    document.addEventListener('input', function (e) {

        if (e.target.classList.contains('case-input')) {

            calculateCasesMatrix();

        }

    });

    calculateCasesMatrix();

