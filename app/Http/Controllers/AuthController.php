<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password; 

class AuthController extends Controller
{
    // 1. Menampilkan Halaman Register
    public function tampilkanRegister()
    {
        return view('auth.register'); 
    }

    // 2. Proses Pendaftaran User Baru
    public function prosesRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed', 
        ]);

        // Simpan langsung ke tabel users MySQL
        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat! Silakan login.');
    }

    // 3. Menampilkan Halaman Login
    public function tampilkanLogin()
    {
        return view('auth.login'); 
    }

    // 4. Proses Masuk / Autentikasi
    public function prosesLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Jika sukses login, lempar ke halaman rekomendasi resep
            return redirect()->intended('/rekomendasi');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang kamu masukkan salah.',
        ])->onlyInput('email');
    }

    
   // 5. Proses Keluar / Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Kamu berhasil logout.');
    } // Menutup fungsi logout dengan benar

    // 6. Proses Kirim Link Lupa Password
    public function prosesLupaPassword(Request $request)
    {
        // Validasi input email wajib diisi dan harus berformat email
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email ini tidak terdaftar di sistem kami.'
        ]);

        // Mengirimkan link token reset password melalui broker bawaan Laravel
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Jika berhasil dikirim, kembalikan ke halaman sebelumnya dengan pesan sukses
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Link pemulihan password telah dikirim ke email Anda! 🚀');
        }

        // Jika gagal karena kendala sistem/email server
        return back()->withErrors(['email' => __($status)]);
    }

    // 7. Menampilkan Form Input Password Baru
    public function tampilkanResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token, 
            'email' => $request->email
        ]);
    }

    // 8. Memproses Perubahan Password Baru di Database
    public function prosesResetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'password'   => Hash::make($password),
                        'updated_at' => now()
                    ]);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Password Anda berhasil diperbarui! Silakan login.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
} 
