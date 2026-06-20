<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'user_id',
        'ticker',
        'type',
        'quantity',
        'price_per_unit',
        'broker_fee',
        'total_amount',
        'transaction_date',
        'notes'
    ];

    // Otomatis update portofolio setelah simpan transaksi
    protected $afterInsert = ['updateUserPortfolio'];

    protected function updateUserPortfolio(array $data)
    {
        $userId = $data['data']['user_id'];
        $ticker = $data['data']['ticker'];

        // Panggil model Portfolio untuk hitung ulang
        $portfolioModel = new \App\Models\PortfolioModel();
        $portfolioModel->recalculate($userId, $ticker);

        return $data;
    }
}
