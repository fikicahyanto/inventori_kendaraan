<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PaymentModel;
use App\Models\RentalModel;

class Payments extends BaseController
{
    protected PaymentModel $paymentModel;
    protected RentalModel $rentalModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->rentalModel  = new RentalModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status');
        $data = [
            'title'    => 'Verifikasi Pembayaran',
            'payments' => $this->paymentModel->getAllWithRental($status),
            'status'   => $status,
        ];
        return view('admin/payments/index', $data);
    }

    public function verify(int $id)
    {
        $payment = $this->paymentModel->find($id);
        if (!$payment) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $this->paymentModel->update($id, [
            'status'      => 'verified',
            'verified_at' => date('Y-m-d H:i:s'),
        ]);

        // Setelah pembayaran diverifikasi, status rental menjadi ongoing
        $this->rentalModel->update($payment['rental_id'], ['status' => 'ongoing']);

        return redirect()->to('/admin/pembayaran')->with('success', 'Pembayaran diverifikasi. Status sewa menjadi berjalan.');
    }

    public function reject(int $id)
    {
        $payment = $this->paymentModel->find($id);
        if (!$payment) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $this->paymentModel->update($id, [
            'status'      => 'rejected',
            'verified_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/pembayaran')->with('success', 'Pembayaran ditolak. Customer perlu mengunggah ulang bukti bayar.');
    }
}
