# Internal Audit Directorate Implementation Plan

## Steps
- [x] Step 1: Read and analyze existing templates (ISCT, Investigation)
- [x] Step 2: Create `resources/views/user/directorate-internal-audit.blade.php`
- [x] Step 3: Update `app/Http/Controllers/Web/DashboardController.php` to register the new directorate
- [x] Step 4: Update `resources/views/user/directorate.blade.php` to add the card in the grid

## Completed ✓
- Created `resources/views/user/directorate-internal-audit.blade.php` with:
  - Sticky section navigation (12 sections)
  - Audit Report Meta (Year, Quarter, Officer, Directorate)
  - Section 1: Introduction (Context + Objective & Scope)
  - Section 2: Commands/Formations Covered (dynamic rows table)
  - Section 3: Funds Allocated & Utilised (with auto-calculated balances & totals)
  - Section 4: Revenue Generated (32pg, 64pg, ETC, ERC, Operations with totals)
  - Section 5-8: Stock tables for 66pg, 34pg, ETC, ERC (auto-calc opening+supplies-issued-damage)
  - Section 9: Observations & Recommendations (side-by-side textareas)
  - Section 10: General Conclusion & Recommendations
  - Sign Off section
  - Full JavaScript auto-calculations & dynamic row add/remove
  - Same NIS theme/layout as existing templates
- Updated `DashboardController.php` with `internal-audit` slug routing and directorate definition
- Updated `directorate.blade.php` with Internal Audit card in the directorates grid

