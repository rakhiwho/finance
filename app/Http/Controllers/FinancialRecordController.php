<?php

namespace App\Http\Controllers;

use App\Models\FinancialRecord;
use Illuminate\Http\Request;

class FinancialRecordController extends Controller
{
    /**
     * Store a new financial record
     */
    public function store(Request $request)
    {
        //  Allow only admin (role_id = 3)
        if ($request->user()->role_id !== 3) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Validate incoming request
        $validated = $request->validate([
            'amount'   => 'required|numeric',
            'type'     => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'date'     => 'required|date',
            'notes'    => 'nullable|string'
        ]);

        //  Attach logged-in user ID
        $validated['user_id'] = $request->user()->id;

        //  Create record
        $record = FinancialRecord::create($validated);

        return response()->json([
            'message' => 'Record created successfully',
            'data'    => $record
        ], 201);
    }

    /**
     * Update an existing financial record
     */
    public function update(Request $request, $id)
    {
        //  Only admin allowed
        if ($request->user()->role_id !== 3) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        //  Find record
        $record = FinancialRecord::find($id);

        if (!$record) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        //  Validate only provided fields
        $validated = $request->validate([
            'amount'   => 'sometimes|numeric',
            'type'     => 'sometimes|in:income,expense',
            'category' => 'sometimes|string|max:255',
            'date'     => 'sometimes|date',
            'notes'    => 'nullable|string'
        ]);

        //  Update record
        $record->update($validated);

        return response()->json([
            'message' => 'Record updated successfully',
            'data'    => $record
        ]);
    }

    /**
     * Get a single financial record
     */
    public function show($id)
    {
        $record = FinancialRecord::find($id);

        if (!$record) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json([
            'data' => $record
        ]);
    }

    /**
     * Get filtered financial records
     */
    public function getFilteredData(Request $request)
    {
        $query = FinancialRecord::query();

        //  Filter by type (income / expense)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        //  Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        //  Filter by start date
        if ($request->filled('startDate')) {
            $query->whereDate('date', '>=', $request->startDate);
        }

        //  Filter by end date
        if ($request->filled('endDate')) {
            $query->whereDate('date', '<=', $request->endDate);
        }

        //  Role-based data restriction
        $user = $request->user();

        if ($user->role === 'viewer') {
            //  Viewer gets limited fields only
            $query->select('id', 'amount', 'type', 'date');
        }

        //  Paginate results
        $records = $query->latest()->paginate(10);

        return response()->json([
            'message' => 'Records fetched successfully',
            'data'    => $records
        ]);
    }

    /**
     * Delete a financial record
     */
    public function destroy(Request $request, $id)
    {
        //  Only admin allowed
        if ($request->user()->role_id !== 3) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        //  Find record
        $record = FinancialRecord::find($id);

        if (!$record) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        //  Delete record
        $record->delete();

        return response()->json([
            'message' => 'Record deleted successfully'
        ]);
    }
}