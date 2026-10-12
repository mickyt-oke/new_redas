<?php

namespace App\Services;

/**
 * Curated dictionary of return_data fields that administrators commonly need
 * to search, score and aggregate across all formations.
 *
 * Each entry contains a concrete example `path` for record-level filtering and
 * an optional `aggregate_path` (which may include `*` wildcards) for nationwide
 * scoring.
 */
class ReturnDataDictionary
{
    private const ENTRIES = [
        // Personnel — HRM cadre
        ['key' => 'hrm_cadre_comptroller_male', 'category' => 'personnel', 'label' => 'Comptroller Cadre — Male', 'path' => 'hrm.cadre.comptroller.male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_cadre_comptroller_female', 'category' => 'personnel', 'label' => 'Comptroller Cadre — Female', 'path' => 'hrm.cadre.comptroller.female', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_cadre_comptroller_total', 'category' => 'personnel', 'label' => 'Comptroller Cadre — Total', 'path' => 'hrm.cadre.comptroller.total', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_cadre_superintendent_male', 'category' => 'personnel', 'label' => 'Superintendent Cadre — Male', 'path' => 'hrm.cadre.superintendent.male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_cadre_superintendent_female', 'category' => 'personnel', 'label' => 'Superintendent Cadre — Female', 'path' => 'hrm.cadre.superintendent.female', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_cadre_superintendent_total', 'category' => 'personnel', 'label' => 'Superintendent Cadre — Total', 'path' => 'hrm.cadre.superintendent.total', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_cadre_inspectorate_male', 'category' => 'personnel', 'label' => 'Inspectorate Cadre — Male', 'path' => 'hrm.cadre.inspectorate.male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_cadre_inspectorate_female', 'category' => 'personnel', 'label' => 'Inspectorate Cadre — Female', 'path' => 'hrm.cadre.inspectorate.female', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_cadre_inspectorate_total', 'category' => 'personnel', 'label' => 'Inspectorate Cadre — Total', 'path' => 'hrm.cadre.inspectorate.total', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_cadre_assistant_male', 'category' => 'personnel', 'label' => 'Assistant Cadre — Male', 'path' => 'hrm.cadre.assistant.male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_cadre_assistant_female', 'category' => 'personnel', 'label' => 'Assistant Cadre — Female', 'path' => 'hrm.cadre.assistant.female', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_cadre_assistant_total', 'category' => 'personnel', 'label' => 'Assistant Cadre — Total', 'path' => 'hrm.cadre.assistant.total', 'data_type' => 'numeric', 'aggregation' => 'sum'],

        // Personnel — HRM rank
        ['key' => 'hrm_rank_acg_male', 'category' => 'personnel', 'label' => 'Rank ACG — Male', 'path' => 'hrm.rank.acg.male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_rank_acg_female', 'category' => 'personnel', 'label' => 'Rank ACG — Female', 'path' => 'hrm.rank.acg.female', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_rank_cis_male', 'category' => 'personnel', 'label' => 'Rank CIS — Male', 'path' => 'hrm.rank.cis.male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_rank_cis_female', 'category' => 'personnel', 'label' => 'Rank CIS — Female', 'path' => 'hrm.rank.cis.female', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_rank_si_male', 'category' => 'personnel', 'label' => 'Rank SI — Male', 'path' => 'hrm.rank.si.male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_rank_si_female', 'category' => 'personnel', 'label' => 'Rank SI — Female', 'path' => 'hrm.rank.si.female', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_rank_asi1_male', 'category' => 'personnel', 'label' => 'Rank ASI 1 — Male', 'path' => 'hrm.rank.asi1.male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'hrm_rank_asi1_female', 'category' => 'personnel', 'label' => 'Rank ASI 1 — Female', 'path' => 'hrm.rank.asi1.female', 'data_type' => 'numeric', 'aggregation' => 'sum'],

        // Passport staff strength
        ['key' => 'passport_staff_total', 'category' => 'personnel', 'label' => 'Passport Staff Strength — Total', 'path' => 'passport.staff_strength.0.total', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'passport_staff_male', 'category' => 'personnel', 'label' => 'Passport Staff Strength — Male', 'path' => 'passport.staff_strength.0.male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'passport_staff_female', 'category' => 'personnel', 'label' => 'Passport Staff Strength — Female', 'path' => 'passport.staff_strength.0.female', 'data_type' => 'numeric', 'aggregation' => 'sum'],

        // Visa cadre
        ['key' => 'visa_cadre_male', 'category' => 'personnel', 'label' => 'Visa Cadre — Male', 'path' => 'visa.cadre.comptroller.male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'visa_cadre_female', 'category' => 'personnel', 'label' => 'Visa Cadre — Female', 'path' => 'visa.cadre.comptroller.female', 'data_type' => 'numeric', 'aggregation' => 'sum'],

        // Border staff
        ['key' => 'border_staff_male', 'category' => 'personnel', 'label' => 'Border Staff — Male', 'path' => 'border.staff.si.male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'border_staff_female', 'category' => 'personnel', 'label' => 'Border Staff — Female', 'path' => 'border.staff.si.female', 'data_type' => 'numeric', 'aggregation' => 'sum'],

        // Facilities — Passport books
        ['key' => 'passport_standard_32p', 'category' => 'facilities', 'label' => 'Standard Passport — 32 Pages', 'path' => 'passport.standard_passport.0.32p', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'passport_standard_64p', 'category' => 'facilities', 'label' => 'Standard Passport — 64 Pages', 'path' => 'passport.standard_passport.0.64p', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'passport_standard_total', 'category' => 'facilities', 'label' => 'Standard Passport — Total', 'path' => 'passport.standard_passport.0.total', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'passport_official_32p', 'category' => 'facilities', 'label' => 'Official Passport — 32 Pages', 'path' => 'passport.official_passport.0.32p', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'passport_official_64p', 'category' => 'facilities', 'label' => 'Official Passport — 64 Pages', 'path' => 'passport.official_passport.0.64p', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'passport_official_total', 'category' => 'facilities', 'label' => 'Official Passport — Total', 'path' => 'passport.official_passport.0.total', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'passport_diplomatic_32p', 'category' => 'facilities', 'label' => 'Diplomatic Passport — 32 Pages', 'path' => 'passport.diplomatic_passport.0.32p', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'passport_diplomatic_64p', 'category' => 'facilities', 'label' => 'Diplomatic Passport — 64 Pages', 'path' => 'passport.diplomatic_passport.0.64p', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'passport_diplomatic_total', 'category' => 'facilities', 'label' => 'Diplomatic Passport — Total', 'path' => 'passport.diplomatic_passport.0.total', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'passport_ecowas_enbic', 'category' => 'facilities', 'label' => 'ECOWAS ENBIC', 'path' => 'passport.ecowas_enbic.0.total', 'data_type' => 'numeric', 'aggregation' => 'sum'],

        // Visa / CERPAC / e-TWP
        ['key' => 'visa_evisa_applications', 'category' => 'visa', 'label' => 'e-Visa Applications', 'path' => 'visa.visas.evisa.0.applications', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'visa_etwp_applications', 'category' => 'visa', 'label' => 'e-TWP Applications', 'path' => 'visa.visas.etwp.0.applications', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'visa_cerpac_issued', 'category' => 'visa', 'label' => 'CERPAC Cards Issued', 'path' => 'visa.cerpac.card_issued', 'data_type' => 'numeric', 'aggregation' => 'sum'],

        // Border movements — land (wildcard aggregate paths)
        ['key' => 'border_land_arrival_male', 'category' => 'borders', 'label' => 'Land Border Arrivals — Male', 'path' => 'border.land.lagos.0.arrival_male', 'aggregate_path' => 'border.land.*.*.arrival_male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'border_land_arrival_female', 'category' => 'borders', 'label' => 'Land Border Arrivals — Female', 'path' => 'border.land.lagos.0.arrival_female', 'aggregate_path' => 'border.land.*.*.arrival_female', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'border_land_departure_male', 'category' => 'borders', 'label' => 'Land Border Departures — Male', 'path' => 'border.land.lagos.0.departure_male', 'aggregate_path' => 'border.land.*.*.departure_male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'border_land_departure_female', 'category' => 'borders', 'label' => 'Land Border Departures — Female', 'path' => 'border.land.lagos.0.departure_female', 'aggregate_path' => 'border.land.*.*.departure_female', 'data_type' => 'numeric', 'aggregation' => 'sum'],

        // Border movements — air
        ['key' => 'border_air_arrival_male', 'category' => 'borders', 'label' => 'Airport Arrivals — Male', 'path' => 'border.airports.lagos.0.arrival_male', 'aggregate_path' => 'border.airports.*.*.arrival_male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'border_air_arrival_female', 'category' => 'borders', 'label' => 'Airport Arrivals — Female', 'path' => 'border.airports.lagos.0.arrival_female', 'aggregate_path' => 'border.airports.*.*.arrival_female', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'border_air_departure_male', 'category' => 'borders', 'label' => 'Airport Departures — Male', 'path' => 'border.airports.lagos.0.departure_male', 'aggregate_path' => 'border.airports.*.*.departure_male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'border_air_departure_female', 'category' => 'borders', 'label' => 'Airport Departures — Female', 'path' => 'border.airports.lagos.0.departure_female', 'aggregate_path' => 'border.airports.*.*.departure_female', 'data_type' => 'numeric', 'aggregation' => 'sum'],

        // Border movements — sea
        ['key' => 'border_sea_passenger_arrival_male', 'category' => 'borders', 'label' => 'Seaport Passenger Arrivals — Male', 'path' => 'border.seaport.lagos.0.passenger_arrival_male', 'aggregate_path' => 'border.seaport.*.*.passenger_arrival_male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'border_sea_passenger_arrival_female', 'category' => 'borders', 'label' => 'Seaport Passenger Arrivals — Female', 'path' => 'border.seaport.lagos.0.passenger_arrival_female', 'aggregate_path' => 'border.seaport.*.*.passenger_arrival_female', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'border_sea_passenger_departure_male', 'category' => 'borders', 'label' => 'Seaport Passenger Departures — Male', 'path' => 'border.seaport.lagos.0.passenger_departure_male', 'aggregate_path' => 'border.seaport.*.*.passenger_departure_male', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'border_sea_passenger_departure_female', 'category' => 'borders', 'label' => 'Seaport Passenger Departures — Female', 'path' => 'border.seaport.lagos.0.passenger_departure_female', 'aggregate_path' => 'border.seaport.*.*.passenger_departure_female', 'data_type' => 'numeric', 'aggregation' => 'sum'],
        ['key' => 'border_sea_boat_arrival', 'category' => 'borders', 'label' => 'Seaport Boats Arrived', 'path' => 'border.seaport.lagos.0.boat_arrival', 'aggregate_path' => 'border.seaport.*.*.boat_arrival', 'data_type' => 'numeric', 'aggregation' => 'sum'],

        // Compliance / metadata
        ['key' => 'report_period', 'category' => 'compliance', 'label' => 'Report Period', 'path' => 'report_period', 'data_type' => 'string', 'aggregation' => 'count'],
        ['key' => 'reporting_officer', 'category' => 'compliance', 'label' => 'Reporting Officer', 'path' => 'reporting_officer', 'data_type' => 'string', 'aggregation' => 'count'],
        ['key' => 'command_name', 'category' => 'compliance', 'label' => 'Command Name', 'path' => 'command_name', 'data_type' => 'string', 'aggregation' => 'count'],
    ];

    private const CATEGORY_LABELS = [
        'personnel' => 'Personnel Strength',
        'facilities' => 'Facilities Issued',
        'visa' => 'Visa / CERPAC / e-TWP',
        'borders' => 'Border Movements',
        'compliance' => 'Compliance & Metadata',
    ];

    /**
     * Return every dictionary entry.
     */
    public static function all(): array
    {
        return self::ENTRIES;
    }

    /**
     * Return entries grouped by category.
     */
    public static function byCategory(): array
    {
        $grouped = [];

        foreach (self::ENTRIES as $entry) {
            $category = $entry['category'];
            $label = self::CATEGORY_LABELS[$category] ?? ucfirst($category);

            if (! isset($grouped[$category])) {
                $grouped[$category] = [
                    'key' => $category,
                    'label' => $label,
                    'entries' => [],
                ];
            }

            $grouped[$category]['entries'][] = $entry;
        }

        return $grouped;
    }

    /**
     * Look up a single dictionary entry by key.
     */
    public static function find(string $key): ?array
    {
        foreach (self::ENTRIES as $entry) {
            if ($entry['key'] === $key) {
                return $entry;
            }
        }

        return null;
    }

    /**
     * All paths known to contain numeric values.
     */
    public static function numericPaths(): array
    {
        return collect(self::ENTRIES)
            ->where('data_type', 'numeric')
            ->pluck('path')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Label for a category slug.
     */
    public static function categoryLabel(string $category): string
    {
        return self::CATEGORY_LABELS[$category] ?? ucfirst($category);
    }
}
