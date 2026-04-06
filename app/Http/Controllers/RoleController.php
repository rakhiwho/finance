<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
public function updateRole(Request $request, $id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

    // ✅ Validate role
    $validated = $request->validate([
        'role' => 'required|exists:roles,id',
    ]);

   

    $user->role_id = $validated['role'];
    $user->save();

    return response()->json([
        'message' => 'Role updated successfully',
        'data' => $user
    ]);
}
}
