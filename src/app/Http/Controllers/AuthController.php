<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        logger()->info('Banco ativo no register:', ['database' => DB::connection()->getDatabaseName()]);

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:tenant.users,email',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if ( !$user || !Hash::check($request->password, $user->password) ) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $token  = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    public function user(Request $request)
    {
        $token      = $request->bearerToken();
        $user       = auth('sanctum')->user();
        $database   = DB::connection()->getDatabaseName();

        return response()->json([
            'user'      => $user,
            'database'  => $database,
        ]);
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json(['message' => "Logout efetuado com sucesso"]);
    }
}
