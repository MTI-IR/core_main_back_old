<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminPanel\BaseResource;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use Throwable;

class CityController extends Controller
{
    public function index(Request $request)
    {
        try {

            $row_number = 10;
            if ($request->get('row_number')) $row_number = $request->get('row_number');

            $page = 1;
            if ($request->get('page')) $page = $request->get('page');

            $order_by = 'id';
            if ($request->get('order_by')) $order_by = $request->get('order_by');

            $cities = City::orderBy($order_by)->paginate(
                $row_number,
                [
                    'id',
                    'name',
                    'phone_code',
                    'state_id',
                ],
                'page',
                $page
            );
            foreach ($cities as $city) {
                $city["state"] = $city->state()->get([
                    'id',
                    'name',
                ]);
            }
            return new BaseResource($cities);
        } catch (Throwable $e) {
            return response($e, 500);
            return response()->json([
                "message" => "something went wrong.",
                "status" => "500"
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $city = City::findOrFail($id);
            $city['state'] = $city->state;
            $city['projects'] = $city->projects;
            return new BaseResource($city);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "No city with this id",
                "status" => "404"
            ], 404);
        }
    }
    public function create(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string|unique:cities",
            "state_id" => "required",
            "phone_code" => "digits:3"
        ]);
        try {
            $state = State::findOrFail($data['state_id']);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "State not found!!!.",
                "status" => "404",
            ], 404);
        }
        try {
            $city = City::make();
            $city->name = $data['name'];
            $city->state_id = $data['state_id'];
            $city->save();
            return response()->json([
                "message" => "City created.",
                "status" => "200"
            ], 200);
        } catch (Throwable $e) {
            return response($e, 500);
            return response()->json([
                "message" => "State not found.",
                "status" => "404"
            ], 404);
        }
    }
    public function destroy(Request $request)
    {
        try {
            $cities = $request->get('cities');
            City::destroy($cities);
            return response()->json([
                "message" => "cities destroyed.",
                "status" => "200"
            ], 200);
        } catch (Throwable $e) {
            return response($e, 500);
            return response()->json([
                "message" => "something went wrong.",
                "status" => "500"
            ], 500);
        }
    }
}
