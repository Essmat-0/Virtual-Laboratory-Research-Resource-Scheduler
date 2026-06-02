<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------------------------------------------
        // 1. ROLES
        // ----------------------------------------------------------------
        $roles = ['Admin', 'PI', 'Researcher', 'Lab_Manager', 'Auditor'];
        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name'       => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $adminRoleId      = DB::table('roles')->where('name', 'Admin')->value('id');
        $piRoleId         = DB::table('roles')->where('name', 'PI')->value('id');
        $researcherRoleId = DB::table('roles')->where('name', 'Researcher')->value('id');
        $LabmRoleId = DB::table('roles')->where('name', 'Lab_Manager')->value('id');
        $auditorRoleId = DB::table('roles')->where('name', 'Auditor')->value('id');

        // ----------------------------------------------------------------
        // 2. USERS  (base columns + columns added in second migration)
        // ----------------------------------------------------------------
        $users = [
            [
                'name'                  => 'Alice Admin',
                'email'                 => 'alice@lab.com',
                'password'              => Hash::make('password'),
                'clearance_level'       => 3,
                'role_id'               => $adminRoleId,
                'is_active'             => true,
                'expiry_date'           => '2027-01-01',
                'systemPrivileges'      => 'superadmin',
            ],
            [
                'name'                  => 'Bob PI',
                'email'                 => 'bob@lab.com',
                'password'              => Hash::make('password'),
                'clearance_level'       => 2,
                'role_id'               => $piRoleId,
                'is_active'             => true,
                'expiry_date'           => '2026-12-31',
                'systemPrivileges'      => 'none',
            ],
            [
                'name'                  => 'Carol Researcher',
                'email'                 => 'carol@lab.com',
                'password'              => Hash::make('password'),
                'clearance_level'       => 2,
                'role_id'               => $researcherRoleId,
                'is_active'             => true,
                'expiry_date'           => '2026-06-30',
                'systemPrivileges'      => 'none',
            ],
            [
                'name'                  => 'Dan Maintenance',
                'email'                 => 'dan@lab.com',
                'password'              => Hash::make('password'),
                'clearance_level'       => 3,
                'role_id'               => $LabmRoleId,
                'is_active'             => true,
                'expiry_date'           => '2027-06-01',
                'systemPrivileges'      => 'none',
            ],
            [
                'name'                  => 'Eve Researcher',
                'email'                 => 'eve@lab.com',
                'password'              => Hash::make('password'),
                'clearance_level'       => 1,
                'role_id'               => $researcherRoleId,
                'is_active'             => false,
                'expiry_date'           => '2025-12-31',
                'systemPrivileges'      => 'none',
            ],
            [
                'name'                  => 'Auditor Hamada',
                'email'                 => 'auditor@lab.com',
                'password'              => Hash::make('password'),
                'clearance_level'       => 1,
                'role_id'               => $auditorRoleId,
                'is_active'             => true,
                'expiry_date'           => '2025-12-31',
                'systemPrivileges'      => 'none',
            ],

            [
                'name'                  => 'PI Hamada',
                'email'                 => 'pi@lab.com',
                'password'              => Hash::make('password'),
                'clearance_level'       => 3,
                'role_id'               => $piRoleId,
                'is_active'             => true,
                'expiry_date'           => '2026-12-31',
                'systemPrivileges'      => 'none',
            ],
        ];


        foreach ($users as $user) {
            DB::table('users')->insert(array_merge($user, [
                'remember_token' => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]));
        }

        [$aliceId, $bobId, $carolId, $danId, $eveId, $auditorId, $PIId] = DB::table('users')->orderBy('id')->pluck('id')->toArray();

        // ----------------------------------------------------------------
        // 3. EQUIPMENT CATEGORIES
        // ----------------------------------------------------------------
        $categories = [
            ['name' => 'Microscopy',       'description' => 'Optical and electron microscopy equipment'],
            ['name' => 'Spectroscopy',     'description' => 'Spectroscopic analysis instruments'],
            ['name' => 'Fabrication',      'description' => 'Material fabrication and deposition tools'],
            ['name' => 'Characterization', 'description' => 'Material and sample characterization tools'],
        ];

        foreach ($categories as $cat) {
            DB::table('equipment_categories')->insert($cat);
        }

        [$microCatId, $specCatId, $fabCatId, $charCatId] = DB::table('equipment_categories')->orderBy('id')->pluck('id')->toArray();

        // ----------------------------------------------------------------
        // 4. EQUIPMENT  (base + columns added in second migration)
        // ----------------------------------------------------------------
        $equipment = [
            [
                'name'                  => 'SEM Zeiss Sigma',
                'status'                => 'Idle',
                'hourly_rate'           => 75.00,
                'required_clearance'    => 3,
                'category_id'           => $microCatId,
                'location_code'         => 'RM-101-A',
                'total_usage_hours'     => 320.5,
                'calibration_threshold' => 500.0,
                'cooldown_buffer'       => 30,
            ],
            [
                'name'                  => 'FTIR Spectrometer',
                'status'                => 'Idle',
                'hourly_rate'           => 40.00,
                'required_clearance'    => 2,
                'category_id'           => $specCatId,
                'location_code'         => 'RM-201-B',
                'total_usage_hours'     => 150.0,
                'calibration_threshold' => 300.0,
                'cooldown_buffer'       => 15,
            ],
            [
                'name'                  => 'Sputter Coater',
                'status'                => 'Maintenance',
                'hourly_rate'           => 55.00,
                'required_clearance'    => 1,
                'category_id'           => $fabCatId,
                'location_code'         => 'RM-301-C',
                'total_usage_hours'     => 490.0,
                'calibration_threshold' => 500.0,
                'cooldown_buffer'       => 60,
            ],
            [
                'name'                  => 'XRD Bruker D8',
                'status'                => 'Idle',
                'hourly_rate'           => 60.00,
                'required_clearance'    => 1,
                'category_id'           => $charCatId,
                'location_code'         => 'RM-102-A',
                'total_usage_hours'     => 80.0,
                'calibration_threshold' => 400.0,
                'cooldown_buffer'       => 0,
            ],
            [
                'name'                  => 'micro micro micro',
                'status'                => 'Locked',
                'hourly_rate'           => 60.00,
                'required_clearance'    => 2,
                'category_id'           => $charCatId,
                'location_code'         => 'RM-102-A',
                'total_usage_hours'     => 80.0,
                'calibration_threshold' => 400.0,
                'cooldown_buffer'       => 0,
            ],
            [
                'name'                  => 'Zerbew micro',
                'status'                => 'Active',
                'hourly_rate'           => 160.00,
                'required_clearance'    => 3,
                'category_id'           => $charCatId,
                'location_code'         => 'RM-102-A',
                'total_usage_hours'     => 80.0,
                'calibration_threshold' => 400.0,
                'cooldown_buffer'       => 0,
            ],
        ];

        foreach ($equipment as $eq) {
            DB::table('equipment')->insert(array_merge($eq, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        [$semId, $ftirId, $sputterID, $xrdId] = DB::table('equipment')->orderBy('id')->pluck('id')->toArray();

        // ----------------------------------------------------------------
        // 5. PROFILES
        // ----------------------------------------------------------------
        $pi_profiles = [
            ['user_id' => $bobId, 'budget_limit' => 3250, 'affiliation' => 'Physics Dept'],
            ['user_id' => $PIId,  'budget_limit' => 5390, 'affiliation' => 'Biology Dept'],
        ];

        foreach ($pi_profiles as $pi_profile) {
            DB::table('pi_profiles')->insert($pi_profile);
        }
        $labm_profiles = [
            ['user_id' => $LabmRoleId, 'managed_Lab_Locations' => 'October, Zayed'],
        ];

        foreach ($labm_profiles as $labm_profile) {
            DB::table('labm_profiles')->insert($labm_profile);
        }
        $auditor_profiles = [
            ['user_id' => $auditorId, 'audit_scope' => 'Finance and Logs'],
        ];

        foreach ($auditor_profiles as $auditor_profile) {
            DB::table('auditor_profiles')->insert($auditor_profile);
        }
        $researcher_profiles = [
            ['user_id' => $carolId, 'academicLevel' => 'PhD', 'pis_id' => $PIId],
        ];

        foreach ($researcher_profiles as $researcher_profile) {
            DB::table('researcher_profiles')->insert($researcher_profile);
        }

        // ----------------------------------------------------------------
        // 6. GRANTS
        // ----------------------------------------------------------------
        $grants = [
            ['name' => 'NSF Grant 2024', 'expiry_date' => '2026-10-07',   'pi_id' => $bobId, 'balance' => 25000.00],
            ['name' => 'DOE Seed Fund',  'expiry_date' => '2026-08-09', 'pi_id' => $PIId, 'balance' => 10000.00],
            ['name' => 'Internal Fund',  'expiry_date' => '2026-01-01', 'pi_id' => $PIId,   'balance' => 5000.00],
        ];

        foreach ($grants as $grant) {
            DB::table('grants')->insert(array_merge($grant, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        [$grant1Id, $grant2Id, $grant3Id] = DB::table('grants')->orderBy('id')->pluck('id')->toArray();

        // ----------------------------------------------------------------
        // 7. CERTIFICATIONS  (base + equipment_category_id from 2nd migration)
        // ----------------------------------------------------------------
        $certifications = [
            ['user_id' => $carolId, 'equipment_category_id' => $microCatId, 'expiry_date' => '2026-12-31'],
            ['user_id' => $carolId, 'equipment_category_id' => $specCatId,  'expiry_date' => '2026-09-30'],

        ];

        foreach ($certifications as $cert) {
            DB::table('certifications')->insert(array_merge($cert, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ----------------------------------------------------------------
        // 8. SAFETY LOGS
        // ----------------------------------------------------------------
        $safetyLogs = [
            ['user_id' => $carolId, 'equipment_category_id' => $microCatId, 'acknowledgment_status' => true,  'user_ip' => '192.168.1.10'],
            ['user_id' => $carolId, 'equipment_category_id' => $specCatId,  'acknowledgment_status' => true,  'user_ip' => '192.168.1.10'],
            ['user_id' => $eveId,   'equipment_category_id' => $charCatId,  'acknowledgment_status' => false, 'user_ip' => '192.168.1.20'],
        ];

        foreach ($safetyLogs as $log) {
            DB::table('safety_logs')->insert(array_merge($log, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        [$safetyLog1Id, $safetyLog2Id, $safetyLog3Id] = DB::table('safety_logs')->orderBy('id')->pluck('id')->toArray();

        // ----------------------------------------------------------------
        // 9. RESERVATIONS
        // ----------------------------------------------------------------
        $reservations = [
            [
                'user_id'       => $carolId,
                'equipment_id'  => $semId,
                'safety_log_id' => $safetyLog1Id,
                'grant_id'      => $grant1Id,
                'start_time'    => Carbon::now()->addDays(1)->setTime(9, 0),
                'end_time'      => Carbon::now()->addDays(1)->setTime(11, 0),
                'status'        => 'Pending',
            ],
            [
                'user_id'       => $carolId,
                'equipment_id'  => $ftirId,
                'safety_log_id' => $safetyLog2Id,
                'grant_id'      => $grant2Id,
                'start_time'    => Carbon::now()->addDays(2)->setTime(13, 0),
                'end_time'      => Carbon::now()->addDays(2)->setTime(15, 0),
                'status'        => 'Approved',
            ],
            [
                'user_id'       => $eveId,
                'equipment_id'  => $xrdId,
                'safety_log_id' => null,
                'grant_id'      => null,
                'start_time'    => Carbon::now()->subDays(3)->setTime(10, 0),
                'end_time'      => Carbon::now()->subDays(3)->setTime(12, 0),
                'status'        => 'Cancelled',
            ],
        ];

        foreach ($reservations as $res) {
            DB::table('reservations')->insert(array_merge($res, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ----------------------------------------------------------------
        // 10. EQUIPMENT SESSIONS
        // ----------------------------------------------------------------
        $sessions = [
            [
                'user_id'         => $carolId,
                'equipment_id'    => $semId,
                'start_time'      => Carbon::now()->subDays(5)->setTime(9, 0),
                'end_time'        => Carbon::now()->subDays(5)->setTime(11, 0),
            ],
            [
                'user_id'         => $carolId,
                'equipment_id'    => $ftirId,
                'start_time'      => Carbon::now()->subDays(3)->setTime(14, 0),
                'end_time'        => Carbon::now()->subDays(3)->setTime(16, 0),

            ],
            [
                'user_id'         => $eveId,
                'equipment_id'    => $xrdId,
                'start_time'      => Carbon::now()->subDays(1)->setTime(10, 0),
                'end_time'        => now(),

            ],
        ];

        foreach ($sessions as $session) {
            DB::table('equipment_sessions')->insert(array_merge($session, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        [$session1Id, $session2Id, $session3Id] = DB::table('equipment_sessions')->orderBy('id')->pluck('id')->toArray();

        // ----------------------------------------------------------------
        // 11. TRANSACTIONS
        // ----------------------------------------------------------------
        $transactions = [
            ['session_id' => $session1Id, 'user_id' => $carolId, 'amount' => 150.00, 'normalized_amount' => 142.50],
            ['session_id' => $session2Id, 'user_id' => $carolId, 'amount' => 80.00,  'normalized_amount' => 76.00],
        ];

        foreach ($transactions as $tx) {
            DB::table('transactions')->insert(array_merge($tx, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ----------------------------------------------------------------
        // 12. MAINTENANCE LOGS
        // ----------------------------------------------------------------
        $maintenanceLogs = [
            [
                'equipment_id' => $sputterID,
                'cost'         => 1200.00,
                'description'  => 'Replaced target material and recalibrated deposition rate. Full system diagnostic performed.',
            ],
            [
                'equipment_id' => $semId,
                'cost'         => 350.00,
                'description'  => 'Replaced filament and performed chamber cleaning. Alignment verified.',
            ],
        ];

        foreach ($maintenanceLogs as $log) {
            DB::table('maintenance_logs')->insert(array_merge($log, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ----------------------------------------------------------------
        // 13. PUBLICATION LINKS
        // ----------------------------------------------------------------
        $publications = [
            ['equipment_id' => $semId, 'pi_id' => $bobId, 'doi' => '10.1038/s41586-024-00001-x'],
            ['equipment_id' => $ftirId, 'pi_id' => $PIId, 'doi' => '10.1021/acs.analchem.4c00001'],
            ['equipment_id' => $xrdId, 'pi_id' => $PIId, 'doi' => '10.1107/S1600576724000001'],
        ];

        foreach ($publications as $pub) {
            DB::table('publication_links')->insert(array_merge($pub, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ----------------------------------------------------------------
        // 14. ROI REPORTS
        // ----------------------------------------------------------------
        $roiReports = [
            ['equipment_id' => $semId,    'roi_score' => 4.25, 'recommendation' => 'Keep'],
            ['equipment_id' => $ftirId,   'roi_score' => 3.10, 'recommendation' => 'Keep'],
            ['equipment_id' => $sputterID, 'roi_score' => 1.50, 'recommendation' => 'Review'],
            ['equipment_id' => $xrdId,    'roi_score' => 2.80, 'recommendation' => 'Keep'],
        ];

        foreach ($roiReports as $report) {
            DB::table('roi_reports')->insert(array_merge($report, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ----------------------------------------------------------------
        // 15. UTILIZATION CACHE
        // ----------------------------------------------------------------
        $utilizationCache = [
            ['equipment_id' => $semId,    'usage_percentage' => 64.10],
            ['equipment_id' => $ftirId,   'usage_percentage' => 50.00],
            ['equipment_id' => $sputterID, 'usage_percentage' => 98.00],
            ['equipment_id' => $xrdId,    'usage_percentage' => 20.00],
        ];

        foreach ($utilizationCache as $cache) {
            DB::table('utilization_cache')->insert(array_merge($cache, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ----------------------------------------------------------------
        // 16. AUDIT TRAILS  (base + user_ip from 2nd migration)
        // ----------------------------------------------------------------
        $auditTrails = [
            ['user_id' => $aliceId, 'action' => 'Created equipment: SEM Zeiss Sigma',  'user_ip' => '10.0.0.1'],
            ['user_id' => $aliceId, 'action' => 'Approved session #1 for Carol',        'user_ip' => '10.0.0.1'],
            ['user_id' => $carolId, 'action' => 'Started session on SEM Zeiss Sigma',   'user_ip' => '192.168.1.10'],
            ['user_id' => $danId,   'action' => 'Logged maintenance on Sputter Coater', 'user_ip' => '192.168.1.30'],
        ];

        foreach ($auditTrails as $trail) {
            DB::table('audit_trails')->insert(array_merge($trail, [
                'created_at' => now(),
            ]));
        }

        // ----------------------------------------------------------------
        // 17. INTERLOCK PROXIES
        // ----------------------------------------------------------------
        $interlocks = [
            ['equipment_id' => $semId,    'access_level' => 'researcher'],
            ['equipment_id' => $sputterID, 'access_level' => 'Admin'],
            ['equipment_id' => $ftirId,   'access_level' => 'researcher'],
            ['equipment_id' => $xrdId,    'access_level' => 'researcher'],
        ];

        foreach ($interlocks as $interlock) {
            DB::table('Interlock_proxies')->insert($interlock);
        }

        // ----------------------------------------------------------------
        // 18. CONSUMABLES
        // ----------------------------------------------------------------
        $consumables = [
            ['name' => 'Carbon Tape',          'stock_level' => 200],
            ['name' => 'Sputter Target (Au)',   'stock_level' => 5],
            ['name' => 'KBr Pellet Powder',     'stock_level' => 50],
            ['name' => 'Sample Holders',        'stock_level' => 30],
        ];

        foreach ($consumables as $consumable) {
            DB::table('consumables')->insert($consumable);
        }

        [$tape, $target, $kbr, $holder] = DB::table('consumables')->orderBy('id')->pluck('id')->toArray();

        // ----------------------------------------------------------------
        // 19. EQUIPMENT CONSUMABLES  (pivot)
        // ----------------------------------------------------------------
        $equipmentConsumables = [
            ['equipment_id' => $semId,    'consumable_id' => $tape],
            ['equipment_id' => $semId,    'consumable_id' => $holder],
            ['equipment_id' => $sputterID, 'consumable_id' => $target],
            ['equipment_id' => $ftirId,   'consumable_id' => $kbr],
        ];

        foreach ($equipmentConsumables as $ec) {
            DB::table('equipment_consumables')->insert($ec);
        }
        //===============
        $transactionGrants = [
            ['transaction_id' => 1, 'grant_id' => $grant1Id,   'percentage' => 50, 'amount' => 150],
            ['transaction_id' => 1, 'grant_id' => $grant2Id,   'percentage' => 50, 'amount' => 150],

        ];

        foreach ($transactionGrants as $tg) {
            DB::table('transaction_grants')->insert($tg);
        }
    }
}