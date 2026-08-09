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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke detail transaksi
    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id');
    }

    // Alias jika kodingan lama memanggil items
    public function items()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id');
    }
}