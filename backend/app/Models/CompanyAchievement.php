<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyAchievement extends Model
{
    protected $fillable = [
        "company_info_id",
        "title",
        "description",
        "sort_order",
    ];

    public function companyInfo()
    {
        return $this->belongsTo(CompanyInfo::class);
    }
}
