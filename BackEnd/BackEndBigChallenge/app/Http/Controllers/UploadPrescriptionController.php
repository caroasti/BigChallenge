<?php

namespace App\Http\Controllers;

use App\Events\UploadPrescription;
use App\Http\Requests\UploadPrescriptionRequest;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str as SupportStr;

class UploadPrescriptionController
{
    public function __invoke(UploadPrescriptionRequest $request, Submission $submission): JsonResponse
    {
        $file = $request->file('prescriptions');
        // The fileName will be uuid, that is a unique identifier of the files.
        $uuid = (string) SupportStr::uuid();
        $folder = config('filesystems.disks.do.folder');
        Storage::temporaryUrl(
            "{$folder}/{$uuid}",
            now()->addWeek()
        );

        // Now I have to update the submission
        $submission->prescriptions = $uuid;
        $submission->state = Submission::STATUS_DONE;
        $submission->save();

        event(new UploadPrescription($submission));

        return response()->json([
            'message' => 'File uploaded successfully',
            'uuid' => $uuid,
        ]);
    }
}
