<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ModifyDatesInFormat;

class RolePermission extends Model
{
    use HasFactory;

    protected $table = "tbl_role_permissions";
    use ModifyDatesInFormat;

    protected $fillable = [
        'role_id',
        'permission_id', // add other fields
    ];

    // Accessor for getting permissions as an array
    public static function getUserPermissions($role_id)
    {
        $permissions = self::where("role_id", $role_id)->get();

        // Transform permission_id into an array
        $permissionsArray = $permissions->map(function ($item) {
            return explode(',', $item->permission_id);
        });

        // Flatten the array in case of multiple role permissions
        return $permissionsArray->flatten()->unique()->toArray();
    }
}
