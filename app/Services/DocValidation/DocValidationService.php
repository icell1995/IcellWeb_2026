<?php
namespace App\Services\DocValidation;

use App\Models\Log\CaseDocumentValidation;

class DocValidationService
{
    public function logging(array $data)
    {
        CaseDocumentValidation::updateOrCreate([
            'document_id' => $data['document_id']
        ], 
            $data
        );
        
        return true;
    }
}