<?php

namespace App\Rules;

use App\Services\MediaUploadService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class AcceptedImageUpload implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail("The {$attribute} field must be a file.");

            return;
        }

        if (!MediaUploadService::isAcceptedImageUpload($value)) {
            $fail("The {$attribute} field must be a supported image file.");
        }
    }
}
