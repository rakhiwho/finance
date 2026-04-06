<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{  

// register {name , email , password}
 
     public function register (Request $request){
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
        ]);
           
$role = Role::where('name', 'viewer')->firstOrFail();
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' =>  $role->id,  
            'is_active' => true,
        ]);

        return response()->json([
            "status"=> true,
            'message' => 'User registered successfully',
            'user' => $user,
        ]);
    }

    //  login {email , password}
    public function login (Request $req){
       $data = $req->validate([
        'email' => "required|email",
        "password" => "required"
       ]);

 
       if (! Auth::attempt([
        'email' => $req->email,
        'password' => $req->password,
      ])) {
        return response()->json([
            'status' => true,
            'message' => 'Invalid credentials',
        ]);
    }

    $user = Auth::user();
    $token = $user->createToken('myToken')->plainTextToken;

     return response()->json([
        "status" =>true,
        "message" => "user logged in",
        "token" => $token
     ]);
    }

    // profile
    public function profile (){
     

        $user =  Auth::user();
        return response()->json([
         "status" =>true,
        "message" => "user logged in",
        "user" => $user
     ]);

    }
    // logout 
    public function logout (){
   Auth::logout();
        return response()->json([   
        "status" =>true,
        "message" => "user logged out",
     ]);

    }

    //    get alll users only by admin with filter by status and search by name or email

    public function allUsers(Request $request)
{
    $authUser = $request->user();

    //  Access Control
   if (!$authUser->role || $authUser->role->name !== 'admin') {
        return response()->json([
            'message' => 'Forbidden: Only admins can view users'
        ], 403);
    }
    $query = User::query();
    //  Filter by status (active/inactive)
    if ($request->has('status')) {
        $query->where('status', $request->status);
    }

    //Search by name/email
    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%");
        });
    }
    //  Pagination  
    $users = $query->paginate(10);
    
    return response()->json([
        'message' => 'Users fetched successfully',
        'data' => $users
    ]);
}


// updates user by id {name , email }
public function update(Request $request, $id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

    // Validation
    $validated = $request->validate([
        'name'  => 'sometimes|string|max:255',
        'email' => "sometimes|email|unique:users,email,$id",
       
    ]);

    // Update name & email
    if (isset($validated['name'])) {
        $user->name = $validated['name'];
    }

    if (isset($validated['email'])) {
        $user->email = $validated['email'];
    }
 
     
    $user->save();

    return response()->json([
        'message' => 'User updated successfully',
        'data' => $user
    ]);
}


//   updates user status by id {status} only by admin
public function updateStatus(Request $request, $id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

    // Validate status
    $validated = $request->validate([
        'status' => 'required|in:active,inactive',
    ]);

    $user->is_active = $validated['status'];
    $user->save();

    return response()->json([
        'message' => 'User status updated successfully',
        'data' => $user
    ]);
}
 
}
