<?php

namespace App\Services;

use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use ZipArchive;

class ExcelReportService
{
    public const TEMPLATES = [
        'quarterly' => [
            'title' => 'Quarterly Consolidated Returns Report',
            'parts' => [
                1 => ['label' => 'Q1 (Jan - Mar)', 'months' => [1, 3]],
                2 => ['label' => 'Q2 (Apr - Jun)', 'months' => [4, 6]],
                3 => ['label' => 'Q3 (Jul - Sep)', 'months' => [7, 9]],
                4 => ['label' => 'Q4 (Oct - Dec)', 'months' => [10, 12]],
            ],
        ],
        'biannual' => [
            'title' => 'Bi-Annual Consolidated Returns Report',
            'parts' => [
                1 => ['label' => 'First Half (Jan - Jun)', 'months' => [1, 6]],
                2 => ['label' => 'Second Half (Jul - Dec)', 'months' => [7, 12]],
            ],
        ],
        'annual' => [
            'title' => 'Annual Consolidated Returns Report',
            'parts' => [
                0 => ['label' => 'Full Year (Jan - Dec)', 'months' => [1, 12]],
            ],
        ],
    ];

    public const COLUMNS = [
        'Ref',
        'Formation/Directorate',
        'CGIS Unit',
        'Category',
        'Report Period',
        'Reporting Officer',
        'Service Number',
        'Status',
        'Current Stage',
        'Submitted At',
        'Last Action At',
        'Comment',
    ];

    /**
     * Resolve a report template to its title, period label and date range.
     *
     * @return array{0: string, 1: string, 2: Carbon, 3: Carbon}
     */
    public static function periodFor(string $reportType, int $year, ?int $part): array
    {
        $template = self::TEMPLATES[$reportType] ?? null;

        if ($template === null) {
            throw new \InvalidArgumentException("Unknown report type [{$reportType}].");
        }

        $partKey = $reportType === 'annual' ? 0 : $part;
        $partDefinition = $template['parts'][$partKey] ?? null;

        if ($partDefinition === null) {
            throw new \InvalidArgumentException("Invalid period part for report type [{$reportType}].");
        }

        [$startMonth, $endMonth] = $partDefinition['months'];

        return [
            $template['title'],
            $partDefinition['label'],
            Carbon::create($year, $startMonth, 1)->startOfMonth(),
            Carbon::create($year, $endMonth, 1)->endOfMonth(),
        ];
    }

    /**
     * Build a consolidated single-worksheet .xlsx for the given returns and
     * return the path of the temporary file.
     */
    public function generate(string $reportType, int $year, ?int $part, Collection $applications): string
    {
        [$title, $label] = self::periodFor($reportType, $year, $part);

        $rows = [
            ['style' => 1, 'cells' => ["{$title} — {$label} {$year}"]],
            ['style' => 1, 'cells' => self::COLUMNS],
        ];

        foreach ($applications as $application) {
            $rows[] = ['style' => 0, 'cells' => $this->rowFor($application)];
        }

        return $this->writeXlsx($rows);
    }

    /**
     * @return array<int, string>
     */
    private function rowFor(Application $application): array
    {
        $data = is_array($application->return_data) ? $application->return_data : [];
        $user = $application->user;

        return [
            'RET-' . str_pad((string) $application->id, 5, '0', STR_PAD_LEFT),
            (string) ($data['command_name'] ?? $application->scope_code ?? '—'),
            (string) ($user?->assigned_cgis_unit_code ?? '—'),
            ucfirst((string) $application->category),
            (string) ($data['report_period'] ?? '—'),
            (string) ($data['reporting_officer'] ?? $user?->name ?? '—'),
            (string) ($user?->service_number ?? '—'),
            ucfirst((string) $application->status),
            ucwords(str_replace('_', ' ', (string) $application->workflow_stage)),
            (string) optional($application->created_at)->toDateTimeString(),
            (string) optional($application->updated_at)->toDateTimeString(),
            (string) ($application->comments ?? ''),
        ];
    }

    /**
     * Minimal XLSX writer (ZipArchive, inline strings, bold header style).
     *
     * @param  array<int, array{style: int, cells: array<int, string>}>  $rows
     */
    private function writeXlsx(array $rows): string
    {
        $sheetRows = '';

        foreach ($rows as $rowIndex => $row) {
            $rowNumber = $rowIndex + 1;
            $cells = '';

            foreach ($row['cells'] as $columnIndex => $value) {
                $ref = $this->columnLetter($columnIndex) . $rowNumber;
                $escaped = htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $cells .= '<c r="' . $ref . '" t="inlineStr" s="' . $row['style'] . '"><is><t>' . $escaped . '</t></is></c>';
            }

            $sheetRows .= '<row r="' . $rowNumber . '">' . $cells . '</row>';
        }

        $path = tempnam(sys_get_temp_dir(), 'redas-report-');

        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Consolidated Returns" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>');
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2">'
            . '<font><sz val="11"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><name val="Calibri"/></font>'
            . '</fonts>'
            . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            . '</cellXfs>'
            . '</styleSheet>');
        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>' . $sheetRows . '</sheetData>'
            . '</worksheet>');
        $zip->close();

        return $path;
    }

    private function columnLetter(int $index): string
    {
        $letter = '';

        do {
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intdiv($index, 26) - 1;
        } while ($index >= 0);

        return $letter;
    }
}
