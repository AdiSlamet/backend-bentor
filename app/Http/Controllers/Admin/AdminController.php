<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // GET /api/admin/admins
    public function index()
    {
        $admins = Admin::all();
        return response()->json([
            'success' => true,
            'data' => $admins
        ]);
    }

    // POST /api/admin/admins (Register Admin)
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:mysql_admin.ADMIN,username',
            'password' => 'required|min:8',
            'nama_lengkap' => 'required',
            'email' => 'required|email|unique:mysql_admin.ADMIN,email',
            'no_telepon' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Simpan admin baru
        $admin = Admin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon
        ]);

        return response()->json([
            'success' => true,
            'data' => $admin
        ], 201);
    }

    // GET /api/admin/admins/{id}
    public function show($id)
    {
        $admin = Admin::find($id);
        
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $admin
        ]);
    }

    // PUT /api/admin/admins/{id}
    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);
        
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak ditemukan'
            ], 404);
        }

        // Validasi update
        // $validator = Validator::make($request->all(), [
        //     'username' => 'sometimes|unique:mysql_admin.ADMIN,username,'.$id.',admin_id',
        //     'email' => 'sometimes|email|unique:mysql_admin.ADMIN,email,'.$id.',admin_id',
        //     'password' => 'sometimes|min:8'
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'errors' => $validator->errors()
        //     ], 422);
        // }

        // Update data
        $admin->update([
            'username' => $request->username ?? $admin->username,
            'password' => $request->password ? Hash::make($request->password) : $admin->password,
            'nama_lengkap' => $request->nama_lengkap ?? $admin->nama_lengkap,
            'email' => $request->email ?? $admin->email,
            'no_telepon' => $request->no_telepon ?? $admin->no_telepon
        ]);

        return response()->json([
            'success' => true,
            'data' => $admin
        ]);
    }

    // DELETE /api/admin/admins/{id}
    public function destroy($id)
    {
        $admin = Admin::find($id);
        
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak ditemukan'
            ], 404);
        }

        $admin->delete();
        return response()->json([
            'success' => true,
            'message' => 'Admin berhasil dihapus'
        ], 204);
    }
}