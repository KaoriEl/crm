<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VKAccounts;
class ProfileVKController extends Controller
{
    public function index()
    {
        $accounts = VKAccounts::all();
        return view('profile.vk', compact('accounts'));
    }
}
