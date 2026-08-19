<?php

namespace App\Services;

use App\Models\Application;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportPdfService
{
    /**
     * Render a single submission's return as a downloadable PDF.
     */
    public static function downloadSubmission(Application $application, ?string $directorateName = null)
    {
        $pdf = Pdf::loadView('pdf.submission', [
            'application' => $application,
            'directorateName' => $directorateName,
            'logoDataUri' => self::logoDataUri(),
        ])->setPaper('a4');

        return $pdf->download('submission-' . $application->id . '.pdf');
    }

    /**
     * Render a filtered submissions report as a downloadable PDF.
     */
    public static function downloadReport($submissions, array $filters, ?string $dashboardTitle = null)
    {
        $pdf = Pdf::loadView('pdf.report', [
            'submissions' => $submissions,
            'filters' => $filters,
            'dashboardTitle' => $dashboardTitle,
            'logoDataUri' => self::logoDataUri(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('submissions-report-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * Embed the NIS logo as a data URI (dompdf-safe, no remote fetch).
     */
    private static function logoDataUri(): ?string
    {
        $path = public_path('nis-logo.png');

        if (! is_file($path)) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
    }
}
