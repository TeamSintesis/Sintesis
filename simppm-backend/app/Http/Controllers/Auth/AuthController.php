<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Controller autentikasi berbasis token (Laravel Sanctum) untuk SIMPPM.
 *
 * Frontend Svelte yang uncoupled berkomunikasi dengan backend sepenuhnya
 * melalui token bearer (stateless), sejalan dengan prinsip 12-factor app
 * "Processes" (VI) — proses backend tidak menyimpan sesi di memori/disk.
 */
class AuthController extends Controller
{
    /**
     * Autentikasi pengguna dan kembalikan token akses.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Auth::getProvider()->validateCredentials($user, $credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan tidak sesuai.'],
            ]);
        }

        // Hapus token lama agar tidak menumpuk pada percobaan login berulang.
        $user->tokens()->delete();

        $token = $user->createToken('simppm-frontend');

        return response()->json([
            'data' => [
                'token' => $token->plainTextToken,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'peran' => $user->peran,
                ],
            ],
        ]);
    }

    /**
     * Cabut token yang sedang digunakan (logout).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['data' => ['message' => 'Berhasil keluar.']]);
    }

    /**
     * Kembalikan data pengguna yang sedang terautentikasi.
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'peran' => $user->peran,
            ],
        ]);
    }
}
