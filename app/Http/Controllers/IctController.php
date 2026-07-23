<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;

class IctController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        $latest = Application::where('user_id', auth()->id())
            ->where('type', 'ict_cybersecurity')
            ->latest()
            ->first();

        $staffStrengthCount = 0;
        $projectCount = 0;
        $hardwareIncidentsCount = 0;
        $softwareIncidentsCount = 0;
        $networkIncidentsCount = 0;
        $cybersecurityIncidentsCount = 0;
        $powerIncidentsCount = 0;
        $communicationIncidentsCount = 0;
        $surveillanceIncidentsCount = 0;
        $providerIncidentsCount = 0;
        $totalIncidentsCount = 0;
        $maintenanceCount = 0;
        $softwareCount = 0;
        $dataBreachCount = 0;
        $cybersecurityCount = 0;
        $idCardCount = 0;
        $midasCount = 0;

        if ($latest && is_array($latest->return_data)) {
            $data = $latest->return_data;

            // 1. Staff Strength
            if (isset($data['staff'])) {
                foreach ($data['staff'] as $cadre => $genders) {
                    $staffStrengthCount += (int)($genders['male'] ?? 0) + (int)($genders['female'] ?? 0);
                }
            }

            // 2. Project activities
            if (isset($data['projects']) && is_array($data['projects'])) {
                $projectCount = count($data['projects']);
            }

            // 3. Incidents
            if (isset($data['incidents_hardware']) && is_array($data['incidents_hardware'])) {
                $hardwareIncidentsCount = count($data['incidents_hardware']);
            }
            if (isset($data['incidents_software']) && is_array($data['incidents_software'])) {
                $softwareIncidentsCount = count($data['incidents_software']);
            }
            if (isset($data['incidents_network']) && is_array($data['incidents_network'])) {
                $networkIncidentsCount = count($data['incidents_network']);
            }
            if (isset($data['incidents_cybersecurity']) && is_array($data['incidents_cybersecurity'])) {
                $cybersecurityIncidentsCount = count($data['incidents_cybersecurity']);
            }
            if (isset($data['incidents_power']) && is_array($data['incidents_power'])) {
                $powerIncidentsCount = count($data['incidents_power']);
            }
            if (isset($data['incidents_communication']) && is_array($data['incidents_communication'])) {
                $communicationIncidentsCount = count($data['incidents_communication']);
            }
            if (isset($data['incidents_surveillance']) && is_array($data['incidents_surveillance'])) {
                $surveillanceIncidentsCount = count($data['incidents_surveillance']);
            }
            if (isset($data['incidents_providers']) && is_array($data['incidents_providers'])) {
                $providerIncidentsCount = count($data['incidents_providers']);
            }
            $totalIncidentsCount = $hardwareIncidentsCount + $softwareIncidentsCount + $networkIncidentsCount + 
                                  $cybersecurityIncidentsCount + $powerIncidentsCount + $communicationIncidentsCount + 
                                  $surveillanceIncidentsCount + $providerIncidentsCount;

            // 4. Hardware Maintenance
            if (isset($data['maintenance']) && is_array($data['maintenance'])) {
                $maintenanceCount = count($data['maintenance']);
            }

            // 5. Software
            if (isset($data['software']) && is_array($data['software'])) {
                $softwareCount = count($data['software']);
            }

            // 6. Data Breach
            if (isset($data['data_breach']) && is_array($data['data_breach'])) {
                foreach ($data['data_breach'] as $row) {
                    $dataBreachCount += (int)($row['no_of_incident'] ?? 0);
                }
            }

            // 7. Cybersecurity Deployment
            if (isset($data['cybersecurity']) && is_array($data['cybersecurity'])) {
                $cybersecurityCount = count($data['cybersecurity']);
            }

            // 8. ID Card Activities
            if (isset($data['id_cards']) && is_array($data['id_cards'])) {
                foreach ($data['id_cards'] as $rank => $zones) {
                    if ($rank === 'total') continue;
                    foreach ($zones as $zone => $val) {
                        if ($zone === 'total') continue;
                        $idCardCount += (int)$val;
                    }
                }
            }

            // 9. MIDAS Deployment
            if (isset($data['midas']) && is_array($data['midas'])) {
                $midasCount = count($data['midas']);
            }
        }

        $recentSubmissions = Application::where('user_id', auth()->id())
            ->where('type', 'ict_cybersecurity')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('user.ict.dashboard', compact(
            'staffStrengthCount',
            'projectCount',
            'totalIncidentsCount',
            'hardwareIncidentsCount',
            'softwareIncidentsCount',
            'networkIncidentsCount',
            'cybersecurityIncidentsCount',
            'maintenanceCount',
            'softwareCount',
            'dataBreachCount',
            'cybersecurityCount',
            'idCardCount',
            'midasCount',
            'recentSubmissions'
        ));
    }

    // Annual Report Workspace
    public function create()
    {
        return view('user.ict.report');
    }

    // Store Report
    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_year' => 'required|integer',
            'remarks' => 'nullable|string',
        ]);

        $status = $request->input('report_status', ($request->input('action') === 'draft' ? 'draft' : 'pending'));
        $period = $request->input('report_year');
        $comments = $request->input('remarks');

        // Exclude framework and action tokens
        $returnData = $request->except(['_token', 'action', 'report_year', 'remarks']);

        $application = Application::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'type' => 'ict_cybersecurity',
                'period' => $period,
            ],
            [
                'status' => $status,
                'return_data' => $returnData,
                'comments' => $comments,
            ]
        );

        $message = $status === 'draft' 
            ? 'ICT & Cybersecurity Annual Report draft saved successfully.'
            : 'ICT & Cybersecurity Annual Report submitted successfully.';

        return redirect()
            ->route('ict.submissions')
            ->with('status', $message);
    }

    // Reports Statistics
    public function reports()
    {
        $submissions = Application::where('user_id', auth()->id())
            ->where('type', 'ict_cybersecurity')
            ->orderBy('period', 'desc')
            ->get();

        $totalSubmissions = $submissions->count();
        $cumulativeProjects = 0;
        $cumulativeIncidents = 0;
        $cumulativeMaintenance = 0;
        $cumulativeIdCards = 0;

        $yearlyData = [];

        foreach ($submissions as $sub) {
            $projects = 0;
            $incidents = 0;
            $maintenance = 0;
            $idCards = 0;

            if (is_array($sub->return_data)) {
                $data = $sub->return_data;

                if (isset($data['projects']) && is_array($data['projects'])) {
                    $projects = count($data['projects']);
                }

                // Incidents sum
                $incTypes = ['incidents_hardware', 'incidents_software', 'incidents_network', 'incidents_cybersecurity', 'incidents_power', 'incidents_communication', 'incidents_surveillance', 'incidents_providers'];
                foreach ($incTypes as $type) {
                    if (isset($data[$type]) && is_array($data[$type])) {
                        $incidents += count($data[$type]);
                    }
                }

                if (isset($data['maintenance']) && is_array($data['maintenance'])) {
                    $maintenance = count($data['maintenance']);
                }

                if (isset($data['id_cards']) && is_array($data['id_cards'])) {
                    foreach ($data['id_cards'] as $rank => $zones) {
                        if ($rank === 'total') continue;
                        foreach ($zones as $zone => $val) {
                            if ($zone === 'total') continue;
                            $idCards += (int)$val;
                        }
                    }
                }
            }

            $cumulativeProjects += $projects;
            $cumulativeIncidents += $incidents;
            $cumulativeMaintenance += $maintenance;
            $cumulativeIdCards += $idCards;

            $yearlyData[] = [
                'year' => $sub->period,
                'status' => $sub->status,
                'projects' => $projects,
                'incidents' => $incidents,
                'maintenance' => $maintenance,
                'id_cards' => $idCards,
                'updated_at' => $sub->updated_at->format('d M Y'),
            ];
        }

        return view('user.ict.reports', compact(
            'totalSubmissions',
            'cumulativeProjects',
            'cumulativeIncidents',
            'cumulativeMaintenance',
            'cumulativeIdCards',
            'yearlyData'
        ));
    }

    // Submitted Reports List
    public function submissions()
    {
        $submissions = Application::where('user_id', auth()->id())
            ->where('type', 'ict_cybersecurity')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.ict.submissions', compact('submissions'));
    }
}
