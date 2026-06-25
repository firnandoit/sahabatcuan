<?php

namespace App\Controllers;

class MarketController extends BaseController
{
    public function updatePrices()
    {
        $db = \Config\Database::connect();

        // 1. Ambil semua ticker unik yang ada di database
        $stocks = $db->table('market_prices')->select('ticker')->get()->getResultArray();

        if (empty($stocks)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No tickers found']);
        }

        $client = \Config\Services::curlrequest();

        foreach ($stocks as $s) {
            $ticker = $s['ticker']; // Contoh: BBRI.JK

            try {
                // 2. Tembak API Yahoo Finance (Endpoint Chart)
                $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$ticker}";

                $response = $client->get($url, [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                    ],
                    'timeout' => 10
                ]);

                $data = json_decode($response->getBody(), true);

                // 3. Ekstrak data harga dari JSON Yahoo
                if (isset($data['chart']['result'][0]['meta'])) {
                    $meta = $data['chart']['result'][0]['meta'];

                    $lastPrice = $meta['regularMarketPrice'] ?? 0;
                    $prevClose = $meta['previousClose'] ?? 0;

                    // Hitung perubahan nominal & persen
                    $changeNominal = $lastPrice - $prevClose;
                    $changePercent = ($prevClose > 0) ? ($changeNominal / $prevClose) * 100 : 0;

                    // 4. Update ke tabel market_prices
                    $db->table('market_prices')
                        ->where('ticker', $ticker)
                        ->update([
                            'last_price'        => $lastPrice,
                            'change_nominal'    => $changeNominal,
                            'change_percentage' => $changePercent,
                            'last_update'       => date('Y-m-d H:i:s')
                        ]);
                }
            } catch (\Exception $e) {
                // Skip jika satu error agar tidak menghentikan loop
                continue;
            }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Prices updated successfully']);
    }
}
