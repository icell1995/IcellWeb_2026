<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SuratPerintahPenahananDocumentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'accident_id' => 'required|uuid|exists:accidents,id',

            'surat_perintah_penyidikan_document_id' => 'nullable|uuid',
            'surat_ketetapan_penetapan_tersangka_id' => 'nullable|uuid',

            'document_number' => 'required|string|max:100',
            'document_date'   => 'required|date',

            'officer_id'     => 'required|exists:officers,id',
            'signatory_id'   => 'required|exists:officers,id',
            'tersangka_id'   => 'required|exists:suspects,id',

            'personnel'      => 'nullable|array',
            'personnel.*'    => 'exists:officers,id',

            'jenis_penahanan' => 'required|string|max:150',

            'lokasi_penahanan' => 'nullable|string',
            'cabang_penahanan' => 'nullable|string',

            'alamat_penahanan' => 'nullable|string',
            'kota_penahanan'   => 'nullable|string',

            'released_at' => 'nullable|date',
        ];
    }

    public function validatedData(): array
    {
        $data = $this->validated();

        $data['is_active'] = true;
        $data['is_legacy'] = false;

        return $data;
    }
}
