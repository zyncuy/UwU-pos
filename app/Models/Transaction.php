<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice',
        'total_price',
        'pay_amount',
        'change_amount',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price ?? 0, 0, ',', '.');
    }

    public function getFormattedPayAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->pay_amount ?? 0, 0, ',', '.');
    }

    public function getFormattedChangeAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->change_amount ?? 0, 0, ',', '.');
    }
}