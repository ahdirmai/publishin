<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller {
    public function __invoke(Request $request): Response {
        return Inertia::render('Dashboard/Index', [
            // TODO Phase 3: inject real analytics data
            'kpi' => [
                'total_followers'  => 0,
                'scheduled_posts'  => 0,
                'engagement_rate'  => 0,
                'reach_30d'        => 0,
            ],
        ]);
    }
}
