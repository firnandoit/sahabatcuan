<?php

namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table            = 'stocks';
    protected $primaryKey       = 'ticker'; // Karena ticker adalah PK (BBCA.JK)
    protected $returnType       = 'array';
    protected $allowedFields    = ['ticker', 'company_name', 'category', 'sector'];
}
