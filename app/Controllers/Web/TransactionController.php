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

    // Menampilkan daftar transaksi
    public function index()
    {
        $data = [
            'title' => 'Riwayat Transaksi',
            'transactions' => $this->transactionModel->orderBy('transaction_date', 'DESC')->findAll()
        ];
        return view('web/transactions/index', $data);
    }

    // Menampilkan form tambah transaksi
    public function add()
    {
        $data = [
            'title' => 'Tambah Transaksi',
            'stocks' => $this->stockModel->findAll() // Untuk dropdown pilihan saham
        ];
        return view('web/transactions/add', $data);
    }

    // Memproses penyimpanan data
    public function store()
    {
        // Validasi sederhana
        if (!$this->validate([
            'ticker'           => 'required',
            'type'             => 'required',
            'quantity'         => 'required|numeric',
            'price_per_unit'   => 'required|numeric',
            'transaction_date' => 'required|valid_date',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $type  = $this->request->getPost('type');
        $price = $this->request->getPost('price_per_unit');
        $qty   = $this->request->getPost('quantity');
        $fee   = $this->request->getPost('broker_fee') ?? 0;

        // Hitung total nominal (Beli = modal + fee, Jual = modal - fee)
        $totalAmount = ($type == 'BUY') ? ($price * $qty) + $fee : ($price * $qty) - $fee;

        $this->transactionModel->insert([
            'user_id'          => 1, // Sementara hardcode ID dari Seeder
            'ticker'           => $this->request->getPost('ticker'),
            'type'             => $type,
            'quantity'         => $qty,
            'price_per_unit'   => $price,
            'broker_fee'       => $fee,
            'total_amount'     => $totalAmount,
            'transaction_date' => $this->request->getPost('transaction_date'),
            'notes'            => $this->request->getPost('notes'),
        ]);

        return redirect()->to('/web/transactions')->with('success', 'Transaksi berhasil dicatat!');
    }
}
