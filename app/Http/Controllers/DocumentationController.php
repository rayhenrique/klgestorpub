<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function manual()
    {
        return view('documentation.manual');
    }

    public function privacy()
    {
        return view('documentation.privacy');
    }

    public function terms()
    {
        return view('documentation.terms');
    }

    public function lgpd()
    {
        return view('documentation.lgpd');
    }
}
