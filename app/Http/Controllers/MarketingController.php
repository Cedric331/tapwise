<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class MarketingController extends Controller
{
    /**
     * Display the landing page.
     */
    public function index(): Response
    {
        return Inertia::render('Marketing/Index');
    }
}
