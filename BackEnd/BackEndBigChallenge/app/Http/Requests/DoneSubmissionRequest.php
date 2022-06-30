<?php

namespace App\Http\Requests;

use App\Models\Submission;
use Illuminate\Foundation\Http\FormRequest;

class DoneSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Submission $submission */
        $submission = $this->route('submission');

        return isset($submission->doctor_id);
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
