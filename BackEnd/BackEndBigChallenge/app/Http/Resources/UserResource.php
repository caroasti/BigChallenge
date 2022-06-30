<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User **/
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->getRoleNames()[0],
            'patientInformation' => new PatientInformationResource($this->whenLoaded('patientInformation')),
            'doctorInformation' => new DoctorInformationResource($this->whenLoaded('doctorInformation')),
        ];
    }
}
