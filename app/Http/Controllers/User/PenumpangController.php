<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\Penumpang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PenumpangController extends Controller
{
    // GET /api/user/penumpang
    public function index()
    {
        $penumpang = Penumpang::all();
        return response()->json([
            'success' => true,
            'data' => $penumpang
        ]);
    }

    // POST /api/user/penumpang (Register)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required',
            'email' => 'required|email|unique:mysql_user.PENUMPANG,email',
            'no_telepon' => 'required',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $penumpang = Penumpang::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon,
            'password' => Hash::make($request->password),
            'verified' => false
        ]);

        return response()->json([
            'success' => true,
            'data' => $penumpang
        ], 201);
    }

    // GET /api/user/penumpang/{id}
    public function show($id)
    {
        $penumpang = Penumpang::find($id);
        
        if (!$penumpang) {
            return response()->json([
                'success' => false,
                'message' => 'Penumpang tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $penumpang
        ]);
    }

    // PUT /api/user/penumpang/{id}
    public function update(Request $request, $id)
    {
        $penumpang = Penumpang::find($id);
        
        if (!$penumpang) {
            return response()->json([
                'success' => false,
                'message' => 'Penumpang tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|email|unique:mysql_user.PENUMPANG,email,'.$id.',user_id',
            'no_telepon' => 'sometimes',
            'password' => 'sometimes|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['nama_lengkap', 'email', 'no_telepon']);
        
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $penumpang->update($data);

        return response()->json([
            'success' => true,
            'data' => $penumpang
        ]);
    }
}