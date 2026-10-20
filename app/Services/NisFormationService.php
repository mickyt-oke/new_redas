<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class NisFormationService
{
    private static ?array $cache = null;

    private const SPECIAL_COMMAND_CODES = [
        'Seme Command' => 'SEME',
        'Idi-Iroko Command' => 'IDIR',
        'Mfum Command' => 'MFUM',
        'Jibia Command' => 'JIBIA',
        'Illela Command' => 'ILLELA',
        'Lagos Seaport Command' => 'LSC',
        'Onne Marine Command' => 'OMC',
        'Lagos Passport Command' => 'LPC',
    ];

    private const STATE_CODE_MAP = [
        'Abia' => 'AB',
        'Adamawa' => 'AD',
        'Akwa-Ibom' => 'AK',
        'Akwa Ibom' => 'AK',
        'Anambra' => 'AN',
        'Bauchi' => 'BA',
        'Bayelsa' => 'BY',
        'Benue' => 'BE',
        'Borno' => 'BO',
        'Cross-River' => 'CR',
        'Cross River' => 'CR',
        'Delta' => 'DE',
        'Ebonyi' => 'EB',
        'Edo' => 'ED',
        'Ekiti' => 'EK',
        'Enugu' => 'EN',
        'FCT' => 'FC',
        'Federal Capital Territory' => 'FC',
        'Gombe' => 'GO',
        'Imo' => 'IM',
        'Jigawa' => 'JI',
        'Kaduna' => 'KD',
        'Kano' => 'KN',
        'Katsina' => 'KT',
        'Kebbi' => 'KE',
        'Kogi' => 'KO',
        'Kwara' => 'KW',
        'Lagos' => 'LA',
        'Nasarawa' => 'NA',
        'Niger' => 'NI',
        'Ogun' => 'OG',
        'Ondo' => 'ON',
        'Osun' => 'OS',
        'Oyo' => 'OY',
        'Plateau' => 'PL',
        'Rivers' => 'RI',
        'Sokoto' => 'SO',
        'Taraba' => 'TA',
        'Yobe' => 'YO',
        'Zamfara' => 'ZA',
    ];

    public static function data(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $path = resource_path('js/nis_formations_extended.json');

        if (! File::exists($path)) {
            return self::$cache = [];
        }

        $decoded = json_decode(File::get($path), true);

        return self::$cache = is_array($decoded) ? $decoded : [];
    }

    public static function formationCategories(): array
    {
        return self::data()['formation_categories'] ?? [];
    }

    public static function zonalOffices(): array
    {
        $offices = self::data()['zonal_offices'] ?? [];
        $mapped = [];

        foreach ($offices as $office) {
            $zone = $office['zone'] ?? '';
            $letter = strtoupper(substr(trim($zone), -1));
            $code = 'ZONE-' . $letter;
            $mapped[] = array_merge($office, [
                'code' => $code,
                'label' => "{$zone} — {$office['headquarters_city']}, {$office['state']}",
            ]);
        }

        return $mapped;
    }

    public static function specialCommands(): array
    {
        $commands = self::data()['special_commands'] ?? [];
        $mapped = [];

        foreach ($commands as $command) {
            $name = $command['name'] ?? '';
            $code = self::SPECIAL_COMMAND_CODES[$name] ?? strtoupper(preg_replace('/[^A-Z]/', '', $name) ?: '');
            $mapped[] = array_merge($command, [
                'code' => $code,
                'label' => "{$name} — {$command['location']}, {$command['state']} ({$command['command_type']})",
            ]);
        }

        return $mapped;
    }

    public static function stateCommands(): array
    {
        $commands = self::data()['state_commands'] ?? [];
        $mapped = [];

        foreach ($commands as $command) {
            $state = $command['state'] ?? '';
            $code = self::STATE_CODE_MAP[$state] ?? null;
            if ($code === null) {
                $code = GeolocationService::stateCodeFromName($state);
            }
            $mapped[] = array_merge($command, [
                'code' => $code ?? strtoupper(preg_replace('/[^A-Z]/', '', $state) ?: ''),
                'label' => "{$command['command']} — {$command['headquarters']}",
            ]);
        }

        return $mapped;
    }

    /**
     * Canonical mapping of state codes to their parent NIS zone.
     * Authoritative mapping supplied by the Service.
     */
    private const STATE_ZONE_MAP = [
        'LA' => 'ZONE-A',
        'OG' => 'ZONE-A',
        'KD' => 'ZONE-B',
        'KN' => 'ZONE-B',
        'KT' => 'ZONE-B',
        'JI' => 'ZONE-B',
        'SO' => 'ZONE-B',
        'ZA' => 'ZONE-B',
        'BA' => 'ZONE-C',
        'GO' => 'ZONE-C',
        'BO' => 'ZONE-C',
        'AD' => 'ZONE-C',
        'YO' => 'ZONE-C',
        'PL' => 'ZONE-C',
        'FC' => 'ZONE-D',
        'KW' => 'ZONE-D',
        'NI' => 'ZONE-D',
        'KE' => 'ZONE-D',
        'AB' => 'ZONE-E',
        'AK' => 'ZONE-E',
        'CR' => 'ZONE-E',
        'EB' => 'ZONE-E',
        'IM' => 'ZONE-E',
        'RI' => 'ZONE-E',
        'OY' => 'ZONE-F',
        'OS' => 'ZONE-F',
        'ON' => 'ZONE-F',
        'EK' => 'ZONE-F',
        'AN' => 'ZONE-G',
        'BY' => 'ZONE-G',
        'DE' => 'ZONE-G',
        'EN' => 'ZONE-G',
        'ED' => 'ZONE-G',
        'BE' => 'ZONE-H',
        'KO' => 'ZONE-H',
        'TA' => 'ZONE-H',
        'NA' => 'ZONE-H',
    ];

    /**
     * Zone to command/formation mapping supplied by the Service.
     */
    public const ZONE_COMMAND_MAP = [
        'SHQ' => ['Headquarters'],
        'ZONE-A' => [
            'Lagos State Command',
            'SEME Command',
            'IDIROKO Border',
            'Lagos Border Patrol',
            'Lagos Sea and Marine Border Patrol',
            'Lagos Passport Command',
            'MMIA',
            'Ogun State Command',
        ],
        'ZONE-B' => [
            'Kaduna State Command',
            'Kano State Command',
            'ITSK',
            'MAKIA',
            'Katsina State Command',
            'Sokoto State Command',
            'Zamfara State Command',
            'Jigawa State Command',
        ],
        'ZONE-C' => [
            'Bauchi State Command',
            'Yobe State Command',
            'Borno State Command',
            'Adamawa State Command',
            'Gombe State Command',
            'Plateau State Command',
        ],
        'ZONE-D' => [
            'Federal Capital Territory',
            'NAIA',
            'Kwara State Command',
            'Niger State Command',
            'Kebbi State Command',
        ],
        'ZONE-E' => [
            'Abia State Command',
            'Akwa Ibom State Command',
            'Cross-River State Command',
            'MFUM',
            'Ebonyi State Command',
            'Imo State Command',
            'NITSOL',
            'Rivers State Command',
            'NITSA',
            'River Marine Command',
        ],
        'ZONE-F' => [
            'Oyo State Command',
            'Osun State Command',
            'Ondo State Command',
            'Ekiti State Command',
        ],
        'ZONE-G' => [
            'Anambra State Command',
            'Bayelsa State Command',
            'Delta State Command',
            'Enugu State Command',
            'Edo State Command',
        ],
        'ZONE-H' => [
            'Benue State Command',
            'Kogi State Command',
            'Taraba State Command',
            'Nasarawa State Command',
        ],
    ];

    /**
     * Zone to state mapping supplied by the Service.
     */
    public const ZONE_STATE_MAP = [
        'SHQ' => ['Headquarters'],
        'ZONE-A' => ['Lagos', 'Ogun'],
        'ZONE-B' => ['Kaduna', 'Kano', 'Katsina', 'Sokoto', 'Zamfara', 'Jigawa'],
        'ZONE-C' => ['Bauchi', 'Yobe', 'Borno', 'Adamawa', 'Gombe', 'Plateau'],
        'ZONE-D' => ['FCT', 'Kwara', 'Niger', 'Kebbi'],
        'ZONE-E' => ['Abia', 'Akwa-Ibom', 'Cross-River', 'Ebonyi', 'Imo', 'Rivers'],
        'ZONE-F' => ['Oyo', 'Osun', 'Ondo', 'Ekiti'],
        'ZONE-G' => ['Anambra', 'Bayelsa', 'Delta', 'Enugu', 'Edo'],
        'ZONE-H' => ['Benue', 'Kogi', 'Taraba', 'Nasarawa'],
    ];

    /**
     * Immigration training schools.
     */
    public const TRAINING_SCHOOLS = [
        'ITSK' => ['label' => 'ITSK', 'state' => 'Kano', 'name' => 'Immigration Training School Kano'],
        'ICSC' => ['label' => 'ICSC', 'state' => 'Sokoto', 'name' => 'Immigration Staff and Command College'],
        'NITSOL' => ['label' => 'NITSOL', 'state' => 'Imo', 'name' => 'Nigeria Immigration Training School Orlu'],
        'NITSA' => ['label' => 'NITSA', 'state' => 'Rivers', 'name' => 'Nigeria Immigration Training School Ahoada'],
    ];

    /**
     * International airport commands.
     */
    public const INTERNATIONAL_AIRPORTS = [
        'MMIA' => ['label' => 'MMIA', 'location' => 'Lagos', 'name' => 'Murtala Mohammed International Airport'],
        'MAKIA' => ['label' => 'MAKIA', 'location' => 'Kano', 'name' => 'Mallam Aminu Kano International Airport'],
        'AIIA' => ['label' => 'AIIA', 'location' => 'Enugu', 'name' => 'Akanu Ibiam International Airport'],
        'PHIA' => ['label' => 'PHIA', 'location' => 'Port-Harcourt', 'name' => 'Port-Harcourt International Airport'],
        'NAIA' => ['label' => 'NAIA', 'location' => 'Abuja', 'name' => 'Nnamdi Aziekwe International Airport'],
    ];

    /**
     * Return the canonical zone code for a state code, if known.
     */
    public static function zoneForState(?string $stateCode): ?string
    {
        if ($stateCode === null || $stateCode === '') {
            return null;
        }

        return self::STATE_ZONE_MAP[strtoupper($stateCode)] ?? null;
    }

    /**
     * Return all state codes grouped by their parent zone.
     */
    public static function stateZones(): array
    {
        $zones = [];

        foreach (self::STATE_ZONE_MAP as $state => $zone) {
            $zones[$zone][] = $state;
        }

        return $zones;
    }

    /**
     * Return the state codes that belong to a given zone.
     */
    public static function statesForZone(?string $zoneCode): array
    {
        if ($zoneCode === null || $zoneCode === '') {
            return [];
        }

        return self::stateZones()[self::normalizeZoneCode($zoneCode)] ?? [];
    }

    /**
     * Normalize a zone code to the canonical hyphen format (ZONE-A).
     */
    public static function normalizeZoneCode(?string $code): ?string
    {
        if ($code === null || $code === '') {
            return null;
        }

        $normalized = strtoupper(str_replace('_', '-', trim($code)));

        if (! preg_match('/^ZONE-[A-H]$/', $normalized)) {
            return null;
        }

        return $normalized;
    }

    public static function allFormationsByCode(): array
    {
        $byCode = [];

        foreach (self::zonalOffices() as $item) {
            $byCode[$item['code']] = array_merge($item, ['type' => 'zonal']);
        }

        foreach (self::stateCommands() as $item) {
            $byCode[$item['code']] = array_merge($item, ['type' => 'state_command']);
        }

        foreach (self::specialCommands() as $item) {
            $byCode[$item['code']] = array_merge($item, ['type' => 'special_command']);
        }

        return $byCode;
    }

    public static function isSpecialCommandCode(?string $code): bool
    {
        if ($code === null || $code === '') {
            return false;
        }

        foreach (self::specialCommands() as $command) {
            if ($command['code'] === $code) {
                return true;
            }
        }

        return false;
    }

    public static function isZonalCode(?string $code): bool
    {
        if ($code === null || $code === '') {
            return false;
        }

        foreach (self::zonalOffices() as $office) {
            if ($office['code'] === $code) {
                return true;
            }
        }

        return false;
    }

    public static function geoStateForCode(?string $code): ?string
    {
        if ($code === null || $code === '') {
            return null;
        }

        foreach (self::zonalOffices() as $office) {
            if ($office['code'] === $code) {
                return self::stateCode($office['state'] ?? '');
            }
        }

        foreach (self::specialCommands() as $command) {
            if ($command['code'] === $code) {
                return self::stateCode($command['state'] ?? '');
            }
        }

        foreach (self::stateCommands() as $command) {
            if ($command['code'] === $code) {
                return self::stateCode($command['state'] ?? '');
            }
        }

        return self::stateCode($code) ?: null;
    }

    public static function labelForCode(?string $code): ?string
    {
        if ($code === null || $code === '') {
            return null;
        }

        foreach (self::allFormationsByCode() as $itemCode => $item) {
            if ($itemCode === $code) {
                return $item['label'] ?? null;
            }
        }

        return null;
    }

    public static function typeForCode(?string $code): ?string
    {
        return self::allFormationsByCode()[$code]['type'] ?? null;
    }

    /**
     * Resolve a human-readable command/formation name for the authenticated user.
     */
    public static function commandNameForUser(?\App\Models\User $user): ?string
    {
        $code = $user?->primary_location_code;

        if ($code === null || $code === '') {
            return null;
        }

        $label = self::labelForCode($code);

        return $label ?? strtoupper((string) $code);
    }

    private static function stateCode(string $stateName): ?string
    {
        $name = trim($stateName);

        if ($name === '') {
            return null;
        }

        if (isset(self::STATE_CODE_MAP[$name])) {
            return self::STATE_CODE_MAP[$name];
        }

        $fromDb = GeolocationService::stateCodeFromName($name);

        return $fromDb ?: null;
    }
}
