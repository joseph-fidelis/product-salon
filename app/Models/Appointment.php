<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'appointment_date',
        'appointment_time',
        'status', // Pending, Approved, Completed, Cancelled, No-Show
        'notes',
        'invoice_id', // Will be populated once appointment is converted to invoice
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointment_date' => 'date',
    ];

    /**
     * Get the services for this appointment.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'appointment_services')
                    ->withPivot('staff_id', 'notes')
                    ->withTimestamps();
    }

    /**
     * Get the invoice associated with this appointment (if any).
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get all the staff assigned to this appointment through appointment_services.
     */
    public function assignedStaff()
    {
        return $this->belongsToMany(Staff::class, 'appointment_services')
                    ->withPivot('service_id')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include pending appointments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope a query to only include approved appointments.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    /**
     * Scope a query to only include completed appointments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    /**
     * Scope a query to only include cancelled appointments.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'Cancelled');
    }

    /**
     * Scope a query to only include no-show appointments.
     */
    public function scopeNoShow($query)
    {
        return $query->where('status', 'No-Show');
    }

    /**
     * Scope a query to only include appointments that haven't been converted to invoices.
     */
    public function scopeNotInvoiced($query)
    {
        return $query->whereNull('invoice_id');
    }

    /**
     * Scope a query to only include appointments that have been converted to invoices.
     */
    public function scopeInvoiced($query)
    {
        return $query->whereNotNull('invoice_id');
    }

    /**
     * Determine if the appointment can be converted to an invoice.
     *
     * @return bool
     */
    public function canConvertToInvoice()
    {
        // Check if it's already converted to an invoice
        if ($this->invoice_id) {
            return false;
        }

        // Check if it's in a status that can be converted
        if (!in_array($this->status, ['Approved', 'Completed'])) {
            return false;
        }

        // Check if at least one service has a staff assigned
        foreach ($this->services as $service) {
            if ($service->pivot->staff_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the total estimated duration of all services in the appointment.
     *
     * @return int Duration in minutes
     */
    public function getTotalDuration()
    {
        return $this->services->sum('timeEstimate');
    }

    /**
     * Get the estimated end time of the appointment.
     *
     * @return string The end time in H:i format
     */
    public function getEndTime()
    {
        $startTime = \Carbon\Carbon::parse($this->appointment_time);
        $endTime = $startTime->addMinutes($this->getTotalDuration());

        return $endTime->format('H:i');
    }
}
