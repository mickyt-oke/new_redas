<?php

namespace App\Services;

use App\Models\Application;

/**
 * Aggregate key nationwide indicators from the nested return_data JSON stored
 * on applications. This service is intentionally read-only and stateless so it
 * can be reused by the Super Admin and CGIS executive dashboards.
 */
class ExecutiveDashboardAggregator
{
    private const DIRECTORATE_LABELS = [
        'hrm' => 'HRM',
        'prs' => 'PRS',
        'finance' => 'F/A',
        'investigation' => 'I&C',
        'passport' => 'POTD',
        'visa' => 'V/R',
        'migration' => 'Migration',
        'border' => 'Border Mngt',
        'ict' => 'ICT',
        'works-logistics' => 'Works & Logistics',
    ];

    private const HRM_CADRES = [
        'comptroller' => 'Comptroller Cadre',
        'superintendent' => 'Superintendent Cadre',
        'inspectorate' => 'Inspectorate Cadre',
        'assistant' => 'Assistant Cadre',
    ];

    private const HRM_RANKS = [
        'dcg' => 'DCG',
        'acg' => 'ACG',
        'cis' => 'CIS',
        'dci' => 'DCI',
        'aci' => 'ACI',
        'csi' => 'CSI',
        'si' => 'SI',
        'dsi' => 'DSI',
        'asi1' => 'ASI 1',
        'asi2' => 'ASI 2',
        'ii' => 'II',
        'aii' => 'AII',
        'ia1' => 'IA 1',
        'ia2' => 'IA 2',
        'ia3' => 'IA 3',
    ];

    private const PASSPORT_RANK_LABELS = [
        0 => 'Deputy Comptroller General',
        1 => 'Assistant Comptroller General',
        2 => 'Comptroller of Immigration',
        3 => 'Deputy Comptroller of Immigration',
        4 => 'Assistant Comptroller of Immigration',
        5 => 'Chief Superintendent of Immigration',
        6 => 'Superintendent of Immigration',
        7 => 'Deputy Superintendent of Immigration',
        8 => 'Assistant Superintendent I',
        9 => 'Assistant Superintendent II',
        10 => 'Inspector of Immigration',
        11 => 'Assistant Inspector of Immigration',
        12 => 'Immigration Assistant I',
        13 => 'Immigration Assistant II',
        14 => 'Immigration Assistant III',
    ];

    /**
     * Build the executive dashboard dataset.
     *
     * @param  \Closure|null  $scope  Optional query scope callback.
     */
    public function aggregate(?\Closure $scope = null): array
    {
        $query = Application::query()
            ->select(['id', 'category', 'scope_code', 'zonal_code', 'return_data', 'status', 'created_at']);

        if ($scope) {
            $scope($query);
        }

        $personnel = $this->initialPersonnel();
        $facilities = $this->initialFacilities();
        $borders = $this->initialBorders();
        $compliance = [];

        foreach ($query->cursor() as $application) {
            $data = $application->return_data ?? [];

            $this->aggregatePersonnel($data, $personnel);
            $this->aggregateFacilities($data, $facilities);
            $this->aggregateBorders($data, $borders);
            $this->aggregateCompliance($data, $compliance, $application);
        }

        return [
            'personnel' => $personnel,
            'facilities' => $facilities,
            'borders' => $borders,
            'compliance' => $compliance,
            'performance' => $this->performanceMetrics($personnel, $facilities, $borders),
        ];
    }

    private function initialPersonnel(): array
    {
        return [
            'total' => 0,
            'male' => 0,
            'female' => 0,
            'cadre' => collect(self::HRM_CADRES)->mapWithKeys(fn ($label, $key) => [$key => ['label' => $label, 'male' => 0, 'female' => 0, 'total' => 0]])->all(),
            'rank' => collect(self::HRM_RANKS)->mapWithKeys(fn ($label, $key) => [$key => ['label' => $label, 'male' => 0, 'female' => 0, 'total' => 0]])->all(),
            'passport_rank' => collect(self::PASSPORT_RANK_LABELS)->mapWithKeys(fn ($label, $key) => [$key => ['label' => $label, 'male' => 0, 'female' => 0, 'total' => 0]])->all(),
            'border_staff' => [],
        ];
    }

    private function initialFacilities(): array
    {
        return [
            'passport_books' => 0,
            'evisa' => 0,
            'e_cerpac' => 0,
            'e_twp' => 0,
            'enbic' => 0,
            'passport_detail' => [
                'standard' => 0,
                'official' => 0,
                'diplomatic' => 0,
                'convention_travel_certificate' => 0,
                'single_travel_emergency_passport' => 0,
                'refugee_travel_document' => 0,
                'ecowas_enbic' => 0,
                'digital_travel_certificate' => 0,
            ],
        ];
    }

    private function initialBorders(): array
    {
        return [
            'land' => ['arrivals' => 0, 'departures' => 0, 'male' => 0, 'female' => 0],
            'sea' => ['arrivals' => 0, 'departures' => 0, 'passenger_arrivals' => 0, 'passenger_departures' => 0, 'crew_arrivals' => 0, 'crew_departures' => 0, 'boats_arrived' => 0, 'boats_departed' => 0],
            'air' => ['arrivals' => 0, 'departures' => 0],
            'land_by_state' => [],
            'air_by_airport' => [],
        ];
    }

    private function aggregatePersonnel(array $data, array &$personnel): void
    {
        // HRM cadre and rank
        if (! empty($data['hrm']) && is_array($data['hrm'])) {
            $hrm = $data['hrm'];

            foreach (['cadre', 'rank'] as $section) {
                if (empty($hrm[$section]) || ! is_array($hrm[$section])) {
                    continue;
                }

                foreach ($hrm[$section] as $key => $row) {
                    $key = strtolower((string) $key);
                    if (! is_array($row)) {
                        continue;
                    }

                    $male = $this->toInt($row['male'] ?? 0);
                    $female = $this->toInt($row['female'] ?? 0);
                    $total = $this->toInt($row['total'] ?? 0);

                    if ($total === 0 && ($male > 0 || $female > 0)) {
                        $total = $male + $female;
                    }

                    if ($section === 'cadre' && isset($personnel['cadre'][$key])) {
                        $personnel['cadre'][$key]['male'] += $male;
                        $personnel['cadre'][$key]['female'] += $female;
                        $personnel['cadre'][$key]['total'] += $total;
                    }

                    if ($section === 'rank' && isset($personnel['rank'][$key])) {
                        $personnel['rank'][$key]['male'] += $male;
                        $personnel['rank'][$key]['female'] += $female;
                        $personnel['rank'][$key]['total'] += $total;
                    }

                    $personnel['male'] += $male;
                    $personnel['female'] += $female;
                    $personnel['total'] += $total > 0 ? $total : ($male + $female);
                }
            }
        }

        // Passport staff strength (indexed array)
        if (! empty($data['passport']['staff_strength']) && is_array($data['passport']['staff_strength'])) {
            foreach ($data['passport']['staff_strength'] as $index => $row) {
                if (! is_array($row)) {
                    continue;
                }

                $male = $this->toInt($row['male'] ?? 0);
                $female = $this->toInt($row['female'] ?? 0);
                $total = $this->toInt($row['total'] ?? 0);

                if ($total === 0 && ($male > 0 || $female > 0)) {
                    $total = $male + $female;
                }

                if (isset($personnel['passport_rank'][$index])) {
                    $personnel['passport_rank'][$index]['male'] += $male;
                    $personnel['passport_rank'][$index]['female'] += $female;
                    $personnel['passport_rank'][$index]['total'] += $total;
                }

                $personnel['male'] += $male;
                $personnel['female'] += $female;
                $personnel['total'] += $total > 0 ? $total : ($male + $female);
            }
        }

        // Visa cadre
        if (! empty($data['visa']['cadre']) && is_array($data['visa']['cadre'])) {
            foreach ($data['visa']['cadre'] as $key => $row) {
                $key = strtolower((string) $key);
                if (! is_array($row)) {
                    continue;
                }

                $male = $this->toInt($row['male'] ?? 0);
                $female = $this->toInt($row['female'] ?? 0);
                $total = $this->toInt($row['total'] ?? 0);

                if ($total === 0 && ($male > 0 || $female > 0)) {
                    $total = $male + $female;
                }

                if (isset($personnel['cadre'][$key])) {
                    $personnel['cadre'][$key]['male'] += $male;
                    $personnel['cadre'][$key]['female'] += $female;
                    $personnel['cadre'][$key]['total'] += $total;
                }

                $personnel['male'] += $male;
                $personnel['female'] += $female;
                $personnel['total'] += $total > 0 ? $total : ($male + $female);
            }
        }

        // Border staff (slugs are rank labels)
        if (! empty($data['border']['staff']) && is_array($data['border']['staff'])) {
            foreach ($data['border']['staff'] as $slug => $row) {
                if (! is_array($row)) {
                    continue;
                }

                $male = $this->toInt($row['male'] ?? 0);
                $female = $this->toInt($row['female'] ?? 0);
                $total = $this->toInt($row['total'] ?? 0);

                if ($total === 0 && ($male > 0 || $female > 0)) {
                    $total = $male + $female;
                }

                $label = ucwords(str_replace('-', ' ', (string) $slug));
                if (! isset($personnel['border_staff'][$label])) {
                    $personnel['border_staff'][$label] = ['label' => $label, 'male' => 0, 'female' => 0, 'total' => 0];
                }

                $personnel['border_staff'][$label]['male'] += $male;
                $personnel['border_staff'][$label]['female'] += $female;
                $personnel['border_staff'][$label]['total'] += $total;

                $personnel['male'] += $male;
                $personnel['female'] += $female;
                $personnel['total'] += $total > 0 ? $total : ($male + $female);
            }
        }
    }

    private function aggregateFacilities(array $data, array &$facilities): void
    {
        if (empty($data['passport']) || ! is_array($data['passport'])) {
            return;
        }

        $passport = $data['passport'];

        $keyMap = [
            'standard_passport' => 'standard',
            'official_passport' => 'official',
            'diplomatic_passport' => 'diplomatic',
            'convention_travel_certificate' => 'convention_travel_certificate',
            'single_travel_emergency_passport' => 'single_travel_emergency_passport',
            'refugee_travel_document' => 'refugee_travel_document',
            'ecowas_enbic' => 'ecowas_enbic',
            'digital_travel_certificate' => 'digital_travel_certificate',
        ];

        foreach ($keyMap as $sourceKey => $detailKey) {
            if (empty($passport[$sourceKey]) || ! is_array($passport[$sourceKey])) {
                continue;
            }

            $sum = 0;
            foreach ($passport[$sourceKey] as $row) {
                if (! is_array($row)) {
                    continue;
                }

                // Passport rows use '32p' and '64p' keys.
                $sum += $this->toInt($row['32p'] ?? 0);
                $sum += $this->toInt($row['64p'] ?? 0);
                $sum += $this->toInt($row['total'] ?? 0);
            }

            if ($sourceKey === 'ecowas_enbic') {
                $facilities['enbic'] += $sum;
            }

            $facilities['passport_books'] += $sum;
            if (isset($facilities['passport_detail'][$detailKey])) {
                $facilities['passport_detail'][$detailKey] += $sum;
            }
        }

        // Visa-related facilities
        if (! empty($data['visa']) && is_array($data['visa'])) {
            $visa = $data['visa'];

            // e-Visa applications
            if (! empty($visa['visas']['evisa']) && is_array($visa['visas']['evisa'])) {
                $facilities['evisa'] += $this->sumVisaGroup($visa['visas']['evisa'], 'applications');
            }

            // e-TWP applications
            if (! empty($visa['visas']['etwp']) && is_array($visa['visas']['etwp'])) {
                $facilities['e_twp'] += $this->sumVisaGroup($visa['visas']['etwp'], 'applications');
            }

            // CERPAC cards issued
            if (! empty($visa['cerpac']) && is_array($visa['cerpac'])) {
                $facilities['e_cerpac'] += $this->toInt($visa['cerpac']['card_issued'] ?? 0);
            }
        }
    }

    private function sumVisaGroup(array $group, string $column): int
    {
        $total = 0;
        foreach ($group as $row) {
            if (is_array($row)) {
                $total += $this->toInt($row[$column] ?? 0);
            }
        }

        return $total;
    }

    private function aggregateBorders(array $data, array &$borders): void
    {
        if (empty($data['border']) || ! is_array($data['border'])) {
            return;
        }

        $border = $data['border'];

        // Land borders: border.land.{stateSlug}.{index}.{arrival_male,arrival_female,departure_male,departure_female}
        if (! empty($border['land']) && is_array($border['land'])) {
            foreach ($border['land'] as $stateSlug => $posts) {
                if (! is_array($posts)) {
                    continue;
                }

                $stateArrivals = 0;
                $stateDepartures = 0;

                foreach ($posts as $post) {
                    if (! is_array($post)) {
                        continue;
                    }

                    $arrMale = $this->toInt($post['arrival_male'] ?? 0);
                    $arrFemale = $this->toInt($post['arrival_female'] ?? 0);
                    $depMale = $this->toInt($post['departure_male'] ?? 0);
                    $depFemale = $this->toInt($post['departure_female'] ?? 0);

                    $borders['land']['arrivals'] += $arrMale + $arrFemale;
                    $borders['land']['departures'] += $depMale + $depFemale;
                    $borders['land']['male'] += $arrMale + $depMale;
                    $borders['land']['female'] += $arrFemale + $depFemale;

                    $stateArrivals += $arrMale + $arrFemale;
                    $stateDepartures += $depMale + $depFemale;
                }

                $label = ucwords(str_replace('-', ' ', (string) $stateSlug));
                if (! isset($borders['land_by_state'][$label])) {
                    $borders['land_by_state'][$label] = ['arrivals' => 0, 'departures' => 0];
                }
                $borders['land_by_state'][$label]['arrivals'] += $stateArrivals;
                $borders['land_by_state'][$label]['departures'] += $stateDepartures;
            }
        }

        // Seaport & Marine: border.seaport.{stateSlug}.{index}.{passenger_arrival_male,...}
        if (! empty($border['seaport']) && is_array($border['seaport'])) {
            foreach ($border['seaport'] as $stateSlug => $ports) {
                if (! is_array($ports)) {
                    continue;
                }

                foreach ($ports as $port) {
                    if (! is_array($port)) {
                        continue;
                    }

                    $pArrM = $this->toInt($port['passenger_arrival_male'] ?? 0);
                    $pArrF = $this->toInt($port['passenger_arrival_female'] ?? 0);
                    $pDepM = $this->toInt($port['passenger_departure_male'] ?? 0);
                    $pDepF = $this->toInt($port['passenger_departure_female'] ?? 0);

                    $cArrM = $this->toInt($port['crew_arrival_male'] ?? 0);
                    $cArrF = $this->toInt($port['crew_arrival_female'] ?? 0);
                    $cDepM = $this->toInt($port['crew_departure_male'] ?? 0);
                    $cDepF = $this->toInt($port['crew_departure_female'] ?? 0);

                    $borders['sea']['passenger_arrivals'] += $pArrM + $pArrF;
                    $borders['sea']['passenger_departures'] += $pDepM + $pDepF;
                    $borders['sea']['crew_arrivals'] += $cArrM + $cArrF;
                    $borders['sea']['crew_departures'] += $cDepM + $cDepF;
                    $borders['sea']['boats_arrived'] += $this->toInt($port['boat_arrival'] ?? 0);
                    $borders['sea']['boats_departed'] += $this->toInt($port['boat_departure'] ?? 0);
                }
            }
        }

        $borders['sea']['arrivals'] = $borders['sea']['passenger_arrivals'] + $borders['sea']['crew_arrivals'];
        $borders['sea']['departures'] = $borders['sea']['passenger_departures'] + $borders['sea']['crew_departures'];

        // International airports: border.airports.{stateSlug}.{index}.{arrival_male,...}
        if (! empty($border['airports']) && is_array($border['airports'])) {
            foreach ($border['airports'] as $stateSlug => $airports) {
                if (! is_array($airports)) {
                    continue;
                }

                $airportArrivals = 0;
                $airportDepartures = 0;

                foreach ($airports as $airport) {
                    if (! is_array($airport)) {
                        continue;
                    }

                    $arr = $this->toInt($airport['arrival_male'] ?? 0) + $this->toInt($airport['arrival_female'] ?? 0);
                    $dep = $this->toInt($airport['departure_male'] ?? 0) + $this->toInt($airport['departure_female'] ?? 0);

                    $borders['air']['arrivals'] += $arr;
                    $borders['air']['departures'] += $dep;

                    $airportArrivals += $arr;
                    $airportDepartures += $dep;
                }

                $label = ucwords(str_replace('-', ' ', (string) $stateSlug));
                if (! isset($borders['air_by_airport'][$label])) {
                    $borders['air_by_airport'][$label] = ['arrivals' => 0, 'departures' => 0];
                }
                $borders['air_by_airport'][$label]['arrivals'] += $airportArrivals;
                $borders['air_by_airport'][$label]['departures'] += $airportDepartures;
            }
        }
    }

    private function aggregateCompliance(array $data, array &$compliance, Application $application): void
    {
        $formation = $this->formationLabel($application);
        $period = $data['report_period'] ?? $application->created_at?->format('Y-m') ?? 'unknown';

        if (! isset($compliance[$formation])) {
            $compliance[$formation] = [
                'formation' => $formation,
                'category' => $application->category,
                'scope_code' => $application->scope_code,
                'zonal_code' => $application->zonal_code,
                'periods' => [],
                'total' => 0,
                'approved' => 0,
                'pending' => 0,
                'returned' => 0,
            ];
        }

        $compliance[$formation]['total']++;
        $compliance[$formation][$application->status]++;

        if (! isset($compliance[$formation]['periods'][$period])) {
            $compliance[$formation]['periods'][$period] = ['total' => 0, 'approved' => 0, 'pending' => 0, 'returned' => 0];
        }
        $compliance[$formation]['periods'][$period]['total']++;
        $compliance[$formation]['periods'][$period][$application->status]++;
    }

    private function formationLabel(Application $application): string
    {
        if ($application->category === SubmissionWorkflow::CATEGORY_DIRECTORATE) {
            return self::DIRECTORATE_LABELS[$application->scope_code] ?? ucfirst((string) $application->scope_code);
        }

        if ($application->category === SubmissionWorkflow::CATEGORY_CGIS) {
            return strtoupper((string) $application->scope_code) . ' Unit';
        }

        if ($application->zonal_code) {
            return 'Zone ' . strtoupper((string) $application->zonal_code);
        }

        return ucfirst((string) $application->scope_code);
    }

    private function performanceMetrics(array $personnel, array $facilities, array $borders): array
    {
        // These are synthetic indicators derived from the aggregated data.
        $total = $personnel['total'] ?: 1;
        $femaleRatio = round(($personnel['female'] / $total) * 100, 1);
        $maleRatio = round(($personnel['male'] / $total) * 100, 1);

        $facilitiesTotal = max(1, $facilities['passport_books'] + $facilities['evisa'] + $facilities['e_cerpac'] + $facilities['e_twp'] + $facilities['enbic']);

        return [
            'gender_balance' => [
                'male' => $maleRatio,
                'female' => $femaleRatio,
            ],
            'facilities_ratio' => [
                'passport' => round(($facilities['passport_books'] / $facilitiesTotal) * 100, 1),
                'evisa' => round(($facilities['evisa'] / $facilitiesTotal) * 100, 1),
                'e_cerpac' => round(($facilities['e_cerpac'] / $facilitiesTotal) * 100, 1),
                'e_twp' => round(($facilities['e_twp'] / $facilitiesTotal) * 100, 1),
                'enbic' => round(($facilities['enbic'] / $facilitiesTotal) * 100, 1),
            ],
            'border_movement' => [
                'land' => $borders['land']['arrivals'] + $borders['land']['departures'],
                'sea' => $borders['sea']['arrivals'] + $borders['sea']['departures'],
                'air' => $borders['air']['arrivals'] + $borders['air']['departures'],
            ],
        ];
    }

    private function toInt(mixed $value): int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        return 0;
    }
}
