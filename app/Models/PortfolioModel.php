<?php

namespace App\Models;

use CodeIgniter\Model;

class PortfolioModel extends Model
{
    protected $table            = 'user_portfolios';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'ticker', 'total_quantity', 'average_price', 'total_investment'];

    /**
     * Fungsi untuk menghitung Average Down secara otomatis
     */
    public function recalculate($userId, $ticker)
    {
        $db = \Config\Database::connect();

        // Ambil semua histori transaksi user untuk saham ini
        $builder = $db->table('transactions');
        $transactions = $builder->where(['user_id' => $userId, 'ticker' => $ticker])
            ->orderBy('transaction_date', 'ASC')
            ->get()->getResultArray();

        $currentQty = 0;
        $totalCost = 0;

        foreach ($transactions as $tx) {
            if ($tx['type'] == 'BUY') {
                $currentQty += $tx['quantity'];
                // Modal bertambah: (Qty * Harga) + Fee
                $totalCost += ($tx['quantity'] * $tx['price_per_unit']) + $tx['broker_fee'];
            } elseif ($tx['type'] == 'SELL') {
                if ($currentQty > 0) {
                    $avgSebelumnya = $totalCost / $currentQty;
                    $currentQty -= $tx['quantity'];
                    // Modal berkurang secara proporsional sesuai porsi yang dijual
                    $totalCost -= ($tx['quantity'] * $avgSebelumnya);
                }
            }
        }

        $finalAvg = ($currentQty > 0) ? ($totalCost / $currentQty) : 0;

        // Update atau Insert ke tabel user_portfolios
        $existing = $this->where(['user_id' => $userId, 'ticker' => $ticker])->first();

        $payload = [
            'user_id'          => $userId,
            'ticker'           => $ticker,
            'total_quantity'   => $currentQty,
            'average_price'    => $finalAvg,
            'total_investment' => $totalCost
        ];

        if ($existing) {
            $this->update($existing['id'], $payload);
        } else {
            $this->insert($payload);
        }
    }
}
