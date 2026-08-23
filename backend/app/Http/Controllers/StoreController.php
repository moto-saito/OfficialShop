<?php

namespace App\Http\Controllers;

use App\Models\StoreInfo;

class StoreController extends Controller
{
    public function show()
    {
        $store = StoreInfo::current();

        return view("store.show", compact("store"));
    }
}
