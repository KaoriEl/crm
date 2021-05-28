<?php

namespace App\Http\Controllers;

use App\Models\OKAccounts;
use Illuminate\Http\Request;

class ProfileOKController extends Controller
{
    public function index()
    {
        $accounts = OKAccounts::all();
        return view('profile.ok', compact('accounts'));
    }
}
