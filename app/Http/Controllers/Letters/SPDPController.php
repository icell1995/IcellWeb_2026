<?php

namespace App\Http\Controllers\Letters;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Accident;
use App\Models\SuratSpdp;
use App\Models\Letters\InvestigationOrderLetter\InvestigationOrderLetter;
use App\Models\SprintGas;
use App\Models\Suspect;
use App\Models\Peoples\AuthorizedSignatory;
use App\Models\Officer;
use App\Models\Meta\Institutions\Court;
use App\Models\Meta\Institutions\Prosecutor;
use App\Models\Polres;
use Carbon\Carbon;
use DB;


class SPDPController extends Controller
{
    public function spdp_index()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $spdp = SuratSpdp::with('officers', 'signatoryOfficer')
            ->where('accident_id', $accidentId)
            ->first();

        return $spdp;
    }

    public function spdp_create(){
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::find($accidentId);
        $no_lp = $accident->no_lp;
        $authorizedSignatories = AuthorizedSignatory::select('*', DB::raw("CONCAT(first_title, ' ', first_name, ' ', last_name, ', ', last_title) AS full_name"))
            ->where('polres_id', $accident->polres_id)
            ->orderBy('first_name')
            ->get();
        $officer = Officer::where('polres_id',$accident->polres_id)->get();
        $sprindik = InvestigationOrderLetter::where('accident_id',$accident->id)->first();
        $springas = SprintGas::where('accident_id',$accident->id)->first();
        $polres = Polres::where('id',$accident->polres_id)->first();
        $kejaksaan = Prosecutor::where('polda_id', $polres->polda_id)->get();
        $pengadilan = Court::where('polda_id', $polres->polda_id)->get();
        $suspects = Suspect::where('accident_id',$accident->id)->first();
        $pejabat = AuthorizedSignatory::where('polres_id',$accident->polres_id)->get();
        $user = Auth::user();
        $Created_By = $user->first_name . ' ' . $user->last_name;
        
        $viewData = [
            'authorizedSignatories' => $authorizedSignatories,
            'officer' => $officer,
            'sprindik' => $sprindik,
            'springas' => $springas,
            'accidentId' => $accidentId,
            'no_lp' => $no_lp,
            'kejaksaan' => $kejaksaan,
            'pengadilan' => $pengadilan,
            'suspects' => $suspects,
            'pejabat' => $pejabat,
            'Created_By' => $Created_By,
        ];

        return view('produktivitas.surat-tugas.spdp.create', $viewData);
    }

    public function spdp_store(Request $request)
{
    $accidentId = htmlspecialchars($request->accident_id_spdp);

    $pengadilan = DB::table('courts')->where('id', $request->pengadilan)->first();
    $kejaksaan = DB::table('prosecutors')->where('id', $request->endorsee_name)->first();

    $user = Auth::user();
    $Created_By = $user->first_name . ' ' . $user->last_name;

    // Memeriksa apakah ada tersangka atau tidak
    if ($request->tersangka === 'ada') {
        $suspectName = $request->suspect_name;
    } else {
        $suspectName = null;
    }

    SuratSpdp::updateOrCreate(['id' => $request->spdp_id], [
        'accident_id' => $accidentId,
        'id_springas' => $request->id_springas,
        'id_sprindik' => $request->id_spdik,
        'kejaksaan_id' => $kejaksaan->id,
        'pengadilan_id' => $pengadilan->id,
        'no_spdp' => $request->no_spdp,
        'no_lp' => $request->no_lp,
        'no_sprindik' => $request->no_sprindik,
        'sprindik_date' => $request->sprindik_date,
        'spdp_date' => Carbon::createFromFormat('d-m-Y', $request->spdp_date)->format('Y-m-d'),
        'category_spdp' => $request->category_spdp,
        'suspect_name' => $suspectName, // Menyimpan nama tersangka sesuai dengan radio button yang dipilih
        'endorsee_name' => $kejaksaan->name,
        'lampiran' => $request->lampiran,
        'tembusan' => $request->tembusan,
        'pengadilan' => $pengadilan->name,
        'latter_signature' => $request->latter_signature,
        'klasifikasi' => $request->klasifikasi,
        'for_attention' => $request->for_attention,
        'Lokasi_Dibuat' => $request->Lokasi_Dibuat,
        'Attachment' => $request->Attachment,
        'Created_By' => $Created_By,
    ]);
    Accident::where('id', $request->accident_id_spdp)
    ->update([
        'last_update' => Carbon::now(),
        'category' => 'D010104',
        'tipe_update' => 'MEMBUAT',
    ]);

    return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
}


    public function spdp_edit(){
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::find($accidentId);
        $spdp = SuratSpdp::where('accident_id', $accidentId)->first();
        $no_lp = $accident->no_lp;
        $authorizedSignatories = AuthorizedSignatory::select('*', DB::raw("CONCAT(first_title, ' ', first_name, ' ', last_name, ', ', last_title) AS full_name"))
            ->where('polres_id', $accident->polres_id)
            ->orderBy('first_name')
            ->get();
        $officer = Officer::where('polres_id',$accident->polres_id)->get();
        $sprindik = InvestigationOrderLetter::where('accident_id',$accident->id)->first();
        $springas = SprintGas::where('accident_id',$accident->id)->first();
        $polres = Polres::where('id',$accident->polres_id)->first();
        $kejaksaan = Prosecutor::where('polda_id', $polres->polda_id)->get();
        $pengadilan = Court::where('polda_id', $polres->polda_id)->get();
        $suspects = Suspect::where('accident_id',$accident->id)->first();
        $pejabat = AuthorizedSignatory::where('polres_id',$accident->polres_id)->get();

        $viewData = [
            'accidentId'=>$accidentId,

        ];

        return view('produktivitas.surat-tugas.surat-penetapan-tersangka.edit',$viewData);
    }

    public function spdp_update(Request $request){

    }

    public function spdp_delete(){
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $spdp = SuratSpdp::where('accident_id', $accidentId)->first();
        $spdp->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function spdp_view(Request $request){
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
    where accidents.id = \''.$accidentId.'\' ');

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

        return view('produktivitas.surat-tugas.spdp.cetak-spdp', $data);
    }
}
