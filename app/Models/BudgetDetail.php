<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetDetail extends Model
{
    protected $fillable = [
        'budget_id',
        'category_id',
        'amount',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id', 'category_id')
                    ->where('status', 'active');
    }
}
