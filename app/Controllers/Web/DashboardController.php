<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $userId = 1; // ID dari Seeder

        // Ambil data portfolio + Join dengan harga pasar terakhir
        $portfolio = $db->table('user_portfolios p')
            ->select('p.*, m.last_price, s.company_name')
            ->join('market_prices m', 'm.ticker = p.ticker', 'left')
            ->join('stocks s', 's.ticker = p.ticker', 'left')
            ->where('p.user_id', $userId)
            ->get()->getResultArray();

        $data = [
            'title' => 'Dashboard SahabatCuan',
            'portfolio' => $portfolio
        ];

        return view('web/dashboard', $data);
    }
}
