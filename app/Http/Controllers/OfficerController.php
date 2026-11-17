<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use App\Models\Officer;
use App\Models\Polda;
use App\Models\Polres;
use App\Models\Ref;
use App\Exports\OfficerExport;
use App\Services\Regional\RegionalScopeService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class OfficerController extends Controller
{
    //
    protected $_title  = 'Daftar Penyidik';

    function __construct()
    {
        view()->share('_title', $this->_title);
    }

    public function index()
    {
        $user = Auth::getUser();
        $baseQuery = DB::table('officers')
            ->leftJoin('polres', 'polres.id', '=', 'officers.polres_id')
            ->leftJoin('polda', 'polda.id', '=', 'polres.polda_id')
            ->select([
                'officers.id as id',
                'officers.first_name',
                'officers.last_name',
                'officers.rank_short_name',
                'polres.name as polres_name',
                'polda.name as polda_name',
                'officers.position',
                'officers.sebagai_kepala',
                'officers.state as officer_state',
            ]);

        switch ($user->role_id) {
            case 2:
                $petugas = (clone $baseQuery)
                    ->where('polda.id', $user->polda_id)
                    ->orderByDesc('officers.created_at')
                    ->paginate(10);
                $polda = Polda::where('id', $user->polda_id)->get();
                $polres = Polres::where('polda_id', $user->polda_id)->get();
                break;

            case 3:
                $petugas = (clone $baseQuery)
                    ->where('polres.id', $user->polres_id)
                    ->orderByDesc('officers.created_at')
                    ->paginate(10);
                $polda = Polda::where('id', $user->polda_id)->get();
                $polres = Polres::where('id', $user->polres_id)->get();
                break;

            default:
                $petugas = (clone $baseQuery)
                    ->orderByDesc('officers.created_at')
                    ->paginate(10);
                $polda = Polda::all();
                $polres = Polres::all();
                break;
        }

        $rank = Ref::where('grp_id', '=', 'RANK')->orderBy('sort')->get();
        $data['petugas'] = $petugas;
        $data['polda'] = $polda;
        $data['status'] = '-';
        $data['user'] = $user;
        $data['polda_input'] = '-';
        $data['polres_input'] = '-';
        $data['polres'] = $polres;
        $data['rank'] = $rank;
        return view('petugas.petugas-index', $data);
    }

    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 5);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }


    public function add()
    {
        $user = Auth::user();
        switch ($user->role_id) {
            case 2:
                $polda = Polda::where('id', '=', $user->polda_id)->get();
                $polres = Polres::where('polda_id', '=', $user->polda_id)->get();
                break;
            default:
                // $pengguna = User::with('role')->where('username','<>',$user)->orderBy('id')->get();
                $polda = Polda::all();
                $polres = Polres::all();
                break;
        }
        return view('petugas.petugas-add', compact('user', 'polda', 'polres'));
    }

    public function add_petugas(Request $request)
    {

        $this->validate($request, [
            'first_name_add' => 'required|max:20',
            'pangkat_add' => 'required',
            'nrp_add' => 'required|numeric',
            'polda_id_add' => 'required',
            'polres_id_add' => 'required',
        ]);

        //cek nrp officer
        $ceknrp =  Officer::where('register_number', '=', '' . $request->nrp_add . '')->first();

        if (!$ceknrp) {

            $officer = Officer::create([
                'id' => $request->nrp_add,
                'register_number' => $request->nrp_add,
                'first_name' =>  $request->first_name_add,
                'last_name' =>  $request->last_name_add,
                'polda_id' =>  $request->polda_id_add,
                'polres_id' =>  $request->polres_id_add,
                // 'polres_id' =>  str_pad((string) $request->polres_id_add, 4, '0', STR_PAD_LEFT),
                'police_id' => (!empty($request->polres_id_add)) ? $request->polres_id_add : $request->polda_id_add,
                'position_short_name' =>  $request->posisi_add,
                'sebagai_kepala' =>  $request->kepala_add,
                'rank_short_name' =>  $request->pangkat_add,
                'state' => '1',
            ]);
            // Session::flash('sukses','Ini notifikasi SUKSES');
            // return redirect('register')->withSucces('Sukses');
            return redirect('petugas')->with('message', 'Sukses menambahkan Petugas!');
        } else {
            return redirect('petugas/petugas-add')->withErrors(['', 'NRP sudah dipakai'])->withInput();
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $targetOfficer = DB::table('officers')->where('id', $id)->first();
        if (!$targetOfficer || !RegionalScopeService::authorizeTargetUser($user, $targetOfficer)) {
            abort(403, 'Akses lintas satker ditolak.');
        }
        switch ($user->role_id) {
            case 2:
                $polda = Polda::where('id', '=', $user->polda_id)->get();
                $polres = Polres::where('polda_id', '=', $user->polda_id)->get();
                break;
            default:
                $polda = Polda::all();
                $polres = Polres::all();
                break;
        }
        $petugas = DB::table('officers')->where('id', $id)->get();
        return view('petugas.petugas-edit', compact('petugas', 'polda', 'polres'));
    }

    public function edit_modal_petugas(Request $request)
    {
        $id = $request->id;
        $officer = DB::table('officers')->where('id', $id)->first();
        if (!$officer || !RegionalScopeService::authorizeTargetUser(Auth::user(), $officer)) {
            abort(403, 'Akses lintas satker ditolak.');
        }
        $polda = Polda::all();
        $polres = Polres::all();
        $data = [
            'officer' => $officer,
            'polda' => $polda,
            'polres' => $polres,
        ];
        return response()->json($data);
    }

    public function edit_petugas(Request $request)
    {
        $targetOfficer = DB::table('officers')->where('id', $request->nrp_editt)->first();
        if (!$targetOfficer || !RegionalScopeService::authorizeTargetUser(Auth::user(), $targetOfficer)) {
            abort(403, 'Akses lintas satker ditolak.');
        }

        $this->validate($request, [
            'first_name_edit' => 'required|max:20',
            'pangkat_edit' => 'required',
            'nrp_edit' => 'required|numeric',
            'polda_id_edit' => 'required',
            'polres_id_edit' => 'required',
        ]);
        Officer::where('id', $request->nrp_editt)
            ->update([
                'id' => $request->nrp_edit,
                'register_number' => $request->nrp_edit,
                'first_name' =>  $request->first_name_edit,
                'last_name' =>  $request->last_name_edit,
                'polda_id' =>  $request->polda_id_edit,
                'polres_id' =>  $request->polres_id_edit,
                'police_id' =>  (!empty($request->polres_id_edit)) ? $request->polres_id_edit : $request->polda_id_edit,
                'position_short_name' =>  $request->posisi_edit,
                'sebagai_kepala' =>  $request->kepala_edit,
                'rank_short_name' =>  $request->pangkat_edit
            ]);

        return redirect('petugas')->with('message', 'Sukses Update Data Petugas!');
    }

    public function search(Request $request)
    {
        $authUser = Auth::user();
        $user = $authUser->role_id;
        $search = $request->input('search');
        $polda_input = $request->input('polda');
        $polres_input = $request->input('polres');
        $status = $request->state;
        if ($polres_input == null) {
            $polres_input = '-';
        }
        if ($authUser->role_id == 2) {
            $polda_input = $authUser->polda_id;
        } elseif ($authUser->role_id == 3) {
            $polda_input = $authUser->polda_id;
            $polres_input = $authUser->polres_id;
        }

        $petugasQuery = DB::table('officers')
            ->leftJoin('polres', 'polres.id', '=', 'officers.polres_id')
            ->leftJoin('polda', 'polda.id', '=', 'polres.polda_id')
            ->select([
                'officers.id as id',
                'officers.first_name',
                'officers.last_name',
                'officers.rank_short_name',
                'polres.name as polres_name',
                'officers.position',
                'polda.name as polda_name',
                'officers.sebagai_kepala',
                'officers.state as officer_state',
            ])
            ->when(!empty($polda_input) && $polda_input !== '-', fn($q) => $q->where('officers.polda_id', $polda_input))
            ->when(!empty($status) && $status !== '-', fn($q) => $q->where('officers.state', $status))
            ->when(!empty($polres_input) && $polres_input !== '-', fn($q) => $q->where('officers.polres_id', $polres_input))
            ->when(!empty($search) && $search !== '-', function ($q) use ($search) {
                $term = '%' . $search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('officers.first_name', 'ilike', $term)
                        ->orWhere('officers.last_name', 'ilike', $term)
                        ->orWhere('officers.id', 'ilike', $term);
                });
            })
            ->orderByDesc('officers.created_at');

        if ($authUser->role_id == 2 || $authUser->role_id == 3) {
            $polda = Polda::where('id', '=', $authUser->polda_id)->get();
            $polres = Polres::where('polda_id', '=', $authUser->polda_id)->get();
        } else {
            $polda = Polda::all();
            $polres = Polres::all();
        }
        $rank = Ref::where('grp_id', '=', 'RANK')->orderBy('sort')->get();
        $data['petugas'] = $petugasQuery->paginate(10)->withQueryString();
        $data['polda'] = $polda;
        $data['user'] = $user;
        $data['polres'] = $polres;
        $data['rank'] = $rank;
        $data['status'] = $status;
        $data['polda_input'] = $polda_input;
        $data['polres_input'] = $polres_input;
        return view('petugas.petugas-index', $data);
    }

    public function delete_petugas($id)
    {
        $targetOfficer = DB::table('officers')->where('id', $id)->first();
        if (!$targetOfficer || !RegionalScopeService::authorizeTargetUser(Auth::user(), $targetOfficer)) {
            abort(403, 'Akses lintas satker ditolak.');
        }
        $petugas = DB::table('officers')->where('id', $id)->get();
        if ($petugas[0]->state == 1) {
            Officer::where('id', $petugas[0]->id)
                ->update(['state' => 0]);
            return redirect('petugas')->with('message', 'Sukses menonaktifkan Petugas!');
        }
        Officer::where('id', $petugas[0]->id)
            ->update(['state' => 1]);
        return redirect('petugas')->with('message', 'Sukses mengaktifkan Petugas!');
    }

    public function export_petugas(Request $request){
        $polda_input = $request->input('polda');
        $polres_input = $request->input('polres');
        $status = $request->state;
        if($polres_input == null){
            $polres_input = '-';
        }
        if($status== null){
            $status = '-';
        }
        // dd($status);
        // dd($search);
        // $petugas = DB::table('officers')->where('first_name', 'like', '%' . $search . '%')->paginate(2);
        $query = DB::table('officers')
            ->leftJoin('polres', 'polres.id', '=', 'officers.polres_id')
            ->leftJoin('polda', 'polda.id', '=', 'polres.polda_id')
            ->select([
                'officers.id as nrp',
                'officers.first_name',
                'officers.last_name',
                'officers.rank_short_name',
                'polres.name as polres_name',
                'officers.position',
                'polda.name as polda_name'
            ]);

        if (!empty($polda_input) && $polda_input !== '-') {
            $query->where('officers.polda_id', '=', $polda_input);
        }
        if (!empty($status) && $status !== '-') {
            $query->where('officers.state', '=', $status);
        }
        if (!empty($polres_input) && $polres_input !== '-') {
            $query->where('officers.polres_id', '=', $polres_input);
        }

        $petugas = $query->orderBy('officers.created_at', 'desc')->get();

        return Excel::download(new OfficerExport($petugas), 'Petugas.xlsx');
    }
}
