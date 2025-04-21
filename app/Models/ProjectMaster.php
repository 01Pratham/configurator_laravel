<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMaster extends Model
{
    use HasFactory;

    use \App\Services\ModifyDatesInFormat;

    protected $table = 'tbl_project_master';
    protected $fillable = [
        'project_name',
        'project_pot_id',
        'is_active'
    ];

    public function quotations()
    {
        return $this->hasMany(ProjectQuotationMaster::class, "project_id")->where('is_deleted', 0);
    }
}
