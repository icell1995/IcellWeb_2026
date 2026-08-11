<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Doc\SuratPemberitahuanPerkembanganHasilPenyidikanDocument\SuratPemberitahuanPerkembanganHasilPenyidikanDocument;
use App\Models\ReportingPerson;

class CekSP2HPController extends Controller
{
    /**
     * Validasi dan mendapatkan data SP2HP berdasarkan nomor LP dan nomor identitas
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCekSP2HP(Request $request)
    {
        try {
        Log::info('CekSP2HP Request', ['nomor_lp' => $request->nomor_lp, 'nomor_identitas' => $request->nomor_identitas]);
        
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nomor_lp' => 'required|string',
            'nomor_identitas' => 'required|string'
        ], [
            'nomor_lp.required' => 'Nomor LP wajib diisi',
            'nomor_identitas.required' => 'Nomor identitas wajib diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $nomorLp = $request->nomor_lp;
        $nomorIdentitas = $request->nomor_identitas;

        // Cari data SP2HP berdasarkan nomor LP
        $sp2hp = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::with([
            'accident.polres.polda', 
            'officers.rank',
            'officers.position',
            'officers.police'
        ])
            ->where('nomor_lp', $nomorLp)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$sp2hp) {
            Log::warning('SP2HP not found', ['nomor_lp' => $nomorLp]);
            return response()->json([
                'success' => false,
                'message' => 'Nomor LP tidak ditemukan'
            ], 404);
        }

        Log::info('SP2HP found', ['id' => $sp2hp->id, 'tipe' => $sp2hp->tipe_sp2hp]);

        // Ambil semua penerima yang terkait dengan SP2HP ini
        $allPenerima = ReportingPerson::where('accident_id', $sp2hp->accident_id)
            ->where('class', 'SP2HP_PENERIMA')
            ->with(['identityType', 'gender', 'nationality', 'ethnic', 'religion', 'education', 'job', 'maritalStatus'])
            ->get();

        // Cek apakah nomor identitas yang diinput ada dalam daftar penerima
        $reportingPerson = $allPenerima->firstWhere('identity_number', $nomorIdentitas);

        Log::info('Penerima check', ['found' => $reportingPerson ? 'yes' : 'no', 'count' => $allPenerima->count()]);

        if (!$reportingPerson) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor identitas tidak ditemukan dalam daftar penerima SP2HP ini'
            ], 404);
        }

        Log::info('Building response data');

        // Ambil data penyidik dari tabel officers
        $penyidikList = [];
        
        // Untuk tipe A2-A7, ambil penyidik dari dokumen A1
        if (in_array($sp2hp->tipe_sp2hp, ['A2', 'A3', 'A4', 'A5', 'A6', 'A7'])) {
            // Cari dokumen A1 untuk accident yang sama
            $latestA1 = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::with([
                'officers.rank',
                'officers.position',
                'officers.police'
            ])
                ->where('accident_id', $sp2hp->accident_id)
                ->where('tipe_sp2hp', 'A1')
                ->whereNull('deleted_at')
                ->latest('created_at')
                ->first();
            
            if ($latestA1 && $latestA1->officers && $latestA1->officers->count() > 0) {
                // Ambil dari dokumen A1
                foreach ($latestA1->officers->where('class', 'INVESTIGATOR') as $officer) {
                    $penyidikList[] = [
                        'nama' => $officer->name,
                        'pangkat' => $officer->rank ? $officer->rank->name : ($officer->rank_id ?? '-'),
                        'nrp' => $officer->register_number,
                        'telp' => $officer->phone_number,
                        'unit' => $officer->police ? $officer->police->full_name : ($officer->police_id ?? '-'),
                        'email' => $officer->email,
                    ];
                }
            }
        } elseif ($sp2hp->officers && $sp2hp->officers->count() > 0) {
            // Untuk A1, ambil langsung dari document ini
            foreach ($sp2hp->officers->where('class', 'INVESTIGATOR') as $officer) {
                $penyidikList[] = [
                    'nama' => $officer->name,
                    'pangkat' => $officer->rank ? $officer->rank->name : ($officer->rank_id ?? '-'),
                    'nrp' => $officer->register_number,
                    'telp' => $officer->phone_number,
                    'unit' => $officer->police ? $officer->police->full_name : ($officer->police_id ?? '-'),
                    'email' => $officer->email,
                ];
            }
        }
        
        // Fallback ke type_specific_data jika masih kosong
        if (empty($penyidikList) && $sp2hp->type_specific_data && isset($sp2hp->type_specific_data['penyidik_list'])) {
            $penyidikList = $sp2hp->type_specific_data['penyidik_list'];
        }

        // Format data penerima
        $penerimaList = [];
        foreach ($allPenerima as $index => $penerima) {
            $isPelapor = ($index === 0); // Penerima pertama adalah pelapor
            
            $penerimaList[] = [
                'peran' => $isPelapor ? 'PELAPOR & PENERIMA' : 'PENERIMA',
                'jenis_identitas' => optional($penerima->identityType)->name,
                'nomor_identitas' => $penerima->identity_number,
                'nama' => $penerima->name,
                'nama_alias' => $penerima->alias_name,
                'tempat_lahir' => $penerima->birth_place,
                'tanggal_lahir' => $penerima->birth_date ? \Carbon\Carbon::parse($penerima->birth_date)->format('d-m-Y') : null,
                'jenis_kelamin' => optional($penerima->gender)->name,
                'nama_ayah' => $penerima->father_name,
                'nama_ibu' => $penerima->mother_name,
                'kewarganegaraan' => optional($penerima->nationality)->name,
                'suku' => optional($penerima->ethnic)->name,
                'agama' => optional($penerima->religion)->name,
                'pendidikan' => optional($penerima->education)->name,
                'pekerjaan' => optional($penerima->job)->name,
                'status_perkawinan' => optional($penerima->maritalStatus)->name,
                'nomor_telepon' => $penerima->phone_number,
                'email' => $penerima->email,
                'alamat' => $penerima->address,
            ];
        }

        // Decode type_specific_data for A2 fields
        $typeSpecificData = is_string($sp2hp->type_specific_data) 
            ? json_decode($sp2hp->type_specific_data, true) 
            : $sp2hp->type_specific_data;
        if (!is_array($typeSpecificData)) {
            $typeSpecificData = [];
        }

        // --- ADDED: Fetch historical data from ALL previous SP2HP types ---
        $historicalData = [
            'a1' => [],
            'a2' => [],
            'a3' => [],
            'a4' => [],
            'a5' => [],
            'a6' => [],
            'a7' => [],
            'a4_tindakan_list' => []
        ];

        if ($sp2hp->accident_id) {
            // Ambil SEMUA dokumen SP2HP terdahulu untuk accident yang sama (KECUALI dokumen saat ini)
            $historyDocs = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::where('accident_id', $sp2hp->accident_id)
                ->where('id', '!=', $sp2hp->id) // Exclude dokumen yang sedang diquery
                ->whereIn('tipe_sp2hp', ['A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7'])
                ->orderBy('created_at', 'desc') // Ambil yang terbaru jika ada duplikat tipe
                ->get();
            
            Log::info('Historical docs found', [
                'count' => $historyDocs->count(),
                'types' => $historyDocs->pluck('tipe_sp2hp')->toArray(),
                'current_doc_id' => $sp2hp->id,
                'accident_id' => $sp2hp->accident_id
            ]);
            
            foreach ($historyDocs as $doc) {
                $docTypeData = is_string($doc->type_specific_data) 
                    ? json_decode($doc->type_specific_data, true) 
                    : $doc->type_specific_data;
                
                if (!is_array($docTypeData)) $docTypeData = [];

                // Simpan data jika belum ada (karena order by desc, yang pertama ketemu adalah yang terbaru)
                $tipe = strtolower($doc->tipe_sp2hp);
                
                if ($doc->tipe_sp2hp == 'A1' && empty($historicalData['a1'])) {
                    $historicalData['a1'] = array_merge($docTypeData, [
                        'a1_nomor_surat' => $doc->nomor_surat,
                        'a1_tanggal_surat' => $doc->tanggal_surat,
                        'a1_created_at' => $doc->created_at,
                    ]);
                } elseif ($doc->tipe_sp2hp == 'A2' && empty($historicalData['a2'])) {
                    Log::info('Found A2 document', [
                        'id' => $doc->id,
                        'a2_fakta_lidik' => $doc->a2_fakta_lidik,
                        'a2_alasan' => $doc->a2_alasan
                    ]);
                    $historicalData['a2'] = array_merge($docTypeData, [
                        'a2_fakta_lidik' => $doc->a2_fakta_lidik,
                        'a2_alasan' => $doc->a2_alasan,
                        'a2_rujukan_a1' => $doc->a2_rujukan_a1,
                        'a2_tanggal_a1' => $doc->a2_tanggal_a1,
                        'a2_created_at' => $doc->created_at,
                    ]);
                } elseif ($doc->tipe_sp2hp == 'A3' && empty($historicalData['a3'])) {
                    $historicalData['a3'] = array_merge($docTypeData, [
                        'a3_sprin_sidik' => $doc->a3_sprin_sidik,
                        'a3_tanggal_sprin' => $doc->a3_tanggal_sprin,
                        'a3_nomor' => $doc->a3_nomor,
                        'a3_tanggal_spdp' => $doc->a3_tanggal_spdp,
                        'a3_rujukan_a1' => $doc->a3_rujukan_a1,
                        'a3_tanggal_a1' => $doc->a3_tanggal_a1,
                        'a3_created_at' => $doc->created_at,
                    ]);
                } elseif ($doc->tipe_sp2hp == 'A4' && empty($historicalData['a4'])) {
                    $historicalData['a4'] = array_merge($docTypeData, [
                        'a4_rujukan_a1' => $doc->a4_rujukan_a1,
                        'a4_tanggal_a1' => $doc->a4_tanggal_a1,
                        'a4_hambatan' => $doc->a4_hambatan,
                        'a4_rencana' => $doc->a4_rencana,
                        'a4_created_at' => $doc->created_at,
                    ]);
                    $historicalData['a4_tindakan_list'] = $doc->a4_tindakan_list;
                    $historicalData['a4_barang_bukti'] = $doc->barang_bukti;
                    $historicalData['a4_catatan'] = $doc->catatan;
                } elseif ($doc->tipe_sp2hp == 'A5' && empty($historicalData['a5'])) {
                    $historicalData['a5'] = array_merge($docTypeData, [
                        'a5_sprin_sidik' => $doc->a5_sprin_sidik,
                        'a5_sp2hp_terakhir' => $doc->a5_sp2hp_terakhir,
                        'a5_alasan_sp3' => $doc->a5_alasan_sp3,
                        'a5_keterangan_sp3' => $doc->a5_keterangan_sp3,
                        'a5_created_at' => $doc->created_at,
                    ]);
                } elseif ($doc->tipe_sp2hp == 'A6' && empty($historicalData['a6'])) {
                    $historicalData['a6'] = array_merge($docTypeData, [
                        'a6_sp2hp_terakhir' => $doc->a6_sp2hp_terakhir,
                        'a6_nama_tersangka' => $doc->a6_nama_tersangka,
                        'a6_nomor_kirim_berkas' => $doc->a6_nomor_kirim_berkas,
                        'a6_tanggal_kirim' => $doc->a6_tanggal_kirim,
                        'a6_tujuan_kejaksaan' => $doc->a6_tujuan_kejaksaan,
                        'a6_created_at' => $doc->created_at,
                    ]);
                } elseif ($doc->tipe_sp2hp == 'A7' && empty($historicalData['a7'])) {
                    $historicalData['a7'] = array_merge($docTypeData, [
                        'a7_nama_tersangka' => $doc->a7_nama_tersangka,
                        'a7_rujukan_tahap1' => $doc->a7_rujukan_tahap1,
                        'a7_nomor_p21' => $doc->a7_nomor_p21,
                        'a7_tanggal_p21' => $doc->a7_tanggal_p21,
                        'a7_nomor_kirim_tahap2' => $doc->a7_nomor_kirim_tahap2,
                        'a7_tanggal_serah_tahap2' => $doc->a7_tanggal_serah_tahap2,
                        'a7_tujuan_kejaksaan' => $doc->a7_tujuan_kejaksaan,
                        'a7_created_at' => $doc->created_at,
                    ]);
                }
            }
        }
        // -----------------------------------------------------------

        // Ambil data signatory (pejabat penandatangan)
        $signatoryData = null;
        $signatory = null;
        
        // Untuk tipe A2-A7, ambil signatory dari dokumen A1
        if (in_array($sp2hp->tipe_sp2hp, ['A2', 'A3', 'A4', 'A5', 'A6', 'A7'])) {
            // Cari dokumen A1 untuk accident yang sama
            $latestA1 = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::with([
                'officers.rank',
                'officers.position',
                'officers.police'
            ])
                ->where('accident_id', $sp2hp->accident_id)
                ->where('tipe_sp2hp', 'A1')
                ->whereNull('deleted_at')
                ->latest('created_at')
                ->first();
            
            if ($latestA1 && $latestA1->officers && $latestA1->officers->count() > 0) {
                $signatory = $latestA1->officers->where('class', 'SIGNATORY')->first();
            }
        } else {
            // Untuk A1, ambil langsung dari document ini
            if ($sp2hp->officers && $sp2hp->officers->count() > 0) {
                $signatory = $sp2hp->officers->where('class', 'SIGNATORY')->first();
            }
        }
        
        if (isset($signatory) && $signatory) {
            $signatoryData = [
                'nama' => $signatory->name,
                'pangkat' => $signatory->rank ? $signatory->rank->name : ($signatory->rank_id ?? '-'),
                'nrp' => $signatory->register_number,
                'jabatan' => $signatory->position ? $signatory->position->name : ($signatory->position_id ?? '-'),
                'telp' => $signatory->phone_number,
                'email' => $signatory->email,
                'unit' => $signatory->police ? $signatory->police->full_name : ($signatory->police_id ?? '-'),
            ];
        }

        // Ambil data tembusan (carbon copies)
        $tembusanList = $typeSpecificData['carbon_copies'] ?? [];

        // Ambil data kendaraan dari berbagai sumber
        $kendaraanDetail = null;
        
        // 1. Cek dari kendaraan_data (field langsung di tabel)
        if (!empty($sp2hp->kendaraan_data)) {
            $kendaraanDetail = is_string($sp2hp->kendaraan_data) 
                ? json_decode($sp2hp->kendaraan_data, true) 
                : $sp2hp->kendaraan_data;
        }
        
        // 2. Fallback ke type_specific_data jika kosong
        if (empty($kendaraanDetail) && isset($typeSpecificData['kendaraan_detail'])) {
            $kendaraanDetail = $typeSpecificData['kendaraan_detail'];
        }
        
        // 3. Untuk A5, A6, A7 - ambil dari dokumen A4 jika masih kosong
        if (empty($kendaraanDetail) && in_array($sp2hp->tipe_sp2hp, ['A5', 'A6', 'A7'])) {
            $parentA4 = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::where('accident_id', $sp2hp->accident_id)
                ->where('tipe_sp2hp', 'A4')
                ->whereNull('deleted_at')
                ->latest('created_at')
                ->first();
            
            if ($parentA4) {
                if (!empty($parentA4->kendaraan_data)) {
                    $kendaraanDetail = is_string($parentA4->kendaraan_data) 
                        ? json_decode($parentA4->kendaraan_data, true) 
                        : $parentA4->kendaraan_data;
                }
            }
        }

        // Tentukan nomor_surat, tanggal_surat, tempat_surat berdasarkan tipe
        $nomorSurat = $sp2hp->nomor_surat;
        $tanggalSurat = $sp2hp->tanggal_surat;
        $tempatSurat = $sp2hp->tempat_surat;
        
        // Untuk A2-A3, ambil dari dokumen A1
        if (in_array($sp2hp->tipe_sp2hp, ['A2', 'A3'])) {
            $parentA1 = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::where('accident_id', $sp2hp->accident_id)
                ->where('tipe_sp2hp', 'A1')
                ->whereNull('deleted_at')
                ->latest('created_at')
                ->first();
            
            if ($parentA1) {
                $nomorSurat = $parentA1->nomor_surat;
                $tanggalSurat = $parentA1->tanggal_surat;
                $tempatSurat = $parentA1->tempat_surat;
            }
        }
        
        // Untuk A5-A7, ambil dari dokumen A4
        if (in_array($sp2hp->tipe_sp2hp, ['A5', 'A6', 'A7'])) {
            $parentA4 = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::where('accident_id', $sp2hp->accident_id)
                ->where('tipe_sp2hp', 'A4')
                ->whereNull('deleted_at')
                ->latest('created_at')
                ->first();
            
            if ($parentA4) {
                $nomorSurat = $parentA4->nomor_surat;
                $tanggalSurat = $parentA4->tanggal_surat;
                $tempatSurat = $parentA4->tempat_surat;
            }
        }

        // Format response dari data SP2HP
        $response = [
            'success' => true,
            'message' => 'Data SP2HP ditemukan',
            'data' => [
                // Informasi Surat
                'nomor_surat' => $nomorSurat,
                'tanggal_surat' => $tanggalSurat ? \Carbon\Carbon::parse($tanggalSurat)->format('d-m-Y') : null,
                'tempat_surat' => $tempatSurat,
                'tipe_sp2hp' => $sp2hp->tipe_sp2hp,
                'tingkat_kasus' => $sp2hp->tingkat_kasus,
                
                // Informasi LP
                'nomor_lp' => $sp2hp->nomor_lp,
                'tanggal_lp' => $sp2hp->tanggal_lp ? \Carbon\Carbon::parse($sp2hp->tanggal_lp)->format('d-m-Y') : null,
                
                // Informasi Pelapor
                'pelapor_nama' => $sp2hp->pelapor_nama,
                'pelapor_alamat' => $sp2hp->pelapor_alamat,
                
                // Informasi Perkara
                'pasal_diduga' => $sp2hp->pasal_diduga,
                'barang_bukti' => in_array($sp2hp->tipe_sp2hp, ['A5', 'A6', 'A7']) 
                    ? ($historicalData['a4_barang_bukti'] ?? $sp2hp->barang_bukti) 
                    : $sp2hp->barang_bukti,
                'catatan' => in_array($sp2hp->tipe_sp2hp, ['A5', 'A6', 'A7']) 
                    ? ($historicalData['a4_catatan'] ?? $sp2hp->catatan) 
                    : $sp2hp->catatan,

                // A1: Tanggal pembuatan
                'a1_created_at' => isset($historicalData['a1']['a1_created_at']) ? \Carbon\Carbon::parse($historicalData['a1']['a1_created_at'])->format('d-m-Y H:i:s') : ($sp2hp->tipe_sp2hp === 'A1' ? \Carbon\Carbon::parse($sp2hp->created_at)->format('d-m-Y H:i:s') : null),

                // A2: Data Khusus (Penetapan Status Perkara) - ambil dari type_specific_data (sudah berisi data inherited)
                'a2_fakta_lidik' => $typeSpecificData['a2_fakta_lidik'] ?? null,
                'a2_alasan' => $typeSpecificData['a2_alasan'] ?? null,
                'a2_created_at' => isset($historicalData['a2']['a2_created_at']) ? \Carbon\Carbon::parse($historicalData['a2']['a2_created_at'])->format('d-m-Y H:i:s') : null,
                
                // A3: Data Khusus (Perkembangan Hasil Penyidikan) - ambil dari type_specific_data
                'a3_sprin_sidik' => $typeSpecificData['a3_sprin_sidik'] ?? null,
                'a3_tanggal_sprin' => $typeSpecificData['a3_tanggal_sprin'] ?? null,
                'a3_nomor_spdp' => $typeSpecificData['a3_nomor_spdp'] ?? null,
                'a3_tanggal_spdp' => $typeSpecificData['a3_tanggal_spdp'] ?? null,
                'a3_created_at' => isset($historicalData['a3']['a3_created_at']) ? \Carbon\Carbon::parse($historicalData['a3']['a3_created_at'])->format('d-m-Y H:i:s') : null,

                // A4: Tindakan Yang Telah Dilakukan (array of objects with nama + keterangan)
                'tindakan_dilakukan' => $typeSpecificData['a4_tindakan_list'] ?? $sp2hp->a4_tindakan_list ?? [],
                'a4_hambatan' => $typeSpecificData['a4_hambatan'] ?? null,
                'a4_rencana' => $typeSpecificData['a4_rencana'] ?? null,
                'a4_created_at' => isset($historicalData['a4']['a4_created_at']) ? \Carbon\Carbon::parse($historicalData['a4']['a4_created_at'])->format('d-m-Y H:i:s') : null,
                
                // A5: Data Khusus (Penghentian Penyidikan / SP3) - ambil dari type_specific_data
                'a5_sprin_sidik' => $typeSpecificData['a5_sprin_sidik'] ?? null,
                'a5_sp2hp_terakhir' => $typeSpecificData['a5_sp2hp_terakhir'] ?? null,
                'a5_alasan_sp3' => $typeSpecificData['a5_alasan_sp3'] ?? null,
                'a5_keterangan_sp3' => $typeSpecificData['a5_keterangan_sp3'] ?? null,
                'a5_created_at' => isset($historicalData['a5']['a5_created_at']) ? \Carbon\Carbon::parse($historicalData['a5']['a5_created_at'])->format('d-m-Y H:i:s') : null,
                
                // A6: Data Khusus (Pelimpahan Berkas Perkara Tahap 1) - ambil dari type_specific_data
                'a6_sp2hp_terakhir' => $typeSpecificData['a6_sp2hp_terakhir'] ?? null,
                'a6_nama_tersangka' => $typeSpecificData['a6_nama_tersangka'] ?? null,
                'a6_nomor_kirim_berkas' => $typeSpecificData['a6_nomor_kirim_berkas'] ?? null,
                'a6_tanggal_kirim' => $typeSpecificData['a6_tanggal_kirim'] ?? null,
                'a6_tujuan_kejaksaan' => $typeSpecificData['a6_tujuan_kejaksaan_name'] ?? $typeSpecificData['a6_tujuan_kejaksaan'] ?? null,
                'a6_created_at' => isset($historicalData['a6']['a6_created_at']) ? \Carbon\Carbon::parse($historicalData['a6']['a6_created_at'])->format('d-m-Y H:i:s') : null,
                
                // A7: Data Khusus (Pelimpahan Berkas Perkara Tahap 2) - ambil dari type_specific_data
                'a7_nama_tersangka' => $typeSpecificData['a7_nama_tersangka'] ?? null,
                'a7_rujukan_tahap1' => $typeSpecificData['a7_rujukan_tahap1'] ?? null,
                'a7_nomor_p21' => $typeSpecificData['a7_nomor_p21'] ?? null,
                'a7_tanggal_p21' => $typeSpecificData['a7_tanggal_p21'] ?? null,
                'a7_nomor_kirim_tahap2' => $typeSpecificData['a7_nomor_kirim_tahap2'] ?? null,
                'a7_tanggal_serah_tahap2' => $typeSpecificData['a7_tanggal_serah_tahap2'] ?? null,
                'a7_tujuan_kejaksaan' => $typeSpecificData['a7_tujuan_kejaksaan_name'] ?? $typeSpecificData['a7_tujuan_kejaksaan'] ?? null,
                'a7_created_at' => isset($historicalData['a7']['a7_created_at']) ? \Carbon\Carbon::parse($historicalData['a7']['a7_created_at'])->format('d-m-Y H:i:s') : null, 
                
                
                
                // Detail Kendaraan (untuk tipe A4-A7)
                'kendaraan_detail' => $kendaraanDetail,
                
                // Informasi Penyidik (array - bisa lebih dari 1)
                'penyidik' => $penyidikList,
                
                // Informasi Pejabat Penandatangan
                'pejabat_penandatangan' => $signatoryData,
                
                // Informasi Penerima (array - semua penerima SP2HP)
                'daftar_penerima' => $penerimaList,
                
                // Informasi Satuan Kerja
                'satuan_kerja' => [
                    'polres' => optional(optional($sp2hp->accident)->polres)->name,
                    'polda' => optional(optional(optional($sp2hp->accident)->polres)->polda)->name,
                ],
                
                // Tembusan (Carbon Copies)
                'tembusan' => $tembusanList,

                // Status & Timeline
                'status' => $sp2hp->status,
                'dibuat_pada' => $sp2hp->created_at ? \Carbon\Carbon::parse($sp2hp->created_at)->format('d-m-Y H:i:s') : null,
            ]
        ];

        return response()->json($response, 200);
        
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('CekSP2HP API Error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }
}