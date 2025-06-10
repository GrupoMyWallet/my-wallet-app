<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'ano' => 'nullable|integer|min:2000|max:2099',
            'mes' => 'nullable|integer|min:1|max:12',
            'categoria_id' => 'nullable|integer|exists:categorias,id',
        ]);

        $dashboardData = $this->dashboardService->getDashboardData(
            auth()->id(),
            $filters
        );

        return Inertia::render('Dashboard', [
            'dashboard' => $dashboardData,
            'filters' => array_merge([
                'ano' => now()->year,
                'mes' => null,
                'categoria_id' => null,
            ], $filters),
        ]);
    }

    public function getData(Request $request)
    {
        $filters = $request->validate([
            'ano' => 'nullable|integer|min:2000|max:2099',
            'mes' => 'nullable|integer|min:1|max:12',
            'categoria_id' => 'nullable|integer|exists:categorias,id',
        ]);

        return response()->json(
            $this->dashboardService->getDashboardData(auth()->id(), $filters)
        );
    }
}