<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'status',
        'store_name',
        'store_description',
        'store_address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
