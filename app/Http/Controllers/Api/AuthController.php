<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate(['name' => ['required','string','max:100'], 'email' => ['required','email','max:100','unique:users,email'], 'phone' => ['nullable','string','max:20'], 'password' => ['required','string','min:6','confirmed']]);
        $user = User::create(['name' => $data['name'], 'email' => strtolower($data['email']), 'phone' => $data['phone'] ?? null, 'password' => Hash::make($data['password']), 'role' => 'customer']);
        $token = auth('api')->login($user);
        return response()->json(['message' => 'Registrasi berhasil.', 'user' => new UserResource($user), 'access_token' => $token, 'token_type' => 'Bearer', 'expires_in' => auth('api')->factory()->getTTL() * 60], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required','email'], 'password' => ['required','string']]);
        $credentials['email'] = strtolower($credentials['email']);
        if (!$token = auth('api')->attempt($credentials)) return response()->json(['message' => 'Email atau password salah.'], 401);
        return response()->json(['message' => 'Login berhasil.', 'user' => new UserResource(auth('api')->user()), 'access_token' => $token, 'token_type' => 'Bearer', 'expires_in' => auth('api')->factory()->getTTL() * 60]);
    }

    public function me(Request $request) { return response()->json(['user' => new UserResource($request->user())]); }
    public function logout() { auth('api')->logout(); return response()->json(['message' => 'Token berhasil dikeluarkan.']); }
}
