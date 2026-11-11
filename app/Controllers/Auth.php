<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(session()->get('role') === 'admin' ? '/admin' : '/');
        }
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        if ($user['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Akun Anda tidak aktif. Hubungi admin.');
        }

        session()->set([
            'logged_in' => true,
            'user_id'   => $user['id'],
            'name'      => $user['name'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'photo'     => $user['photo'],
        ]);

        if ($user['role'] === 'admin') {
            return redirect()->to('/admin')->with('success', 'Selamat datang kembali, ' . $user['name']);
        }

        return redirect()->to('/')->with('success', 'Login berhasil. Selamat datang, ' . $user['name']);
    }

    public function register()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/');
        }
        return view('auth/register');
    }

    public function attemptRegister()
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[150]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'phone'    => 'required|min_length[9]|max_length[20]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
        ];

        $messages = [
            'confirm_password' => [
                'matches' => 'Konfirmasi password tidak sama dengan password.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'phone'    => $this->request->getPost('phone'),
            'address'  => $this->request->getPost('address'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => 'pegawai',
            'status'   => 'active',
        ];

        $this->userModel->insert($data);

        return redirect()->to('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah logout.');
    }
}
