<?php

namespace App\Services;

final class PreviewRenderer
{
    private const LOWERCASE_WORDS = ['of', 'in', 'to', 'and', 'the', 'for', 'by', 'on', 'a', 'an', 'if', 'any'];

    private const SPECIAL_WORDS = [
        'nimcos' => 'NIMCOS', 'mou' => 'MoU', 'mous' => 'MoUs', 'midas' => 'MIDAS',
        'evisa' => 'e-Visa', 'dfu' => 'DFU', 'dofit' => 'DOFIT', 'ecowas' => 'ECOWAS', 'cerpac' => 'CERPAC',
        'lasc' => 'LASC', 'seme' => 'SEME', 'idbc' => 'IDBC', 'lspmc' => 'LSPMC', 'lapc' => 'LAPC',
        'mmia' => 'MMIA', 'ogsc' => 'OGSC', 'kdsc' => 'KDSC', 'knsc' => 'KNSC', 'itsk' => 'ITSK',
        'makia' => 'MAKIA', 'ktsc' => 'KTSC', 'jsbc' => 'JSBC', 'sosc' => 'SOSC', 'icsc' => 'ICSC',
        'illela' => 'ILLELA', 'zmsc' => 'ZMSC', 'jgsc' => 'JGSC', 'basc' => 'BASC', 'ybsc' => 'YBSC',
        'bosc' => 'BOSC', 'adsc' => 'ADSC', 'gmsc' => 'GMSC', 'plsc' => 'PLSC', 'fctc' => 'FCTC',
        'naia' => 'NAIA', 'kwsc' => 'KWSC', 'ngsc' => 'NGSC', 'kbsc' => 'KBSC', 'absc' => 'ABSC',
        'aksc' => 'AKSC', 'crsc' => 'CRSC', 'mfum' => 'MFUM', 'ebsc' => 'EBSC', 'imsc' => 'IMSC',
        'nitsol' => 'NITSOL', 'rvsc' => 'RVSC', 'phia' => 'PHIA', 'nitsa' => 'NITSA', 'rvmc' => 'RVMC',
        'oysc' => 'OYSC', 'ossc' => 'OSSC', 'odsc' => 'ODSC', 'eksc' => 'EKSC', 'ansc' => 'ANSC',
        'bysc' => 'BYSC', 'dtsc' => 'DTSC', 'ensc' => 'ENSC', 'aiia' => 'AIIA', 'edsc' => 'EDSC',
        'bnsc' => 'BNSC', 'kgsc' => 'KGSC', 'trsc' => 'TRSC', 'nasc' => 'NASC',
    ];

    /**
     * Build an ordered list of preview sections from return data.
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     * @return array<int, array{label: string, html: string}>
     */
    public static function buildSections(array $data, array $options = []): array
    {
        $skipKeys = $options['skipKeys'] ?? [];
        $directorateSlug = $options['directorateSlug'] ?? null;
        $directorateNames = $options['directorateNames'] ?? [];
        $sectionLabels = $options['sectionLabels'] ?? [];

        $sections = [];
        $flatFields = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $skipKeys, true) || self::isBlankValue($value)) {
                continue;
            }

            if ($key === $directorateSlug && is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    if (self::isBlankValue($subValue)) {
                        continue;
                    }
                    if (is_array($subValue)) {
                        $sections[] = [
                            'label' => $sectionLabels[$subKey] ?? self::humanize($subKey),
                            'html' => self::renderData($subValue),
                        ];
                    } else {
                        $flatFields[$subKey] = $subValue;
                    }
                }
                continue;
            }

            if (is_array($value)) {
                $sections[] = [
                    'label' => $sectionLabels[$key] ?? $directorateNames[$key] ?? self::humanize($key),
                    'html' => self::renderData($value),
                ];
            } else {
                $flatFields[$key] = $value;
            }
        }

        if ($flatFields !== []) {
            array_unshift($sections, ['label' => 'Return Details', 'html' => self::kvTable($flatFields)]);
        }

        return $sections;
    }

    public static function humanize(string|int $key): string
    {
        $words = preg_split('/[_\-\s]+/', (string) $key) ?: [];
        $out = [];
        foreach ($words as $w) {
            $l = strtolower($w);
            if (isset(self::SPECIAL_WORDS[$l])) {
                $out[] = self::SPECIAL_WORDS[$l];
            } elseif (strlen($l) <= 3 && ctype_alpha($l) && ! in_array($l, self::LOWERCASE_WORDS, true)) {
                $out[] = strtoupper($l);
            } else {
                $out[] = ucfirst($l);
            }
        }
        return implode(' ', $out);
    }

    public static function formatValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }
        if (is_string($value)) {
            return self::sanitizeString($value);
        }
        return (string) $value;
    }

    /**
     * Strip tags and event-handler attributes from a string before display.
     */
    private static function sanitizeString(string $value): string
    {
        $value = strip_tags($value);
        $value = preg_replace('/\bon\w+\s*=\s*["\']?[^"\']*["\']?/i', '', $value) ?? $value;
        return trim($value);
    }

    private static function isEmptyValue(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }
        if (is_array($value)) {
            foreach ($value as $v) {
                if (! self::isEmptyValue($v)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Treat null, empty string, zero and the string "0" as blank.
     * Used when deciding whether to drop an entire section/table.
     */
    private static function isBlankValue(mixed $value): bool
    {
        if ($value === null || $value === '' || $value === 0 || $value === '0') {
            return true;
        }
        if (is_array($value)) {
            foreach ($value as $v) {
                if (! self::isBlankValue($v)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    private static function isAssoc(array $arr): bool
    {
        return $arr !== [] && array_keys($arr) !== range(0, count($arr) - 1);
    }

    private static function isFlat(array $arr): bool
    {
        foreach ($arr as $v) {
            if (is_array($v)) {
                return false;
            }
        }
        return true;
    }

    private static function kvTable(array $pairs): string
    {
        $out = '<table class="report-table kv-table"><tbody>';
        foreach ($pairs as $key => $value) {
            if (self::isEmptyValue($value)) {
                continue;
            }
            $out .= '<tr><td class="kv-key">' . e(self::humanize($key)) . '</td><td>'
                . e(self::formatValue($value)) . '</td></tr>';
        }
        return $out . '</tbody></table>';
    }

    private static function matrixTable(array $rows): string
    {
        $columns = [];
        foreach ($rows as $row) {
            foreach (array_keys($row) as $col) {
                if (! in_array($col, $columns, true)) {
                    $columns[] = $col;
                }
            }
        }

        $out = '<table class="report-table"><thead><tr><th style="width:32%;">Item</th>';
        foreach ($columns as $col) {
            $out .= '<th>' . e(self::humanize($col)) . '</th>';
        }
        $out .= '</tr></thead><tbody>';
        foreach ($rows as $label => $row) {
            if (self::isEmptyValue($row)) {
                continue;
            }
            $out .= '<tr><td class="row-head">' . e(self::humanize($label)) . '</td>';
            foreach ($columns as $col) {
                $out .= '<td>' . e(self::formatValue($row[$col] ?? null)) . '</td>';
            }
            $out .= '</tr>';
        }
        return $out . '</tbody></table>';
    }

    private static function rowsTable(array $rows): string
    {
        $rows = array_values(array_filter($rows, function ($row) {
            if (! is_array($row)) {
                return ! self::isEmptyValue($row);
            }
            foreach ($row as $v) {
                if (! self::isEmptyValue($v)) {
                    return true;
                }
            }
            return false;
        }));

        if ($rows === []) {
            return '';
        }

        $columns = [];
        foreach ($rows as $row) {
            foreach (array_keys($row) as $col) {
                if (! in_array($col, $columns, true)) {
                    $columns[] = $col;
                }
            }
        }

        $out = '<table class="report-table"><thead><tr><th style="width:44px;">S/N</th>';
        foreach ($columns as $col) {
            $out .= '<th>' . e(self::humanize($col)) . '</th>';
        }
        $out .= '</tr></thead><tbody>';
        foreach ($rows as $i => $row) {
            $out .= '<tr><td>' . ($i + 1) . '</td>';
            foreach ($columns as $col) {
                $cell = is_array($row) ? ($row[$col] ?? null) : null;
                $out .= '<td>' . (is_array($cell) ? self::renderData($cell) : e(self::formatValue($cell))) . '</td>';
            }
            $out .= '</tr>';
        }
        return $out . '</tbody></table>';
    }

    private static function renderData(mixed $value): string
    {
        if (! is_array($value)) {
            return self::isEmptyValue($value) ? '' : '<span>' . e(self::formatValue($value)) . '</span>';
        }
        if ($value === [] || self::isEmptyValue($value)) {
            return '';
        }

        if (! self::isAssoc($value)) {
            $allArrays = count(array_filter($value, 'is_array')) === count($value);
            if (! $allArrays) {
                $filled = array_values(array_filter($value, fn ($v) => ! self::isEmptyValue($v)));
                return $filled === [] ? '' : '<span>' . e(implode(', ', array_map([self::class, 'formatValue'], $filled))) . '</span>';
            }
            return self::rowsTable($value);
        }

        $scalars = [];
        $matrixRows = [];
        $nested = [];
        foreach ($value as $key => $item) {
            if (self::isEmptyValue($item)) {
                continue;
            }
            if (! is_array($item)) {
                $scalars[$key] = $item;
            } elseif (self::isFlat($item)) {
                $matrixRows[$key] = $item;
            } else {
                $nested[$key] = $item;
            }
        }

        $out = '';
        if ($scalars !== []) {
            $out .= self::kvTable($scalars);
        }
        if ($matrixRows !== []) {
            $out .= self::matrixTable($matrixRows);
        }
        foreach ($nested as $key => $item) {
            $label = (strlen((string) $key) === 1 && ctype_alpha((string) $key))
                ? 'Zone ' . strtoupper((string) $key)
                : self::humanize($key);
            $out .= '<div class="sub-section-title">' . e($label) . '</div>';
            $out .= self::renderData($item);
        }

        return $out;
    }
}
