<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminPanel\BaseResource;
use App\Models\State;
use Illuminate\Http\Request;
use Throwable;

class StateController extends Controller
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

            $states = State::orderBy($order_by)->paginate(
                $row_number,
                [
                    '*'
                ],
                'page',
                $page
            );
            return new BaseResource($states);
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
            $state = State::findOrFail($id);
            $state['cities'] = $state->cities;
            $state['projects'] = $state->projects;
            return new BaseResource($state);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "No state with this id",
                "status" => "404"
            ], 404);
        }
    }
    public function create(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string"
        ]);
        try {
            $state = State::make();
            $state->name = $data['name'];
            $state->save();
            return response()->json([
                "message" => "State created.",
                "status" => "200"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "This state is already exist",
                "status" => "405"
            ], 405);
            return response($e, 500);
        }
    }
    public function destroy(Request $request)
    {
        try {
            $states = $request->get('states');
            State::destroy($states);
            return response()->json([
                "message" => "States destroyed.",
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
