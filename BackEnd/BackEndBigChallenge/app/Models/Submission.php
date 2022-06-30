<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/** @method static Builder patientListSubmissions() */
/** @method static Builder doctorListSubmissions() */
class Submission extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'inProgress';
    const STATUS_DONE = 'done';

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when(
            $filters['state'] ?? false,
            fn ($query, $state) => $query
                ->where('state', $state)
        );
    }

    public static function scopePatientListSubmissions($query)
    {
        return $query->where('patient_id', Auth::user()->id);
    }

    public static function scopeDoctorListSubmissions($query)
    {
        return $query->where('doctor_id', Auth::user()->id);
    }
}
