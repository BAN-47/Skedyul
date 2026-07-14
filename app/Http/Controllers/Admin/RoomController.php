<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::orderBy('room_name')->get();
        return view('admin.rooms', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     * (Not needed — you're using a modal, not a separate page)
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_name'         => 'required|string|max:100',
            'room_type'         => 'required|string|max:100',
            'room_capacity'     => 'required|integer|min:1',
            'room_is_available' => 'boolean',
            'room_building'     => 'nullable|string|max:150',
            'room_location'     => 'nullable|string|max:150',
        ]);

        $validated['room_is_available'] = $validated['room_is_available'] ?? true;

        try {
            $room = Room::create($validated);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23505') {
                return response()->json([
                    'success' => false,
                    'message' => 'A room named "' . $validated['room_name'] . '" already exists. Please choose a different name.',
                ], 409);
            }
            throw $e;
        }

        return response()->json([
            'success' => true,
            'message' => 'Room "' . $room->room_name . '" added successfully!',
            'room'    => $room,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $room = Room::findOrFail($id);
        return response()->json($room);
    }

    /**
     * Show the form for editing the specified resource.
     * (Not needed — you're using a modal, not a separate page)
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
        $room = Room::findOrFail($id);

        $validated = $request->validate([
            'room_name'         => 'required|string|max:100',
            'room_type'         => 'required|string|max:100',
            'room_capacity'     => 'required|integer|min:1',
            'room_is_available' => 'boolean',
            'room_building'     => 'nullable|string|max:150',
            'room_location'     => 'nullable|string|max:150',
        ]);

        try {
            $room->update($validated);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23505') {
                return response()->json([
                    'success' => false,
                    'message' => 'A room named "' . $validated['room_name'] . '" already exists. Please choose a different name.',
                ], 409);
            }
            throw $e;
        }

        return response()->json([
            'success' => true,
            'message' => 'Room "' . $room->room_name . '" updated successfully!',
            'room'    => $room,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $room = Room::findOrFail($id);
        $name = $room->room_name;

        try {
            $room->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete "' . $name . '" — it has existing class schedules assigned to it. Remove those schedules first.',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Room "' . $name . '" deleted successfully.',
        ]);
    }
}