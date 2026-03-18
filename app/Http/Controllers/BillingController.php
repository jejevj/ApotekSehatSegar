<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function restricted()
    {
        return view('billing.restricted');
    }
}

