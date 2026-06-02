<?php

namespace App\Services;

use App\Models\RoiReport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}


    public function storeOrUpdateUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            $role = Role::where('name', $data['user_role'])->firstOrFail();

            $userValues = [
                'name'       => $data['user_name'],
                'role_id'    => $role->id,
                'is_active'     => true,
                'expiry_date'   => $data['expiry_date'],
                'systemPrivileges' => $data['system_priviliges']
            ];
            if (!empty($data['user_pass'])) {
                $userValues['password'] = Hash::make($data['user_pass']);
            }

            $user = User::updateOrCreate(['email' => $data['user_email']], $userValues);
            $this->updateProfile($user, $role->name, $data);
            return $user;
        });
    }

    protected function updateProfile(User $user, string $roleName, array $data)
    {
        switch ($roleName) {
            case 'PI':
                $user->piProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'budget_limit' => $data['budget_limit'],
                        'affiliation'  => $data['affiliation']
                    ]
                );
                break;

            case 'Auditor':
                $user->auditorProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    ['audit_scope' => $data['audit_scope']]
                );
                break;

            case 'Lab_Manager':
                $user->labmProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    ['managed_Lab_Locations' => $data['lab_locations']]
                );
                break;
        }
    }
    public function deleteUser($id)
    {
        return  User::where('id', $id)->firstOrFail()->delete();
    }

}