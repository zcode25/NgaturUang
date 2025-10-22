<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'user_id',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgetDetails()
    {
        return $this->hasMany(BudgetDetail::class);
    }
}
