<?php

namespace App\Http\Controllers\MainCore;

use App\Http\Controllers\Controller;
use App\Http\Resources\MainCore\SiteInfoResource;
use App\Models\State;
use Illuminate\Http\Request;
use Throwable;

class SiteInfoController extends Controller
{
    public function states(Request $request)
    {
        $states = State::all()->sortBy('name');
        return new SiteInfoResource($states);
    }
    public function cities(Request $request, $state_id)
    {
        try {
            $state = State::findOrFail($state_id);
            $cities = $state->cities()->orderBy('name', 'desc');
            return new SiteInfoResource($cities);
        } catch (Throwable $e) {
            return response()->json([
                "massage" => "There is no state whit this ID",
                "status" => 404,
            ], 404);
        }
    }
}
