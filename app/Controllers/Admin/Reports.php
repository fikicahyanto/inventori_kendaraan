<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RentalModel;

class Reports extends BaseController
{
    protected RentalModel $rentalModel;

    public function __construct()
    {
        $this->rentalModel = new RentalModel();
    }

    public function index()
    {
        $start = $this->request->getGet('start') ?: date('Y-m-01');
        $end   = $this->request->getGet('end') ?: date('Y-m-d');

        $rentals = $this->rentalModel->revenueBetween($start, $end);
        $totalRevenue = array_sum(array_column($rentals, 'total_price'));

        $data = [
            'title'        => 'Laporan Transaksi',
            'rentals'      => $rentals,
            'start'        => $start,
            'end'          => $end,
            'totalRevenue' => $totalRevenue,
        ];

        return view('admin/reports/index', $data);
    }

    /**
     * Export laporan ke CSV sederhana (bisa dibuka di Excel).
     */
    public function export()
    {
        $start = $this->request->getGet('start') ?: date('Y-m-01');
        $end   = $this->request->getGet('end') ?: date('Y-m-d');
        $rentals = $this->rentalModel->revenueBetween($start, $end);

        $filename = "laporan-rental-{$start}-sampai-{$end}.csv";

        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', "attachment; filename={$filename}");

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Invoice', 'Customer', 'Tanggal Mulai', 'Tanggal Selesai', 'Total Hari', 'Subtotal', 'Denda', 'Total', 'Status']);

        foreach ($rentals as $r) {
            fputcsv($output, [
                $r['invoice_code'], $r['customer_name'], $r['start_date'], $r['end_date'],
                $r['total_days'], $r['subtotal'], $r['late_fee'], $r['total_price'], $r['status'],
            ]);
        }

        fclose($output);
        exit;
    }
}
