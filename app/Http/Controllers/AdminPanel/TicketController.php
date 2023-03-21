<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminPanel\BaseResource;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Throwable;

class TicketController extends Controller
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

            $tickets = Ticket::orderBy($order_by)->paginate(
                $row_number,
                [
                    '*'
                ],
                'page',
                $page
            );
            return new BaseResource($tickets);
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

            $ticket = Ticket::findOrFail($id);
            $ticket['project'] = $ticket->project;
            $ticket['user'] = $ticket->user;
            $ticket['time'] = $ticket->time;
            return new BaseResource($ticket);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "There is no ticket width this id",
                "status" => "404"
            ], 404);
        }
    }
    public function check(Request $request)
    {
        $data = $request->validate([
            "id" => "required",
            "admin_explanation" => "required|text",
        ]);
        try {
            $ticket = Ticket::findOrFail($data['id']);
            $ticket->check  = true;
            $ticket->admin_explanation = $data['admin_explanation'];
            return new BaseResource($ticket);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "There is no ticket width this id",
                "status" => "404"
            ], 404);
        }
    }
    public function destroy(Request $request)
    {
        try {
            $tickets = $request->get('tickets');
            Ticket::destroy($tickets);
            return response()->json([
                "message" => "tickets destroyed.",
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
