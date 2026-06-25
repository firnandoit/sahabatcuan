<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\StockModel;

class StockController extends BaseController
{
    protected $stockModel;

    public function __construct()
    {
        $this->stockModel = new StockModel();
    }

    public function index()
    {
        return view('web/stocks/index', ['title' => 'Master Data Saham']);
    }

    public function getStocks()
    {
        $data = $this->stockModel->findAll();
        return $this->response->setJSON(['data' => $data]);
    }

    public function store()
    {
        $ticker     = strtoupper($this->request->getPost('ticker'));
        $old_ticker = $this->request->getPost('old_ticker');
        $isUpdate   = $this->request->getPost('is_update');

        $data = [
            'ticker'       => $ticker,
            'company_name' => $this->request->getPost('company_name'),
            'category'     => $this->request->getPost('category'),
            'sector'       => $this->request->getPost('sector'),
        ];

        try {
            if ($isUpdate == "1") {
                // Jika ticker diubah, cek apakah ticker baru sudah dipakai orang lain
                if ($ticker !== $old_ticker) {
                    if ($this->stockModel->find($ticker)) {
                        return $this->response->setJSON(['status' => 'error', 'message' => 'Ticker baru sudah terdaftar!']);
                    }
                }

                // Gunakan Query Builder untuk update agar bisa mengubah Primary Key
                $this->stockModel->builder()
                    ->where('ticker', $old_ticker)
                    ->update($data);

                $msg = "Data emiten berhasil diperbarui";
            } else {
                // Logika Insert Baru
                if ($this->stockModel->find($ticker)) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Ticker sudah terdaftar!']);
                }
                $this->stockModel->insert($data);
                $msg = "Emiten baru berhasil ditambahkan";
            }

            return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
        } catch (\Exception $e) {
            // Jika ada error database, kirimkan pesan errornya ke console
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($ticker)
    {
        if ($this->stockModel->delete($ticker)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Emiten berhasil dihapus']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
