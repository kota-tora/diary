<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class DiaryController extends Controller
{
    public function index()
    {
        return view('lists.index');
    }

    public function create()
    {
        return view('create.index');
    }

}
