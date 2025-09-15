<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $req)
    {
        $data = $req->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password'  => ['required', 'string', 'min:6', 'max:100', 'confirmed'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'address'   => ['nullable', 'string', 'max:500'],
        ], [], [
            'full_name' => 'Họ tên',
            'email'     => 'Email',
            'password'  => 'Mật khẩu',
        ]);

        // Mặc định gán role "customer"
        $customerRoleId = Role::where('role_name', 'customer')->value('role_id') ?? 3;
        // DB của bạn có "customer" id=3 :contentReference[oaicite:2]{index=2}

        $user = User::create([
            'full_name' => $data['full_name'],
            'email'     => $data['email'],
            'phone'     => $req->input('phone'),
            'address'   => $req->input('address'),
            'password'  => Hash::make($data['password']),
            'role_id'   => $customerRoleId,
        ]);

        Auth::login($user);
        return redirect()->route('dashboard')->with('ok', 'Đăng ký thành công!');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($data, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // admin/employee → dashboard, còn lại → store.index
            if ($user->hasRole(['admin', 'employee'])) {
                return redirect()->intended(route('dashboard'));
            }
            return redirect()->intended(route('store.index'));
        }

        return back()->withErrors(['email' => 'Thông tin đăng nhập không đúng.'])->onlyInput('email');
    }
    public function logout(Request $req)
    {
        Auth::logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();
        return redirect()->route('login');
    }
}
