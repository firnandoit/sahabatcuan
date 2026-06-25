<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\StockModel;

class TransactionController extends BaseController
{
    protected $transactionModel;
    protected $stockModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->stockModel = new StockModel();
    }

    // Halaman utama transaksi
    public function index()
    {
        $data = [
            'title'  => 'Riwayat Transaksi',
            'stocks' => $this->stockModel->findAll()
        ];
        return view('web/transactions/index', $data);
    }

    // API untuk menarik data ke DataTables (JSON)
    public function getTransactions()
    {
        // AMBIL ID USER YANG SEDANG LOGIN
        $userId = session()->get('user_id');

        // HAK AKSES DATA: Hanya ambil transaksi milik user yang login
        $data = $this->transactionModel
            ->where('user_id', $userId)
            ->orderBy('transaction_date', 'DESC')
            ->findAll();

        return $this->response->setJSON(['data' => $data]);
    }

    // Proses simpan via AJAX (Modal)
    public function storeAjax()
    {
        $rules = [
            'ticker'           => 'required',
            'type'             => 'required',
            'quantity'         => 'required|numeric',
            'price_per_unit'   => 'required|numeric',
            'transaction_date' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $type  = $this->request->getPost('type');
        $price = $this->request->getPost('price_per_unit');
        $qty   = $this->request->getPost('quantity');
        $fee   = $this->request->getPost('broker_fee') ?? 0;

        $totalAmount = ($type == 'BUY') ? ($price * $qty) + $fee : ($price * $qty) - $fee;

        // SIMPAN DATA
        $this->transactionModel->insert([
            // GUNAKAN SESSION USER ID (BUKAN HARDCODE 1)
            'user_id'          => session()->get('user_id'),
            'ticker'           => $this->request->getPost('ticker'),
            'type'             => $type,
            'quantity'         => $qty,
            'price_per_unit'   => $price,
            'broker_fee'       => $fee,
            'total_amount'     => $totalAmount,
            'transaction_date' => $this->request->getPost('transaction_date'),
            'notes'            => $this->request->getPost('notes'),
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Transaksi berhasil disimpan!'
        ]);
    }
}
