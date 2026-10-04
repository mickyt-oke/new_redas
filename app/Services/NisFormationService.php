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
        'Akwa Ibom' => 'AK',
        'Anambra' => 'AN',
        'Bauchi' => 'BA',
        'Bayelsa' => 'BY',
        'Benue' => 'BE',
        'Borno' => 'BO',
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
            $code = 'ZONE_' . $letter;
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
