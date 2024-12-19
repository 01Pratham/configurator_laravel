<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Auth;
use Laravel\Sanctum\HasApiTokens;
use App\Services\ModifyDatesInFormat;

class LoginMaster extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = "tbl_login_master";

    use ModifyDatesInFormat;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_code',
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'department',
        'designation',
        'manager_code',
        'user_role',
        'crm_user_id',
        'applicable_discounting_percentage',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'username_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Optionally, append the name attribute when converting to array or JSON
    protected $appends = ['name'];

    /**
     * Customize the array output to replace first_name and last_name with name.
     *
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();
        $name = $this->first_name . ' ' . $this->last_name;
        $keys = array_keys($array);
        $firstNamePosition = array_search('first_name', $keys);
        $lastNamePosition = array_search('last_name', $keys);
        unset($array['first_name']);
        unset($array['last_name']);
        if ($firstNamePosition !== false) {
            $array = array_slice($array, 0, $firstNamePosition, true) +
                ['name' => $name] +
                array_slice($array, $firstNamePosition, null, true);
        }

        return $array;
    }

    /**
     * Get the name attribute.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public static function getUser(int $user_id = 0): array
    {
        $user = ($user_id != 0) ? self::where("id", $user_id)->first() : Auth::user();

        $array = [
            "id" => $user->id,
            "crm_user_id" => $user->crm_user_id,
            "username" => $user->username,
            "permissions" => RolePermission::getUserPermissions($user->user_role),
            "name" => $user->name,
            "manager_code" => $user->manager_code,
            "estimate_count" => ProjectQuotationMaster::where("owner", $user->crm_user_id)->count(),
            "applicable_discounting_percentage" => $user->applicable_discounting_percentage,
        ];

        return $array;
    }
}
