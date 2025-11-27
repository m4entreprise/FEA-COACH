<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LegalController extends Controller
{
    /**
     * Display the CGVU page.
     */
    public function cgvu()
    {
        return Inertia::render('Legal/Cgvu');
    }

    /**
     * Display the Privacy Policy page.
     */
    public function privacy()
    {
        return Inertia::render('Legal/Privacy');
    }
}
