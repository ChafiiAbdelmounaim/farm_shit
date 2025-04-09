<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'category_id', 'current_quantity', 'description'];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function transactions() {
        return $this->hasMany(InventoryTransaction::class);
    }
}
