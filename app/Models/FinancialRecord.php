<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class FinancialRecord extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'category',
        'date',
        'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
