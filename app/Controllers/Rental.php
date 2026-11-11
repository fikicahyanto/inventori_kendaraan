<?php

namespace App\Controllers;

use App\Models\RentalModel;
use App\Models\RentalItemModel;
use App\Models\PaymentModel;

class Rental extends BaseController
{
    protected RentalModel $rentalModel;
    protected RentalItemModel $rentalItemModel;
    protected PaymentModel $paymentModel;

    public function __construct()
    {
        $this->rentalModel     = new RentalModel();
        $this->rentalItemModel = new RentalItemModel();
        $this->paymentModel    = new PaymentModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $data = [
            'title'   => 'Rental Saya',
            'rentals' => $this->rentalModel->getForUser($userId),
        ];
        return view('rental/history', $data);
    }

    public function detail(int $id)
    {
        $userId = session()->get('user_id');
        $rental = $this->rentalModel->find($id);

        if (!$rental || $rental['user_id'] != $userId) {
            return redirect()->to('/rental-saya')->with('error', 'Data rental tidak ditemukan.');
        }

        $data = [
            'title'    => 'Detail Rental #' . $rental['invoice_code'],
            'rental'   => $rental,
            'items'    => $this->rentalItemModel->getItemsByRental($id),
            'payments' => $this->paymentModel->getByRental($id),
        ];

        return view('rental/detail', $data);
    }

    public function uploadPayment(int $id)
    {
        $userId = session()->get('user_id');
        $rental = $this->rentalModel->find($id);

        if (!$rental || $rental['user_id'] != $userId) {
            return redirect()->to('/rental-saya')->with('error', 'Data rental tidak ditemukan.');
        }

        $rules = [
            'proof_image' => 'uploaded[proof_image]|is_image[proof_image]|max_size[proof_image,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('proof_image');
        $newName = $file->getRandomName();
        $file->move(FCPATH . 'assets/uploads/payments', $newName);

        $this->paymentModel->insert([
            'rental_id'      => $id,
            'amount'         => $rental['total_price'],
            'payment_method' => $this->request->getPost('payment_method') ?: 'Transfer Bank',
            'proof_image'    => $newName,
            'status'         => 'pending',
            'paid_at'        => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/rental-saya/' . $id)
            ->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
    }

    public function cancel(int $id)
    {
        $userId = session()->get('user_id');
        $rental = $this->rentalModel->find($id);

        if (!$rental || $rental['user_id'] != $userId) {
            return redirect()->to('/rental-saya')->with('error', 'Data rental tidak ditemukan.');
        }

        if (!in_array($rental['status'], ['pending', 'approved'])) {
            return redirect()->to('/rental-saya/' . $id)->with('error', 'Rental ini tidak bisa dibatalkan.');
        }

        $this->rentalModel->update($id, ['status' => 'cancelled']);

        return redirect()->to('/rental-saya')->with('success', 'Pesanan rental berhasil dibatalkan.');
    }
}
