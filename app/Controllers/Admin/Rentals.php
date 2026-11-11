<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RentalModel;
use App\Models\RentalItemModel;
use App\Models\KendaraanModel;
use App\Models\UserModel;
use App\Models\PaymentModel;

class Rentals extends BaseController
{
    protected RentalModel $rentalModel;
    protected RentalItemModel $rentalItemModel;
    protected PaymentModel $paymentModel;
    protected KendaraanModel $kendaraanModel;
    protected UserModel $userModel;

    // Denda keterlambatan per hari = 10% dari total harga sewa/hari rata-rata
    const LATE_FEE_PERCENT_PER_DAY = 0.10;

    public function __construct()
    {
        $this->rentalModel     = new RentalModel();
        $this->rentalItemModel = new RentalItemModel();
        $this->paymentModel    = new PaymentModel();
        $this->kendaraanModel     = new KendaraanModel();
        $this->userModel       = new UserModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status');
        $data = [
            'title'   => 'Manajemen Transaksi Sewa',
            'penggunaans' => $this->rentalModel->getAllWithUser($status),
            'status'  => $status,
        ];
        return view('admin/rentals/index', $data);
    }

    public function detail(int $id)
    {
        $rental = $this->rentalModel->getDetail($id);
        if (!$rental) {
            return redirect()->to('/admin/transaksi')->with('error', 'Data tidak ditemukan.');
        }

        $data = [
            'title'    => 'Detail Transaksi #' . $rental['invoice_code'],
            'rental'   => $rental,
            'items'    => $this->rentalItemModel->getItemsByRental($id),
            'payments' => $this->paymentModel->getByRental($id),
        ];

        return view('admin/rentals/detail', $data);
    }

    public function approve(int $id)
    {
        $rental = $this->rentalModel->find($id);
        if (!$rental || $rental['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Transaksi tidak bisa disetujui.');
        }

        $this->rentalModel->update($id, ['status' => 'approved']);
        return redirect()->to('/admin/transaksi/' . $id)->with('success', 'Transaksi disetujui. Menunggu pembayaran customer.');
    }

    /**
     * Verifikasi KTP akun peminjam (disimpan di tabel users).
     */
    public function verifyBorrowerKtp(int $id)
    {
        $rental = $this->rentalModel->find($id);
        if (!$rental) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $userModel = new \App\Models\UserModel();
        $userModel->update($rental['user_id'], [
            'ktp_verified'    => 'verified',
            'ktp_verified_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/transaksi/' . $id)->with('success', 'KTP peminjam berhasil diverifikasi.');
    }

    public function rejectBorrowerKtp(int $id)
    {
        $rental = $this->rentalModel->find($id);
        if (!$rental) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $userModel = new \App\Models\UserModel();
        $userModel->update($rental['user_id'], [
            'ktp_verified'    => 'rejected',
            'ktp_verified_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/transaksi/' . $id)->with('success', 'KTP peminjam ditolak. Customer perlu mengunggah ulang di halaman profil.');
    }

    /**
     * Verifikasi KTP penerima/pengambil unit (khusus rental yang diwakilkan),
     * disimpan langsung di tabel rentals karena sifatnya per-transaksi.
     */
    public function verifyRecipientKtp(int $id)
    {
        $rental = $this->rentalModel->find($id);
        if (!$rental || $rental['is_self_pickup'] === 'yes') {
            return redirect()->back()->with('error', 'Transaksi ini tidak memiliki data penerima yang diwakilkan.');
        }

        $this->rentalModel->update($id, [
            'recipient_ktp_verified'    => 'verified',
            'recipient_ktp_verified_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/transaksi/' . $id)->with('success', 'KTP penerima berhasil diverifikasi.');
    }

    public function rejectRecipientKtp(int $id)
    {
        $rental = $this->rentalModel->find($id);
        if (!$rental || $rental['is_self_pickup'] === 'yes') {
            return redirect()->back()->with('error', 'Transaksi ini tidak memiliki data penerima yang diwakilkan.');
        }

        $this->rentalModel->update($id, [
            'recipient_ktp_verified'    => 'rejected',
            'recipient_ktp_verified_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/transaksi/' . $id)->with('success', 'KTP penerima ditolak.');
    }

    public function reject(int $id)
    {
        $rental = $this->rentalModel->find($id);
        if (!$rental || $rental['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Transaksi tidak bisa ditolak.');
        }

        $reason = $this->request->getPost('reason');
        $this->rentalModel->update($id, ['status' => 'rejected', 'notes' => $reason]);
        return redirect()->to('/admin/transaksi/' . $id)->with('success', 'Transaksi ditolak.');
    }

    public function delete(int $id)
    {
        $rental = $this->rentalModel->find($id);
        if (!$rental) {
            return redirect()->to('/admin/transaksi')->with('error', 'Transaksi tidak ditemukan.');
        }

        // Item dan pembayaran terkait terhapus melalui ON DELETE CASCADE.
        $this->rentalModel->delete($id);

        return redirect()->to('/admin/transaksi')->with('success', 'Transaksi ' . $rental['invoice_code'] . ' berhasil dihapus.');
    }

    /**
     * Tandai laptop sudah dikembalikan. Hitung denda otomatis
     * jika tanggal pengembalian melewati end_date.
     */
    public function returned(int $id)
    {
        $rental = $this->rentalModel->find($id);
        if (!$rental || !in_array($rental['status'], ['ongoing', 'approved'])) {
            return redirect()->back()->with('error', 'Transaksi tidak dapat diselesaikan.');
        }

        $returnDate = $this->request->getPost('actual_return_date') ?: date('Y-m-d');
        $lateDays = max(0, (strtotime($returnDate) - strtotime($rental['end_date'])) / 86400);

        $lateFee = 0;
        if ($lateDays > 0) {
            $dailyRate = $rental['subtotal'] / max(1, $rental['total_days']);
            $lateFee = round($lateDays * $dailyRate * self::LATE_FEE_PERCENT_PER_DAY);
        }

        $this->rentalModel->update($id, [
            'status'             => 'returned',
            'actual_return_date' => $returnDate,
            'late_fee'           => $lateFee,
            'total_price'        => $rental['subtotal'] + $lateFee,
        ]);

        $msg = $lateFee > 0
            ? "Rental diselesaikan. Denda keterlambatan: Rp " . number_format($lateFee, 0, ',', '.')
            : 'Rental diselesaikan tepat waktu, tanpa denda.';

        return redirect()->to('/admin/transaksi/' . $id)->with('success', $msg);
    }

    // ===================== FORM TAMBAH (INPUT MANUAL OLEH ADMIN) =====================
    public function create()
    {
        $data = [
            'title'     => 'Input Penyewaan Baru',
            'customers' => $this->userModel->where('role', 'customer')->findAll(),
            'kendaraans'   => $this->kendaraanModel->where('status', 'baik')->where('stock >', 0)->findAll(),
        ];
        return view('admin/rentals/form', $data);
    }

    // ===================== SIMPAN (CREATE) =====================
    public function simpan()
    {
        $rules = [
            'user_id'    => 'required|numeric',
            'start_date' => 'required|valid_date',
            'end_date'   => 'required|valid_date',
            'laptop_id'  => 'required',
            'qty'        => 'required',
        ];
        if ($this->request->getPost('is_self_pickup') === 'no') {
            $rules['recipient_name']       = 'required|max_length[150]';
            $rules['recipient_phone']      = 'required|max_length[20]';
            $rules['recipient_ktp_number'] = 'required|max_length[30]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $laptopIds = $this->request->getPost('laptop_id');
        $qtys      = $this->request->getPost('qty');

        if (empty($laptopIds) || empty($qtys)) {
            return redirect()->back()->withInput()->with('error', 'Minimal pilih 1 laptop untuk disewa.');
        }

        $startDate = $this->request->getPost('start_date');
        $endDate   = $this->request->getPost('end_date');
        $totalDays = $this->calculateDays($startDate, $endDate);
        $rentalType = $this->request->getPost('rental_type') === 'monthly' ? 'monthly' : 'daily';
        $billableUnits = $rentalType === 'monthly' ? max(1, (int) ceil($totalDays / 30)) : $totalDays;

        if ($totalDays < 1) {
            return redirect()->back()->withInput()->with('error', 'Tanggal selesai harus setelah tanggal mulai.');
        }

        // Validasi stok semua item dulu sebelum menyimpan apapun
        $itemsValid = [];
        foreach ($laptopIds as $i => $laptopId) {
            $qty = (int) ($qtys[$i] ?? 0);
            if ($qty < 1) {
                continue;
            }
            $laptop = $this->kendaraanModel->find($laptopId);
            if (!$laptop) {
                return redirect()->back()->withInput()->with('error', 'Salah satu laptop tidak ditemukan.');
            }
            if ($qty > $laptop['stock']) {
                return redirect()->back()->withInput()->with('error', "Stok {$laptop['name']} tidak mencukupi. Tersedia: {$laptop['stock']}.");
            }
            $itemsValid[] = ['laptop' => $laptop, 'qty' => $qty];
        }

        if (empty($itemsValid)) {
            return redirect()->back()->withInput()->with('error', 'Minimal pilih 1 laptop dengan jumlah valid.');
        }

        $subtotal = 0;
        foreach ($itemsValid as $it) {
            $rate = $rentalType === 'monthly' ? $it['laptop']['price_per_month'] : $it['laptop']['price_per_day'];
            $subtotal += $it['qty'] * $rate * $billableUnits;
        }

        $isSelfPickup = $this->request->getPost('is_self_pickup') ?: 'yes';
        $ktpPhoto = $isSelfPickup === 'no' ? $this->uploadKtpPhoto() : null;

        $rentalId = $this->rentalModel->insert([
            'invoice_code'          => $this->rentalModel->generateInvoiceCode(),
            'user_id'               => $this->request->getPost('user_id'),
            'start_date'            => $startDate,
            'end_date'              => $endDate,
            'total_days'            => $totalDays,
            'rental_type'           => $rentalType,
            'subtotal'              => $subtotal,
            'late_fee'              => 0,
            'total_price'           => $subtotal,
            'status'                => $this->request->getPost('status') ?: 'approved',
            'notes'                 => $this->request->getPost('notes'),
            'is_self_pickup'        => $isSelfPickup,
            'recipient_name'        => $isSelfPickup === 'no' ? $this->request->getPost('recipient_name') : null,
            'recipient_phone'       => $isSelfPickup === 'no' ? $this->request->getPost('recipient_phone') : null,
            'recipient_relation'    => $isSelfPickup === 'no' ? $this->request->getPost('recipient_relation') : null,
            'recipient_ktp_number'  => $isSelfPickup === 'no' ? $this->request->getPost('recipient_ktp_number') : null,
            'recipient_ktp_photo'   => $ktpPhoto,
            'recipient_ktp_verified'=> 'unverified',
        ], true);

        foreach ($itemsValid as $it) {
            $rate = $rentalType === 'monthly' ? $it['laptop']['price_per_month'] : $it['laptop']['price_per_day'];
            $itemSubtotal = $it['qty'] * $rate * $billableUnits;
            $this->rentalItemModel->insert([
                'rental_id'     => $rentalId,
                'laptop_id'     => $it['laptop']['id'],
                'qty'           => $it['qty'],
                'price_per_day' => $it['laptop']['price_per_day'],
                'price_per_month' => $it['laptop']['price_per_month'],
                'subtotal'      => $itemSubtotal,
            ]);
            $this->kendaraanModel->reduceStock($it['laptop']['id'], $it['qty']);
        }

        return redirect()->to('/admin/transaksi')->with('success', 'Penyewaan baru berhasil diinput.');
    }

    private function calculateDays($start, $end): int
    {
        $diff = (strtotime($end) - strtotime($start)) / 86400;
        return (int) max(1, $diff);
    }
}
