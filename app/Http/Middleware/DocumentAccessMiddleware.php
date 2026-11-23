<?php

namespace App\Http\Middleware;

use App\Models\Accident;
use App\Services\Regional\RegionalScopeService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentAccessMiddleware
{
    /**
     * Peta prefix rute dokumen ke nama tabel database untuk resolusi otomatis accident_id & proteksi Cross-Accident IDOR.
     *
     * @var array<string, string>
     */
    protected const ROUTE_PREFIX_TABLE_MAP = [
        'berita-acara-serah-terima-tersangka-dan-barang-bukti-document' => 'doc.berita_acara_serah_terima_tersangka_documents',
        'undangan-gelar-perkara-document'                               => 'doc.undangan_gelar_perkara_documents',
        'surat-kuasa-document'                                          => 'doc.surat_kuasa_documents',
        'surat-permintaan-blokir-rekening-bank-document'                => 'doc.surat_permintaan_blokir_rekening_bank_documents',
        'surat-penunjukan-penasihat-hukum-document'                     => 'doc.surat_penunjukan_penasihat_hukum_documents',
        'surat-permintaan-buka-blokir-rekening-bank-document'           => 'doc.surat_permintaan_buka_blokir_rekening_bank_documents',
        'daftar-pencarian-barang-document'                              => 'doc.daftar_pencarian_barang_documents',
        'surat-permintaan-visum-document'                               => 'doc.surat_permintaan_visum_documents',
        'surat-pencabutan-dpb-document'                                 => 'doc.surat_pencabutan_dpb_documents',
        'daftar-saksi-document'                                         => 'doc.daftar_saksi_documents',
        'daftar-ahli-document'                                          => 'doc.daftar_ahli_documents',
        'daftar-tersangka-document'                                     => 'doc.daftar_tersangka_documents',
        'resume-document'                                               => 'doc.resume_documents',
        'surat-perintah-penangkapan-document'                           => 'doc.surat_perintah_penangkapan_documents',
        'surat-daftar-pencarian-orang-document'                         => 'doc.surat_daftar_pencarian_orang_documents',
        'surat-perintah-tugas-penangkapan-document'                     => 'doc.surat_perintah_tugas_penangkapan_documents',
        'surat-perintah-penggeledahan-rumah-document'                   => 'doc.surat_perintah_penggeledahan_rumah_documents',
        'surat-panggilan-saksi-document'                                => 'doc.surat_panggilan_saksi_documents',
        'rencana-penyidikan-document'                                   => 'doc.rencana_penyidikan_documents',
        'surat-panggilan-tersangka-document'                            => 'doc.surat_panggilan_tersangka_documents',
        'surat-permintaan-keterangan-ahli-document'                     => 'doc.surat_permintaan_keterangan_ahli_documents',
        'berita-acara-pemeriksaan-saksi-document'                       => 'doc.berita_acara_pemeriksaan_saksi_documents',
        'berita-acara-pemeriksaan-ahli-document'                        => 'doc.berita_acara_pemeriksaan_ahli_documents',
        'berita-acara-pemeriksaan-tersangka-document'                   => 'doc.berita_acara_pemeriksaan_tersangka_documents',
        'surat-perintah-penggeledahan-angkutan-document'                => 'doc.surat_perintah_penggeledahan_angkutan_documents',
        'surat-perintah-penggeledahan-badan-document'                   => 'doc.surat_perintah_penggeledahan_badan_documents',
        'surat-perintah-penyitaan-document'                             => 'doc.surat_perintah_penyitaan_documents',
        'surat-perintah-pengembalian-barang-bukti-document'             => 'doc.surat_perintah_pengembalian_barang_bukti_documents',
        'surat-ketetapan-penghentian-penyelidikan-document'             => 'doc.surat_ketetapan_penghentian_penyelidikan_documents',
        'surat-ketetapan-penghentian-penyidikan-document'               => 'doc.surat_ketetapan_penghentian_penyidikan_documents',
        'surat-perintah-penghentian-penyelidikan-document'              => 'doc.surat_perintah_penghentian_penyelidikan_documents',
        'surat-perintah-penghentian-penyidikan-document'                => 'doc.surat_perintah_penghentian_penyidikan_documents',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $rawAccidentId = $request->query('accident_id')
            ?: ($request->input('accident_id')
            ?: ($request->route('accident_id') ?: null));

        $routeDocId = $request->route('id');
        $resolvedDocAccidentId = null;

        if (!empty($routeDocId) && is_string($routeDocId) && Str::isUuid($routeDocId)) {
            $resolvedDocAccidentId = $this->resolveAccidentIdFromRouteDocument($request, $routeDocId);
        }

        // Jika accident_id belum dikirim di query/input tetapi ada {id} dokumen, gunakan accident_id milik dokumen
        if (empty($rawAccidentId) && !empty($resolvedDocAccidentId)) {
            $rawAccidentId = $resolvedDocAccidentId;
            $request->merge(['accident_id' => $resolvedDocAccidentId]);
        }

        $accidentId = is_string($rawAccidentId) ? trim($rawAccidentId) : null;

        if (empty($accidentId)) {
            return redirect()->route('produktivitas')->with('error', 'Parameter ID Kecelakaan tidak ditemukan');
        }

        if (!Str::isUuid($accidentId)) {
            return redirect()->route('produktivitas')->with('error', 'Parameter ID Kecelakaan tidak valid');
        }

        // Proteksi Cross-Accident IDOR: jika dokumen {id} ditemukan dan memiliki accident_id berbeda dari parameter request
        if (!empty($resolvedDocAccidentId) && $resolvedDocAccidentId !== $accidentId) {
            return redirect()->route('produktivitas')->with('error', 'Dokumen tidak sesuai dengan ID Perkara Kecelakaan');
        }

        try {
            $accident = Accident::with('polres')->where('id', $accidentId)->first();
            if (!$accident) {
                return redirect()->route('produktivitas')->with('error', 'Data Kecelakaan tidak ditemukan');
            }

            $user = $request->user();
            if ($user && !RegionalScopeService::authorizeAccident($user, $accident)) {
                return redirect()->route('produktivitas')->with('error', 'Anda tidak memiliki hak akses ke berkas perkara Satker ini');
            }

            $request->attributes->set('authorized_accident', $accident);
        } catch (\Throwable $e) {
            return redirect()->route('produktivitas')->with('error', 'Data Kecelakaan tidak ditemukan');
        }

        return $next($request);
    }

    /**
     * Resolusi accident_id dari tabel dokumen berdasarkan segmen URL dan parameter {id}.
     */
    protected function resolveAccidentIdFromRouteDocument(Request $request, string $docId): ?string
    {
        $segments = $request->segments();
        // Format URL: /document/{doc-prefix}/{id}/...
        $docPrefix = $segments[1] ?? null;
        if (empty($docPrefix) || !isset(self::ROUTE_PREFIX_TABLE_MAP[$docPrefix])) {
            return null;
        }

        $table = self::ROUTE_PREFIX_TABLE_MAP[$docPrefix];

        try {
            $accidentId = DB::table($table)->where('id', $docId)->value('accident_id');
            return is_string($accidentId) && $accidentId !== '' ? $accidentId : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
