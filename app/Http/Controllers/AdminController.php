<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function stats()
    {

        $productDistribution = Zone::all()->mapWithKeys(function ($zone) {
            $count = Produit::where('description', 'like', '%' . $zone->nom . '%')->count();
            return [$zone->nom => $count];
        });

        $newProducts = Produit::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'productDistribution' => $productDistribution,
            'newProducts' => $newProducts,
        ]);
    }
}
