<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'invoice_date',
        'payment_method',
        'subtotal',
        'tax',
        'total',
        'notes',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'invoice_date' => 'date',
        'subtotal' => 'float',
        'tax' => 'float',
        'total' => 'float',
    ];

    /**
     * Get the items for the invoice.
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the commissions for the invoice.
     */
    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }
}
