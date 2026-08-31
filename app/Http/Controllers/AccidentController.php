<?php

namespace App\Http\Controllers;

// use App\Services\TranslatePoliceService;
//use App\Helpers\TranslateIdHelper;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Polda;
use App\Models\Polres;
use App\Models\Accident;
use App\Models\AccidentResolution;
use App\Models\SuratTugas;
use App\Models\SuratPenyidikan;
// use App\Models\SuratPenyitaan;
use App\Models\SuratSpdp;
use App\Models\DaftarSaksi;
use App\Models\DaftarTersangka;
use App\Models\DaftarBarangBukti;
use App\Models\Dpo;
use App\Models\Dpb;
use App\Models\uploadImage;
use App\Models\Sp2hp;
use App\Models\SuratP21\SuratP21Tahap1;
use App\Models\SuratP21\SuratP21Tahap2;
use App\Models\UploadSuratKetetapan;
use App\Models\Suspect;
use App\Models\LaporanPolisi;
use App\Models\SP3;
use App\Models\SprintGas;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument as Sprindik;
use App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument as Sprinlidik;
use App\Models\Officer;
use App\Models\OfficerSpringas;
use App\Models\Meta\Institutions\Prosecutor;
use App\Models\Meta\Institutions\Court;
use App\Models\Peoples\AuthorizedSignatory;
use App\Models\Lib\DocumentCategory;

use App\Models\InvolvedPeople;
use App\Models\Lib\Police;
use App\Models\ReportedPerson;

use App\Traits\AccidentQueryTraits;
use App\Models\Log\CaseResolutionValidation as LogSelra;

// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Query\Builder;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use ZipArchive;
use Zip;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;


class AccidentController extends Controller
{
    //
    protected $_accidentModel;

    protected $_view = 'accident';

    function __construct(Accident $_accidentModel)
    {
        $this->$_accidentModel = $_accidentModel;

        view()->share('_view', $this->_view);
    }

    use AccidentQueryTraits;

    public function index(Request $request)
    {
        $no_lp = $request->input('no_lp');
        $user = Auth::user();
        $tipe_laka = 0;

        $allPolresIds = $this->getEffectivePolresId($user->polres_id);
        $allPoldasIds = $this->getEffectivePoldaId($user->polda_id);

        switch ($user->role_id) {
            case 2:
                $poldas = Polda::where('id', '=', $user->polda_id)->get();
                $polress = Polres::where('polda_id', '=', $user->polda_id)->get();
                $polda = $user->polda_id;
                $polres = '-';

                $apiPolda = $user->polda_id;
                /*$polres = TranslateIdHelper::getTranslatePoliceResorId(
                    regionalId: $polda,
                    resorId: $polres
                );*/

                break;
            case 3:
                if ($user->officer && $user->officer->polres_id == 0) {
                    $polda = $user->polda_id;
                    $polres = '-';
                    $polress = Polres::where('id', $allPolresIds)
                        ->where('state', '=', '1')->get();
                } else {
                    $polda = $user->polda_id;
                    $polres = $user->polres_id;
                    $polress = Polres::where('id', '=', $allPolresIds)->get();
                }

                // $poldas = Polda::where('id', '=', $user->polda_id)->get();
                $poldas = Polda::where('id', '=', $allPoldasIds)->get();

                $apiPolres = implode(',', $allPolresIds);
                $apiPolda = implode(',', $allPoldasIds);
                /*$polres = TranslateIdHelper::getTranslatePoliceResorId(
                    regionalId: $polda,
                    resorId: $polres
                );*/

                break;
            case 4:
                $poldas = Polda::where('id', '=', $user->polda_id)->get();
                $polress = Polres::where('id', '=', $user->polres_id)->get();
                $polda = $user->polda_id;
                $polres = $user->polres_id;

                /*$polres = TranslateIdHelper::getTranslatePoliceResorId(
                    regionalId: $polda,
                    resorId: $polres
                );*/

                break;
            default:
                $poldas = Polda::all();
                $polress = Polres::all();
                $polda = '-';
                $polres = '-';
		
		$apiPolda = implode(',', $allPoldasIds);
		$apiPolda = $apiPolda === '0' ? "-" : $apiPolda;

		$apiPolres = implode(',', $allPolresIds);
		$apiPolres = $apiPolres === '0' ? "-" : $apiPolres;

        }

//dd($apiPolres);

        // $response = Http::get('https://irsms.korlantas.polri.go.id/irsmsapi/api/get_accident_icell?polres_id='.$id);
        // $data = $response->json();
        // return view('accident.accident-index',compact('data'));
        // if($no_lp == null){
        // get data from api with guzzle

        $curl = curl_init();
        curl_setopt_array($curl, array(
            //  CURLOPT_URL => "https://irsms.korlantas.polri.go.id/irsmsapi/api/get_accident_icell?no_lp=".$no_lp."&polda=".$polda."&polres=".$polres,
            CURLOPT_URL => "https://irsms.korlantas.polri.go.id/irsmsapi/api/get_accident_icell?no_lp=" . $no_lp . "&polda=" . $apiPolda . "&polres=" . $apiPolres . "&tipe_laka=" . $tipe_laka,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'Key: 09s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataviewICELL',
                'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return view('home');
        } else {
            //  print_r(json_decode($response));
            $get_data = json_decode($response);
            //  $data = $response->json();
        }

        if ($get_data->status == "failed") {
            $status = $get_data->status;
            return view('accident.accident-index', compact('status', 'user', 'poldas', 'polress'));
        } else {
            $status = $get_data->status;
            $data = $this->paginate($get_data->result);
            $data->appends($request->all());
        }
        // dd($data);
        // dd($curl);

        // dd($allPoldasIds);
        return view('accident.accident-index', compact('status', 'data', 'poldas', 'polress'));
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        $no_lp = $request->no_lp ?? '';
        $accident_date = $request->accident_date ?? '';
        $tipe_laka = $request->tipe_laka ?? 0;
        
        switch ($user->role_id) {
            case 2:
                $poldas = Polda::where('id', '=', $user->polda_id)->get();
                $polress = Polres::where('polda_id', '=', $user->polda_id)->get();
                $polda = $request->polda_id ?? $user->polda_id;
                if ($polda != '-' && $polda != $user->polda_id) {
                    $polda = $user->polda_id;
                }
                $polres = $request->polres_id ?? '-';
                break;
            case 3:
                $poldas = Polda::where('id', '=', $user->polda_id)->get();
                $polress = Polres::where('id', '=', $user->polres_id)->get();
                $polda = $request->polda_id ?? $user->polda_id;
                if ($polda != '-' && $polda != $user->polda_id) {
                    $polda = $user->polda_id;
                }
                $polres = $request->polres_id ?? $user->polres_id;
                if ($polres != '-' && $polres != $user->polres_id) {
                    $polres = $user->polres_id;
                }
                break;
            case 4:
                $poldas = Polda::where('id', '=', $user->polda_id)->get();
                $polress = Polres::where('id', '=', $user->polres_id)->get();
                $polda = $user->polda_id;
                $polres = $user->polres_id;
                if ($polda == null) {
                    $polda = $user->polda_id;
                } else if ($polda != $user->polda_id) {
                    $polda = $user->polda_id;
                }

                if ($polres == null) {
                    $polres = $user->polres_id;
                } else if ($polres != $user->polres_id) {
                    $polres = $user->polres_id;
                }

                /*$polres = TranslateIdHelper::getTranslatePoliceResorId(
                        regionalId: $polda,
                        resorId: $polres
                    );*/
                break;
            default:
                $poldas = Polda::all();
                $polress = Polres::all();
                $polda = $request->polda_id;
                $polres = $request->polres_id;

                if ($polda == null) {
                    $polda = '-';
                } else {
                    $polda = $polda;
                }

                if ($polres == null) {
                    $polres = '-';
                } else {
                    $polres = $polres;
                }
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            //  CURLOPT_URL => "https://irsms.korlantas.polri.go.id/irsmsapi/api/search?no_lp=".$no_lp."&polda=".$polda."&polres=".$polres,
            //  CURLOPT_URL => "https://irsms.korlantas.polri.go.id/irsmsapi/api/get_accident_search_icell?no_lp=".$no_lp."&polda=".$polda."&polres=".$polres."&accident_date=".$accident_date,
            CURLOPT_URL => "https://irsms.korlantas.polri.go.id/irsmsapi/api/get_accident_search_icell",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'Key: 09s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataviewICELL',
                'Content-Type: application/json',
            ),
            CURLOPT_POSTFIELDS => json_encode([
                'no_lp' => $no_lp,
                'polda' => $polda,
                'polres' => $polres,
                'accident_date' => $accident_date,
                'tipe_laka' => $tipe_laka
            ])
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            //  print_r(json_decode($response));
            $get_data = json_decode($response);

            //  $data = $response->json();
        }
        if ($get_data->status == "failed") {
            $status = $get_data->status;
            return view('accident.accident-index', compact('status', 'user', 'poldas', 'polress'));
        } else {
            $status = $get_data->status;
            $data = $this->paginate($get_data->result);
            $data->appends($request->all());
        }
        //  $status=$get_data->status;
        //  $data = $this->paginate($get_data->result);
        return view('accident.accident-index', compact('status', 'data', 'poldas', 'polress'));
    }

    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 5);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function view(Request $request)
    {
        $accident_id = $request->input('accident_id');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://irsms.korlantas.polri.go.id/irsmsapi/api/view?accident_id=" . $accident_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'Key: 09s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataviewICELL',
                'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $get_data = json_decode($response);

        $data = array(
            'result' => $get_data->result,
            'person' => $get_data->person
        );
        // $data=$get_data->result;
        // $data['accident_date']=$get_data[0]->accident_date;
        return view($this->_view . '/accident-view', compact('data'));
    }

    public function save(Request $request)
    {
        $accident_id = $request->id;

        $accident = Accident::where('id', '=', $accident_id)->count();

        if ($accident != 0) {
            return redirect('accident')->withErrors(['Nomor LP sudah terdapat di Perkara Ditangani'])->withInput();
        }

        DB::beginTransaction();
        try {
            //call data from API https://irsms.korlantas.polri.go.id/irsmsapi/api/view using HTTP facade with header
            $response = Http::withHeaders([
                'Key' => '09s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataviewICELL',
                'Content-Type' => 'application/json',
            ])->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/view?accident_id=' . $accident_id);

            $accidentResponse = $response->json();
            $accidentResult = $accidentResponse['result'][0];
            $accidentPersons = $accidentResponse['person'];

            Accident::create([
                'id' => $accidentResult['id'],
                'officer_id' =>  $accidentResult['officer_id'],
                'dors_id' => $accidentResult['dors_id'],
                'rank_id' => $accidentResult['rank_id'],
                'officer_first_name' => $accidentResult['first_name'],
                'officer_last_name' => $accidentResult['last_name'],
                'polres_id' =>  $accidentResult['polres_id'],
                'police_id' =>  $accidentResult['polres_id'],
                'accident_date' =>  Carbon::createFromFormat('d/m/Y', $accidentResult['accident_date'])->format('Y-m-d'),
                'accident_time' =>  $accidentResult['accident_time'],
                'report_date' =>  Carbon::createFromFormat('d/m/Y', $accidentResult['report_date'])->format('Y-m-d'),
                'report_time' => $accidentResult['report_time'],
                'no_lp' =>  $accidentResult['no_lp'],
                'latitude' =>  $accidentResult['latitude'],
                'longtitude' =>  $accidentResult['longtitude'],
                'accident_type_id' =>  $accidentResult['accident_type_id'],
                'md' =>  $accidentResult['md'],
                'lb' =>  $accidentResult['lb'],
                'lr' =>  $accidentResult['lr'],
                'road_name' =>  $accidentResult['road_name'],
                'weather_cond_id' =>  $accidentResult['weather_cond_id'],
                'light_cond_id' =>  $accidentResult['light_cond_id'],
                'road_function_id' =>  $accidentResult['road_function_id'],
                'road_state_id' =>  $accidentResult['road_state_id'],
                'damage_lose_desc' =>  $accidentResult['damage_lose_desc'],
                'urgent_accident_id' =>  $accidentResult['urgent_accident_id'],
                'state' => 1,
                'selra_flag' => 'S0107',
                'total_ranmor' => $accidentResult['total_ranmor'],
                'special_info' => (empty($accidentResult['informasi_khusus'])) ? "-" : str_replace(" ", "_", strtoupper($accidentResult['informasi_khusus']))
            ]);

            foreach ($accidentPersons as $accidentPerson) {
                InvolvedPeople::create([
                    'id' => $accidentPerson['id'],
                    'accident_id' => $accidentPerson['accident_id'],

                    'name' => $accidentPerson['first_name'] . ' ' . $accidentPerson['last_name'],

                    'birth_date' => Carbon::parse($accidentPerson['birth_date'])->format('Y-m-d'),
                    'age' => $accidentPerson['age'],

                    'identity_type_id' => null,
                    'identity_number' => $accidentPerson['identity_no'],
                    'religion_id' => null,
                    'nationality' => null,
                    'job_id' => null,
                    'education_id' => null,
                    'gender_id' => null,

                    'address' => $accidentPerson['address'],

                    'is_active' => ($accidentPerson['state'] == 1) ? true : false,

                    'class' => $accidentPerson['position'],
                ]);
            }

            DB::commit();

            if (env('APP_MODE') == 'PRODUCTION') {
                $headers = [
                    'Content-Type' => 'application/json',
                    'Key' => 'Hy6d3K1d93LOHRfbeE0KKly1YK9t4YdGsbNDEvyxAYI=icell'
                ];

                $response = Http::withHeaders($headers)->post('https://irsms.korlantas.polri.go.id/irsmsapi/api/icellSelra', [
                    'id' => $accident_id,
                    'selra_flag' =>  'S0107',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('accident')->withErrors(['Terjadi kesalahan sistem'])->withInput();
        }

        return redirect('produktivitas');
    }

    public function list_produktivitas()
    {
        $user = Auth::user();
        $officerId = $user->register_number;

        // $lockExpr = DB::raw("(CASE WHEN NULLIF(a.state_irsms::text,'')::int IN (0,1,2) THEN 1 ELSE 0 END) AS is_locked_by_irsms");  // FIX

        $lockExpr = DB::raw("
        CASE
        WHEN a.state_irsms IS NOT NULL
        AND TRIM(a.state_irsms::text) ~ '^[0-9]+$'
        AND (TRIM(a.state_irsms::text))::int IN (0,1,2)
        THEN 1
        ELSE 0
        END AS is_locked_by_irsms
        ");

        switch ($user->role_id) {
            case 1:
                $polda = '-';
                $polres = '-';

                $accident = $this->getAccidentQuery()
                    ->addSelect(['a.state_irsms', $lockExpr])
                    ->orderByDesc('a.created_at')
                    ->limit(200000)
                    ->get();

                $poldas = Polda::all();
                $polress = Polres::where('polda_id', '=', '01')->get();
                // dd($accident);
                break;
            case 3:
                $polda = $user->polda_id;
                $polres = $user->polres_id;
                $police = $user->police_id;

                $allPolresIds = $this->getEffectivePolresId($user->polres_id);
                $allPoldaIds = $this->getEffectivePoldaId($polda);

                if ($user->officer && $user->officer->polres_id == 0) {
                    $accident = $this->getAccidentQuery()
                        ->where('polres_id', 'ilike', $police . '%')
                        ->addSelect(['a.state_irsms', $lockExpr])
                        ->orderBy('a.created_at', 'desc')
                        ->get();
                    $polress = Polres::where('id', 'ILIKE', $police . '%')
                        ->where('state', '=', '1')->get();
                } else {
                    // $polres = implode(',',$allPolresIds);

                    $accident = $this->getAccidentQuery()
                        ->whereIn('polres_id', $allPolresIds)
                        ->addSelect(['a.state_irsms', $lockExpr])
                        ->orderBy('a.created_at', 'desc')
                        ->get();
                    $polress = Polres::whereIN('id', $allPolresIds)
                        ->where('state', '=', '1')->get();
                }

                // $poldas = Polda::where('id', '=', $polda)->get();
                $poldas = Polda::where('id', '=', $allPoldaIds)->get();
                break;
            case 4:
                $polda  = $user->polda_id;
                $polres = $user->polres_id;

                // Ambil base result
                $accident = $this->getStatusAcc($polres, $officerId);

                if (
                    $accident instanceof \Illuminate\Database\Query\Builder
                    || $accident instanceof \Illuminate\Database\Eloquent\Builder
                ) {

                    // CATATAN: pastikan alias tabel accidents di getStatusAcc() = 'a'.
                    // Jika beda (mis. 'acc'), ganti 'a.' di bawah & pada $lockExpr.
                    $accident = $accident
                        ->addSelect(['a.state_irsms', $lockExpr])
                        ->orderByDesc('a.created_at')   // aman ditambahkan untuk konsistensi urutan
                        ->get();
                } else {
                    // Fallback jika getStatusAcc() mengembalikan array/koleksi/stdClass
                    $accident = collect($accident)->map(function ($row) {
                        // Normalisasi ke array agar mudah dimodifikasi
                        $arr = (array) $row;

                        // Baca state_irsms secara defensif
                        $raw = isset($arr['state_irsms']) ? $arr['state_irsms'] : null;
                        $locked = 0;
                        if ($raw !== null && $raw !== '') {
                            $s = trim((string) $raw);
                            if ($s !== '' && ctype_digit($s)) {
                                $v = (int) $s;
                                $locked = in_array($v, [0, 1, 2], true) ? 1 : 0;
                            }
                        }

                        // Set kolom turunan agar blade bisa selalu akses properti
                        $arr['is_locked_by_irsms'] = $locked;

                        // Kembalikan sebagai stdClass
                        return (object) $arr;
                    })
                        // Jika ada kolom created_at, urutkan desc agar konsisten
                        ->sortByDesc(function ($o) {
                            return $o->created_at ?? null;
                        })
                        ->values();
                }
                // dd($accident);

                $poldas  = Polda::where('id', '=', $polda)->get();
                $polress = Polres::where('id', '=', $polres)->get();
                break;
            case 5:
                $polda = $user->polda_id;
                $polres = $user->polres_id;

                $accident = $this->getAccidentQuery()
                    ->where('polres_id', '=', $polres)
                    ->addSelect(['a.state_irsms', $lockExpr])
                    ->orderBy('a.created_at', 'desc')
                    ->get();

                $poldas = Polda::where('id', '=', $polda)->get();
                $polress = Polres::where('id', '=', $polres)->get();
                break;
        }
        // dd($accident);

        // --- REJECT MODAL: scope polres berdasarkan role ---
        $polresFilterIds = null;
        if ($user->role_id == 3) {
            $polresFilterIds = isset($allPolresIds) && !empty($allPolresIds) ? $allPolresIds : ($polres ? [$polres] : null);
        } elseif (in_array($user->role_id, [4, 5])) {
            $polresFilterIds = $polres ? [$polres] : null;
        } else {
            // role 1: default tidak memunculkan modal; set [] bila ingin all-Polda
            $polresFilterIds = null;
        }

        $rejectedToShow = collect();

        if (!empty($polresFilterIds)) {
            // subquery: id reject terakhir per accident
            $logTable = (new LogSelra)->getTable(); // aman jika pakai schema
            $lastRejectSub = DB::table($logTable)
                ->select(DB::raw('MAX(id) AS last_id'), 'accident_id')
                ->where('updated_status_name', 'rejected') // simpan 'rejected' huruf kecil
                ->groupBy('accident_id');

            // join ke row log terakhir + filter polres + filter "belum ada SELRA baru"
            $rejectedToShow = DB::table($logTable . ' as l')
                ->joinSub($lastRejectSub, 'lr', function ($j) {
                    $j->on('l.id', '=', 'lr.last_id');
                })
                ->join('accidents as a', 'a.id', '=', 'l.accident_id')
                ->leftJoin('accident_resolutions as ar', function ($j) {
                    // ada SELRA baru setelah waktu reject? Jika ya, nanti ar.id ≠ null → tidak ditampilkan
                    $j->on('ar.accident_id', '=', 'l.accident_id')
                        ->whereRaw('ar.created_at > COALESCE(l.rejected_at, l.created_at)');
                })
                ->whereIn('a.polres_id', $polresFilterIds)
                // opsional: batasi 7 hari terakhir biar tidak bejibun
                ->where(function ($q) {
                    $q->whereNotNull('l.rejected_at')
                        ->where('l.rejected_at', '>=', Carbon::now()->subDays(7))
                        ->orWhere(function ($q2) {
                            // kalau rejected_at null, fallback created_at
                            $q2->whereNull('l.rejected_at')
                                ->where('l.created_at', '>=', Carbon::now()->subDays(7));
                        });
                })
                // hanya yang BELUM ada selra baru (ar.id null)
                ->whereNull('ar.id')
                ->orderByDesc(DB::raw('COALESCE(l.rejected_at, l.created_at)'))
                ->get([
                    'l.id as log_id',
                    'l.accident_id',
                    'l.accident_number',
                    'l.type_name',
                    'l.reject_reason',
                    'l.rejected_at',
                    'l.created_at as log_created_at',
                    'a.no_lp',
                    'a.polres_id',
                ]);
        }

        // $state_irsms = Accident::where('state_irsms', '<>', '9')->get();

        $data['accident'] = $this->paginate_produktivitas($accident);
        $data['poldas'] = $poldas;
        $data['polress'] = $polress;
        $data['rejectedToShow'] = $rejectedToShow;

        // dd($data);
        // $data['state_irsms'] = $state_irsms;

        // dd($data);

        return view('produktivitas.produktivitas-index', $data);
    }

    public function search_produktivitas(Request $request)
    {
        $filters = [
            'polda' => $request->input('polda', '-'),
            'polres' => $request->input('polres', '-'),
            'status' => $request->input('status', '-'),
            'tanggal' => $request->tgl_kejadian ? Carbon::parse($request->tgl_kejadian)->format('Y-m-d') : '-',
            'no_lp' => $request->input('no_lp', '-'),
            'level' => $request->input('level', '-')
        ];

        $user = Auth::user();
        $officerId = $user->register_number;
        // $lockExpr = DB::raw("(CASE WHEN NULLIF(a.state_irsms::text,'')::int IN (0,1,2) THEN 1 ELSE 0 END) AS is_locked_by_irsms");
        $lockExpr = DB::raw("
            CASE
            WHEN a.state_irsms IS NOT NULL
            AND TRIM(a.state_irsms::text) ~ '^[0-9]+$'
            AND (TRIM(a.state_irsms::text))::int IN (0,1,2)
            THEN 1 ELSE 0
            END AS is_locked_by_irsms
        ");

        switch ($user->role_id) {
            case 1:
                if ($filters['polda'] == null) {
                    $filters['polda'] = '-';
                }
                if ($filters['polres'] == null) {
                    $filters['polres'] = '-';
                }
                // $polda = $polda ?? '-';
                // $polres = $polres ?? '-';

                $query = $this->getAccidentQuery();
                $filtered = $this->applyFilters($query, $filters);

                $accident = $filtered
                    ->addSelect(['a.state_irsms', $lockExpr])
                    ->orderBy('a.created_at', 'desc')
                    ->limit(200000)
                    ->get();

                $poldas = Polda::all();
                $polress = Polres::where('polda_id', '=', '01')->get();
                break;
            case 3:
                if ($filters['polda'] == '-' || $filters['polda'] == $user->polda_id) {
                    $relatedPoldaIds = $this->getEffectivePoldaId($user->polda_id);

                    // $filters['polda_array'] = $relatedPoldaIds;

                    $filters['polda'] = $relatedPoldaIds;
                }

                if ($filters['polres'] == '-' || $filters['polres'] == $user->polres_id || $filters['polres'] == $user->police_id) {
                    $relatedPolresIds = $this->getEffectivePolresId($user->polres_id);

                    // $filters['polres_array'] = $relatedPolresIds;

                    $filters['polres'] = $relatedPolresIds;
                }

                $query = $this->getAccidentQuery();
                $filtered = $this->applyFilters($query, $filters);

                $accident = $filtered
                    // ->addSelect(['a.state_irsms', $lockExpr])
                    ->orderBy('a.created_at', 'desc')
                    ->limit(200000)
                    ->get();

                $poldas = Polda::where('id', '=', $user->polda_id)->get();
                $polress = Polres::where('id', '=', $user->polres_id)->get();

                break;
            case 4:
                // SELALU kunci ke user (role 4 tidak boleh loncat satker)
                $polda  = $user->polda_id;
                $polres = $user->polres_id;

                // Ambil input yang diperlukan (tanpa override polda/polres)
                $no_lp        = $request->input('no_lp');
                $checkTanggal = $request->filled('tgl_kejadian') ? 1 : 0;
                $checkstatus  = $request->input('status');      // boleh '-' / null
                $level        = $request->input('level');       // boleh '-' / null
                $tanggal      = $request->filled('tgl_kejadian')
                    ? Carbon::parse($request->tgl_kejadian)->format('Y-m-d')
                    : null;

                // Panggil builder/koleksi dari service
                $accident = $this->searchGetAccident(
                    $officerId,
                    $polda,
                    $polres,
                    $checkTanggal,
                    $checkstatus,
                    $tanggal,
                    $no_lp,
                    $level
                );

                if (
                    $accident instanceof \Illuminate\Database\Query\Builder
                    || $accident instanceof \Illuminate\Database\Eloquent\Builder
                ) {

                    // PASTIKAN alias tabel accidents di searchGetAccident() = 'a'
                    // Kalau bukan 'a', ganti prefiks 'a.' di dua baris bawah
                    $accident = $accident
                        ->addSelect([
                            DB::raw('a.state_irsms AS state_irsms'),
                            $lockExpr,
                        ])
                        ->orderByDesc('a.created_at')
                        ->limit(200000)
                        ->get();
                } else {
                    // === Fallback untuk koleksi/stdClass ===
                    $accCol = collect($accident);

                    // petakan id -> state_irsms dari tabel accidents (hindari N+1)
                    $ids = $accCol->pluck('id')->filter()->unique()->values();
                    $irsmsMap = $ids->isNotEmpty()
                        ? DB::table('accidents')->whereIn('id', $ids)->pluck('state_irsms', 'id')
                        : collect();

                    $accident = $accCol->map(function ($row) use ($irsmsMap) {
                        $arr = (array) $row;

                        // sematkan state_irsms bila belum ada
                        if (!array_key_exists('state_irsms', $arr)) {
                            $arr['state_irsms'] = $irsmsMap[$arr['id'] ?? null] ?? null;
                        }

                        // hitung is_locked_by_irsms aman numerik
                        $raw    = $arr['state_irsms'] ?? null;
                        $locked = 0;
                        if ($raw !== null && $raw !== '') {
                            $s = trim((string) $raw);
                            if ($s !== '' && ctype_digit($s)) {
                                $v = (int) $s;
                                $locked = in_array($v, [0, 1, 2], true) ? 1 : 0;
                            }
                        }
                        $arr['is_locked_by_irsms'] = $locked;

                        return (object) $arr;
                    })
                        ->sortByDesc(fn($o) => $o->created_at ?? null)
                        ->values();
                }

                // dropdown tetap dikunci ke satker user
                $poldas  = Polda::where('id', '=', $polda)->get();
                $polress = Polres::where('id', '=', $polres)->get();
                break;
            case 5:
                if ($filters['polda'] == null || $filters['polda'] == $user->polda_id) {
                    $filters['polda'] = $user->polda_id;
                }

                if ($filters['polres'] == '-' || $filters['polres'] == $user->polres_id || $filters['polres'] == $user->police_id) {
                    $filters['polres'] = $user->polres_id ?? $user->police_id;
                }

                $query = $this->getAccidentQuery();
                $filtered = $this->applyFilters($query, $filters);

                $accident = $filtered
                    ->addSelect(['a.state_irsms', $lockExpr])
                    ->orderBy('a.created_at', 'desc')
                    ->limit(200000)
                    ->get();

                $poldas = Polda::where('id', '=', $user->polda_id)->get();
                $polress = Polres::where('id', '=', $user->polres_id)->get();
                break;
        }
        // dd($poldas);

        $data['accident'] = $this->paginate_produktivitas($accident);
        $data['poldas'] = $poldas;
        $data['polress'] = $polress;

        $data['accident']->appends($request->all());

        return view('produktivitas.produktivitas-index', $data);
    }

    public function paginate_produktivitas($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 5);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function view_produktivitas_accident(Request $request)
    {
        $get_accident = $request->input('accident_id');

        // Ambil log reject terakhir
        $lastSelraReject = LogSelra::where('accident_id', $get_accident)
            ->where('updated_status_name', 'rejected') // simpan 'rejected' huruf kecil
            ->latest('id')
            ->first();

        // Cek apakah sudah ada SELRA baru setelah waktu reject itu
        $hasNewSelraAfterReject = false;
        if ($lastSelraReject) {
            $pivotTime = $lastSelraReject->rejected_at ?: $lastSelraReject->created_at;

            $hasNewSelraAfterReject = AccidentResolution::where('accident_id', $get_accident)
                ->where('created_at', '>', $pivotTime)
                ->exists();
        }

        $rejectReasons = [];
        if (!empty($lastSelraReject->reject_reason)) {
            // $decoded = json_decode($lastSelraReject->reject_reason, true);
            // $rejectReasons = is_array($decoded) ? $decoded : [];

            $reasons = $lastSelraReject->reject_reason;

            $decoded = json_decode($reasons, true);

            if (is_array($decoded)) {
                $rejectReasons = is_array($decoded) ? $decoded : [];
            }
            else if(is_string($reasons) && trim($reasons) !== ''){
                $rejectReasons = [trim($reasons)];
            }
        }
        $data['rejectReasons'] = $rejectReasons;

        // Modal hanya muncul kalau ada reject & belum ada SELRA baru
        $data['lastSelraReject']       = $lastSelraReject;
        $data['shouldShowRejectModal'] = $lastSelraReject && ! $hasNewSelraAfterReject;

        $date_now = Carbon::now();
        $documentStages = DocumentCategory::whereHas('children', function ($query) {
            $query->whereNotNull('route');
        })->where('category', 'STAGE')->where('is_active', true)->orderBy('id', 'ASC')->get();

        $accident = DB::select('select id,
            no_lp, selra_flag, md,
            accidents.accident_type_id,
            CASE
                WHEN LEFT(accidents.accident_type_id, 4) IN (\'A072\', \'A073\') THEN \'Tunggal\'
                ELSE \'Kontra\'
            END AS category_laka,
            accidents.report_date as accident_report_date,
            to_char(accidents.accident_date, \'DD-MM-YYYY\') as accident_date ,
            to_char(accidents.created_at,  \'DD-MM-YYYY\') as accident_tindak_lanjut ,
            polres_id,
            CASE
                WHEN selra_flag <> \'S0107\' THEN AGE(accidents.updated_at,accidents.created_at) ELSE AGE(\'' . $date_now . '\',accidents.created_at)
            END AS accident_proses ,
            to_char(accidents.last_update, \'DD-MM-YYYY HH24:MI:SS\') as accident_last_update,
            tipe_update,
            (select ref.name from ref where id = accidents.category) AS tipe_berkas
        from accidents where id = \'' . $get_accident . '\'');
        $selra_flag = DB::select('select selra_flag from accidents where id = \'' . $get_accident . '\'');
        $state_selra_flag = DB::select('select state_selra_flag from accidents where id = \'' . $get_accident . '\'');
        $p21Tahap2Status = DB::table('accidents')->where('id', $get_accident)->first();
        $selra = DB::select('select * from ref where grp_id = \'S01\'');
        $gender = DB::select('select * from ref where grp_id = \'G01\'');
        $education = DB::select('select * from ref where grp_id = \'E01\'');
        $identity_type = DB::select('select * from ref where grp_id = \'G02\'');
        $religion = DB::select('select * from ref where grp_id = \'R01\'');

        $polres = $accident[0]->polres_id;

        $user = Auth::user();
        switch ($user->role_id) {
            case 2:
                /*$polres = TranslateIdHelper::getTranslatePoliceResorId(
                    regionalId: Auth::user()->police_id,
                    resorId: $polres
                );*/

                $officer = DB::table('officers')
                    ->where('polres_id', '=', $polres)
                    ->get();
                break;
            case 3:
                /*$polres = TranslateIdHelper::getTranslatePoliceResorId(
                    regionalId: Auth::user()->police_id,
                    resorId: $polres
                );*/

                $officer = DB::table('officers')
                    ->where('polres_id', '=', $polres)
                    ->get();
                break;
            case 4:
                /*$polres = TranslateIdHelper::getTranslatePoliceResorId(
                    regionalId: Auth::user()->police_id,
                    resorId: $polres
                );*/

                $officer = DB::table('officers')
                    ->where('polres_id', '=', $polres)
                    ->get();
                break;
            default:
                $officer = DB::table('officers')
                    ->where('polres_id', '=', $polres)
                    ->get();
        }
        $springas_officer = DB::select('select * from legacy.officer_springas left join legacy.springas on legacy.officer_springas.sprint_gas_id = legacy.springas.id left join officers on legacy.officer_springas.officer_id = officers.id where legacy.springas.accident_id = \'' . $get_accident . '\' ');

        //kategori 1
        $penandatangan = AuthorizedSignatory::select('*', DB::raw("CONCAT(first_title, ' ', first_name, ' ', last_name, ', ', last_title) AS full_name"))
            ->where('polres_id', $accident[0]->polres_id)
            ->orderBy('first_name')
            ->get();

        //kategori 1
        $surat_perintah_tugas = DB::select('select * from surat_tugas left join officers on surat_tugas.officer_id = officers.id where accident_id = \'' . $get_accident . '\' ');
        $springas = DB::select('select * from legacy.springas where accident_id = \'' . $get_accident . '\' ');
        $surat_perintah_penyelidikan = DB::select('select * from surat_penyelidikan left join officers on surat_penyelidikan.officer_id = officers.id where accident_id = \'' . $get_accident . '\' ');
        // $surat_perintah_penyidikan = DB::select('select * from accident_dtl where accident_id = \''.$get_accident.'\' and category_id = \'SP0102\' and state = \'1\'');
        $surat_perintah_penyidikan = DB::select('select * from surat_penyidikan left join officers on surat_penyidikan.officer_id = officers.id where accident_id = \'' . $get_accident . '\' ');
        $sprindik = Sprindik::where('accident_id', $get_accident)->first();
        $sprinlidik = Sprinlidik::where('accident_id', $get_accident)->first();
        //$surat_spdp =DB::select('select * from surat_spdp left join accidents on surat_spdp.accident_id = accidents.id where accidents.id = \''.$get_accident.'\' ');
        $spdp = DB::select('select * from spdpp left join accidents on spdpp.accident_id = accidents.id where accidents.id = \'' . $get_accident . '\' ');
        $spdp_upload = DB::select('select * from spdp_upload left join accidents on spdp_upload.accident_id = accidents.id where accidents.id = \'' . $get_accident . '\' ');
        $spdik = DB::table('doc.surat_perintah_penyidikan_documents')->select('id', 'document_number', 'document_date')->where('accident_id', $get_accident)->first();
        $spgas = DB::table('legacy.springas')->select('id')->where('accident_id', $get_accident)->first();
        $lhgp = DB::select('select * from lhgp where accident_id = \'' . $get_accident . '\' ');
        // $pengadilan =DB::table('courts')->select('id', 'name')->get();
        // $kejaksaan =DB::table('prosecutors')->select('id', 'name')->get();
        $polresjaksa = Polres::where('id', $polres)->first();
        $jaksa = Prosecutor::where('polda_id', $polresjaksa->polda_id)->get();
        $pengadilan = Court::where('polda_id', $polresjaksa->polda_id)->get();

        $pejabat = DB::table('authorized_signatories')->where('polres_id', $polres)->get();
        $suspects = DB::table('suspects')->select('id', 'name')->where('accident_id', $get_accident)->first();
        // $officer = DB::table('officers')->where('polres_id', $accident[0]->polres_id)->where('sebagai_kepala', '!=', '-')->get();
        $surat_berkas = DB::select('select * from accident_dtl where accident_id = \'' . $get_accident . '\' and category_id =  \'SP0103\' and state = \'1\'');
        $LaporanPolisi = DB::select("select * from laporan_polisi where accident_id = '$get_accident'");
        $BAPenangkapanTKP = DB::select("select * from ba_pengangkapan_tkp where accident_id = '$get_accident'");
        $BAPemotretan = DB::select("select * from ba_pemotretan where accident_id = '$get_accident'");
        $BAPengambilanDarah = DB::select("select * from ba_pengambilan_darah where accident_id = '$get_accident'");
        $laporan_hasil_penyelidikan = DB::select("select * from laporan_hasil_penyelidikan where accident_id = '$get_accident'");
        $BAIntrogasi = DB::select("select * from ba_introgasi where accident_id = '$get_accident'");
        $SpdpUpload = DB::select("select * from spdp_upload where accident_id = '$get_accident'");
        $Sddl = DB::select("select * from suspect_determination_decision_letters where accident_id = '$get_accident'");
        //total surat ketegori 1
        $DocLaporanPolisi = DB::table("laporan_polisi")->where("accident_id", "=", "$get_accident")->count();
        $DocBAPenangkapanTKP = DB::table("ba_pengangkapan_tkp")->where("accident_id", "=", $get_accident)->count();
        $DocPemotretan = DB::table("ba_pemotretan")->where("accident_id", "=", $get_accident)->count();
        $DocBAPengambilanDarah = DB::table("ba_pengambilan_darah")->where("accident_id", "=", $get_accident)->count();
        $Doclaporan_hasil_penyelidikan = DB::table("laporan_hasil_penyelidikan")->where("accident_id", "=", $get_accident)->count();
        $DocBAIntrogasi = DB::table("ba_introgasi")->where("accident_id", "=", $get_accident)->count();

        $DocSuratTugas1 = DB::select(
            "select
        (case when accident_id='$get_accident' then 1 end) as total
        from legacy.springas
        group by accident_id = '$get_accident'"
        );
        $DocSuratTugas2 = DB::select(
            "select
        (case when accident_id='$get_accident' then 1 end) as total
        from legacy.investigation_warrants
        group by accident_id = '$get_accident'"
        );
        $DocSuratTugas3 = DB::select(
            "select
        (case when accident_id='$get_accident' then 1 end) as total
        from legacy.investigation_order_letters
        group by accident_id = '$get_accident'"
        );
        // $DocSuratTugas4 = DB::table("surat_spdp")->where("accident_id","=","$get_accident")->count();
        $DocSuratTugas4 = DB::table("spdpp")->where("accident_id", "=", "$get_accident")->count();
        $DocSuratTugas5 = DB::table("lhgp")->where("accident_id", "=", "$get_accident")->count();
        $DocSuratTugas6 = DB::table("suspect_determination_decision_letters")->where("accident_id", "=", "$get_accident")->count();

        $DocSp2hp = DB::select("
        select
        (case when accident_id='$get_accident' then 1 end) as total
        from sp2hp
        group by accident_id = '$get_accident'");
        if ($DocSuratTugas1 == []) {
            $TotalSuratTugas1 = "0";
        } else {
            $TotalSuratTugas1 = json_encode(end($DocSuratTugas1)->total);
        }

        if ($DocSuratTugas2 == []) {
            $TotalSuratTugas2 = "0";
        } else {
            $TotalSuratTugas2 = json_encode(end($DocSuratTugas2)->total);
        }

        if ($DocSuratTugas3 == []) {
            $TotalSuratTugas3 = "0";
        } else {
            $TotalSuratTugas3 = json_encode(end($DocSuratTugas3)->total);
        }

        if ($DocSp2hp == []) {
            $TotalSp2hp = "0";
        } else {
            $TotalSp2hp = json_encode(end($DocSp2hp)->total);
        }
        $TotalKategori1 = round((((int)$TotalSuratTugas1 + (int)$TotalSuratTugas2 + (int)$TotalSuratTugas3 + $DocSuratTugas4 + (int)$TotalSp2hp + (int)$DocLaporanPolisi + (int)$DocBAPenangkapanTKP + (int)$DocPemotretan + (int)$DocSuratTugas5 + (int)$DocSuratTugas6 + $DocBAPengambilanDarah + $Doclaporan_hasil_penyelidikan + $DocBAIntrogasi) / 13) * 100);

        //kategori 2
        $daftar_saksi = DB::select('select * from daftar_saksi where accident_id = \'' . $get_accident . '\'');
        $surat_perintah_membawa_saksi = DB::select('select * from surat_perintah_membawa_saksi where accident_id = \'' . $get_accident . '\'');
        $berita_acara_membawa_saksi = DB::select('select * from berita_acara_membawa_saksi where accident_id = \'' . $get_accident . '\'');
        $berita_acara_penyumpahan_saksi = DB::select('select * from berita_acara_penyumpahan_saksi where accident_id = \'' . $get_accident . '\'');
        $berita_pemeriksaan_saksi = DB::select('select * from berita_pemeriksaan_saksi where accident_id = \'' . $get_accident . '\'');
        $berita_pemeriksaan_ahli = DB::select('select * from berita_pemeriksaan_ahli where accident_id = \'' . $get_accident . '\'');

        //total kategori 2
        $DocSuratSaksi1 = DB::table("surat_perintah_membawa_saksi")->where("accident_id", "=", "$get_accident")->count();
        $DocSuratSaksi2 = DB::table("berita_acara_membawa_saksi")->where("accident_id", "=", "$get_accident")->count();
        $DocSuratSaksi3 = DB::table("berita_acara_penyumpahan_saksi")->where("accident_id", "=", "$get_accident")->count();
        $DocSaksi = DB::select("
        select
            ( case when accident_id='$get_accident' then 1 else 0 end) as total
        from daftar_saksi
        group by accident_id = '$get_accident'");
        if ($DocSaksi == []) {
            $ArrayDocSaksi = "0";
        } else {
            $ArrayDocSaksi = json_encode(end($DocSaksi)->total);
        }
        $DocSuratSaksi5 = DB::table("berita_pemeriksaan_saksi")->where("accident_id", "=", "$get_accident")->count();
        $DocSuratSaksi6 = DB::table("berita_pemeriksaan_ahli")->where("accident_id", "=", "$get_accident")->count();

        $TotalKategori2 = ($DocSuratSaksi1 + $DocSuratSaksi2 + $DocSuratSaksi3 + (int)$ArrayDocSaksi + $DocSuratSaksi5 + $DocSuratSaksi6) / 6 * 100;


        //kategori 3
        $daftar_tersangka = DB::select('select * from daftar_tersangka where accident_id = \'' . $get_accident . '\'');
        $surat_panggilan_tersangka = DB::select('select * from surat_panggilan_tersangka where accident_id = \'' . $get_accident . '\'');
        // $surat_perintah_penangkapan = DB::select('select * from surat_perintah_penangkapan where accident_id = \''.$get_accident.'\'');
        $berita_acara_pemeriksaan_tersangka = DB::select('select * from berita_acara_pemeriksaan_tersangka where accident_id = \'' . $get_accident . '\'');
        $berita_acara_konfrontasi = DB::select('select * from berita_acara_konfrontasi where accident_id = \'' . $get_accident . '\'');
        $berita_acara_rekonstruksi = DB::select('select * from berita_acara_rekonstruksi where accident_id = \'' . $get_accident . '\'');
        $sket_tkp = DB::select('select * from sket_tkp where accident_id = \'' . $get_accident . '\'');
        $surat_bantuan_penangkapan = DB::select('select * from surat_bantuan_penangkapan where accident_id = \'' . $get_accident . '\'');
        $berita_penyerahan_tersangka = DB::select('select * from berita_penyerahan_tersangka where accident_id = \'' . $get_accident . '\'');
        // $berita_pelepasan_tersangka = DB::select('select * from berita_pelepasan_tersangka where accident_id = \''.$get_accident.'\'');

        //total kategori 3
        $DocTersangka1 = DB::select("
        select
        ( (case when accident_id = '$get_accident' then 1 else 0 end) ) as total
        from daftar_tersangka
        group by accident_id='$get_accident'");
        $DocSuratTersangka2 = DB::table("surat_panggilan_tersangka")->where("accident_id", "=", "$get_accident")->count();
        // $DocSuratTersangka3 = DB::table("surat_perintah_penangkapan")->where("accident_id","=","$get_accident")->count();
        $DocSuratTersangka4 = DB::table("berita_acara_pemeriksaan_tersangka")->where("accident_id", "=", "$get_accident")->count();
        $DocSuratTersangka5 = DB::table("berita_acara_konfrontasi")->where("accident_id", "=", "$get_accident")->count();
        $DocSuratTersangka6 = DB::table("berita_acara_rekonstruksi")->where("accident_id", "=", "$get_accident")->count();
        $DocSuratTersangka7 = DB::table("sket_tkp")->where("accident_id", "=", "$get_accident")->count();
        $DocSuratTersangka8 = DB::table("surat_bantuan_penangkapan")->where("accident_id", "=", "$get_accident")->count();
        $DocSuratTersangka9 = DB::table("berita_penyerahan_tersangka")->where("accident_id", "=", "$get_accident")->count();
        // $DocSuratTersangka10 = DB::table("berita_pelepasan_tersangka")->where("accident_id","=","$get_accident")->count();

        if ($DocTersangka1 == []) {
            $ArrayDocTersangka1 = "0";
        } else {
            $ArrayDocTersangka1 = json_encode(end($DocTersangka1)->total);
        }
        $TotalKategori3 = (
            (int)$ArrayDocTersangka1 + $DocSuratTersangka2 +
            $DocSuratTersangka4 + $DocSuratTersangka5 + $DocSuratTersangka6 + $DocSuratTersangka7 +
            $DocSuratTersangka8 + $DocSuratTersangka9
        ) / 10 * 100;


        //kategori 4
        $surat_perintah_penahanan = DB::select('select * from surat_perintah_penahanan where accident_id = \'' . $get_accident . '\'');
        $berita_acara_penahanan = DB::select('select * from berita_acara_penahanan where accident_id = \'' . $get_accident . '\'');
        $permintaan_perpanjangan_penahanan = DB::select('select * from permintaan_perpanjangan_penahanan where accident_id = \'' . $get_accident . '\'');
        $berita_penahanan_lanjutan = DB::select('select * from berita_penahanan_lanjutan where accident_id = \'' . $get_accident . '\'');
        $berita_pencabutan_pembatalan_penahanan = DB::select('select * from berita_pencabutan_pembatalan_penahanan where accident_id = \'' . $get_accident . '\'');
        $berita_pengeluaran_penahanan = DB::select('select * from berita_pengeluaran_penahanan where accident_id = \'' . $get_accident . '\'');
        $surat_pembatalan_penahanan = DB::select('select * from surat_pembatalan_penahanan where accident_id = \'' . $get_accident . '\'');
        $surat_penahanan_lanjutan = DB::select('select * from surat_penahanan_lanjutan where accident_id = \'' . $get_accident . '\'');
        $surat_pencabutan_pembatalan_penahanan = DB::select('select * from surat_pencabutan_pembatalan_penahanan where accident_id = \'' . $get_accident . '\'');
        $surat_perpanjangan_penahanan = DB::select('select * from surat_perpanjangan_penahanan where accident_id = \'' . $get_accident . '\'');
        $surat_pengiriman_berkas_perkara = DB::select('select * from surat_pengiriman_berkas_perkara where accident_id = \'' . $get_accident . '\'');
        $tanda_terima_berkas_perkara = DB::select('select * from tanda_terima_berkas_perkara where accident_id = \'' . $get_accident . '\'');

        // total kategori 4
        $DocPenahanan1 = DB::table("surat_perintah_penahanan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenahanan2 = DB::table("berita_acara_penahanan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenahanan3 = DB::table("permintaan_perpanjangan_penahanan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenahanan4 = DB::table("berita_penahanan_lanjutan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenahanan5 = DB::table("berita_pencabutan_pembatalan_penahanan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenahanan6 = DB::table("berita_pengeluaran_penahanan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenahanan7 = DB::table("surat_pembatalan_penahanan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenahanan8 = DB::table("surat_penahanan_lanjutan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenahanan9 = DB::table("surat_pencabutan_pembatalan_penahanan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenahanan10 = DB::table("surat_perpanjangan_penahanan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenahanan11 = DB::table("surat_pengiriman_berkas_perkara")->where("accident_id", "=", "$get_accident")->count();
        $DocPenahanan12 = DB::table("tanda_terima_berkas_perkara")->where("accident_id", "=", "$get_accident")->count();

        $TotalKategori4 = round((
            ($DocPenahanan1 + $DocPenahanan2 + $DocPenahanan3 + $DocPenahanan4 + $DocPenahanan5 +
                $DocPenahanan6 + $DocPenahanan7 + $DocPenahanan8 + $DocPenahanan9 + $DocPenahanan10 +
                $DocPenahanan11 + $DocPenahanan12) / 12) * 100);

        //kategori 5
        $surat_izin_penggeledahan = DB::select('select * from surat_izin_penggeledahan where accident_id = \'' . $get_accident . '\'');
        $surat_perintah_penggeledahan = DB::select('select * from surat_perintah_penggeledahan where accident_id = \'' . $get_accident . '\'');
        $surat_persetujuan_penggeledahan = DB::select('select * from surat_persetujuan_penggeledahan where accident_id = \'' . $get_accident . '\'');
        $berita_acara_penggeledahan = DB::select('select * from berita_acara_penggeledahan where accident_id = \'' . $get_accident . '\'');

        //total ketegori 5
        $DocPenggeledahan1 = DB::table("surat_izin_penggeledahan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenggeledahan2 = DB::table("surat_perintah_penggeledahan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenggeledahan3 = DB::table("surat_persetujuan_penggeledahan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenggeledahan4 = DB::table("berita_acara_penggeledahan")->where("accident_id", "=", "$get_accident")->count();
        $TotalKategori5 = ((($DocPenggeledahan1 + $DocPenggeledahan2 + $DocPenggeledahan3 + $DocPenggeledahan4) / 4) * 100);

        //kategori 6
        $surat_izin_penyitaan = DB::select('select * from surat_izin_penyitaan where accident_id = \'' . $get_accident . '\'');
        $surat_persetujuan_penyitaan = DB::select('select * from surat_persetujuan_penyitaan where accident_id = \'' . $get_accident . '\'');
        $daftar_barang_bukti = DB::select('select * from daftar_barang_bukti where accident_id = \'' . $get_accident . '\'');
        $surat_penyitaan = DB::select('select * from surat_penyitaan left join officers on surat_penyitaan.officer_id = officers.id where accident_id = \'' . $get_accident . '\' ');
        $berita_acara_penyitaan = DB::select('select * from berita_acara_penyitaan where accident_id = \'' . $get_accident . '\'');
        $surat_pengiriman_berkas_perkara = DB::select('select * from surat_pengiriman_berkas_perkara where accident_id = \'' . $get_accident . '\'');
        $tanda_terima_berkas_perkara = DB::select('select * from tanda_terima_berkas_perkara where accident_id = \'' . $get_accident . '\'');
        $surat_pengiriman_tersangka_barang_bukti = DB::select('select * from surat_pengiriman_tersangka_barang_bukti where accident_id = \'' . $get_accident . '\'');
        $berita_acara_serah_terima_tersangka = DB::select('select * from berita_acara_serah_terima_tersangka where accident_id = \'' . $get_accident . '\'');
        $surat_bantuan_penyelidikan = DB::select('select * from surat_bantuan_penyelidikan where accident_id = \'' . $get_accident . '\'');
        $surat_pentitipan_barang = DB::select('select * from surat_pentitipan_barang where accident_id = \'' . $get_accident . '\'');
        $surat_pengembalian_sitaan = DB::select('select * from surat_pengembalian_sitaan where accident_id = \'' . $get_accident . '\'');
        $berita_penitipan_barang = DB::select('select * from berita_penitipan_barang where accident_id = \'' . $get_accident . '\'');
        $berita_pengembalian_sitaan = DB::select('select * from berita_pengembalian_sitaan where accident_id = \'' . $get_accident . '\'');
        $ketetapan_ijin_penyitaan = DB::select('select * from ketetapan_ijin_penyitaan where accident_id = \'' . $get_accident . '\'');
        $ketetapan_persetujuan_penyitaan = DB::select('select * from ketetapan_persetujuan_penyitaan where accident_id = \'' . $get_accident . '\'');
        $surat_tanda_penerimaan = DB::select('select * from surat_tanda_penerimaan where accident_id = \'' . $get_accident . '\'');
        $surat_pengantar = DB::select('select * from surat_pengantar where accident_id = \'' . $get_accident . '\'');
        $berita_penyerahan_berkas = DB::select('select * from berita_penyerahan_berkas where accident_id = \'' . $get_accident . '\'');
        $laporan_gelar_perkara = DB::select('select * from laporan_gelar_perkara where accident_id = \'' . $get_accident . '\'');
        $laporan_perkara_khusus = DB::select('select * from laporan_perkara_khusus where accident_id = \'' . $get_accident . '\'');

        //total ketegori 6
        $DocPenyitaan1 = DB::table("surat_izin_penyitaan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenyitaan2 = DB::table("surat_persetujuan_penyitaan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenyitaan3 = DB::select("
                        select
                        ( (case when accident_id = '$get_accident' then 1 else 0 end) ) as total
                        from daftar_barang_bukti
                        group by accident_id='$get_accident'");
        if ($DocPenyitaan3 == []) {
            $ArrayDocPenyitaan3 = "0";
        } else {
            $ArrayDocPenyitaan3 = json_encode(end($DocPenyitaan3)->total);
        }
        $DocPenyitaan4 = DB::select("
                        select
                        ( (case when accident_id = '$get_accident' then 1 else 0 end) ) as total
                        from surat_penyitaan
                        group by accident_id='$get_accident'");
        if ($DocPenyitaan4 == []) {
            $ArrayDocPenyitaan4 = "0";
        } else {
            $ArrayDocPenyitaan4 = json_encode(end($DocPenyitaan4)->total);
        }
        $DocPenyitaan5 = DB::table("berita_acara_penyitaan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenyitaan6 = DB::table("surat_pengiriman_berkas_perkara")->where("accident_id", "=", "$get_accident")->count();
        $DocPenyitaan7 = DB::table("tanda_terima_berkas_perkara")->where("accident_id", "=", "$get_accident")->count();
        $DocPenyitaan8 = DB::table("surat_pengiriman_tersangka_barang_bukti")->where("accident_id", "=", "$get_accident")->count();
        $DocPenyitaan9 = DB::table("berita_acara_serah_terima_tersangka")->where("accident_id", "=", "$get_accident")->count();
        $DocPenyitaan10 = DB::table("surat_bantuan_penyelidikan")->where("accident_id", "=", "$get_accident")->count();
        $TotalKategori6 = (
            (
                $DocPenyitaan1 + $DocPenyitaan2 + (int)$ArrayDocPenyitaan3 + (int)$ArrayDocPenyitaan4 + $DocPenyitaan5 +
                $DocPenyitaan6 + $DocPenyitaan7 + $DocPenyitaan8 + $DocPenyitaan9 + $DocPenyitaan10
            ) / 10 * 100);


        //kategori 7
        $surat_persetujuan_penyegelan = DB::select('select * from surat_persetujuan_penyegelan where accident_id = \'' . $get_accident . '\'');
        $surat_penyegelan = DB::select('select * from surat_penyegelan left join officers on surat_penyegelan.officer_id = officers.id where accident_id = \'' . $get_accident . '\' ');
        $berita_acara_penyegelan = DB::select('select * from berita_acara_penyegelan where accident_id = \'' . $get_accident . '\'');

        //total ketegori 7
        $DocPenyegelan1 = DB::table("surat_persetujuan_penyegelan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenyegelan2 = DB::select("
            select
            ( (case when accident_id = '$get_accident' then 1 else 0 end) ) as total
            from surat_penyegelan
            group by accident_id='$get_accident'");
        if ($DocPenyegelan2 == []) {
            $ArrayDocPenyegelan2 = "0";
        } else {
            $ArrayDocPenyegelan2 = json_encode(end($DocPenyegelan2)->total);
        }
        $DocPenyegelan3 = DB::table("berita_acara_penyegelan")->where("accident_id", "=", "$get_accident")->count();
        $TotalKategori7 = round(($DocPenyegelan1 + (int)$ArrayDocPenyegelan2 + $DocPenyegelan3) / 3 * 100);
        //kategori 8

        $surat_permintaan_bantuan_labfor = DB::select('select * from surat_permintaan_bantuan_labfor where accident_id = \'' . $get_accident . '\'');
        $surat_hasil_pemeriksaan_labfor = DB::select('select * from surat_hasil_pemeriksaan_labfor where accident_id = \'' . $get_accident . '\'');
        $surat_permintaan_bantuan_identifikasi = DB::select('select * from surat_permintaan_bantuan_identifikasi where accident_id = \'' . $get_accident . '\'');
        $surat_hasil_pemeriksaan_identifikasi = DB::select('select * from surat_hasil_pemeriksaan_identifikasi where accident_id = \'' . $get_accident . '\'');
        $ketetapan_khusus_surat = DB::select('select * from ketetapan_khusus_surat where accident_id = \'' . $get_accident . '\'');
        $perintah_pemeriksaan_surat = DB::select('select * from perintah_pemeriksaan_surat where accident_id = \'' . $get_accident . '\'');
        $berita_pemeriksaan_surat = DB::select('select * from berita_pemeriksaan_surat where accident_id = \'' . $get_accident . '\'');

        //total kategori 8
        $DocPemeriksaan1 = DB::table("surat_permintaan_bantuan_labfor")->where("accident_id", "=", "$get_accident")->count();
        $DocPemeriksaan2 = DB::table("surat_hasil_pemeriksaan_labfor")->where("accident_id", "=", "$get_accident")->count();
        $DocPemeriksaan3 = DB::table("surat_permintaan_bantuan_identifikasi")->where("accident_id", "=", "$get_accident")->count();
        $DocPemeriksaan4 = DB::table("surat_hasil_pemeriksaan_identifikasi")->where("accident_id", "=", "$get_accident")->count();
        $DocPemeriksaan5 = DB::table("ketetapan_khusus_surat")->where("accident_id", "=", "$get_accident")->count();
        $DocPemeriksaan6 = DB::table("perintah_pemeriksaan_surat")->where("accident_id", "=", "$get_accident")->count();
        $DocPemeriksaan7 = DB::table("berita_pemeriksaan_surat")->where("accident_id", "=", "$get_accident")->count();
        $TotalKategori8 = round(($DocPemeriksaan1 + $DocPemeriksaan2 + $DocPemeriksaan3 + $DocPemeriksaan4 +
            $DocPemeriksaan5 + $DocPemeriksaan6 + $DocPemeriksaan7) / 7 * 100);


        //kategori 9
        $surat_blokir_rekening_bank = DB::select('select * from surat_blokir_rekening_bank where accident_id = \'' . $get_accident . '\'');
        $berita_acara_blokir_rekening_bank = DB::select('select * from berita_acara_blokir_rekening_bank where accident_id = \'' . $get_accident . '\'');
        $surat_pembukaan_blokir_rekening_bank = DB::select('select * from surat_pembukaan_blokir_rekening_bank where accident_id = \'' . $get_accident . '\'');
        $berita_acara_pembukaan_blokir_rekening_bank = DB::select('select * from berita_acara_pembukaan_blokir_rekening_bank where accident_id = \'' . $get_accident . '\'');

        //total document kategori 9
        $DocPemblokiran1 = DB::table("surat_blokir_rekening_bank")->where("accident_id", "=", "$get_accident")->count();
        $DocPemblokiran2 = DB::table("berita_acara_blokir_rekening_bank")->where("accident_id", "=", "$get_accident")->count();
        $DocPemblokiran3 = DB::table("surat_pembukaan_blokir_rekening_bank")->where("accident_id", "=", "$get_accident")->count();
        $DocPemblokiran4 = DB::table("berita_acara_pembukaan_blokir_rekening_bank")->where("accident_id", "=", "$get_accident")->count();
        $TotalKategori9  = round(($DocPemblokiran1 + $DocPemblokiran2 + $DocPemblokiran3 + $DocPemblokiran4) / 4 * 100);

        //kategori 10
        $dpo = DB::select('select * from dpo where accident_id = \'' . $get_accident . '\'');
        $surat_pencabutan_tersangka = DB::select('select * from surat_pencabutan_tersangka where accident_id = \'' . $get_accident . '\'');
        $surat_pencabutan_barang = DB::select('select * from surat_pencabutan_barang where accident_id = \'' . $get_accident . '\'');

        //total document kategori 10
        $DocDpo1 = DB::select("
            select
            ( (case when accident_id = '$get_accident' then 1 else 0 end) ) as total
            from dpo
            group by accident_id='$get_accident'");
        if ($DocDpo1 == []) {
            $ArrayDocDpo1 = "0";
        } else {
            $ArrayDocDpo1 = json_encode(end($DocDpo1)->total);
        }
        $DocDpo2 = DB::table("surat_pencabutan_tersangka")->where("accident_id", "=", "$get_accident")->count();
        $DocDpo3 = DB::select("
            select
            ( (case when accident_id = '$get_accident' then 1 else 0 end) ) as total
            from surat_pencabutan_barang
            group by accident_id='$get_accident'");
        if ($DocDpo3 == []) {
            $ArrayDocDpo3 = "0";
        } else {
            $ArrayDocDpo3 = json_encode(end($DocDpo3)->total);
        }
        $DocDpo4 = DB::table("surat_pencabutan_barang")->where("accident_id", "=", "$get_accident")->count();
        $TotalKategori10 = round((
            (int)$ArrayDocDpo1 + $DocDpo2 + (int)$ArrayDocDpo3) / 3 * 100);

        $showImage = uploadImage::where('accident_id', '=', $get_accident)->get();

        //kategori 11
        $surat_penghentian_penyelidikan = DB::select('select * from surat_perintah_penyelidikan where accident_id = \'' . $get_accident . '\'');
        $surat_ketetapan_penyelidikan = DB::select('select * from surat_ketetapan_penyelidikan where accident_id = \'' . $get_accident . '\'');
        $surat_pencabutan_penyelidikan = DB::select('select * from surat_pencabutan_penyelidikan where accident_id = \'' . $get_accident . '\'');
        $surat_penyelidikan_lanjutan = DB::select('select * from surat_penyelidikan_lanjutan where accident_id = \'' . $get_accident . '\'');
        $berita_penghentian_penyelidikan = DB::select('select * from berita_penghentian_penyelidikan where accident_id = \'' . $get_accident . '\'');
        $persetujuan_pejabat_berwenang = DB::select('select * from persetujuan_pejabat_berwenang where accident_id = \'' . $get_accident . '\'');
        $surat_penghentian_penyidikan = DB::select('select * from sp3 where accident_id = \'' . $get_accident . '\'');
        $surat_ketetapan_penyidikan = DB::select('select * from surat_ketetapan_penyidikan where accident_id = \'' . $get_accident . '\'');
        $putusan_pra_peradilan = DB::select('select * from putusan_pra_peradilan where accident_id = \'' . $get_accident . '\'');
        $surat_pencabutan_penyidikan = DB::select('select * from surat_pencabutan_penyidikan where accident_id = \'' . $get_accident . '\'');
        $surat_penyidikan_lanjutan = DB::select('select * from surat_penyidikan_lanjutan where accident_id = \'' . $get_accident . '\'');
        $berita_penghentian_penyidikan = DB::select('select * from berita_penghentian_penyidikan where accident_id = \'' . $get_accident . '\'');
        $surat_pernyataan = DB::select('select * from surat_pernyataan where accident_id = \'' . $get_accident . '\'');
        $surat_kesepakatan_perdamaian = DB::select('select * from surat_kesepakatan_perdamaian where accident_id = \'' . $get_accident . '\'');
        $upload_surat_ketetapan = DB::select('select * from upload_surat_ketetapan where accident_id = \'' . $get_accident . '\'');
        $no_spdp = DB::select('select document_number from doc.surat_pemberitahuan_dimulainya_penyidikan_documents where accident_id = \'' . $get_accident . '\'');
        $value_spdp = isset($no_spdp[0]->document_number) ? $no_spdp[0]->document_number : '';

        //kategori 12
        $surat_penetapan_tersangka = DB::select('select * from surat_penetapan_tersangka where accident_id = \'' . $get_accident . '\'');
        $surat_perintah_penangkapan = DB::select('select * from surat_perintah_penangkapan where accident_id = \'' . $get_accident . '\'');
        $surat_membawa_menghadapkan = DB::select('select * from surat_membawa_menghadapkan where accident_id = \'' . $get_accident . '\'');
        $surat_pelepasan_tersangka = DB::select('select * from surat_pelepasan_tersangka where accident_id = \'' . $get_accident . '\'');
        $berita_acara_penangkapan = DB::select('select * from berita_acara_penangkapan where accident_id = \'' . $get_accident . '\'');
        $berita_pelepasan_tersangka = DB::select('select * from berita_pelepasan_tersangka where accident_id = \'' . $get_accident . '\'');
        //total-kategori 12
        $DocPenangkapan1 = DB::table("surat_penetapan_tersangka")->where("accident_id", "=", "$get_accident")->count();
        $DocPenangkapan2 = DB::table("surat_perintah_penangkapan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenangkapan3 = DB::table("surat_membawa_menghadapkan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenangkapan4 = DB::table("surat_pelepasan_tersangka")->where("accident_id", "=", "$get_accident")->count();
        $DocPenangkapan5 = DB::table("berita_acara_penangkapan")->where("accident_id", "=", "$get_accident")->count();
        $DocPenangkapan6 = DB::table("berita_pelepasan_tersangka")->where("accident_id", "=", "$get_accident")->count();
        $TotalKategori12 = round(($DocPenangkapan1 + $DocPenangkapan2 + $DocPenangkapan3 + $DocPenangkapan4 + $DocPenangkapan5 + $DocPenangkapan6) / 6 * 100);

        //kategori 13
        $suratP21Tahap1 = DB::table('surat_p21_tahap_1')->where('accident_id', $get_accident)->first();
        $suratP21Tahap2 = DB::table('surat_p21_tahap_2')->where('accident_id', $get_accident)->first();

        //total-kategori 13
        $DocP21Tahap1 = DB::table("surat_p21_tahap_1")->where("accident_id", "=", "$get_accident")->count();
        $DocP21Tahap2 = DB::table("surat_p21_tahap_2")->where("accident_id", "=", "$get_accident")->count();
        $TotalKategori13  = round(($DocP21Tahap1 + $DocP21Tahap2) / 2 * 100);

        $documentTypes = [
            'suratPerintahPenyelidikanDocuments',
            'suratPerintahPenyidikanDocuments',
            'suratPerintahTugasDocuments',
            'laporanHasilGelarPerkaraDocuments',
            'suratKetetapanTentangPenetapanTersangkaDocuments',
            'suratPemberitahuanDimulainyaPenyidikanDocuments',
            'suratPemberitahuanPerkembanganHasilPenyidikanDocuments',
            'beritaAcaraPenahananDocuments',
        ];

        $accidentDocument = Accident::with($documentTypes)
            ->where('id', $get_accident)
            ->first();

        $accidentDocumentCollection = collect();
        $countAccidentDocumentCollection = collect();
        foreach ($documentTypes as $documentType) {
            $documents = $accidentDocument->$documentType;
            if (!$documents->isEmpty()) {
                $accidentDocumentCollection = $accidentDocumentCollection->merge($documents);

                if ($documentType == 'suratPemberitahuanDimulainyaPenyidikanDocuments') {
                    $countAccidentDocumentCollection = $countAccidentDocumentCollection->put($documentType, [
                        "count" => $documents->whereIn('status_id', ['85', '86'])->count()
                    ]);
                } elseif ($documentType == 'suratPerintahPenyelidikanDocuments') {
                    $countAccidentDocumentCollection = $countAccidentDocumentCollection->put($documentType, [
                        "count" => $documents->whereIn('status_id', ['11', '85', '86'])->count()
                    ]);

                    $countAccidentDocumentCollection = $countAccidentDocumentCollection->put($documentType . 'RequiredUnlockForm', [
                        "count" => $documents->whereIn('status_id', ['5', '6', '7', '11', '86'])->count()
                    ]);
                } elseif ($documentType == 'suratKetetapanTentangPenetapanTersangkaDocuments') {
                    $countAccidentDocumentCollection = $countAccidentDocumentCollection->put($documentType, [
                        "count" => $documents->whereIn('status_id', ['85', '86'])->count()
                    ]);
                }
            }
        }

        $accidentDocumentCollection = $accidentDocumentCollection->sortByDesc('created_at');
        $data['accidentDocuments'] = $accidentDocumentCollection;
        $data['countAccidentDocuments'] = $countAccidentDocumentCollection;

        $page = request()->query('page');
        $data['page'] = $page;

        if (!empty($page) && $page == 'participants') {
            $reportedPersons = ReportedPerson::where('accident_id', $get_accident)->get();

            $data['reportedPersons'] = $reportedPersons;
        }

        $data['id'] = $accident[0]->id;
        $data['no_lp'] = $accident[0]->no_lp;
        $data['category_laka'] = $accident[0]->category_laka;
        $data['accident_md'] = $accident[0]->md;
        $data['accident_selra_flag'] = $accident[0]->selra_flag;
        $data['accident_date'] = $accident[0]->accident_date;
        $data['accident_report_date'] = $accident[0]->accident_report_date;
        $data['accident_tindak_lanjut'] = $accident[0]->accident_tindak_lanjut;
        $data['accident_proses'] = $accident[0]->accident_proses;
        $data['accident_last_update'] = $accident[0]->accident_last_update;
        $data['tipe_update'] = $accident[0]->tipe_update;
        $data['tipe_berkas'] = $accident[0]->tipe_berkas;
        $data['selra'] = $selra;
        $data['selra_flag'] = $selra_flag;
        $data['state_selra_flag'] = $state_selra_flag;
        $data['gender'] = $gender;
        $data['education'] = $education;
        $data['identity_type'] = $identity_type;
        $data['religion'] = $religion;
        $data['p21_tahap_2_status'] = $p21Tahap2Status->state_selra_flag;
        $data['id_spdik'] = ($spdik) ? $spdik->id : '';
        $data['id_spgas'] = ($spgas) ? $spgas->id : '';
        $data['pengadilan_id'] = $pengadilan;
        $data['kejaksaan_id'] = $jaksa;
        $data['officer'] = $officer;

        $data['polres'] = $polres;
        $data['penandatangan'] = $penandatangan;
        $data['springas_officer'] = $springas_officer;


        //kategori 1
        // $springas = $springas == []?null:$springas;
        $data['springas'] = $springas;
        $data['surat_perintah_tugas'] = $surat_perintah_tugas;
        $data['surat_perintah_penyelidikan'] = $surat_perintah_penyelidikan;
        $data['surat_perintah_penyidikan'] = $surat_perintah_penyidikan;
        $data['sprinlidik'] = $sprinlidik;
        $data['sprindik'] = $sprindik;
        // $data['surat_spdp'] = $surat_spdp;
        $data['spdp'] = $spdp;
        $data['spdp_upload'] = $spdp_upload;
        $data['spdik_letter_number'] = ($spdik) ? $spdik->document_number : '';
        $data['spdik_issued_date'] = Carbon::parse(($spdik) ? $spdik->document_date : '')->format('d F Y');
        $data['spgas'] = $spgas;
        $data['lhgp'] = $lhgp;
        $data['no_spdp'] = $spdp == null ? null : $spdp[0]->no_spdp;
        $data['surat_berkas'] = $surat_berkas;
        $data['officer'] = $officer;
        $data['LaporanPolisi'] = $LaporanPolisi;
        $data['BAPenangkapanTKP'] = $BAPenangkapanTKP;
        $data['BAPemotretan'] = $BAPemotretan;
        $data['BAPengambilanDarah'] = $BAPengambilanDarah;
        $data['laporan_hasil_penyelidikan'] = $laporan_hasil_penyelidikan;
        $data['BAIntrogasi'] = $BAIntrogasi;
        $data['SpdpUpload'] = $SpdpUpload;
        $data['Sddl'] = $Sddl;
        $data['pejabat'] = $pejabat;
        $data['suspectsName'] = ($suspects) ? $suspects->name : '';
        $data['TotalKategori1'] = $TotalKategori1;

        //kategori 2
        $data['daftar_saksi'] = $daftar_saksi;
        $data['surat_perintah_membawa_saksi'] = $surat_perintah_membawa_saksi;
        $data['berita_acara_membawa_saksi'] = $berita_acara_membawa_saksi;
        $data['berita_acara_penyumpahan_saksi'] = $berita_acara_penyumpahan_saksi;
        $data['berita_pemeriksaan_saksi'] = $berita_pemeriksaan_saksi;
        $data['berita_pemeriksaan_ahli'] = $berita_pemeriksaan_ahli;
        $data['TotalKategori2'] = $TotalKategori2;

        //kategori 3
        $data['daftar_tersangka'] = $daftar_tersangka;
        $data['surat_panggilan_tersangka'] = $surat_panggilan_tersangka;
        // $data['surat_perintah_penangkapan']=$surat_perintah_penangkapan;
        $data['berita_acara_pemeriksaan_tersangka'] = $berita_acara_pemeriksaan_tersangka;
        $data['berita_acara_konfrontasi'] = $berita_acara_konfrontasi;
        $data['berita_acara_rekonstruksi'] = $berita_acara_rekonstruksi;
        $data['sket_tkp'] = $sket_tkp;
        $data['surat_bantuan_penangkapan'] = $surat_bantuan_penangkapan;
        $data['berita_penyerahan_tersangka'] = $berita_penyerahan_tersangka;
        // $data['berita_pelepasan_tersangka']=$berita_pelepasan_tersangka;
        $data['TotalKategori3'] = $TotalKategori3;

        //kategori 4
        $data['berita_penahanan_lanjutan'] = $berita_penahanan_lanjutan;
        $data['berita_pencabutan_pembatalan_penahanan'] = $berita_pencabutan_pembatalan_penahanan;
        $data['berita_pengeluaran_penahanan'] = $berita_pengeluaran_penahanan;
        $data['surat_pembatalan_penahanan'] = $surat_pembatalan_penahanan;
        $data['surat_penahanan_lanjutan'] = $surat_penahanan_lanjutan;
        $data['surat_pencabutan_pembatalan_penahanan'] = $surat_pencabutan_pembatalan_penahanan;
        $data['surat_perpanjangan_penahanan'] = $surat_perpanjangan_penahanan;
        $data['surat_perintah_penahanan'] = $surat_perintah_penahanan;
        $data['berita_acara_penahanan'] = $berita_acara_penahanan;
        $data['permintaan_perpanjangan_penahanan'] = $permintaan_perpanjangan_penahanan;
        $data['TotalKategori4'] = $TotalKategori4;

        //kategori 5
        $data['surat_izin_penggeledahan'] = $surat_izin_penggeledahan;
        $data['surat_perintah_penggeledahan'] = $surat_perintah_penggeledahan;
        $data['surat_persetujuan_penggeledahan'] = $surat_persetujuan_penggeledahan;
        $data['berita_acara_penggeledahan'] = $berita_acara_penggeledahan;
        $data['TotalKategori5'] = $TotalKategori5;

        //kategori 6
        $data['surat_izin_penyitaan'] = $surat_izin_penyitaan;
        $data['surat_persetujuan_penyitaan'] = $surat_persetujuan_penyitaan;
        $data['daftar_barang_bukti'] = $daftar_barang_bukti;
        $data['surat_penyitaan'] = $surat_penyitaan;
        $data['berita_acara_penyitaan'] = $berita_acara_penyitaan;
        $data['surat_pengiriman_berkas_perkara'] = $surat_pengiriman_berkas_perkara;
        $data['tanda_terima_berkas_perkara'] = $tanda_terima_berkas_perkara;
        $data['surat_pengiriman_tersangka_barang_bukti'] = $surat_pengiriman_tersangka_barang_bukti;
        $data['berita_acara_serah_terima_tersangka'] = $berita_acara_serah_terima_tersangka;
        $data['surat_bantuan_penyelidikan'] = $surat_bantuan_penyelidikan;
        $data['surat_pentitipan_barang'] = $surat_pentitipan_barang;
        $data['surat_pengembalian_sitaan'] = $surat_pengembalian_sitaan;
        $data['berita_penitipan_barang'] = $berita_penitipan_barang;
        $data['berita_pengembalian_sitaan'] = $berita_pengembalian_sitaan;
        $data['ketetapan_ijin_penyitaan'] = $ketetapan_ijin_penyitaan;
        $data['ketetapan_persetujuan_penyitaan'] = $ketetapan_persetujuan_penyitaan;
        $data['surat_tanda_penerimaan'] = $surat_tanda_penerimaan;
        $data['surat_pengantar'] = $surat_pengantar;
        $data['berita_penyerahan_berkas'] = $berita_penyerahan_berkas;
        $data['laporan_gelar_perkara'] = $laporan_gelar_perkara;
        $data['laporan_perkara_khusus'] = $laporan_perkara_khusus;
        $data['TotalKategori6'] = $TotalKategori6;

        //kategori 7
        $data['surat_persetujuan_penyegelan'] = $surat_persetujuan_penyegelan;
        $data['surat_penyegelan'] = $surat_penyegelan;
        $data['berita_acara_penyegelan'] = $berita_acara_penyegelan;
        $data['TotalKategori7'] = $TotalKategori7;


        //kategori 8
        $data['surat_permintaan_bantuan_labfor'] = $surat_permintaan_bantuan_labfor;
        $data['surat_hasil_pemeriksaan_labfor'] = $surat_hasil_pemeriksaan_labfor;
        $data['surat_permintaan_bantuan_identifikasi'] = $surat_permintaan_bantuan_identifikasi;
        $data['surat_hasil_pemeriksaan_identifikasi'] = $surat_hasil_pemeriksaan_identifikasi;
        $data['ketetapan_khusus_surat'] = $ketetapan_khusus_surat;
        $data['perintah_pemeriksaan_surat'] = $perintah_pemeriksaan_surat;
        $data['berita_pemeriksaan_surat'] = $berita_pemeriksaan_surat;
        $data['TotalKategori8'] = $TotalKategori8;

        //kategori 9
        $data['surat_blokir_rekening_bank'] = $surat_blokir_rekening_bank;
        $data['berita_acara_blokir_rekening_bank'] = $berita_acara_blokir_rekening_bank;
        $data['surat_pembukaan_blokir_rekening_bank'] = $surat_pembukaan_blokir_rekening_bank;
        $data['berita_acara_pembukaan_blokir_rekening_bank'] = $berita_acara_pembukaan_blokir_rekening_bank;
        $data['TotalKategori9'] = $TotalKategori9;

        //kategori 10
        $data['dpo'] = $dpo;
        $data['surat_pencabutan_tersangka'] = $surat_pencabutan_tersangka;
        $data['surat_pencabutan_barang'] = $surat_pencabutan_barang;
        $data['TotalKategori10'] = $TotalKategori10;

        //kategori 11
        $data['surat_penghentian_penyelidikan'] = $surat_penghentian_penyelidikan;
        $data['surat_ketetapan_penyelidikan'] = $surat_ketetapan_penyelidikan;
        $data['surat_pencabutan_penyelidikan'] = $surat_pencabutan_penyelidikan;
        $data['surat_penyelidikan_lanjutan'] = $surat_penyelidikan_lanjutan;
        $data['berita_penghentian_penyelidikan'] = $berita_penghentian_penyelidikan;
        $data['persetujuan_pejabat_berwenang'] = $persetujuan_pejabat_berwenang;
        $data['surat_penghentian_penyidikan'] = $surat_penghentian_penyidikan;
        $data['surat_ketetapan_penyidikan'] = $surat_ketetapan_penyidikan;
        $data['putusan_pra_peradilan'] = $putusan_pra_peradilan;
        $data['surat_pencabutan_penyidikan'] = $surat_pencabutan_penyidikan;
        $data['surat_penyidikan_lanjutan'] = $surat_penyidikan_lanjutan;
        $data['berita_penghentian_penyidikan'] = $berita_penghentian_penyidikan;
        $data['surat_pernyataan'] = $surat_pernyataan;
        $data['surat_kesepakatan_perdamaian'] = $surat_kesepakatan_perdamaian;
        $data['upload_surat_ketetapan'] = $upload_surat_ketetapan;
        $data['no_spdp'] = $no_spdp;
        $data['value_spdp'] = $value_spdp;

        //kategori 12
        $data['surat_penetapan_tersangka'] = $surat_penetapan_tersangka;
        $data['surat_perintah_penangkapan'] = $surat_perintah_penangkapan;
        $data['surat_membawa_menghadapkan'] = $surat_membawa_menghadapkan;
        $data['surat_pelepasan_tersangka'] = $surat_pelepasan_tersangka;
        $data['berita_acara_penangkapan'] = $berita_acara_penangkapan;
        $data['berita_pelepasan_tersangka'] = $berita_pelepasan_tersangka;
        $data['TotalKategori12'] = $TotalKategori12;

        //kategori 13
        $data['surat_p21_tahap_1'] = $suratP21Tahap1;
        $data['surat_p21_tahap_2'] = $suratP21Tahap2;

        $p21Spdp = DB::table('spdp')->where('accident_id', $get_accident)->first();
        $findPolres = Polres::where('id', ($accident[0]->polres_id) ? $accident[0]->polres_id : 0)->first();
        $data['surat_p21_province'] = ($findPolres) ? $findPolres->polres_province : '-';
        $data['surat_p21_polres'] = ($findPolres) ? $findPolres->name : '-';
        $data['surat_p21_polres_address'] = ($findPolres) ? $findPolres->address . ', ' . $findPolres->polres_district : '-';
        $data['surat_p21_date'] = Carbon::now()->format('d F Y');
        $data['surat_p21_start_date'] = ($suratP21Tahap1) ? $suratP21Tahap1->p21_date : '-';
        $data['surat_p21_place'] = ($findPolres) ? $findPolres->polres_district : '-';
        $data['surat_p21_letter_recepient'] = ($findPolres) ? $findPolres->kejaksaan_name : '-';
        $data['surat_p21_letter_recepient_place'] = ($findPolres) ? $findPolres->kejaksaan_district : '-';
        $data['surat_p21_no_spdp'] = ($p21Spdp) ? $p21Spdp->no_spdp : '';
        $data['surat_p21_spdp_date'] = ($p21Spdp) ? $p21Spdp->spdp_date : '';
        $data['surat_p21_suspects'] = DB::table('daftar_tersangka')->where('accident_id', $get_accident)->get();
        $data['surat_p21_no_lp'] = (Accident::where('id', $get_accident)->first()) ? Accident::where('id', $get_accident)->first()->no_lp : '-';
        $data['surat_p21_accident_date'] = (Accident::where('id', $get_accident)->first()) ? Carbon::parse(Accident::where('id', $get_accident)->first()->lp_date)->format('Y-m-d') : '';
        $data['surat_p21_description'] = (Accident::where('id', $get_accident)->first()) ? Accident::where('id', $get_accident)->first()->damage_lose_desc : '-';
        $data['surat_p21_penyidik_name'] = "-";
        $data['surat_p21_penyidik_position'] = "-";
        $data['surat_p21_penyidik_nrp'] = "-";
        $data['surat_p21_no'] = ($suratP21Tahap1) ? $suratP21Tahap1->no_p21 : '-';
        $data['surat_p21_evidences'] = DB::table('daftar_barang_bukti')->where('accident_id', $get_accident)->get();
        $data['TotalKategori13'] = $TotalKategori13;
        $data['documentStages'] = $documentStages;
        $data['showImage'] = $showImage;

        return view('produktivitas.produktivitas-view', $data);
    }

    public function create_surat_p21_tahap_1()
    {
        $data['accident_id'] = request()->accident_id;

        return view('produktivitas.surat-p21.create-surat-p21-tahap-1', $data);
    }

    public function submitSelra(Request $request, $accidentId)
    {
        $request->validate([
            'selraDate' => [
                'required',
            ],
            'selraNumber' => [
                'required',
                'max:255',
            ],
            'selraType' => [
                'required',
            ],
            'uploadDate' => [
                'required',
            ],
            'selraFile' => [
                'required',
                'mimes:pdf',
                'max: 8192'
            ],
            'is_completed_with_rj' => [
                'nullable',
            ],
        ]);

        $selraTypeId = htmlspecialchars($request->selraType);
        $selraDate = htmlspecialchars($request->selraDate);
        $selraNumber = htmlspecialchars($request->selraNumber);
        $uploadedAt = date('Y-m-d H:i:s');
        $userAuth = Auth::user();

        $selraFile = $request->file('selraFile');

        DB::beginTransaction();
        try {
            $selra = NULL;
            $accident = Accident::find($accidentId);

            if ($selraTypeId == "P21TAHAP2") {
                $selra = DB::table('ref')->where('id', 'S0101')->first();

                $accident->selra_flag = $selra->id;
                $accident->state_selra_flag = 1;
            } else {
                $selra = DB::table('ref')->where('id', $selraTypeId)->first();

                $accident->selra_flag = $selra->id;
                $accident->state_selra_flag = 0;
            }

            $accident->is_resolved_with_rj = $request->has('is_completed_with_rj')
            || $request->boolean('is_completed_with_rj');

            $accident->last_update = Carbon::now();
            $accident->category = 'D110115';
            $accident->tipe_update = 'UPLOAD';
            $accident->save();

            $fileName = uniqid() . '-' . str_replace(' ', '_', $selraFile->getClientOriginalName());

            $accidentResolutionData = [
                'accident_id' => $accident->id,
                'type_id' => $selra->id,
                'type_name' => $selra->name,
                'flag' => ($selraTypeId == "P21TAHAP2") ? 'TAHAP 2' : null,
                'number' => $selraNumber,
                'date' => $selraDate,
                'uploaded_at' => $uploadedAt,
                'file' => $fileName,
            ];

            $accidentResolution = AccidentResolution::updateOrCreate(
                ['accident_id' => $accident->id],
                $accidentResolutionData
            );

            //check if file already exists
            $accidentResolutionFile = $accidentResolution->file;
            if ($accidentResolutionFile) {
                $filePath = public_path('file/penghentian/upload-surat-ketetapan/' . $accidentResolutionFile);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            //upload file
            $selraFile->move(public_path('file/penghentian/upload-surat-ketetapan'), $fileName);
            UploadSuratKetetapan::updateOrCreate(
                [
                    'accident_id' => $accident->id,
                    'initial' => 'upload-surat-ketetapan',
                ],
                [
                    'name' => $fileName,
                    'category' => 'D110115',
                    'created_by' => $userAuth->first_name . ' ' . $userAuth->last_name,
                ]
            );

            if (env('APP_MODE') == 'PRODUCTION') {
                $headers = [
                    'Content-Type' => 'application/json',
                    'Key' => 'Hy6d3K1d93LOHRfbeE0KKly1YK9t4YdGsbNDEvyxAYI=icell'
                ];

                $response = Http::withHeaders($headers)->post('https://irsms.korlantas.polri.go.id/irsmsapi/api/icellSelra', [
                    'id' => $accident->id,
                    'selra_flag' => $selra->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // dd($e);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data');
        }

        // Redirect with param accident_id
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function update_selra(Request $request)
    {
        $accidentId = $request->accident_id;

        DB::beginTransaction();
        try {
            $accident = Accident::findOrFail($accidentId);

            $accident->selra_flag = $request->selra_flag;
            $accident->state_selra_flag = $request->state_selra_flag;
            $accident->save();

            DB::commit();

            if (env('APP_MODE') == 'PRODUCTION') {
                $headers = [
                    'Content-Type' => 'application/json',
                    'Key' => 'Hy6d3K1d93LOHRfbeE0KKly1YK9t4YdGsbNDEvyxAYI=icell'
                ];

                $response = Http::withHeaders($headers)->post('https://irsms.korlantas.polri.go.id/irsmsapi/api/icellSelra', [
                    'id' => $accident->id,
                    'selra_flag' => $accident->selra_flag,
                ]);
            }

            return response()->json([$accident]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([$th]);
        }
    }

    public function update_state_selra(Request $request)
    {
        $accident = Accident::updateOrCreate(
            ['id' => $request->accident_id],
            [
                'state_selra_flag' => $request->state_selra_flag,
            ]
        );

        return response()->json($accident);
    }


    public function get_saksi(Request $request)
    {
        $accident = $request->accident_id;
        // $accident = 'eff6a3c4-660a-4e8e-950d-0e7a68cf863b';
        if ($request->ajax()) {
            // $data = DB::select('select * from daftar_saksi where accident_id = \''.$accident.'\' order by created_at')->get();
            $get_data = DB::select('select saksi.id as id,saksi.name as name,
                                    ref.name as gender,saksi.city as city,
                                    to_char(birth_date, \'DD-MM-YYYY\') as birth_date,
                                    saksi.citizen as citizen
                                    from daftar_saksi as saksi left join ref on ref.id = saksi.gender
                                    where accident_id = \'' . $accident . '\' order by saksi.created_at ');
            $data['saksi'] = $get_data;
            // $data = DaftarSaksi::where('accident_id',''.$accident.'')->get();
            return Datatables::of($data['saksi'])
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editBook">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteBook">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function delete_saksi(Request $request)
    {
        $saksi = DaftarSaksi::find($request->id);
        $saksi->delete();
        return response()->json('sukses');
    }

    public function edit_saksi(Request $request)
    {
        $id = $request->id;
        $get_saksi = DB::select('select id,name as name_saksi,gender,city,to_char(birth_date, \'DD-MM-YYYY\') as birth_date,religion,job,education,phone,citizen,address from daftar_saksi where id = \'' . $id . '\' ');
        $saksi = $get_saksi[0];
        return response()->json($saksi);
    }

    public function add_surat_p21_tahap_1(Request $request)
    {
        $accident_id = $request->accident_id_p21_tahap_1;

        $province = htmlspecialchars($request->province);
        $polres = htmlspecialchars($request->polres);
        $polresAddress = htmlspecialchars($request->polres_address);
        $noP21 = htmlspecialchars($request->no_p21);
        $p21Date = htmlspecialchars($request->p21_date);
        $p21Location = htmlspecialchars($request->p21_location);
        $classification = htmlspecialchars($request->classification);
        $attachment = htmlspecialchars($request->attachment);
        $subject = htmlspecialchars($request->subject);
        $letterRecipient = htmlspecialchars($request->letter_recipient);
        $recipientLocation = htmlspecialchars($request->recipient_location);
        $noSpdp = htmlspecialchars($request->no_spdp);
        $spdpDate = htmlspecialchars($request->spdp_date);
        $noLp = htmlspecialchars($request->no_lp);
        $accidentDate = htmlspecialchars($request->accident_date);
        $offenseArticles = $request->offense_articles;
        $suspects = $request->suspects;
        $incidentDescription = htmlspecialchars($request->incident_description);
        $cc = $request->cc;
        $penyidikName = htmlspecialchars($request->penyidik_name);
        $penyidikPosition = htmlspecialchars($request->penyidik_position);
        $penyidikNrp = htmlspecialchars($request->penyidik_nrp);

        // JSON Values creator
        $offenseArticlesJSON = json_encode($offenseArticles);
        $ccJSON = json_encode($cc);

        // get all suspect from id
        $suspectList = [];
        $noSuspect = 0;

        foreach ($suspects as $suspect) {
            $get_suspect = DB::table('daftar_tersangka')->where('id', $suspect)->first();

            $suspectList[$noSuspect] = $get_suspect->id;
            $noSuspect++;
        }
        $suspectsJSON = json_encode($suspectList);
        SuratP21Tahap1::create([
            'accident_id' => $accident_id,

            'province_name' => $province,
            'polres_name' => $polres,
            'polres_address' => $polresAddress,
            'no_p21' => $noP21,
            'p21_date' => $p21Date,
            'p21_location' => $p21Location,
            'classification' => $classification,
            'attachment' => $attachment,
            'subject' => $subject,
            'letter_recipient' => $letterRecipient,
            'recipient_location' => $recipientLocation,
            'no_spdp' => $noSpdp,
            'spdp_date' => $spdpDate,
            'no_lp' => $noLp,
            'accident_date' => $accidentDate,
            'suspects' => $suspectsJSON,
            'description' => $incidentDescription,
            'cc' => $ccJSON,
            'offense_articles' => $offenseArticlesJSON,
            'penyidik_name' => $penyidikName,
            'penyidik_position' => $penyidikPosition,
            'penyidik_nrp' => $penyidikNrp,

            'created_by' => Auth::user()->name,
        ]);

        Accident::where('id', $accident_id)
            ->update([
                'last_update' => Carbon::now(),
                'category' => 'D010104',
                'tipe_update' => 'MEMBUAT'
            ]);
        return back();
    }

    public function json_surat_p21_tahap_1(Request $request)
    {
        $accident_id = $request->accident_id;
        $surat_p21_tahap_1 = DB::table('surat_p21_tahap_1')->where('accident_id', $accident_id)->first();
        return response()->json($surat_p21_tahap_1);
    }

    public function edit_surat_p21_tahap_1(Request $request)
    {
        $accident_id = $request->edit_accident_id_p21_tahap_1;

        $province = htmlspecialchars($request->province);
        $polres = htmlspecialchars($request->polres);
        $polresAddress = htmlspecialchars($request->polres_address);
        $noP21 = htmlspecialchars($request->no_p21);
        $p21Date = htmlspecialchars($request->p21_date);
        $p21Location = htmlspecialchars($request->p21_location);
        $classification = htmlspecialchars($request->classification);
        $attachment = htmlspecialchars($request->attachment);
        $subject = htmlspecialchars($request->subject);
        $letterRecipient = htmlspecialchars($request->letter_recipient);
        $recipientLocation = htmlspecialchars($request->recipient_location);
        $noSpdp = htmlspecialchars($request->no_spdp);
        $spdpDate = htmlspecialchars($request->spdp_date);
        $noLp = htmlspecialchars($request->no_lp);
        $accidentDate = htmlspecialchars($request->accident_date);
        $offenseArticles = $request->offense_articles;
        $suspects = $request->suspects;
        $incidentDescription = htmlspecialchars($request->incident_description);
        $cc = $request->cc;
        $penyidikName = htmlspecialchars($request->penyidik_name);
        $penyidikPosition = htmlspecialchars($request->penyidik_position);
        $penyidikNrp = htmlspecialchars($request->penyidik_nrp);

        // JSON Values creator
        $offenseArticlesJSON = json_encode($offenseArticles);
        $ccJSON = json_encode($cc);

        // get all suspect from id
        $suspectList = [];
        $noSuspect = 0;

        foreach ($suspects as $suspect) {
            $get_suspect = DB::table('daftar_tersangka')->where('id', $suspect)->first();

            $suspectList[$noSuspect] = $get_suspect->id;
            $noSuspect++;
        }
        $suspectsJSON = json_encode($suspectList);

        SuratP21Tahap1::where('accident_id', $accident_id)
            ->update([
                'province_name' => $province,
                'polres_name' => $polres,
                'polres_address' => $polresAddress,
                'no_p21' => $noP21,
                'p21_date' => $p21Date,
                'p21_location' => $p21Location,
                'classification' => $classification,
                'attachment' => $attachment,
                'subject' => $subject,
                'letter_recipient' => $letterRecipient,
                'recipient_location' => $recipientLocation,
                'no_spdp' => $noSpdp,
                'spdp_date' => $spdpDate,
                'no_lp' => $noLp,
                'accident_date' => $accidentDate,
                'suspects' => $suspectsJSON,
                'description' => $incidentDescription,
                'cc' => $ccJSON,
                'offense_articles' => $offenseArticlesJSON,
                'penyidik_name' => $penyidikName,
                'penyidik_position' => $penyidikPosition,
                'penyidik_nrp' => $penyidikNrp,

                'created_by' => Auth::user()->name,
            ]);

        Accident::where('id', $accident_id)
            ->update([
                'last_update' => Carbon::now(),
                'category' => 'D010104',
                'tipe_update' => 'MENGUBAH'
            ]);
        return back();
    }

    public function view_surat_p21_tahap_1(Request $request)
    {
        $accident_id = $request->input('accident_id');
        $surat_p21_tahap_1 = DB::table('surat_p21_tahap_1')->where('accident_id', $accident_id)->first();
        $accident = DB::table('accidents')->where('id', $accident_id)->first();
        $accident_date = Carbon::parse($accident->accident_date)->format('d F Y');
        $accident_time = Carbon::parse($accident->accident_time)->format('H:m');

        $data['accident'] = $accident;
        $data['accident_time'] = $accident_time;
        $data['accident_date'] = Carbon::parse($accident->accident_date)->format('d F Y');
        $data['road_name'] = $accident->road_name;

        $data['province_name'] = $surat_p21_tahap_1->province_name;
        $data['polres_name'] = $surat_p21_tahap_1->polres_name;
        $data['polres_address'] = $surat_p21_tahap_1->polres_address;
        $data['no_p21'] = $surat_p21_tahap_1->no_p21;
        $data['p21_date'] = Carbon::parse($surat_p21_tahap_1->p21_date)->format('d F Y');
        $data['p21_location'] = $surat_p21_tahap_1->p21_location;
        $data['classification'] = $surat_p21_tahap_1->classification;
        $data['attachment'] = $surat_p21_tahap_1->attachment;
        $data['subject'] = $surat_p21_tahap_1->subject;
        $data['letter_recipient'] = $surat_p21_tahap_1->letter_recipient;
        $data['recipient_location'] = $surat_p21_tahap_1->recipient_location;
        $data['no_spdp'] = $surat_p21_tahap_1->no_spdp;
        $data['spdp_date'] = Carbon::parse($surat_p21_tahap_1->spdp_date)->format('d F Y');
        $data['no_lp'] = $surat_p21_tahap_1->no_lp;
        $data['suspects'] = $surat_p21_tahap_1->suspects;
        $data['description'] = $surat_p21_tahap_1->description;
        $data['cc'] = $surat_p21_tahap_1->cc;
        $data['offense_articles'] = $surat_p21_tahap_1->offense_articles;
        $data['penyidik_name'] = $surat_p21_tahap_1->penyidik_name;
        $data['penyidik_position'] = $surat_p21_tahap_1->penyidik_position;
        $data['penyidik_nrp'] = $surat_p21_tahap_1->penyidik_nrp;

        return view('produktivitas.surat-p21.cetak-surat-p21-tahap-1', $data);
    }

    public function add_surat_p21_tahap_2(Request $request)
    {
        $accident_id = $request->accident_id_p21_tahap_2;

        $province = htmlspecialchars($request->province);
        $polres = htmlspecialchars($request->polres);
        $polresAddress = htmlspecialchars($request->polres_address);
        $noP21 = htmlspecialchars($request->no_p21);
        $p21Date = htmlspecialchars($request->p21_date);
        $p21StartDate = htmlspecialchars($request->p21_start_date);
        $p21Location = htmlspecialchars($request->p21_location);
        $classification = htmlspecialchars($request->classification);
        $attachment = htmlspecialchars($request->attachment);
        $subject = htmlspecialchars($request->subject);
        $letterRecipient = htmlspecialchars($request->letter_recipient);
        $recipientLocation = htmlspecialchars($request->recipient_location);
        $evidences = $request->evidences;
        $noLp = htmlspecialchars($request->no_lp);
        $accidentDate = htmlspecialchars($request->accident_date);
        $offenseArticles = $request->offense_articles;
        $suspects = $request->suspects;
        $incidentDescription = htmlspecialchars($request->incident_description);
        $cc = $request->cc;
        $penyidikName = htmlspecialchars($request->penyidik_name);
        $penyidikPosition = htmlspecialchars($request->penyidik_position);
        $penyidikNrp = htmlspecialchars($request->penyidik_nrp);

        // JSON Values creator
        $offenseArticlesJSON = json_encode($offenseArticles);
        $ccJSON = json_encode($cc);
        $evidencesJSON = json_encode($evidences);
        $suspectsJSON = json_encode($suspects);

        SuratP21Tahap2::create([
            'accident_id' => $accident_id,

            'province_name' => $province,
            'polres_name' => $polres,
            'polres_address' => $polresAddress,
            'no_p21' => $noP21,
            'p21_date' => $p21Date,
            'p21_location' => $p21Location,
            'p21_start_date' => $p21StartDate,
            'classification' => $classification,
            'attachment' => $attachment,
            'subject' => $subject,
            'letter_recipient' => $letterRecipient,
            'recipient_location' => $recipientLocation,
            'evidences' => $evidencesJSON,
            'no_lp' => $noLp,
            'accident_date' => $accidentDate,
            'suspects' => $suspectsJSON,
            'description' => $incidentDescription,
            'cc' => $ccJSON,
            'offense_articles' => $offenseArticlesJSON,
            'penyidik_name' => $penyidikName,
            'penyidik_position' => $penyidikPosition,
            'penyidik_nrp' => $penyidikNrp,

            'created_by' => Auth::user()->name,
        ]);

        Accident::where('id', $accident_id)
            ->update([
                'last_update' => Carbon::now(),
                'category' => 'D010104',
                'tipe_update' => 'MEMBUAT'
            ]);
        return back();
    }

    public function json_surat_p21_tahap_2(Request $request)
    {
        $accident_id = $request->accident_id;
        $surat_p21_tahap_2 = SuratP21Tahap2::where('accident_id', $accident_id)->first();
        return response()->json($surat_p21_tahap_2);
    }

    public function edit_surat_p21_tahap_2(Request $request)
    {
        $accident_id = $request->accident_id_edit_p21_tahap_2;

        $province = htmlspecialchars($request->province);
        $polres = htmlspecialchars($request->polres);
        $polresAddress = htmlspecialchars($request->polres_address);
        $noP21 = htmlspecialchars($request->no_p21);
        $p21Date = htmlspecialchars($request->p21_date);
        $p21StartDate = htmlspecialchars($request->p21_start_date);
        $p21Location = htmlspecialchars($request->p21_location);
        $classification = htmlspecialchars($request->classification);
        $attachment = htmlspecialchars($request->attachment);
        $subject = htmlspecialchars($request->subject);
        $letterRecipient = htmlspecialchars($request->letter_recipient);
        $recipientLocation = htmlspecialchars($request->recipient_location);
        $evidences = $request->evidences;
        $noLp = htmlspecialchars($request->no_lp);
        $accidentDate = htmlspecialchars($request->accident_date);
        $offenseArticles = $request->offense_articles;
        $suspects = $request->suspects;
        $incidentDescription = htmlspecialchars($request->incident_description);
        $cc = $request->cc;
        $penyidikName = htmlspecialchars($request->penyidik_name);
        $penyidikPosition = htmlspecialchars($request->penyidik_position);
        $penyidikNrp = htmlspecialchars($request->penyidik_nrp);

        // JSON Values creator
        $offenseArticlesJSON = json_encode($offenseArticles);
        $ccJSON = json_encode($cc);
        $evidencesJSON = json_encode($evidences);
        $suspectsJSON = json_encode($suspects);

        SuratP21Tahap2::where('accident_id', $accident_id)
            ->update([
                'province_name' => $province,
                'polres_name' => $polres,
                'polres_address' => $polresAddress,
                'no_p21' => $noP21,
                'p21_date' => $p21Date,
                'p21_location' => $p21Location,
                'p21_start_date' => $p21StartDate,
                'classification' => $classification,
                'attachment' => $attachment,
                'subject' => $subject,
                'letter_recipient' => $letterRecipient,
                'recipient_location' => $recipientLocation,
                'evidences' => $evidencesJSON,
                'no_lp' => $noLp,
                'accident_date' => $accidentDate,
                'suspects' => $suspectsJSON,
                'description' => $incidentDescription,
                'cc' => $ccJSON,
                'offense_articles' => $offenseArticlesJSON,
                'penyidik_name' => $penyidikName,
                'penyidik_position' => $penyidikPosition,
                'penyidik_nrp' => $penyidikNrp,

                'created_by' => Auth::user()->name,
            ]);

        Accident::where('id', $accident_id)
            ->update([
                'last_update' => Carbon::now(),
                'category' => 'D010104',
                'tipe_update' => 'MENGUBAH'
            ]);
        return back();
    }

    public function view_surat_p21_tahap_2(Request $request)
    {
        $accident_id = $request->input('accident_id');
        $surat_p21_tahap_2 = DB::table('surat_p21_tahap_2')->where('accident_id', $accident_id)->first();
        $surat_p21_tahap_1 = DB::table('surat_p21_tahap_1')->where('accident_id', $accident_id)->first();
        $accident = DB::table('accidents')->where('id', $accident_id)->first();
        $accident_date = Carbon::parse($accident->accident_date)->format('d F Y');
        $accident_time = Carbon::parse($accident->accident_time)->format('H:m');

        $data['accident'] = $accident;
        $data['accident_time'] = $accident_time;
        $data['accident_date'] = Carbon::parse($accident->accident_date)->format('d F Y');
        $data['road_name'] = $accident->road_name;

        $data['province_name'] = $surat_p21_tahap_2->province_name;
        $data['polres_name'] = $surat_p21_tahap_2->polres_name;
        $data['polres_address'] = $surat_p21_tahap_2->polres_address;
        $data['no_p21'] = $surat_p21_tahap_2->no_p21;
        $data['p21_date'] = Carbon::parse($surat_p21_tahap_2->p21_date)->format('d F Y');
        $data['p21_start_date'] = Carbon::parse($surat_p21_tahap_2->p21_start_date)->format('d F Y');
        $data['p21_location'] = $surat_p21_tahap_2->p21_location;
        $data['classification'] = $surat_p21_tahap_2->classification;
        $data['attachment'] = $surat_p21_tahap_2->attachment;
        $data['subject'] = $surat_p21_tahap_2->subject;
        $data['letter_recipient'] = $surat_p21_tahap_2->letter_recipient;
        $data['recipient_location'] = $surat_p21_tahap_2->recipient_location;
        $data['evidences'] = $surat_p21_tahap_2->evidences;
        $data['no_lp'] = $surat_p21_tahap_2->no_lp;
        $data['suspects'] = $surat_p21_tahap_2->suspects;
        $data['description'] = $surat_p21_tahap_2->description;
        $data['cc'] = $surat_p21_tahap_2->cc;
        $data['offense_articles'] = $surat_p21_tahap_2->offense_articles;
        $data['penyidik_name'] = $surat_p21_tahap_2->penyidik_name;
        $data['penyidik_position'] = $surat_p21_tahap_2->penyidik_position;
        $data['penyidik_nrp'] = $surat_p21_tahap_2->penyidik_nrp;
        $data['no_spdp'] = $surat_p21_tahap_1->no_spdp;
        $data['spdp_date'] = $surat_p21_tahap_1->spdp_date;

        return view('produktivitas.surat-p21.cetak-surat-p21-tahap-2', $data);
    }

    public function add_surat_tugas(Request $request)
    {
        $officer = $request->has('officer_id') ? $request->officer_id : [];
        $user = Auth::user();
        $created_by = $user->first_name . ' ' . $user->last_name;

        $surat_tugas = SprintGas::updateOrcreate(['id' => $request->springas_id], [
            'accident_id' => $request->accident_id_springas,
            'no_surat' => $request->no_surat,
            'no_lp' => $request->no_lp_springas,
            'no_sprindik' => $request->no_sprindik,
            'tanggal_springas' => Carbon::createFromFormat('d-m-Y', $request->tanggal_springas)->format('Y-m-d'),
            'lokasi' => $request->lokasi,
            'tanggal_dimulai' => Carbon::createFromFormat('d-m-Y', $request->tanggal_dimulai)->format('Y-m-d'),
            'tanggal_berakhir' => Carbon::createFromFormat('d-m-Y', $request->tanggal_berakhir)->format('Y-m-d'),
            'pejabat_penandatangan' => $request->pejabat_penandatangan,
            'ketua_tim' => $request->ketua_tim,
            'created_by' => $created_by
        ]);

        $surat_tugas_id = $surat_tugas->id;
        $sprintgas = SprintGas::find($surat_tugas_id);

        $surat_tugas->officer()->attach($officer);

        Accident::where('id', $request->accident_id_springas)
            ->update([
                'last_update' => Carbon::now(),
                'category' => 'D010101',
                'tipe_update' => 'MEMBUAT'
            ]);
        return back();
    }

    public function edit_surat_tugas(Request $request)
    {
        $officer    = $request->edit_officer_surat_tugas == null ? [] : $request->edit_officer_surat_tugas;

        $surat_tugas = SprintGas::where('id', $request->springas_id)->first();
        $surat_tugas->update([
            'no_surat' => $request->no_surat,
            'tanggal_springas' => Carbon::createFromFormat('d-m-Y', $request->tanggal_springas)->format('Y-m-d'),
            'lokasi' => $request->lokasi,
            'tanggal_dimulai' => Carbon::createFromFormat('d-m-Y', $request->tanggal_dimulai)->format('Y-m-d'),
            'tanggal_berakhir' => Carbon::createFromFormat('d-m-Y', $request->tanggal_berakhir)->format('Y-m-d'),
            'pejabat_penandatangan' => $request->pejabat_penandatangan,
            'ketua_tim' => $request->ketua_tim
        ]);

        $surat_tugas->officer()->detach();
        foreach ($officer as $item) {
            $surat_tugas->officer()->attach($item);
        }

        Accident::where('id',  $request->accident_id_edit_springas)
            ->update([
                'last_update' => Carbon::now(),
                'category' => 'D010101',
                'tipe_update' => 'MENGUBAH'
            ]);
        return back();
    }

    // public function view_surat_tugas(Request $request)
    // {   $accident_id=$request->input('accident_id');
    //     $surat_perintah_tugas = DB::select('select * from surat_tugas left join officers on surat_tugas.officer_id = officers.id where accident_id = \''.$accident_id.'\' ');
    //     $accident =DB::select('select * from accidents where id= \''.$accident_id.'\' ');
    //     $accident_date = Carbon::parse($accident[0]->accident_date)->format('d F Y');


    //     $data['surat_perintah_tugas']=$surat_perintah_tugas;
    //     $data['accident']=$accident[0];
    //     $data['accident_date']=$accident_date ;
    //     return view('produktivitas.surat-tugas.cetak-surat-tugas',$data);
    // }

    public function view_surat_tugas(Request $request)
    {
        $accident_id = $request->input('accident_id');
        $accident = DB::select('select * from accidents where id= \'' . $accident_id . '\' ');
        $springas = DB::select('select * from legacy.springas where accident_id= \'' . $accident_id . '\' ');
        $sprindik = DB::select('select * from legacy.investigation_order_letters where accident_id= \'' . $accident_id . '\' ');
        $officer_springas = DB::select('select * from legacy.officer_springas left join legacy.springas on legacy.officer_springas.sprint_gas_id = legacy.springas.id left join officers on legacy.officer_springas.officer_id = officers.id where legacy.springas.accident_id = \'' . $accident_id . '\' ');
        $leader = DB::select('select * from legacy.springas
        left join officers on legacy.springas.ketua_tim = officers.id
        where legacy.springas.accident_id = \'' . $accident_id . '\'');
        $accident_date = Carbon::parse($accident[0]->accident_date)->format('d F Y');
        $tanggal_dimulai = Carbon::parse($springas[0]->tanggal_dimulai)->format('d F Y');

        $data['accident'] = $accident[0];
        $data['officer_springas'] = $officer_springas;
        $data['leader'] = $leader[0];
        $data['no_surat'] = $springas[0]->no_surat;
        $data['no_lp'] = $springas[0]->no_lp;
        $data['no_sprindik'] = $sprindik[0]->letter_number;
        $data['accident_date'] = $accident_date;
        $data['tanggal_dimulai'] = $tanggal_dimulai;

        return view('produktivitas.surat-tugas.cetak-springas', $data);
    }

    public function add_surat_penyelidikan(Request $request)
    {
        $officer    = $request->officer_id_penyelidikan == null ? [] : $request->officer_id_penyelidikan;
        $get_accident = DB::select('select id from accidents where id = \'' . $request->accident_id_surat_penyelidikan . '\'');
        $get = $get_accident[0]->id;
        $accident = Accident::find($get);

        $accident->officer_surat_penyelidikan()->attach($officer);
        Accident::where('id', $get)
            ->update([
                'last_update' => Carbon::now(),
                'category' => 'D010102',
                'tipe_update' => 'MEMBUAT'
            ]);
        return back();
    }

    public function edit_surat_penyelidikan(Request $request)
    {
        $officer    = $request->edit_officer_surat_penyelidikan == null ? [] : $request->edit_officer_surat_penyelidikan;
        $get_accident = DB::select('select id from accidents where id = \'' . $request->accident_id_edit_surat_penyelidikan . '\'');

        $get = $get_accident[0]->id;

        $accident = Accident::find($get);

        $accident->officer_surat_penyelidikan()->sync($officer);
        Accident::where('id', $get)
            ->update([
                'last_update' => Carbon::now(),
                'category' => 'D010102',
                'tipe_update' => 'MENGUBAH'
            ]);
        return back();
    }

    public function view_surat_penyelidikan(Request $request)
    {
        $accident_id = $request->input('accident_id');
        $surat_perintah_penyelidikan = DB::select('select * from surat_penyelidikan left join officers on surat_penyelidikan.officer_id = officers.id where accident_id = \'' . $accident_id . '\' ');
        $accident = DB::select('select * from accidents where id= \'' . $accident_id . '\' ');
        $accident_date = Carbon::parse($accident[0]->accident_date)->format('d F Y');


        $data['surat_perintah_penyelidikan'] = $surat_perintah_penyelidikan;
        $data['accident'] = $accident[0];
        $data['accident_date'] = $accident_date;
        return view('produktivitas.surat-tugas.cetak-surat-penyelidikan', $data);
    }

    public function add_surat_penyidikan(Request $request)
    {

        $officer    = $request->officer_id_penyidikan == null ? [] : $request->officer_id_penyidikan;
        $get_accident = DB::select('select id from accidents where id = \'' . $request->accident_id_surat_penyidikan . '\'');
        $get = $get_accident[0]->id;
        $accident = Accident::find($get);

        $accident->officer_surat_penyidikan()->attach($officer);
        Accident::where('id', $get)
            ->update([
                'last_update' => Carbon::now(),
                'category' => 'D010103',
                'tipe_update' => 'MEMBUAT'
            ]);

        return back();
    }

    public function edit_surat_penyidikan(Request $request)
    {
        $officer    = $request->edit_officer_surat_penyidikan == null ? [] : $request->edit_officer_surat_penyidikan;
        $get_accident = DB::select('select id from accidents where id = \'' . $request->accident_id_edit_surat_penyidikan . '\'');

        $get = $get_accident[0]->id;

        $accident = Accident::find($get);

        $accident->officer_surat_penyidikan()->sync($officer);
        Accident::where('id', $get)
            ->update([
                'last_update' => Carbon::now(),
                'category' => 'D010103',
                'tipe_update' => 'MENGUBAH'
            ]);
        return back();
    }

    public function view_surat_penyidikan(Request $request)
    {
        $accident_id = $request->input('accident_id');
        $surat_perintah_penyidikan = DB::select('select * from surat_penyidikan left join officers on surat_penyidikan.officer_id = officers.id where accident_id = \'' . $accident_id . '\' ');
        $accident = DB::select('select * from accidents where id= \'' . $accident_id . '\' ');
        $accident_date = Carbon::parse($accident[0]->accident_date)->format('d F Y');

        $data['surat_perintah_penyidikan'] = $surat_perintah_penyidikan;
        $data['accident'] = $accident[0];
        $data['accident_date'] = $accident_date;
        return view('produktivitas.surat-tugas.cetak-surat-penyidikan', $data);
    }

    // public function add_surat_spdp(Request $request)
    public function add_surat_spdp(Request $request)
    {
        $pengadilan = DB::table('courts')->where('id', $request->pengadilan)->first();
        $kejaksaan = DB::table('prosecutors')->where('id', $request->endorsee_name)->first();
        SuratSpdp::create([
            'accident_id' => $request->accident_id_spdp,
            'id_springas' => $request->id_spgas,
            'id_sprindik' => $request->id_spdik,
            'kejaksaan_id' => $kejaksaan->id,
            'pengadilan_id' => $pengadilan->id,
            'no_spdp' => $request->no_spdp,
            'no_lp' => $request->no_lp,
            'no_sprindik' => $request->no_sprindik,
            'sprindik_date' => $request->sprindik_date,
            'spdp_date' => Carbon::createFromFormat('d-m-Y', $request->spdp_date)->format('Y-m-d'),
            'category_spdp' => $request->category_spdp,
            'suspect_name' => $request->suspect_name,
            'endorsee_name' => $kejaksaan->name,
            'lampiran' => $request->lampiran,
            'tembusan' => $request->tembusan,
            'pengadilan' => $pengadilan->name,
            'latter_signature' => $request->latter_signature,
            'klasifikasi' => $request->klasifikasi,
            'for_attention' => $request->for_attention,
            'Lokasi_Dibuat' => $request->Lokasi_Dibuat,
            'Attachment' => $request->Attachment,
            'Created_By' => $request->Created_By,
            'Updated_By' => $request->Updated_By,
        ]);

        Accident::where('id', $request->accident_id_spdp)
            ->update([
                'last_update' => Carbon::now(),
                'category' => 'D010104',
                'tipe_update' => 'MEMBUAT',
            ]);

        return back();
    }

    public function edit_surat_spdp(Request $request)
    {
        $accident_id = $request->input('accident_id_spdp');
        $spdp = SuratSpdp::where('accident_id', $accident_id)->first();
        $sprindik = Sprindik::where('accident_id', $accident_id)->first();

        if ($spdp) {
            $spdp->no_spdp = $request->input('no_spdp');
            $sprindik->letter_number = $request->input('no_sprindik');
            $spdp->sprindik_date = Carbon::createFromFormat('d-m-Y', $request->input('sprindik_date'))->toDateString();
            $spdp->spdp_date = Carbon::createFromFormat('d-m-Y', $request->input('spdp_date'))->toDateString();
            $spdp->category_spdp = $request->input('category_spdp');
            $spdp->kejaksaan_id = $request->input('endorsee_name');
            $spdp->save();

            return redirect()->back()->with('success', 'Surat SPDP berhasil diperbarui');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui surat SPDP. Data tidak ditemukan.');
        }
    }

    // public function edit_surat_spdp(Request $request)
    // {
    // SuratSpdp::where('accident_id', $request->accident_id_edit_spdp)
    //     ->update([
    //         'no_spdp' => $request->no_spdp,
    //         'kejaksaan_id' => $request->kejaksaan_id,
    //         'pengadilan_id' => $request->pengadilan_id,
    //         'no_lp' => $request->no_lp,
    //         'no_sprindik' => $request->no_sprindik,
    //         'sprindik_date' => $request->sprindik_date,
    //         'spdp_date' => $request->spdp_date,
    //         'category_spdp' => $request->category_spdp,
    //         'suspect_name' => $request->suspect_name,
    //         'endorsee_name' => $request->endorsee_name,
    //         'lampiran' => $request->lampiran,
    //         'tembusan' => $request->tembusan,
    //         'pengadilan' => $request->pengadilan,
    //         'latter_signature' => $request->latter_signature,
    //         'klasifikasi' => $request->klasifikasi,
    //         'for_attention' => $request->for_attention,
    //         'Lokasi_Dibuat' => $request->Lokasi_Dibuat,
    //         'Attachment' => $request->Attachment,
    //         'Created_By' => $request->Created_By,
    //         'Updated_By' => $request->Updated_By
    //     ]);
    // Accident::where('id', $request->accident_id_edit_spdp)
    //     ->update([
    //         'last_update' => Carbon::now(),
    //         'category' => 'D010104',
    //         'tipe_update' => 'MENGUBAH'
    //     ]);

    // return back();
    // }

    public function view_surat_spdp(Request $request)
    {
        $accidentId = htmlspecialchars($request->query('accident_id'));

        $suratSpdp = SuratSpdp::where('accident_id', $accidentId)->first();

        $kejaksaan = Prosecutor::where('id', $suratSpdp->kejaksaan_id)->first();

        $polres = Polres::where('id', $accidentId)->first();

        $daftarTersangka = DB::select('select polda.full_name as polda_name, polres.full_name as polres_name, polda.address as polda_alamat, polres.address as polres_alamat,
        polres.polres_district as polres_kecamatan, polres.polres_zipcode as polres_kodepos,
        suspects.name as nama_tersangka, suspects.identity_number as nomor_identitas, country.name as kewarganegaraan,
        genders.name as jenis_kelamin, suspects.birth_place as tempat_lahir, suspects.birth_date as tanggal_lahir,
        jobs.name as pekerjaan, religions.name as agama, suspects.address as alamat
    from accidents
        left join polres on accidents.polres_id = polres.id
        left join polda on polda.id = polres.polda_id
        left join suspects on accidents.id = suspects.accident_id
        left join genders on suspects.gender = genders.id
        left join jobs on suspects.occupation = jobs.id
        left join religions on suspects.religion = religions.id
        left join country on suspects.country = country.id
    where accidents.id = \'' . $accidentId . '\' ');

        $accident = Accident::where('id', $accidentId)->first();
        $accidentDate = Carbon::parse($accident->accident_date)->translatedFormat('d F Y');
        $accidentTime = Carbon::parse($accident->accident_time)->format('H:m');

        $data = [
            'no_spdp' => $suratSpdp->no_spdp,
            'endorsee_name' => $suratSpdp->endorsee_name,
            'klasifikasi' => $suratSpdp->klasifikasi,
            'lampiran' => $suratSpdp->lampiran,
            'no_sprindik' => $suratSpdp->no_sprindik,
            'sprindik_date' => $suratSpdp->sprindik_date,
            'spdp_date' => $suratSpdp->spdp_date,
            'accident' => $accident,
            'accident_date' => $accidentDate,
            'accident_time' => $accidentTime,
            'road_name' => $accident->road_name,
            'description' => $accident->damage_lose_desc,
            'daftar_tersangka' => $daftarTersangka,
            'polres' => $polres,
            'regency' => $kejaksaan->regency
            // 'address_polres' => $polres->address

        ];

        return view('produktivitas.surat-tugas.cetak-surat-spdp', $data);
    }


    public function add_sp3(Request $request)
    {

        SP3::updateOrcreate(
            ['id' => $request->sp3_id],
            [
                'accident_id' => $request->accident_id_sp3,
                'no_lp' => $request->accident_no_lp_sp3,
                'no_sp3' => $request->no_sp3,
                'no_spdp' => $request->no_spdp,
                'no_surat_perintah_penyidikan' => $request->no_surat_perintah_penyidikan,
                'tanggal_sp_dik' => Carbon::createFromFormat('d-m-Y', $request->tanggal_sp_dik)->format('Y-m-d'),
                // 'no_sprindik'=>$request->no_sprindik,
                'penerima_surat' => $request->penerima_surat,
                'klasifikasi' => $request->klasifikasi,
                'tanggal_berlaku' => Carbon::createFromFormat('d-m-Y', $request->tanggal_berlaku)->format('Y-m-d'),
                'alasan' => $request->alasan,
                'lampiran' => $request->lampiran,
            ]
        );
        Accident::where('id', $request->accident_id_sp3)
            ->update([
                'last_update' => Carbon::now(),
                'category' => 'D110107',
                'tipe_update' => 'MEMBUAT'
            ]);
        return back();
    }

    public function edit_sp3(Request $request)
    {
        SP3::where('id', $request->id_sp3)
            ->update([
                'no_surat_perintah_penyidikan' => $request->no_surat_perintah_penyidikan,
                'no_sp3' => $request->no_sp3,
                'tanggal_sp_dik' => Carbon::createFromFormat('d-m-Y', $request->tanggal_sp_dik)->format('Y-m-d'),
                'no_sprindik' => $request->no_sprindik,
                'penerima_surat' => $request->penerima_surat,
                'klasifikasi' => $request->klasifikasi,
                'tanggal_berlaku' => Carbon::createFromFormat('d-m-Y', $request->tanggal_berlaku)->format('Y-m-d'),
                'alasan' => $request->alasan,
                'lampiran' => $request->lampiran,
            ]);
        Accident::where('id', $request->accident_id_edit_sp3)
            ->update([
                'last_update' => Carbon::now(),
                'category' => 'D010107',
                'tipe_update' => 'MENGUBAH'
            ]);
        return back();
    }

    public function view_sp3(Request $request)
    {
        $accident_id = $request->input('accident_id');
        $sp3 = DB::select('select * from sp3 where accident_id = \'' . $accident_id . '\' ');
        $accident = DB::select('select * from accidents where id= \'' . $accident_id . '\' ');
        // $surat_tugas = DB::select('select * from surat_tugas left join officers on surat_tugas.officer_id = officers.id where accident_id = \''.$accident_id.'\' ');
        $accident_date = Carbon::parse($accident[0]->accident_date)->format('d F Y');
        $accident_time = Carbon::parse($accident[0]->accident_time)->format('H:m');
        $tanggal_sp_dik = Carbon::parse($sp3[0]->tanggal_sp_dik)->format('d F Y');
        $officer_springas = DB::select('select * from legacy.officer_springas left join legacy.springas on legacy.officer_springas.sprint_gas_id = legacy.springas.id left join officers on legacy.officer_springas.officer_id = officers.id where legacy.springas.accident_id = \'' . $accident_id . '\' ');

        // $data['surat_tugas']=$surat_tugas;
        $data['officer_springas'] = $officer_springas;
        $data['no_sp3'] = $sp3[0]->no_sp3;
        $data['no_lp'] = $sp3[0]->no_lp;
        $data['no_surat_perintah_penyidikan'] = $sp3[0]->no_surat_perintah_penyidikan;
        $data['alasan'] = $sp3[0]->alasan;
        $data['tanggal_sp_dik'] = $tanggal_sp_dik;
        $data['accident'] = $accident[0];
        $data['accident_date'] = $accident_date;
        $data['accident_time'] = $accident_time;
        // $data['description']=$accident[0]->damage_lose_desc;

        return view('produktivitas.surat-penghentian.cetak-surat-sp3', $data);
    }

    //kategori 2
    public function add_saksi(Request $request)
    {

        // $validator = Validator::make($request->all(), [
        //     'name' => 'required',
        //     'gender' => 'required',
        //     'city' => 'required',
        //     'birth_date' => 'required',
        //     'religion' => 'required',
        //     'job' => 'required',
        //     'education' => 'required',
        //     'phone' => 'required',
        //     'citizen' => 'required',
        //     'address' => 'required',
        // ]);

        // $this->validate($request,[
        //     'accident_id' => 'required',
        //     'name' => 'required',
        //     'gender' => 'required',
        //     'city' => 'required',
        //     'birth_date'=>'required',
        //     'religion' => 'required',
        //     'city' => 'required',
        //     'birth_date'=>'required',
        //     'religion' => 'required',
        //     'job' => 'required',
        //     'education'=>'required',
        //     'phone' => 'required',
        //     'citizen'=>'required',
        //     'address' => 'required',

        // ]);

        // if ($validator->passes()) {
        $saksi = DaftarSaksi::updateOrCreate(
            ['id' => $request->saksi_id],
            [
                'accident_id' => $request->accident_id_saksi,
                'name' =>  $request->name,
                'gender' =>  $request->gender,
                'city' => $request->city,
                'birth_date' =>  Carbon::createFromFormat('d-m-Y', $request->birth_date)->format('Y-m-d'),
                'religion' =>  $request->religion,
                'job' =>  $request->job,
                'education' =>  $request->education,
                'phone' =>  $request->phone,
                'citizen' => $request->citizen,
                'address' => $request->address,
            ]
        );
        // return response()->json(['success'=>'Added new records.']);
        return response()->json($saksi);
        // }

        // return response()->json(['error'=>$validator->errors()]);

    }

    function fetch(Request $request)
    {

        $user = Auth::getUser();
        if ($request->get('query')) {
            switch ($user->role_id) {
                case 2:
                    $polda = $user->polda_id;
                    $polres = '-';

                    /*$polres = TranslateIdHelper::getTranslatePoliceResorId(
                    regionalId: $polda,
                    resorId: $polres
                );*/

                    $query = $request->get('query');
                    $data = DB::table('officers')
                        ->where('first_name', 'iLIKE', "%{$query}%")
                        ->where('polda_id', '=', $polda)
                        ->get();
                    break;
                case 3:
                    $polda = $user->polda_id;
                    $polres = $user->polres_id;

                    /*$polres = TranslateIdHelper::getTranslatePoliceResorId(
                    regionalId: $polda,
                    resorId: $polres
                );*/

                    $query = $request->get('query');
                    $data = DB::table('officers')
                        ->where('first_name', 'iLIKE', "%{$query}%")
                        ->where('polres_id', '=', $polres)
                        ->get();
                    break;
                case 4:
                    $polda = $user->polda_id;
                    $polres = $user->polres_id;

                    /*$polres = TranslateIdHelper::getTranslatePoliceResorId(
                    regionalId: $polda,
                    resorId: $polres
                );*/

                    $query = $request->get('query');
                    $data = DB::table('officers')
                        ->where('first_name', 'iLIKE', "%{$query}%")
                        ->where('polres_id', '=', $polres)
                        ->get();
                    break;
                default:
                    $polda = '-';
                    $polres = '-';
                    $polda = $user->polda_id;
                    $polres = $user->polres_id;

                    /*$polres = TranslateIdHelper::getTranslatePoliceResorId(
                regionalId: $polda,
                resorId: $polres
            );*/

                    $query = $request->get('query');
                    $data = DB::table('officers')
                        ->where('first_name', 'iLIKE', "%{$query}%")
                        ->get();
            }

            $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
            foreach ($data as $row) {
                //    $output_id = $row->id;
                $output .= '
       <li value="' . $row->id . '"><a class="dropdown-item" "href="">' . $row->id . ' - ' . $row->first_name . ' ' . $row->last_name . '</a></li>
       ';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    public function rekap()
    {
        $user = Auth::user();
        switch ($user->role_id) {
            case 2:
                $polda = $user->polda_id;
                $polres = '-';

                /*$polres = TranslateIdHelper::getTranslatePoliceResorId(
                    regionalId: $polda,
                    resorId: $polres
                );*/

                $accident = DB::select(
                    'select accidents.id as id,
                    polres.name as name,
                    accidents.no_lp,md,lb,lr,road_name,accident_date
                    from accidents join polres on accidents.polres_id = polres.id
                    join polda on polda.id = polres.polda_id
                    where polda.id = \'' . $polda . '\'
                    order by accidents.created_at desc
                    '
                );

                break;
            case 3:
                $polda = $user->polda_id;
                $polres = $user->polres_id;

                /*$polres = TranslateIdHelper::getTranslatePoliceResorId(
                    regionalId: $polda,
                    resorId: $polres
                );*/

                $accident = DB::select(
                    'select accidents.id as id,
                    polres.name as name,
                    accidents.no_lp,md,lb,lr,road_name ,accident_date
                    from accidents join polres on accidents.polres_id = polres.id
                    where polres.id = \'' . $polres . '\'
                    order by accidents.created_at desc
                    '
                );
                break;
            case 4:
                $polda = $user->polda_id;
                $polres = $user->polres_id;

                /*$polres = TranslateIdHelper::getTranslatePoliceResorId(
                    regionalId: $polda,
                    resorId: $polres
                );*/

                $accident = DB::select(
                    'select accidents.id as id,
                    polres.name as name,
                    accidents.no_lp,md,lb,lr,road_name ,accident_date
                    from accidents join polres on accidents.polres_id = polres.id
                    where polres.id = \'' . $polres . '\'
                    order by accidents.created_at desc
                    '
                );
                break;
            default:
                $polda = '-';
                $polres = '-';

                $accident = DB::select(
                    'select accidents.id as id,
                polres.name as name,
                accidents.no_lp,md,lb,lr,road_name ,accident_date
                from accidents join polres on accidents.polres_id = polres.id
                order by accidents.created_at desc
                '
                );
        }
        return view('rekap.rekap-index', compact('accident'));
    }

    public function get_tersangka(Request $request)
    {
        $accident = $request->accident_id;
        // $accident = 'eff6a3c4-660a-4e8e-950d-0e7a68cf863b';
        if ($request->ajax()) {
            // $data = DB::select('select * from daftar_saksi where accident_id = \''.$accident.'\' order by created_at')->get();
            // select saksi.id as id,saksi.name as name,
            //                         ref.name as gender,saksi.city as city,
            //                         to_char(birth_date, \'DD-MM-YYYY\') as birth_date,
            //                         saksi.citizen as citizen
            //                         from daftar_saksi as saksi left join ref on ref.id = saksi.gender
            //                         where accident_id = \''.$accident.'\' order by saksi.created_at
            $get_data = DB::select('select
                                    tersangka.id as id,
                                    ref.name as gender,
                                    tersangka.name as name,
                                    tersangka.city as city,
                                    to_char(birth_date, \'DD-MM-YYYY\') as birth_date,
                                    tersangka.citizen as citizen,
                                    (select ref.name from ref where ref.id=tersangka.identity_type) as identity_type,
                                    tersangka.identity_no as identity_no
                                    from daftar_tersangka as tersangka left join ref on ref.id = tersangka.gender
                                    where accident_id = \'' . $accident . '\' order by tersangka.created_at');
            $data['tersangka'] = $get_data;
            // $data = DaftarSaksi::where('accident_id',''.$accident.'')->get();
            return Datatables::of($data['tersangka'])
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editTersangka">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteTersangka">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function edit_tersangka(Request $request)
    {
        $id = $request->id;
        $get_tersangka = DB::select('select * from daftar_tersangka where id = \'' . $id . '\' ');
        $tersangka = $get_tersangka[0];
        return response()->json($tersangka);
    }

    public function add_tersangka(Request $request)
    {
        $tersangka = DaftarTersangka::updateOrCreate(
            ['id' => $request->tersangka_id],
            [
                'accident_id' => $request->accident_id,
                'name' =>  $request->name,
                'gender' =>  $request->gender,
                'city' => $request->city,
                'birth_date' =>  Carbon::createFromFormat('d-m-Y', $request->birth_date)->format('Y-m-d'),
                'religion' =>  $request->religion,
                'job' =>  $request->job,
                'education' =>  $request->education,
                'phone' =>  $request->phone,
                'citizen' => $request->citizen,
                'address' => $request->address,
                'identity_type' => $request->identity_type,
                'identity_no' => $request->identity_no,
            ]
        );
        // return response()->json(['success'=>'Added new records.']);
        return response()->json($tersangka);
        // }

        // return response()->json(['error'=>$validator->errors()]);

    }

    public function delete_tersangka(Request $request)
    {
        $tersangka = DaftarTersangka::find($request->id);
        $tersangka->delete();
        return response()->json('sukses');
    }

    public function get_barang_bukti(Request $request)
    {
        $accident = $request->accident_id;
        // $accident = 'eff6a3c4-660a-4e8e-950d-0e7a68cf863b';
        if ($request->ajax()) {
            // $data = DB::select('select * from daftar_saksi where accident_id = \''.$accident.'\' order by created_at')->get();
            $get_data = DB::select('select * from daftar_barang_bukti where accident_id = \'' . $accident . '\' order by created_at ');
            $data['barang_bukti'] = $get_data;
            // $data = DaftarSaksi::where('accident_id',''.$accident.'')->get();
            return Datatables::of($data['barang_bukti'])
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editBarang">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteBarang">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    // public function get_barang_bukti(Request $request)
    // {
    //      $accident = $request->accident_id;
    //     // $accident = 'eff6a3c4-660a-4e8e-950d-0e7a68cf863b';
    //         if ($request->ajax()) {
    //         // $data = DB::select('select * from daftar_saksi where accident_id = \''.$accident.'\' order by created_at')->get();
    //         $get_data = DB::select('select * from daftar_barang_bukti where accident_id = \''.$accident.'\' order by created_at ');
    //         $data['barang_bukti']=$get_data;
    //         // $data = DaftarSaksi::where('accident_id',''.$accident.'')->get();
    //         return Datatables::of($data['barang_bukti'])
    //                 ->addIndexColumn()
    //                 ->addColumn('action', function($row){

    //                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editBarangBukti">Edit</a>';

    //                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteBarangBukti">Delete</a>';

    //                         return $btn;
    //                 })
    //                 ->rawColumns(['action'])
    //                 ->make(true);
    //     }
    // }

    public function add_barang_bukti(Request $request)
    {
        $barangbukti = DaftarBarangBukti::updateOrCreate(
            ['id' => $request->barang_bukti_id],
            [
                'accident_id' => $request->accident_id_barang_bukti,
                'nama_barang' =>  $request->nama_barang,
                'jumlah_barang' =>  $request->jumlah_barang,
            ]
        );
        // return response()->json(['success'=>'Added new records.']);
        return response()->json($barangbukti);
        // }

        // return response()->json(['error'=>$validator->errors()]);

    }


    public function add_surat_penyitaan(Request $request)
    {
        $officer    = $request->officer_id_penyitaan == null ? [] : $request->officer_id_penyitaan;
        $get_accident = DB::select('select id from accidents where id = \'' . $request->accident_id_surat_penyitaan . '\'');
        $get = $get_accident[0]->id;
        $accident = Accident::find($get);

        $accident->officer_surat_penyitaan()->attach($officer);
        return back();
    }

    // public function view_surat_tugas(Request $request)
    // {   $accident_id=$request->input('accident_id');
    //     $surat_perintah_tugas = DB::select('select * from surat_tugas left join officers on surat_tugas.officer_id = officers.id where accident_id = \''.$accident_id.'\' ');
    //     $accident =DB::select('select * from accidents where id= \''.$accident_id.'\' ');
    //     $accident_date = Carbon::parse($accident[0]->accident_date)->format('d F Y');


    //     $data['surat_perintah_tugas']=$surat_perintah_tugas;
    //     $data['accident']=$accident[0];
    //     $data['accident_date']=$accident_date ;
    //     return view('produktivitas.surat-tugas.cetak-surat-tugas',$data);
    // }

    public function view_surat_penyitaan(Request $request)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $surat_penyitaan = DB::select('select * from surat_penyitaan left join officers on surat_penyitaan.officer_id = officers.id where accident_id = \'' . $accidentId . '\' ');
        // $accident =DB::select('select * from accidents where id= \''.$accident_id.'\' ');
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();
        // $accident_date = Carbon::parse($accident[0]->accident_date)->format('d F Y');

        // $data['surat_penyitaan']=$surat_penyitaan;
        // $data['accident']=$accident[0];
        // $data['accident_date']=$accident_date ;
        $viewData = [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'surat_penyitaan' => $surat_penyitaan,
        ];

        return view('produktivitas.surat-penyitaan.cetak-surat-penyitaan', $viewData);
    }

    public function edit_surat_penyitaan(Request $request)
    {
        $officer    = $request->edit_officer_surat_penyitaan == null ? [] : $request->edit_officer_surat_penyitaan;
        $get_accident = DB::select('select id from accidents where id = \'' . $request->accident_id_edit_surat_penyitaan . '\'');

        $get = $get_accident[0]->id;

        $accident = Accident::find($get);

        $accident->officer_surat_penyitaan()->sync($officer);
        return back();
    }

    public function add_surat_perintah_penyegelan(Request $request)
    {
        $officer    = $request->officer_id_penyegelan == null ? [] : $request->officer_id_penyegelan;
        $get_accident = DB::select('select id from accidents where id = \'' . $request->accident_id_surat_penyegelan . '\'');
        $get = $get_accident[0]->id;
        $accident = Accident::find($get);

        $accident->officer_surat_penyegelan()->attach($officer);
        return back();
    }

    public function edit_surat_penyegelan(Request $request)
    {
        $officer    = $request->edit_officer_surat_penyegelan == null ? [] : $request->edit_officer_surat_penyegelan;
        $get_accident = DB::select('select id from accidents where id = \'' . $request->accident_id_edit_surat_penyegelan . '\'');

        $get = $get_accident[0]->id;

        $accident = Accident::find($get);

        $accident->officer_surat_penyegelan()->sync($officer);
        return back();
    }

    public function get_dpo(Request $request)
    {
        $accident = $request->accident_id;
        if ($request->ajax()) {
            $get_data = DB::select('select dpo.id as id,dpo.name as name,ref.name as gender,deskripsi_dpo from dpo left join ref on dpo.gender = ref.id where accident_id = \'' . $accident . '\' order by dpo.created_at ');
            $data['dpo'] = $get_data;
            return Datatables::of($data['dpo'])
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editDpo">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteDpo">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }


    public function add_dpo(Request $request)
    {
        $dpo = Dpo::updateOrCreate(
            ['id' => $request->dpo_id],
            [
                'accident_id' => $request->accident_id_dpo,
                'name' =>  $request->name_dpo,
                'gender' =>  $request->gender_dpo,
                'deskripsi_dpo' => $request->deskripsi_dpo,
                'state' => $request->dpo_tangkaps
            ]
        );
        // return response()->json(['success'=>'Added new records.']);
        return response()->json($dpo);
        // }

        // return response()->json(['error'=>$validator->errors()]);
    }

    public function edit_dpo(Request $request)
    {
        $id = $request->id;
        $get_dpo = DB::select('select * from dpo where id = \'' . $id . '\' ');
        $dpo = $get_dpo[0];
        return response()->json($dpo);
    }

    public function delete_dpo(Request $request)
    {
        $dpo = Dpo::find($request->id);
        $dpo->delete();
        return response()->json('sukses');
    }

    public function get_dpb(Request $request)
    {
        $accident = $request->accident_id;
        if ($request->ajax()) {
            $get_data = DB::select('select * from dpb where accident_id = \'' . $accident . '\' order by created_at ');
            $data['dpb'] = $get_data;
            return Datatables::of($data['dpb'])
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editDpb">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteDpb">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }


    public function add_dpb(Request $request)
    {
        $dpb = Dpb::updateOrCreate(
            ['id' => $request->dpb_id],
            [
                'accident_id' => $request->accident_id_dpb,
                'jenis' =>  $request->jenis_dpb,
                'no_tnkb' =>  $request->no_tnkb,
                'deskripsi_dpb' => $request->deskripsi_dpb,
                'state' => '0'
            ]
        );
        // return response()->json(['success'=>'Added new records.']);
        return response()->json($dpb);
        // }

        // return response()->json(['error'=>$validator->errors()]);
    }

    public function edit_dpb(Request $request)
    {
        $id = $request->id;
        $get_dpb = DB::select('select * from dpb where id = \'' . $id . '\' ');
        $dpb = $get_dpb[0];
        return response()->json($dpb);
    }

    public function delete_dpb(Request $request)
    {
        $dpb = Dpb::find($request->id);
        $dpb->delete();
        return response()->json('sukses');
    }

    public function get_sp2hp(Request $request)
    {
        $accident = $request->accident_id;
        if ($request->ajax()) {
            // $get_data = DB::select('select saksi.id as id,saksi.name as name,
            //                         ref.name as gender,saksi.city as city,
            //                         to_char(birth_date, \'DD-MM-YYYY\') as birth_date,
            //                         saksi.citizen as citizen
            //                         from daftar_saksi as saksi left join ref on ref.id = saksi.gender
            //                         where accident_id = \''.$accident.'\' order by saksi.created_at ');

            $get_data = DB::select('select sp2hp.id as id,
             kota,
             tanggal_terbit,
             tipe, tingkat,
             concat(nomor_surat_1,\' /  \',nomor_surat_2,\' /  \',nomor_surat_3,\' /  \',nomor_surat_4,\' /  \',nomor_surat_5) as nomor_surat,
             name,address,
             deskripsi
             from sp2hp
             where accident_id = \'' . $accident . '\' order by sp2hp.created_at');
            $data['sp2hp'] = $get_data;
            return Datatables::of($data['sp2hp'])
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editSp2hp">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteSp2hp">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function add_sp2hp(Request $request)
    {
        $sp2hp = Sp2hp::updateOrCreate(
            ['id' => $request->sp2hp_id],
            [
                'accident_id' => $request->accident_id_sp2hp,
                'tipe' =>  $request->tipe,
                'tingkat' =>  $request->tingkat,
                'kota' =>  $request->kota,
                'tanggal_terbit' =>  Carbon::createFromFormat('d-m-Y', $request->tanggal_terbit)->format('Y-m-d'),
                'nomor_surat_1' => $request->nomor_surat_1,
                'nomor_surat_2' =>  $request->nomor_surat_2,
                'nomor_surat_3' =>  $request->nomor_surat_3,
                'nomor_surat_4' =>  $request->nomor_surat_4,
                'nomor_surat_5' =>  $request->nomor_surat_5,
                'name' =>  $request->name,
                'address' => $request->address,
                'deskripsi' => $request->deskripsi,
                'created_by' => $request->created_by,
            ]
        );
        return response()->json($sp2hp);
    }

    public function delete_sp2hp(Request $request)
    {
        $saksi = Sp2hp::find($request->id);
        $saksi->delete();
        return response()->json('sukses');
    }

    public function edit_sp2hp(Request $request)
    {
        $id = $request->id;
        $get_sp2hp = DB::select('select id,
        accident_id,
        tipe,
        tingkat,
        kota,
        to_char(tanggal_terbit, \'DD-MM-YYYY\') as tanggal_terbit,
        nomor_surat_1,
        nomor_surat_2,
        nomor_surat_3,
        nomor_surat_4,
        nomor_surat_5,
        name,
        address,
        deskripsi,
        created_by
        from sp2hp where id = \'' . $id . '\'
        ');
        $sp2hp = $get_sp2hp[0];
        return response()->json($sp2hp);
    }

    public function deleteImage(Request $request)
    {
        $id = $request->id;
        $name = $request->name_image;
        $deleteImageStorage = uploadImage::find($id);
        $path = public_path() . '/imageUpload/' . $deleteImageStorage->name;
        unlink($path);
        $delete = uploadImage::where('id', '=', $id)->delete();

        return back()->with('succes', 'image has been delete');
    }

    // public function file_upload_ketetapan(Request $request){

    //     return back();
    // }


    public function downloadall(Request $req)
    {
        $id  = $req->accident_id;
        $zip = new ZipArchive;
        $checkdata = DB::select("select name from ba_pemotretan where accident_id='$id'");
        $checkdata2 = DB::table("ba_pemotretan")->select("name")->where("accident_id", "=", $id)->first();
        $pathname = "$id.zip";
        $doc = [
            "/tugas/surat_tugas" => DB::select("select name from surat_tugas where accident_id='$id'"),
            "/tugas/surat_perintah_penyelidikan" => DB::select("select name from surat_perintah_penyelidikan where accident_id='$id'"),
            "/tugas/surat_perintah_penyidikan" => DB::select("select name from surat_perintah_penyidikan where accident_id='$id'"),
            "/tugas/surat_spdp" => DB::select("select name from surat_spdp where accident_id='$id'"),
            "/tugas/laporan_polisi" => DB::select("select name from laporan_polisi where accident_id='$id'"),
            "/tugas/BA_Pemotretan" => DB::select("select name from ba_pemotretan where accident_id='$id'"),
            "/tugas/BA_Penangkapan_TKP" => DB::select("select name from ba_Pengangkapan_tkp where accident_id='$id'"),

            "/saksi/berita-acara-membawa-saksi" => DB::select("select name from berita_acara_membawa_saksi where accident_id='$id'"),
            "/saksi/berita-acara-penyumpahan-saksi" => DB::select("select name from berita_acara_penyumpahan_saksi where accident_id='$id'"),
            "/saksi/surat-perintah-membawa-saksi" => DB::select("select name from surat_perintah_membawa_saksi where accident_id='$id'"),
            "/tersangka/surat-perintah-penangkapan" => DB::select("select name from surat_perintah_penangkapan where accident_id='$id'"),
            "/penahanan/berita-acara-penahanan" => DB::select("select name from berita_acara_penahanan where accident_id='$id'"),
            "/penahanan/pencabutan-pembatalan-penahanan" => DB::select("select name from surat_pencabutan_penyelidikan where accident_id='$id'"),
            "/penahanan/surat-perintah-penahanan" => DB::select("select name from surat_perintah_penahanan where accident_id='$id'"),
            "/penahanan/surat-perpanjangan-penahanan" => DB::select("select name from surat_perpanjangan_penahanan where accident_id='$id'"),
            "/penggeledahan/berita-acara-penggeledahan" => DB::select("select name from berita_acara_penggeledahan where accident_id='$id'"),
            "/penggeledahan/perintah-penggeledahan" => DB::select("select name from surat_perintah_penggeledahan where accident_id='$id'"),
            "/penggeledahan/surat-persetujuan-penggeledahan" => DB::select("select name from surat_persetujuan_penggeledahan where accident_id='$id'"),
            "/penyitaan/surat-izin-penyitaan" => DB::select("select name from surat_izin_penyitaan where accident_id='$id'"),
            "/penyitaan/surat-persetujuan-penyitaan" => DB::select("select name from surat_persetujuan_penyitaan where accident_id='$id'"),
            "/labfor/surat-hasil-pemeriksaan-identifikasi" => DB::select("select name from surat_hasil_pemeriksaan_identifikasi where accident_id='$id'"),
            "/labfor/surat-hasil-pemeriksaan-labfor" => DB::select("select name from surat_hasil_pemeriksaan_labfor where accident_id='$id'"),
            "/labfor/surat-permintaan-bantuan-identifikasi" => DB::select("select name from surat_permintaan_bantuan_identifikasi where accident_id='$id'"),
            "/labfor/surat-permintaan-bantuan-labfor" => DB::select("select name from surat_permintaan_bantuan_labfor where accident_id='$id'"),
            "/dpo-dpb/surat-pencabutan-tersangka" => DB::select("select name from surat_pencabutan_tersangka where accident_id='$id'"),
        ];

        if ($zip->open(public_path($pathname), ZipArchive::CREATE) === TRUE) {
            foreach ($doc as $publicpath => $filesname) {
                $files = File::files(public_path() . "/file" . $publicpath);
                foreach ($files as $key => $value) {
                    if ($filesname == []) {
                        break;
                    } else {
                        $relativeNameInZipFile = basename($filesname[0]->name);
                        $zip->addFile($value, $relativeNameInZipFile);
                    }
                }
            }


            $zip->close();
        }

        // Download the generated zip
        return response()->download(public_path($pathname));
    }
}
