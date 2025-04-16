<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Service;

class Staff extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'emergency_contact',
        'commission',
        
    ];

    public function specialization()
    {
        return $this->belongsToMany(Service::class);
    }
}
