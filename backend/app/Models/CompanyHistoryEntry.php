<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyHistoryEntry extends Model
{
    protected $fillable = [
        "company_info_id",
        "year",
        "event",
        "sort_order",
    ];

    public function companyInfo()
    {
        return $this->belongsTo(CompanyInfo::class);
    }
}
