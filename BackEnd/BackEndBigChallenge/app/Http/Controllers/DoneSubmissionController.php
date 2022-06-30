<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoneSubmissionRequest;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DoneSubmissionController extends Controller
{
    public function __invoke(DoneSubmissionRequest $request, Submission $submission): JsonResponse
    {
        $submission->update(['doctor_id' => Auth::user()->id, 'state' => Submission::STATUS_DONE]);

        return response()->json([
            'status' => 200,
            'message' => 'Doctor completed the submission successfully',
        ]);
    }
}