<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketStoreRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ticket::query();

        $user = $request->user()->id;
        try {

            $query->orderBy('created_at', 'desc');

            if (auth()->user()->role == 'user') {
                $query->where('user_id', '=', $user);
            }

            if ($request->search) {
                $query->where('code', 'like', '%' . $request->search . '%')
                    ->orWhere('title', 'like', '%' . $request->search . '%');
            }

            if ($request->priority) {
                $query->where('priority', 'like', '%' . $request->search . '%');
            }

            if ($request->status) {
                $query->where('status', 'like', '%' . $request->search . '%');
            }

            $ticket = $query->get();

            return response()->json([
                'message' => "Success get ticket",
                'data' => TicketResource::collection($ticket)
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed get ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketStoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $data['code'] = 'TIC-' . rand(10000, 99999);

        DB::beginTransaction();
        try {

            $ticket = Ticket::query()->create($data);

            DB::commit();

            return response()->json([
                'message' => "Success create ticket",
                'data' => new TicketResource($ticket)
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed create ticket',
                'error' => $e->getMessage()
            ] . 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $ticket = Ticket::query()->where('code', '=', $id)->firstOrFail();

            if (!$ticket) {
                $error = "Ticket not found";
                throw new Exception($error, 404);
            }

            if (auth()->user()->role == 'user' && $ticket->user_id != auth()->user()->id) {
                $error = "You are not allowed to access this ticket.";
                throw new Exception($error, 403);
            }


            return response()->json([
                'message' => "Success get ticket" . $id,
                'data' => new TicketResource($ticket)
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => "Failed get ticket" . $id,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
