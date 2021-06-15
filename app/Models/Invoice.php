<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'invoice_address',
        'delivery_address',
        'invoice_number',
        'invoice_term',
    ];

    public function item()
    {
        return $this->hasMany(InvoiceItem::class);
    }


}
