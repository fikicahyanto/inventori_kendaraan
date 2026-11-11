<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $user = $this->userModel->find(session()->get('user_id'));
        return view('profile/index', ['title' => 'Profil Saya', 'user' => $user]);
    }

    public function update()
    {
        $userId = session()->get('user_id');

        $rules = [
            'name'  => 'required|min_length[3]|max_length[150]',
            'phone' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'       => $this->request->getPost('name'),
            'phone'      => $this->request->getPost('phone'),
            'address'    => $this->request->getPost('address'),
        ];

        // Jika nomor KTP diubah, status verifikasi direset karena perlu dicek ulang oleh admin
        $newKtpNumber = $this->request->getPost('ktp_number');
        $user = $this->userModel->find($userId);
        if ($newKtpNumber !== $user['ktp_number']) {
            $data['ktp_number']      = $newKtpNumber;
            $data['ktp_verified']    = 'unverified';
            $data['ktp_verified_at'] = null;
        }

        $ktpFile = $this->request->getFile('ktp_photo');
        if ($ktpFile && $ktpFile->isValid() && !$ktpFile->hasMoved()) {
            $newKtpName = $ktpFile->getRandomName();
            $ktpFile->move(FCPATH . 'assets/uploads/ktp', $newKtpName);
            $data['ktp_photo']       = $newKtpName;
            // Foto KTP baru diunggah, perlu diverifikasi ulang oleh admin
            $data['ktp_verified']    = 'unverified';
            $data['ktp_verified_at'] = null;
        }

        $file = $this->request->getFile('photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/uploads/users', $newName);
            $data['photo'] = $newName;
            session()->set('photo', $newName);
        }

        $this->userModel->update($userId, $data);
        session()->set('name', $data['name']);

        return redirect()->to('/profil')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $userId = session()->get('user_id');
        $user   = $this->userModel->find($userId);

        $oldPassword = $this->request->getPost('old_password');
        $newPassword = $this->request->getPost('new_password');
        $confirm     = $this->request->getPost('confirm_password');

        if (!password_verify($oldPassword, $user['password'])) {
            return redirect()->to('/profil')->with('error', 'Password lama salah.');
        }

        if (strlen($newPassword) < 6) {
            return redirect()->to('/profil')->with('error', 'Password baru minimal 6 karakter.');
        }

        if ($newPassword !== $confirm) {
            return redirect()->to('/profil')->with('error', 'Konfirmasi password tidak sesuai.');
        }

        $this->userModel->update($userId, ['password' => password_hash($newPassword, PASSWORD_DEFAULT)]);

        return redirect()->to('/profil')->with('success', 'Password berhasil diubah.');
    }
}
