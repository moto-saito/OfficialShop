<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;

class CompanyController extends Controller
{
    public function show()
    {
        $company = CompanyInfo::current();
        $company->load(["historyEntries", "achievements"]);

        return view("company.show", compact("company"));
    }
}
