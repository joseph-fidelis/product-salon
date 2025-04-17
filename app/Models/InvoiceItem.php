<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id',
        'service_id',
        'service_name',
        'staff_id',
        'staff_name',
        'quantity',
        'price',
        'discount',
        'total',
        'commission',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float',
        'discount' => 'float',
        'total' => 'float',
        'commission' => 'float',
    ];

    /**
     * Get the invoice that owns the item.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the service associated with the item.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the staff associated with the item.
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
