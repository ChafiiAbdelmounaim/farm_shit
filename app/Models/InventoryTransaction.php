<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',          // Who recorded the transaction
        'used_by_user_id',  // Who physically used the product
        'type',             // 'in' or 'out'
        'quantity',
        'field_id',         // Only for 'out' transactions
        'date',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by_user_id');
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function scopeIncoming($query)
    {
        return $query->where('type', 'in');
    }

    public function scopeOutgoing($query)
    {
        return $query->where('type', 'out');
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function getTypeNameAttribute()
    {
        return $this->type === 'in' ? 'Stock In' : 'Stock Out';
    }
}
