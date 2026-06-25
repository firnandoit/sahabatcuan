<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MarketUpdate extends BaseCommand
{
    protected $group       = 'Market';
    protected $name        = 'market:update'; // Nama perintah yang akan dipanggil
    protected $description = 'Update harga saham terbaru dari Yahoo Finance.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        // 1. Ambil semua ticker unik yang dimiliki user di portfolio
        // Pastikan ticker di database berakhiran .JK (contoh: BBRI.JK)
        $stocks = $db->table('user_portfolios')->select('ticker')->distinct()->get()->getResultArray();

        if (empty($stocks)) {
            CLI::error("Tidak ada data ticker di portofolio.");
            return;
        }

        CLI::write("Memulai update harga saham...", "yellow");

        $client = \Config\Services::curlrequest();

        foreach ($stocks as $s) {
            $ticker = $s['ticker'];
            CLI::write("Mengambil data untuk: $ticker", "cyan");

            try {
                // 2. Request ke Yahoo Finance
                $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$ticker}";

                $response = $client->get($url, [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                    ],
                    'timeout' => 10
                ]);

                $data = json_decode($response->getBody(), true);

                if (isset($data['chart']['result'][0]['meta'])) {
                    $meta = $data['chart']['result'][0]['meta'];

                    $lastPrice     = $meta['regularMarketPrice'] ?? 0;
                    $prevClose     = $meta['previousClose'] ?? 0;
                    $changeNominal = $lastPrice - $prevClose;
                    $changePercent = ($prevClose > 0) ? ($changeNominal / $prevClose) * 100 : 0;

                    // 3. Update atau Insert ke tabel market_prices
                    // Kita gunakan 'replace' agar jika belum ada dia insert, jika sudah ada dia update
                    $db->table('market_prices')->replace([
                        'ticker'            => $ticker,
                        'last_price'        => $lastPrice,
                        'change_nominal'    => $changeNominal,
                        'change_percentage' => $changePercent,
                        'last_update'       => date('Y-m-d H:i:s')
                    ]);

                    CLI::write("BERHASIL: $ticker = Rp " . number_format($lastPrice), "green");
                } else {
                    CLI::error("Format data Yahoo tidak sesuai untuk $ticker");
                }
            } catch (\Exception $e) {
                CLI::error("Gagal mengambil data $ticker: " . $e->getMessage());
            }

            // Jeda 1 detik agar tidak diblokir Yahoo
            sleep(1);
        }

        CLI::write("Selesai memperbarui harga pasar!", "green");
    }
}
