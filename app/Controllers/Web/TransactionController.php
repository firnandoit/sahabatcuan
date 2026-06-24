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
            'stocks' => $this->stockModel->findAll() // Untuk dropdown di Modal
        ];
        return view('web/transactions/index', $data);
    }

    // API untuk menarik data ke DataTables (JSON)
    public function getTransactions()
    {
        $data = $this->transactionModel->orderBy('transaction_date', 'DESC')->findAll();
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

        $this->transactionModel->insert([
            'user_id'          => 1, // Hardcode ID 1
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
