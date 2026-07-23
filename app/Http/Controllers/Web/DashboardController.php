<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
	public function showDirectorate(string $slug): View
	{
		$directorates = $this->directorates();
		abort_unless(isset($directorates[$slug]), 404);

		return view('user.directorate-form', [
			'slug' => $slug,
			'directorate' => $directorates[$slug],
			'allDirectorates' => $directorates,
		]);
	}

	public function storeDirectorate(Request $request, string $slug): RedirectResponse
	{
		$directorates = $this->directorates();
		abort_unless(isset($directorates[$slug]), 404);

		$request->validate([
			'report_period' => ['required', 'date_format:Y-m'],
			'reporting_officer' => ['required', 'string', 'max:120'],
		]);

		return redirect()
			->route('user.directorates.show', $slug)
			->with('status', $directorates[$slug]['name'] . ' return submitted successfully.');
	}

	private function directorates(): array
	{
		return [
			'hrm' => [
				'name' => 'Human Resources Management (HRM)',
				'icon' => 'fas fa-users',
				'sections' => [
					[
						'title' => 'Directorate Personnel Strength',
						'fields' => [
							['name' => 'staff_comptroller_cadre', 'label' => 'Comptroller Cadre', 'type' => 'number'],
							['name' => 'staff_superintendent_cadre', 'label' => 'Superintendent Cadre', 'type' => 'number'],
							['name' => 'staff_inspectorate_cadre', 'label' => 'Inspectorate Cadre', 'type' => 'number'],
							['name' => 'staff_assistant_cadre', 'label' => 'Assistant Cadre', 'type' => 'number'],
						],
					],
					[
						'title' => 'Career Progression and Training',
						'fields' => [
							['name' => 'officer_promotions', 'label' => 'Officer Promotions', 'type' => 'number'],
							['name' => 'upgrading_cases', 'label' => 'Upgrading Cases', 'type' => 'number'],
							['name' => 'conversion_cases', 'label' => 'Conversion Cases', 'type' => 'number'],
							['name' => 'training_programmes', 'label' => 'Training Programmes Conducted', 'type' => 'number'],
							['name' => 'workshops_count', 'label' => 'Workshops/Seminars', 'type' => 'number'],
							['name' => 'hrm_challenges', 'label' => 'Challenges', 'type' => 'textarea'],
						],
					],
				],
			],
			'prs' => [
				'name' => 'Planning, Research and Statistics (PRS)',
				'icon' => 'fas fa-chart-bar',
				'sections' => [
					[
						'title' => 'Research and Statistics',
						'fields' => [
							['name' => 'research_topics', 'label' => 'No. of Research Topics', 'type' => 'number'],
							['name' => 'research_completed', 'label' => 'Completed Research Studies', 'type' => 'number'],
							['name' => 'cost_implication', 'label' => 'Research Cost Implication (NGN)', 'type' => 'number'],
							['name' => 'nominal_roll_updates', 'label' => 'Nominal Roll Updates', 'type' => 'number'],
						],
					],
					[
						'title' => 'Monitoring and Evaluation',
						'fields' => [
							['name' => 'personnel_reports', 'label' => 'Personnel Reports Reviewed', 'type' => 'number'],
							['name' => 'financial_reports', 'label' => 'Financial Reports Reviewed', 'type' => 'number'],
							['name' => 'capital_expenditure_reviews', 'label' => 'Capital Expenditure Reviews', 'type' => 'number'],
							['name' => 'prs_recommendations', 'label' => 'Recommendations / Way Forward', 'type' => 'textarea'],
						],
					],
				],
			],
			'finance' => [
				'name' => 'Finance and Accounts',
				'icon' => 'fas fa-coins',
				'sections' => [
					[
						'title' => 'Revenue Performance Report',
						'fields' => [
							['name' => 'local_revenue_passport', 'label' => 'Passport Revenue (Local)', 'type' => 'number'],
							['name' => 'local_revenue_visa', 'label' => 'Visa Revenue (Local)', 'type' => 'number'],
							['name' => 'local_revenue_e_pass', 'label' => 'E-Pass Revenue (Local)', 'type' => 'number'],
							['name' => 'local_revenue_other', 'label' => 'Other Revenue (IGR)', 'type' => 'number'],
						],
					],
					[
						'title' => 'Expenditure and Budget',
						'fields' => [
							['name' => 'foreign_revenue_total', 'label' => 'Foreign Revenue Total', 'type' => 'number'],
							['name' => 'expenditure_profile_total', 'label' => 'Expenditure Profile Total', 'type' => 'number'],
							['name' => 'budget_amount', 'label' => 'Amount Budgeted', 'type' => 'number'],
							['name' => 'release_amount', 'label' => 'Amount Released', 'type' => 'number'],
							['name' => 'finance_challenges', 'label' => 'Challenges', 'type' => 'textarea'],
						],
					],
				],
			],
			'investigation' => [
				'name' => 'Investigation and Compliance',
				'icon' => 'fas fa-search',
				'sections' => [
					[
						'title' => 'Breach of Immigration Laws',
						'fields' => [
							['name' => 'breach_cases', 'label' => 'Breach Cases Handled', 'type' => 'number'],
							['name' => 'arrests_made', 'label' => 'Arrests Made', 'type' => 'number'],
							['name' => 'prosecution_cases', 'label' => 'Cases Prosecuted', 'type' => 'number'],
							['name' => 'convictions', 'label' => 'Convictions Secured', 'type' => 'number'],
						],
					],
					[
						'title' => 'Specialized Operations',
						'fields' => [
							['name' => 'interpol_activities', 'label' => 'Interpol Activities', 'type' => 'number'],
							['name' => 'dofit_cases', 'label' => 'DOFIT Cases', 'type' => 'number'],
							['name' => 'suspect_index_watchlist', 'label' => 'Watch-listed Persons', 'type' => 'number'],
							['name' => 'screening_centre_detainees', 'label' => 'Screening Centre Detainees', 'type' => 'number'],
							['name' => 'investigation_remarks', 'label' => 'Remarks', 'type' => 'textarea'],
						],
					],
				],
			],
			'passport' => [
				'name' => 'Passport and Other Travel Documents',
				'icon' => 'fas fa-passport',
				'sections' => [
					[
						'title' => 'Executive Summary of Passport Operations',
						'fields' => [
							['name' => 'processing_centres_reporting', 'label' => 'Passport Processing Centres Reporting', 'type' => 'number'],
							['name' => 'total_applications_received', 'label' => 'Total Applications Received', 'type' => 'number'],
							['name' => 'total_applications_enrolled', 'label' => 'Total Applications Enrolled', 'type' => 'number'],
							['name' => 'booklets_issued', 'label' => 'No. of Booklets Issued', 'type' => 'number'],
						],
					],
					[
						'title' => 'Revenue and Stock Returns',
						'fields' => [
							['name' => 'passport_revenue_total', 'label' => 'Passport Revenue Total (NGN)', 'type' => 'number'],
							['name' => 'etc_booklets_issued', 'label' => 'ECOWAS Travel Certificates Issued', 'type' => 'number'],
							['name' => 'enbic_produced', 'label' => 'ENBIC Produced', 'type' => 'number'],
							['name' => 'passport_stock_balance', 'label' => 'Passport Stock Balance', 'type' => 'number'],
							['name' => 'passport_challenges', 'label' => 'Challenges', 'type' => 'textarea'],
						],
					],
				],
			],
			'visa' => [
				'name' => 'Visa and Residency',
				'icon' => 'fas fa-stamp',
				'sections' => [
					[
						'title' => 'State Coordination Activities',
						'fields' => [
							['name' => 'state_applications_received', 'label' => 'Applications Received', 'type' => 'number'],
							['name' => 'state_applications_treated', 'label' => 'Applications Treated', 'type' => 'number'],
							['name' => 'state_cards_issued', 'label' => 'Cards Issued', 'type' => 'number'],
							['name' => 'state_pending_cases', 'label' => 'Pending Cases', 'type' => 'number'],
						],
					],
					[
						'title' => 'Quota and CERPAC Activities',
						'fields' => [
							['name' => 'quota_files_treated', 'label' => 'Quota Files Treated', 'type' => 'number'],
							['name' => 'diplomatic_facilities_issued', 'label' => 'Diplomatic Facilities Issued', 'type' => 'number'],
							['name' => 'cerpac_cards_issued', 'label' => 'CERPAC Cards Issued', 'type' => 'number'],
							['name' => 'evisa_received', 'label' => 'E-Visa Applications Received', 'type' => 'number'],
							['name' => 'evisa_approved', 'label' => 'E-Visa Applications Approved', 'type' => 'number'],
							['name' => 'evisa_denied', 'label' => 'E-Visa Applications Denied', 'type' => 'number'],
						],
					],
				],
			],
			'migration' => [
				'name' => 'Migration Directorate',
				'icon' => 'fas fa-globe-africa',
				'sections' => [
					[
						'title' => 'Migrant Data and Permits',
						'fields' => [
							['name' => 'migrant_ereg_count', 'label' => 'Migrant E-Registrations', 'type' => 'number'],
							['name' => 'work_permits_issued', 'label' => 'Foreigners Issued Work Permits', 'type' => 'number'],
							['name' => 'migrant_students', 'label' => 'Migrant Students Recorded', 'type' => 'number'],
							['name' => 'seasonal_migrants_employed', 'label' => 'Seasonal Migrants Employed', 'type' => 'number'],
							['name' => 'border_migrants_employed', 'label' => 'Border Migrants Employed', 'type' => 'number'],
						],
					],
					[
						'title' => 'International Cooperation',
						'fields' => [
							['name' => 'mou_africa', 'label' => 'MoUs with African Countries', 'type' => 'number'],
							['name' => 'mou_europe', 'label' => 'MoUs with European Countries', 'type' => 'number'],
							['name' => 'mou_asia', 'label' => 'MoUs with Asian Countries', 'type' => 'number'],
							['name' => 'mou_americas', 'label' => 'MoUs with Americas', 'type' => 'number'],
							['name' => 'migration_summary', 'label' => 'Summary / Remarks', 'type' => 'textarea'],
						],
					],
				],
			],
			'border' => [
				'name' => 'Border Management',
				'icon' => 'fas fa-border-all',
				'sections' => [
					[
						'title' => 'Land and Sea Border Returns',
						'fields' => [
							['name' => 'land_arrivals', 'label' => 'Land Border Arrivals', 'type' => 'number'],
							['name' => 'land_departures', 'label' => 'Land Border Departures', 'type' => 'number'],
							['name' => 'sea_arrivals', 'label' => 'Sea Border Arrivals', 'type' => 'number'],
							['name' => 'sea_departures', 'label' => 'Sea Border Departures', 'type' => 'number'],
							['name' => 'passenger_crew_count', 'label' => 'Passengers/Crew Processed', 'type' => 'number'],
						],
					],
					[
						'title' => 'Ordinance and Deportation Activities',
						'fields' => [
							['name' => 'arms_serviceable', 'label' => 'Serviceable Arms', 'type' => 'number'],
							['name' => 'arms_unserviceable', 'label' => 'Unserviceable Arms', 'type' => 'number'],
							['name' => 'ammunition_rounds_used', 'label' => 'Ammunition Rounds Used', 'type' => 'number'],
							['name' => 'migrants_deported', 'label' => 'Migrants Deported from Nigeria', 'type' => 'number'],
							['name' => 'migrants_repatriated', 'label' => 'Migrants Repatriated', 'type' => 'number'],
						],
					],
				],
			],
			'ict' => [
				'name' => 'ICT Directorate',
				'icon' => 'fas fa-laptop-code',
				'sections' => [
					[
						'title' => 'Project and System Activities',
						'fields' => [
							['name' => 'project_programme_activities', 'label' => 'Project/Programme Activities', 'type' => 'number'],
							['name' => 'systems_operational', 'label' => 'Systems Fully Operational', 'type' => 'number'],
							['name' => 'midas_sites_deployed', 'label' => 'MIDAS Deployment Sites', 'type' => 'number'],
							['name' => 'software_delivered', 'label' => 'Software Deliverables', 'type' => 'number'],
						],
					],
					[
						'title' => 'Maintenance and Security',
						'fields' => [
							['name' => 'hardware_maintenance_cases', 'label' => 'Hardware Maintenance Cases', 'type' => 'number'],
							['name' => 'data_breach_incidents', 'label' => 'Data Breach Incidents', 'type' => 'number'],
							['name' => 'cybersecurity_incidents', 'label' => 'Cybersecurity Incidents', 'type' => 'number'],
							['name' => 'security_controls_deployed', 'label' => 'Security Controls Deployed', 'type' => 'number'],
							['name' => 'ict_other_reports', 'label' => 'Other Reports', 'type' => 'textarea'],
						],
					],
				],
			],
			'works-logistics' => [
				'name' => 'Works and Logistics',
				'icon' => 'fas fa-truck',
				'sections' => [
					[
						'title' => 'Store and Warehouse',
						'fields' => [
							['name' => 'store_items_received', 'label' => 'Store Items Received', 'type' => 'number'],
							['name' => 'store_items_issued', 'label' => 'Store Items Issued', 'type' => 'number'],
							['name' => 'stationery_balance', 'label' => 'Stationery Balance', 'type' => 'number'],
							['name' => 'oil_gas_balance', 'label' => 'Oil and Gas Balance', 'type' => 'number'],
						],
					],
					[
						'title' => 'Transport and Maintenance',
						'fields' => [
							['name' => 'fleet_operational', 'label' => 'Operational Fleet Vehicles', 'type' => 'number'],
							['name' => 'fleet_under_repair', 'label' => 'Fleet Under Repair', 'type' => 'number'],
							['name' => 'projects_awarded', 'label' => 'Projects Awarded', 'type' => 'number'],
							['name' => 'projects_completed', 'label' => 'Projects Completed', 'type' => 'number'],
							['name' => 'notable_activities', 'label' => 'Other Notable Activities', 'type' => 'textarea'],
						],
					],
				],
			],
		];
	}
}

