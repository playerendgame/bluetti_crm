<?php

namespace App\Http\Controllers\Ajax\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function createAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8'
        ]);

        if($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Failed',
                'errors' =>$validator->errors()
            ], 422);
        }

        try{
            $admin = Admin::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $admin->createToken('Admin API Token');

            $this->logActivity('admin', $admin);

            return response()->json([
                'success' => true,
                'message' => 'Admin created successfully',
                'admin' => [
                    'id' => $admin->id,
                    'first_name' => $admin->first_name,
                    'last_name' => $admin->last_name,
                    'email' => $admin->email,
                    'created_at' => $admin->created_at,
                    'updated_at' => $admin->updated_at
                ]
            ], 201);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating Admin: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAdmin($id)
    {
        try{
            $admin = Admin::find($id);

            if(!$admin){
                return response()->json([
                    'success' => false,
                    'message' => 'Admin not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'admin' => [
                    'id' => $admin->id,
                    'first_name' => $admin->first_name,
                    'last_name' => $admin->last_name,
                    'email' => $admin->email,
                    'created_at' => $admin->created_at,
                    'updated_at' => $admin->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetch admin: ' .$e->getmessage()
            ], 500);
        }
    }
}
