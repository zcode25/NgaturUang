<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id', 'name', 'type', 'currency', 'begin_balance',
        'account_number', 'bank_name', 'description', 'status'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
