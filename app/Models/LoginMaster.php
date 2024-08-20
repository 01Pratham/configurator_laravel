<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Auth;
use Laravel\Sanctum\HasApiTokens;

class LoginMaster extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = "tbl_login_master";

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
        'first_name',
        'last_name'
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

    public static function getUser(int $user_id = 0): array
    {
        $user = ($user_id != 0) ? self::where("id", $user_id) : Auth::user();

        $array = [
            "id" => $user->id,
            "crm_user_id" => $user->crm_user_id,
            "permissions" => RolePermission::getUserPermissions($user->user_role),
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "manager_code" => $user->manager_code,
            "estimate_count" => SavedEstimate::where("emp_code", $user->crm_user_id)->count(),
            "applicable_discounting_percentage" => $user->applicable_discounting_percentage,
        ];

        return $array;
    }

    public function getNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }
}
