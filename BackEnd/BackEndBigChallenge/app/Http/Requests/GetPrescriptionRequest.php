<?php

namespace App\Http\Requests;

use App\Models\Submission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class GetPrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Submission $submission */
        $submission = $this->route('submission');

        return ($submission->state == Submission::STATUS_DONE) && (($submission->patient_id == Auth::user()->id) || ($submission->doctor_id == Auth::user()->id));
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
