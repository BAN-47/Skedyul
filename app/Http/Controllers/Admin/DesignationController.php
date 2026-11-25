<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DesignationController extends Controller
{
    public function index()
    {
        if (request()->expectsJson()) {
            return response()->json(Designation::orderBy('name')->get());
        }

        $designations = Designation::orderBy('name')->paginate(10)->withQueryString();
        return view('admin.designations', compact('designations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'not_regex:/[<>]/', Rule::unique('designations', 'name')],
            'day_cap' => ['required', 'integer', 'min:0', 'max:100'],
            'evening_cap' => ['required', 'integer', 'min:0', 'max:100'],
            'max_faculty_count' => ['nullable', 'integer', 'min:1'],
        ], [
            'not_regex' => 'Angle brackets (< and >) are not allowed.',
        ]);

        $designation = Designation::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Designation created successfully.',
            'designation' => $designation,
        ]);
    }

    public function update(Request $request, Designation $designation)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'not_regex:/[<>]/', Rule::unique('designations', 'name')->ignore($designation->id)],
            'day_cap' => ['required', 'integer', 'min:0', 'max:100'],
            'evening_cap' => ['required', 'integer', 'min:0', 'max:100'],
            'max_faculty_count' => ['nullable', 'integer', 'min:1'],
        ], [
            'not_regex' => 'Angle brackets (< and >) are not allowed.',
        ]);

        $designation->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Designation updated successfully.',
            'designation' => $designation,
        ]);
    }

    public function destroy(Designation $designation)
    {
        if ($designation->faculty()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete designation because faculty are assigned to it.',
            ], 422);
        }

        $designation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Designation deleted successfully.',
        ]);
    }
}
