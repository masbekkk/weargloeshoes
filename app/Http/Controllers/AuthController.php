<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use illuminate\Validation\Rule;
use App\Mail\SentToEmail;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $request->validate(
            [
                'namareg' => 'required',
                'emailreg' => 'required|unique:users,email',
                'phonereg' => 'required',
                'passwordreg' => 'required|min:6',
                'confirm-pass' => 'required|min:6|same:passwordreg'
            ],
            [
                'namareg.required' => 'Please input your name.',
                'emailreg.required' => 'Please input your email',
                'emailreg.unique' => 'Email has already taken, please input another email',
                'phonereg.required' => 'Please input your phone number',
                'passwordreg.required' => 'Please input your password',
                'passwordreg.min' => 'The password must be at least 10 characters',
                'confirm-pass.required' => 'Please input your confirmation password',
                'confirm-pass.same' => "Confirmation password don't match with password"
            ]
        );

        DB::table('users')->insert([
            'nama' => $request->namareg,
            'email' => $request->emailreg,
            'phone' => $request->phonereg,
            'password' => password_hash($request->passwordreg, PASSWORD_DEFAULT),
        ]);

        $request->session()->flash('register', 'Register Success!');
        Mail::to($request->emailreg)->send(new SentToEmail($request->namareg, DB::getPdo()->lastInsertID()));
        
            // $report = new AuthController();
            // $content = new Request();
            // $content = $request;
            // return $this->login($content);
        
        return redirect('/login-register');
    }

    public function login(Request $request)
    {

        $request->validate(
            [
                'email' => 'required',
                'password' => 'required|min:6',
            ],
            [
                'email.required' => 'Please input your email',
                'password.required' => 'Please input your password',
            ]
        );

        $log = DB::table('users')->where([
            'email', $request->email,
            'status' => 1
        ])->first();

        if($log == null) {
            $request->session()->flash('login', 'Email not verified');
            return redirect('login-register');
        }
        // $user = Auth::id();
        // $currentUser = DB::table('users')->find($user);
        $logUser = $log->nama;

        if ($log) {
            if (Hash::check($request->password, $log->password)) {
                session(['success' => true]);
                session(['user' => $logUser]);
                // $userSession = $request->session->put('user', $log->nama);
                return redirect('/');
                // return ($log->email);
                // return ($logSession);
            }
        }

        $request->session()->flash('login', 'Wrong password or email! Please check your email or password again');
        // return redirect('login');
        return redirect('login-register');
    }

    public function adminLogin(Request $request)
    {

        $request->validate(
            [
                'email' => 'required',
                'password' => 'required|min:10',
            ],
            [
                'email.required' => 'Please input your email',
                'password.required' => 'Please input your password',
            ]
        );

        $log = DB::table('users')->where('id', '5')->first();

        if ($log) {
            if (Hash::check($request->password, $log->password)) {
                session(['admin' => true]);
                return redirect('/admin');
            }
        }

        $request->session()->flash('login', 'Wrong password or email! Please check your email or password again');
        return redirect('admin-login');
    }


    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/');
    }

    public function delete($id)
    {
        DB::table('users')->where('id', $id)->delete();
        return redirect('/admin/users');
    }
}
