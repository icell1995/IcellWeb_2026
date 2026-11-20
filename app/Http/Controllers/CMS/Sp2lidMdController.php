<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\AccidentResolution;
use App\Models\UploadSuratKetetapan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class Sp2lidMdController extends Controller
{
    /**
     * Tampilkan daftar LP dengan Selra SP2LID dan Korban MD > 0
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 15, 25, 50, 100]) ? $perPage : 10;

        $search = trim((string) $request->get('q', ''));
        $fileStatus = $request->get('file_status', 'all');
        $categoryLaka = $request->get('category_laka', 'all');
        $period = $request->get('period', 'this_year');
        $dateType = $request->get('date_type', 'report_date');
        $fromDate = $request->get('from');
        $toDate = $request->get('to');

        // Batas data: Hanya dari tahun lalu dan tahun ini
        $thisYear = Carbon::now()->year;
        $lastYear = Carbon::now()->subYear()->year;
        $minDate = Carbon::now()->subYear()->startOfYear(); // 1 Januari tahun lalu
        $minDateFormatted = $minDate->format('Y-m-d');

        // Handle preset periode
        if ($period === 'today') {
            $fromDate = Carbon::today()->format('Y-m-d');
            $toDate = Carbon::today()->format('Y-m-d');
            $periodDisplay = 'Hari Ini (' . Carbon::today()->locale('id')->translatedFormat('d F Y') . ')';
        } elseif ($period === 'this_week') {
            $fromDate = Carbon::now()->startOfWeek()->format('Y-m-d');
            $toDate = Carbon::now()->endOfWeek()->format('Y-m-d');
            $periodDisplay = 'Minggu Ini (' . Carbon::parse($fromDate)->format('d/m') . ' - ' . Carbon::parse($toDate)->format('d/m/Y') . ')';
        } elseif ($period === 'this_month') {
            $fromDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $toDate = Carbon::now()->endOfMonth()->format('Y-m-d');
            $periodDisplay = 'Bulan Ini (' . Carbon::now()->locale('id')->translatedFormat('F Y') . ')';
        } elseif ($period === 'this_year') {
            $fromDate = Carbon::now()->startOfYear()->format('Y-m-d');
            $toDate = Carbon::now()->endOfYear()->format('Y-m-d');
            $periodDisplay = 'Tahun Ini (' . $thisYear . ')';
        } elseif ($period === 'last_year') {
            $fromDate = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
            $toDate = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
            $periodDisplay = 'Tahun Lalu (' . $lastYear . ')';
        } elseif ($fromDate || $toDate) {
            $period = 'custom';
            // Pastikan tidak melompat sebelum batas minimal tahun lalu
            if (!$fromDate || Carbon::parse($fromDate)->lt($minDate)) {
                $fromDate = $minDateFormatted;
            }
            $fromStr = Carbon::parse($fromDate)->locale('id')->translatedFormat('d M Y');
            $toStr = $toDate ? Carbon::parse($toDate)->locale('id')->translatedFormat('d M Y') : 'Sekarang';
            $periodDisplay = $fromStr . ' - ' . $toStr;
        } else {
            $period = 'all';
            $fromDate = $minDateFormatted;
            $periodDisplay = $lastYear . ' - ' . $thisYear . ' (Tahun Lalu & Tahun Ini)';
        }

        if (!$request->ajax()) {
            $totalAll = 0;
            $totalMdCount = 0;

            return view('cms.sp2lid-md.index', compact(
                'totalAll',
                'totalMdCount',
                'search',
                'fileStatus',
                'categoryLaka',
                'period',
                'dateType',
                'periodDisplay',
                'fromDate',
                'toDate',
                'lastYear',
                'thisYear',
                'minDateFormatted',
                'perPage'
            ));
        }

        // Query dasar: MD > 0 dan Selra SP2LID
        $query = Accident::query()
            ->select(
                'accidents.id',
                'accidents.no_lp',
                'accidents.accident_type_id',
                'accidents.md',
                'accidents.selra_flag',
                'accidents.report_date',
                'accidents.accident_date',
                'accidents.polres_id',
                DB::raw("CASE WHEN LEFT(accidents.accident_type_id, 4) IN ('A072', 'A073') THEN 'Tunggal' ELSE 'Kontra' END AS category_laka")
            )
            ->where('accidents.md', '>', 0)
            ->where(function ($q) {
                $q->where('accidents.selra_flag', 'S0108')
                    ->orWhereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('accident_resolutions')
                            ->whereColumn('accident_resolutions.accident_id', 'accidents.id')
                            ->where('accident_resolutions.type_id', 'S0108');
                    });
            })
            ->with([
                'accidentResolution:id,accident_id,type_id,type_name,file,number,date',
                'uploadSuratKetetapan:id,accident_id,name',
                'polres:id,name,polda_id',
                'polres.polda:id,name',
                'ref:id,name'
            ]);

        // Filter Pencarian No LP atau Satker
        if ($search !== '') {
            $driver = DB::connection()->getDriverName();
            $likeOp = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';
            $qSearch = str_replace(' ', '%', $search);

            $query->where(function ($qq) use ($qSearch, $likeOp) {
                $qq->where('no_lp', $likeOp, "%{$qSearch}%")
                    ->orWhereHas('polres', fn($w) => $w->where('name', $likeOp, "%{$qSearch}%"))
                    ->orWhereHas('polda', fn($w) => $w->where('name', $likeOp, "%{$qSearch}%"));
            });
        }

        // Filter Periode Tanggal
        if ($fromDate) {
            $fromParsed = Carbon::parse($fromDate)->startOfDay();
            if ($dateType === 'accident_date') {
                $query->where('accidents.accident_date', '>=', $fromParsed);
            } elseif ($dateType === 'selra_date') {
                $query->whereHas('accidentResolution', fn($r) => $r->where('date', '>=', $fromParsed));
            } else {
                $query->where('accidents.report_date', '>=', $fromParsed);
            }
        }
        if ($toDate) {
            $toParsed = Carbon::parse($toDate)->endOfDay();
            if ($dateType === 'accident_date') {
                $query->where('accidents.accident_date', '<=', $toParsed);
            } elseif ($dateType === 'selra_date') {
                $query->whereHas('accidentResolution', fn($r) => $r->where('date', '<=', $toParsed));
            } else {
                $query->where('accidents.report_date', '<=', $toParsed);
            }
        }

        // Filter Kategori Laka (Kontra / Tunggal)
        if ($categoryLaka === 'Tunggal') {
            $query->whereIn(DB::raw("LEFT(accidents.accident_type_id, 4)"), ['A072', 'A073']);
        } elseif ($categoryLaka === 'Kontra') {
            $query->where(function ($q) {
                $q->whereNull('accidents.accident_type_id')
                    ->orWhereNotIn(DB::raw("LEFT(accidents.accident_type_id, 4)"), ['A072', 'A073']);
            });
        }

        // Filter Status File Ketetapan
        if ($fileStatus === 'uploaded') {
            $query->where(function ($q) {
                $q->whereHas('accidentResolution', fn($r) => $r->whereNotNull('file'))
                    ->orWhereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('upload_surat_ketetapan')
                            ->whereColumn('upload_surat_ketetapan.accident_id', 'accidents.id');
                    });
            });
        } elseif ($fileStatus === 'not_uploaded') {
            $query->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereDoesntHave('accidentResolution')
                        ->orWhereHas('accidentResolution', fn($r) => $r->whereNull('file'));
                })
                ->whereNotExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('upload_surat_ketetapan')
                        ->whereColumn('upload_surat_ketetapan.accident_id', 'accidents.id');
                });
            });
        }

        // Hitung total & jumlah MD dalam 1 query tunggal
        $statsQuery = (clone $query)->toBase();
        $statsQuery->columns = [];
        $statsQuery->orders = null;
        $stats = $statsQuery->selectRaw('COUNT(*) as total_all, COALESCE(SUM(accidents.md), 0) as total_md')->first();
        $totalAll = (int) ($stats->total_all ?? 0);
        $totalMdCount = (int) ($stats->total_md ?? 0);

        $dt = DataTables::of($query)
            ->addIndexColumn()
                ->addColumn('no_lp', function ($row) {
                    $lp = e($row->no_lp ?? '-');
                    $polda = isset($row->polres->polda->name) ? '<span><i class="bi bi-shield-shaded me-1"></i>' . e($row->polres->polda->name) . '</span>' : '';
                    $polres = isset($row->polres->name) ? '<span><i class="bi bi-building me-1"></i>' . e($row->polres->name) . '</span>' : '';
                    $tglLapor = $row->report_date ? '<span><i class="bi bi-calendar-event me-1"></i>Lapor: ' . Carbon::parse($row->report_date)->format('d/m/Y') . '</span>' : '';
                    $tglKejadian = $row->accident_date ? '<span><i class="bi bi-calendar2-check me-1"></i>Kejadian: ' . Carbon::parse($row->accident_date)->format('d/m/Y') . '</span>' : '';

                    $meta = [];
                    if ($polda) $meta[] = $polda;
                    if ($polres) $meta[] = $polres;
                    if ($tglLapor) $meta[] = $tglLapor;
                    if ($tglKejadian) $meta[] = $tglKejadian;

                    return '<div class="fw-bold text-primary fs-6 mb-1">' . $lp . '</div>
                            <div class="d-flex flex-wrap gap-2 text-muted small align-items-center">' . implode('<span>•</span>', $meta) . '</div>';
                })
                ->addColumn('category_laka', function ($row) {
                    $cat = $row->category_laka ?? 'Kontra';
                    if ($cat === 'Kontra') {
                        return '<span class="badge bg-danger text-white px-3 py-2 rounded-pill shadow-sm" title="Kecelakaan Melibatkan Lebih dari Satu Pihak / Kendaraan"><i class="bi bi-arrows-collapse me-1"></i>Kontra</span>';
                    }
                    return '<span class="badge bg-info text-dark px-3 py-2 rounded-pill shadow-sm" title="Kecelakaan Tunggal"><i class="bi bi-person me-1"></i>Tunggal</span>';
                })
                ->addColumn('md', function ($row) {
                    return '<span class="badge-md shadow-sm"><i class="bi bi-person-x-fill"></i> ' . (int) $row->md . ' MD</span>';
                })
                ->addColumn('selra', function ($row) {
                    $selraName = $row->accidentResolution?->type_name ?? $row->ref?->name ?? 'SP2LID';
                    return '<span class="badge-selra shadow-sm">' . e($selraName) . '</span>';
                })
                ->addColumn('preview_surat', function ($row) {
                    $resolution = $row->accidentResolution;
                    $uploadKetetapan = $row->uploadSuratKetetapan;
                    $hasFile = !empty($uploadKetetapan?->name) || !empty($resolution?->file);
                    $fileName = $uploadKetetapan?->name ?? $resolution?->file;
                    $previewUrl = route('cms.sp2lid-md.preview', ['id' => $row->id]);

                    if ($hasFile) {
                        $html = '<div class="d-inline-flex align-items-center gap-1">
                                    <button type="button" 
                                            class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 shadow-sm btn-preview-modal"
                                            data-url="' . $previewUrl . '"
                                            data-no-lp="' . e($row->no_lp ?? '-') . '"
                                            data-filename="' . e($fileName) . '">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                        <span>Preview Surat</span>
                                    </button>
                                    <a href="' . $previewUrl . '" target="_blank" class="btn btn-sm btn-outline-secondary" title="Buka Dokumen di Tab Baru">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                </div>';
                        if ($resolution?->number) {
                            $html .= '<div class="small text-muted mt-1">No: ' . e(Str::limit($resolution->number, 25)) . '</div>';
                        }
                        if ($resolution?->date) {
                            $html .= '<div class="small text-muted">Tgl: ' . Carbon::parse($resolution->date)->format('d/m/Y') . '</div>';
                        }
                        return $html;
                    }

                    return '<span class="badge bg-light text-secondary border px-3 py-2"><i class="bi bi-x-circle me-1 text-danger"></i>Belum Ada File</span>';
                })
                ->addColumn('aksi', function ($row) {
                    $url = route('view_produktivitas_accident', ['accident_id' => $row->id]);
                    return '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-outline-primary" title="Buka Detail LP di ICELL"><i class="bi bi-eye"></i> LP</a>';
                })
                ->rawColumns(['no_lp', 'category_laka', 'md', 'selra', 'preview_surat', 'aksi'])
                ->with([
                    'totalAll' => $totalAll,
                    'totalMdCount' => $totalMdCount,
                    'periodDisplay' => $periodDisplay
                ]);

        // Hindari query COUNT(*) ganda bawaan Yajra DataTables jika tidak ada search box
        if (empty($request->input('search.value'))) {
            $dt->setTotalRecords($totalAll)->setFilteredRecords($totalAll);
        } else {
            $dt->setTotalRecords($totalAll);
        }

        return $dt->make(true);
    }

    /**
     * Preview dokumen Surat Ketetapan PDF secara inline
     */
    public function previewPdf($accidentId)
    {
        $accident = Accident::find($accidentId);
        if (!$accident) {
            abort(404, 'Data kecelakaan tidak ditemukan.');
        }

        // 1. Cek dari upload_surat_ketetapan
        $upload = UploadSuratKetetapan::where('accident_id', $accidentId)->orderBy('created_at', 'desc')->first();
        $fileName = $upload?->name;

        // 2. Fallback cek dari accident_resolutions
        if (!$fileName) {
            $resolution = AccidentResolution::where('accident_id', $accidentId)->first();
            $fileName = $resolution?->file;
        }

        if ($fileName) {
            $filePath = public_path('file/penghentian/upload-surat-ketetapan/' . $fileName);
            if (file_exists($filePath)) {
                return response()->file($filePath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"'
                ]);
            }
        }

        return response('
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Dokumen Tidak Ditemukan</title>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            </head>
            <body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
                <div class="card shadow p-4 text-center" style="max-width: 500px;">
                    <div class="text-danger mb-3" style="font-size: 3rem;">⚠️</div>
                    <h4 class="fw-bold text-dark">Surat Ketetapan Belum Diupload</h4>
                    <p class="text-muted mt-2">
                        Dokumen fisik Surat Ketetapan untuk LP <strong>' . htmlspecialchars($accident->no_lp ?? '-') . '</strong> belum diupload atau file tidak ditemukan di server penyimpanan.
                    </p>
                </div>
            </body>
            </html>
        ', 404, ['Content-Type' => 'text/html']);
    }
}
