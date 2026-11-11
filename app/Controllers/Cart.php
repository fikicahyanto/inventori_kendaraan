<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\LaptopModel;
use App\Models\RentalModel;
use App\Models\RentalItemModel;

class Cart extends BaseController
{
    protected CartModel $cartModel;
    protected LaptopModel $laptopModel;

    public function __construct()
    {
        $this->cartModel   = new CartModel();
        $this->laptopModel = new LaptopModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $data = [
            'title' => 'Keranjang Sewa',
            'items' => $this->cartModel->getCartByUser($userId),
        ];
        return view('cart/index', $data);
    }

    public function add()
    {
        $userId   = session()->get('user_id');
        $laptopId = (int) $this->request->getPost('laptop_id');
        $qty      = max(1, (int) $this->request->getPost('qty'));

        $laptop = $this->laptopModel->find($laptopId);
        if (!$laptop || $laptop['status'] !== 'available') {
            return $this->response->setJSON(['success' => false, 'message' => 'Laptop tidak tersedia.']);
        }

        $existing = $this->cartModel->findItem($userId, $laptopId);
        if ($existing) {
            $this->cartModel->update($existing['id'], ['qty' => $existing['qty'] + $qty]);
        } else {
            $this->cartModel->insert(['user_id' => $userId, 'laptop_id' => $laptopId, 'qty' => $qty]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Berhasil ditambahkan ke keranjang.',
            'cart_count' => $this->cartModel->countItems($userId),
        ]);
    }

    public function update()
    {
        $userId = session()->get('user_id');
        $id     = (int) $this->request->getPost('id');
        $qty    = max(1, (int) $this->request->getPost('qty'));

        $item = $this->cartModel->find($id);
        if (!$item || $item['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Item tidak ditemukan.']);
        }

        $this->cartModel->update($id, ['qty' => $qty]);

        $laptop  = $this->laptopModel->find($item['laptop_id']);
        $subtotal = $laptop['price_per_day'] * $qty;

        return $this->response->setJSON([
            'success' => true,
            'subtotal' => $subtotal,
            'formatted_subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
        ]);
    }

    public function remove(int $id)
    {
        $userId = session()->get('user_id');
        $item = $this->cartModel->find($id);
        if ($item && $item['user_id'] == $userId) {
            $this->cartModel->delete($id);
        }
        return redirect()->to('/cart')->with('success', 'Item dihapus dari keranjang.');
    }

    public function count()
    {
        $userId = session()->get('user_id');
        return $this->response->setJSON(['count' => $this->cartModel->countItems($userId)]);
    }

    public function checkout()
    {
        $userId    = session()->get('user_id');
        $startDate = $this->request->getPost('start_date');
        $endDate   = $this->request->getPost('end_date');
        $rentalType = $this->request->getPost('rental_type') === 'monthly' ? 'monthly' : 'daily';

        if (empty($startDate) || empty($endDate) || strtotime($endDate) < strtotime($startDate)) {
            return redirect()->back()->with('error', 'Tanggal sewa tidak valid.');
        }

        $items = $this->cartModel->getCartByUser($userId);
        if (empty($items)) {
            return redirect()->to('/cart')->with('error', 'Keranjang Anda masih kosong.');
        }

        // ==== Data penerima/pengambil unit laptop ====
        $isSelfPickup = $this->request->getPost('is_self_pickup') ?: 'yes';
        $recipientData = [
            'is_self_pickup' => $isSelfPickup,
            'recipient_name' => null,
            'recipient_phone' => null,
            'recipient_relation' => null,
            'recipient_ktp_number' => null,
            'recipient_ktp_photo' => null,
        ];

        if ($isSelfPickup === 'no') {
            $rules = [
                'recipient_name'       => 'required|min_length[3]',
                'recipient_phone'      => 'required|min_length[9]',
                'recipient_ktp_number' => 'required|min_length[10]|max_length[16]',
                'recipient_ktp_photo'  => 'uploaded[recipient_ktp_photo]|is_image[recipient_ktp_photo]|max_size[recipient_ktp_photo,2048]',
            ];
            $messages = [
                'recipient_name'       => ['required' => 'Nama penerima wajib diisi jika laptop diwakilkan.'],
                'recipient_ktp_photo'  => ['uploaded' => 'Foto KTP penerima wajib diunggah jika laptop diwakilkan.'],
            ];

            if (!$this->validate($rules, $messages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $ktpFile = $this->request->getFile('recipient_ktp_photo');
            $ktpName = $ktpFile->getRandomName();
            $ktpFile->move(FCPATH . 'assets/uploads/ktp', $ktpName);

            $recipientData['recipient_name']       = $this->request->getPost('recipient_name');
            $recipientData['recipient_phone']      = $this->request->getPost('recipient_phone');
            $recipientData['recipient_relation']   = $this->request->getPost('recipient_relation');
            $recipientData['recipient_ktp_number'] = $this->request->getPost('recipient_ktp_number');
            $recipientData['recipient_ktp_photo']  = $ktpName;
        }

        $totalDays = (strtotime($endDate) - strtotime($startDate)) / 86400 + 1;
        $totalMonths = max(1, (int) ceil($totalDays / 30));

        // Validasi ketersediaan stok riil untuk setiap item pada rentang tanggal
        foreach ($items as $item) {
            $available = $this->laptopModel->availableStock($item['laptop_id'], $startDate, $endDate);
            if ($item['qty'] > $available) {
                return redirect()->to('/cart')->with('error',
                    "Stok '{$item['name']}' tidak cukup untuk tanggal tersebut. Tersisa {$available} unit.");
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $rentalModel     = new RentalModel();
        $rentalItemModel = new RentalItemModel();

        $subtotal = 0;
        foreach ($items as $item) {
            $rate = $rentalType === 'monthly' ? $item['price_per_month'] : $item['price_per_day'];
            $subtotal += $rate * $item['qty'] * ($rentalType === 'monthly' ? $totalMonths : $totalDays);
        }

        $rentalId = $rentalModel->insert(array_merge([
            'invoice_code' => $rentalModel->generateInvoiceCode(),
            'user_id'      => $userId,
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'total_days'   => $totalDays,
            'rental_type'  => $rentalType,
            'subtotal'     => $subtotal,
            'late_fee'     => 0,
            'total_price'  => $subtotal,
            'status'       => 'pending',
            'notes'        => $this->request->getPost('notes'),
        ], $recipientData));

        foreach ($items as $item) {
            $rentalItemModel->insert([
                'rental_id'     => $rentalId,
                'laptop_id'     => $item['laptop_id'],
                'qty'           => $item['qty'],
                'price_per_day' => $item['price_per_day'],
                'price_per_month' => $item['price_per_month'],
                'subtotal'      => ($rentalType === 'monthly' ? $item['price_per_month'] * $totalMonths : $item['price_per_day'] * $totalDays) * $item['qty'],
            ]);
        }

        $this->cartModel->clearUserCart($userId);

        $db->transComplete();

        return redirect()->to('/rental-saya/' . $rentalId)
            ->with('success', 'Pesanan sewa berhasil dibuat! Menunggu konfirmasi admin.');
    }
}
