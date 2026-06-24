<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $userId = 1;

        $portfolio = $db->table('user_portfolios p')
            ->select('p.*, m.last_price, s.company_name')
            ->join('market_prices m', 'm.ticker = p.ticker', 'left')
            ->join('stocks s', 's.ticker = p.ticker', 'left')
            ->where('p.user_id', $userId)
            ->get()->getResultArray();

        // Hitung Ringkasan Atas
        $totalModal = 0;
        $totalValue = 0;
        foreach ($portfolio as $p) {
            $totalModal += $p['total_investment'];
            $totalValue += $p['total_quantity'] * ($p['last_price'] ?? 0);
        }
        $totalPL = $totalValue - $totalModal;

        $data = [
            'title' => 'Dashboard SahabatCuan',
            'portfolio' => $portfolio,
            'summary' => [
                'total_modal' => $totalModal,
                'total_value' => $totalValue,
                'total_pl' => $totalPL,
                'pl_percent' => ($totalModal > 0) ? ($totalPL / $totalModal) * 100 : 0
            ]
        ];

        return view('web/dashboard', $data);
    }
}
