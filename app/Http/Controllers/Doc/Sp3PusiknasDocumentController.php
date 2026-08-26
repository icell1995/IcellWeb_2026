<?php

namespace App\Http\Controllers\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\PeopleNameHelper;

use App\Services\Doc\DocService;

use App\Models\SP3;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Lib\Ref;
use App\Models\Opt\Status;

use App\Traits\DocsOfficersTraits;

/**
 * Sp3PusiknasDocumentController
 *
 * Controller form web untuk SP3 (Surat Pemberitahuan Penghentian Penyidikan)
 * versi Pusiknas Bareskrim — sesuai skema SPPT-TI.
 *
 * Field SPPT-TI (identitas_dokumen):
 *   nomor, tanggal, nomor_spdp
 *
 * Field SPPT-TI (konten_dokumen):
 *   kode_alasan[] (array integer), pejabat_penandatangan[], daftar_terlapor_atau_tersangka[]
 *
 * Alur: menyimpan ke tabel sp3 yang sudah ada.
 */
class Sp3PusiknasDocumentController extends Controller
{
    protected $docService;

    use DocsOfficersTraits;

    // Master alasan penghentian perkara (sesuai referensi SPPT-TI)
    public static $masterAlasan = [
        1  => 'Tidak terdapat cukup bukti',
        2  => 'Peristiwa bukan merupakan tindak pidana',
        3  => 'Tersangka meninggal dunia',
        4  => 'Daluwarsa',
        5  => 'Ne bis in idem',
        6  => 'Tersangka tidak dapat dipertanggungjawabkan (Pasal 44 KUHP)',
        7  => 'Perkara diselesaikan di luar pengadilan',
        8  => 'Penerapan restorative justice',
    ];

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident   = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        // Ambil SPDP yang sudah diterbitkan
        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        // Tersangka di perkara ini
        $suspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->get();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);
        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()->active()->valid()
            ->orderBy('first_name')->get();

        $masterAlasan = self::$masterAlasan;

        return view('docs.sp3-pusiknas-document.create', compact(
            'accidentId',
            'accident',
            'spdpDocuments',
            'suspects',
            'authorizedSignatories',
            'masterAlasan'
        ));
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $accidentId = htmlspecialchars($request->accident_id);

        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // identitas_dokumen
        $noSp3      = htmlspecialchars($request->noSp3);
        $tanggalSp3 = htmlspecialchars($request->tanggalSp3);
        $noSpdp     = htmlspecialchars($request->noSpdp);

        // konten_dokumen
        $kodeAlasan  = $request->kode_alasan ?? [];   // array of int
        $signatoryId = htmlspecialchars($request->signatory);
        $suspects    = $request->suspects ?? [];

        // file upload
        $fileName = null;
        if ($request->hasFile('dokumen_digital')) {
            $file = $request->file('dokumen_digital');
            $fileName = time() . '_sp3_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path('documents/attachments/'), $fileName);
        }



        // Cek duplikat
        $exists = SP3::where('accident_id', $accidentId)->where('no_sp3', 'ILIKE', $noSp3)->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'SP3 dengan nomor ' . $noSp3 . ' sudah ada.');
        }

        DB::beginTransaction();
        try {
            // Simpan ke tabel sp3
            // Field kode_alasan disimpan sebagai JSON di kolom alasan
            $sp3 = SP3::create([
                'accident_id'               => $accidentId,
                'no_lp'                     => $request->noLp ?? null,
                'no_spdp'                   => $noSpdp,
                'no_sp3'                    => $noSp3,
                'no_surat_perintah_penyidikan' => '-',
                'tanggal_sp_dik'            => $tanggalSp3,
                'penerima_surat'            => '-',
                'klasifikasi'               => '-',
                'tanggal_berlaku'           => $tanggalSp3,
                // kode_alasan (array integer) disimpan sebagai JSON
                'alasan'                    => json_encode(array_map('intval', $kodeAlasan)),
                // Data penandatangan & tersangka tersimpan via messages
                'lampiran'                  => json_encode([
                    'signatory_id'     => $signatoryId,
                    'suspect_ids'      => $suspects,
                    'sumber'           => 'PUSIKNAS_FORM',
                    'kode_alasan_raw'  => $kodeAlasan,
                    'file_name'        => $fileName,
                ]),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function show($id)
    {
        $accidentId  = htmlspecialchars(request()->query('accident_id'));
        $sp3         = SP3::with(['accident', 'accident.polres', 'accident.polres.polda', 'accident.suspects'])->where('id', $id)->firstOrFail();
        $accident    = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();
        $masterAlasan = self::$masterAlasan;

        $kodeAlasan = json_decode($sp3->alasan, true) ?? [];
        $extraData  = json_decode($sp3->lampiran, true) ?? [];

        return view('docs.sp3-pusiknas-document.show', compact(
            'accidentId',
            'accident',
            'sp3',
            'masterAlasan',
            'kodeAlasan',
            'extraData'
        ));
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function edit($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $sp3        = SP3::where('id', $id)->firstOrFail();
        $accident   = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)->get();

        $suspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))->get();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);
        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()->active()->valid()
            ->orderBy('first_name')->get();

        $masterAlasan   = self::$masterAlasan;
        $kodeAlasan     = json_decode($sp3->alasan, true) ?? [];
        $extraData      = json_decode($sp3->lampiran, true) ?? [];

        return view('docs.sp3-pusiknas-document.edit', compact(
            'accidentId',
            'accident',
            'sp3',
            'spdpDocuments',
            'suspects',
            'authorizedSignatories',
            'masterAlasan',
            'kodeAlasan',
            'extraData'
        ));
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $accidentId = htmlspecialchars($request->accident_id);
        $validator  = $this->validateForm($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $sp3        = SP3::where('id', $id)->firstOrFail();
        $kodeAlasan = $request->kode_alasan ?? [];
        $extraData  = json_decode($sp3->lampiran, true) ?? [];

        // file upload
        $fileName = null;
        if ($request->hasFile('dokumen_digital')) {
            $file = $request->file('dokumen_digital');
            $fileName = time() . '_sp3_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path('documents/attachments/'), $fileName);
        }

        DB::beginTransaction();
        try {
            $sp3->update([
                'no_sp3'         => htmlspecialchars($request->noSp3),
                'no_spdp'        => htmlspecialchars($request->noSpdp),
                'tanggal_berlaku' => htmlspecialchars($request->tanggalSp3),
                'alasan'         => json_encode(array_map('intval', $kodeAlasan)),
                'lampiran'       => json_encode([
                    'signatory_id'    => htmlspecialchars($request->signatory),
                    'suspect_ids'     => $request->suspects ?? [],
                    'sumber'          => 'PUSIKNAS_FORM',
                    'kode_alasan_raw' => $kodeAlasan,
                    'file_name'       => $fileName ?? ($extraData['file_name'] ?? null),
                ]),
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengubah data: ' . $e->getMessage());
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────
    public function delete($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $sp3        = SP3::where('id', $id)->firstOrFail();

        DB::beginTransaction();
        try {
            $sp3->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus dokumen.');
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    // ─────────────────────────────────────────────
    // VALIDATE FORM (AJAX)
    // ─────────────────────────────────────────────
    public function validateRequestForm(Request $request)
    {
        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return response()->json([
                'code'    => '422',
                'success' => false,
                'errors'  => $validator->errors()->all(),
            ], 422);
        }
        return response()->json(['success' => true, 'message' => 'Data valid, dokumen siap disimpan.']);
    }

    // ─────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────
    private function validateForm(Request $request)
    {
        return Validator::make($request->all(), [
            'noSp3'      => 'required|string|max:255|min:3',
            'tanggalSp3' => 'required|date_format:Y-m-d',
            'noSpdp'     => 'required|string',
            'kode_alasan'   => 'required|array|min:1',
            'kode_alasan.*' => 'required|integer|min:1',
            'signatory'  => 'required',
            'dokumen_digital' => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'noSp3.required'         => 'Mohon mengisi Nomor SP3.',
            'tanggalSp3.required'    => 'Mohon mengisi Tanggal SP3.',
            'noSpdp.required'        => 'Mohon memilih/mengisi Nomor SPDP.',
            'kode_alasan.required'   => 'Mohon memilih minimal 1 Alasan Penghentian.',
            'kode_alasan.min'        => 'Mohon memilih minimal 1 Alasan Penghentian.',
            'signatory.required'     => 'Mohon mengisi Penandatangan.',
            'dokumen_digital.mimes'  => 'Format dokumen digital harus PDF.',
            'dokumen_digital.max'    => 'Ukuran dokumen digital maksimal 5MB.',
        ]);
    }
}
