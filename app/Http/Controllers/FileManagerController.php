<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class FileManagerController extends Controller
{
    public function index()
    {
        return view('file-manager.index');
    }
}
