<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransactionHistory extends Model
{
    use HasFactory;

    protected $table = 'tbl_bank_transaction_history';
}
