<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Letters\InvestigationOrderLetter\InvestigationOrderLetter;
use App\Models\LHGP;
use App\Models\SuratKetetapanPenetapanTersangka;
use App\Models\Officer;
use App\Models\Geography\Country;
use App\Models\Geography\Province;
use App\Models\Geography\Regency;
use App\Models\Geography\District;
use App\Models\Geography\Village;
use App\Models\JenisGelarPerkara;
use App\Models\Ref;
use App\Models\Meta\Institutions\Prosecutor;
use App\Models\Peoples\AuthorizedSignatory;
use App\Models\Polres;
use App\Models\Suspect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use DB;

class SuratTugasController extends Controller
{
    public function getProvinces(Request $request)
    {
        $provinces = Province::where('country_id', $request->country_id)->get();
        return response()->json($provinces);
    }

    public function getRegency(Request $request)
    {
        $regencies = Regency::where('province_id', $request->provinceId)->orderBy('name', 'asc')->get();
        return response()->json($regencies);
    }

    public function getDistrict(Request $request)
    {
        $districts = District::where('regency_id', $request->regencyId)->orderBy('name', 'asc')->get();
        return response()->json($districts);
    }

    public function getVillage(Request $request)
    {
        $villages = Village::where('district_id', $request->districtId)->orderBy('name', 'asc')->get();
        return response()->json($villages);
    }

    public function lhgp_index()
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $lhgp = LHGP::with('officers', 'signatoryOfficer')
            ->where('accident_id', $accidentId)
            ->first();

        return $lhgp;
    }

    public function lhgp_create(){
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $accident = Accident::find($accidentId);
        $no_lp = $accident->no_lp;
        $penandatangan_surat = AuthorizedSignatory::select('*', DB::raw("CONCAT(first_title, ' ', first_name, ' ', last_name, ', ', last_title) AS full_name"))
        ->where('polres_id', $accident->polres_id)
        ->orderBy('first_name')
        ->get();
        $officer = Officer::where('polres_id',$accident->polres_id)->get();
        $sprindik = InvestigationOrderLetter::where('accident_id',$accident->id)->first();
        $jenis_gp = JenisGelarPerkara::select('id','nama_permasalahan')->get();
        $identity_type =DB::table('identity_types')->select('id', 'name')->get();
        $status_kawin =DB::table('marital_statuses')->select('id', 'name')->get();
        $ethnicity =DB::table('suku')->select('id', 'name')->get();
        $genders =DB::table('genders')->select('id', 'name')->get();
        $job =DB::table('jobs')->select('id', 'name')->get();
        $edu =DB::table('educations')->select('id', 'name')->get();
        $religion =DB::table('religions')->select('id', 'name')->get();
        $country = Country::orderBy('name', 'asc')->get();
        $provinces = Province::orderBy('name', 'asc')->get();
        $regency = Regency::orderBy('name', 'asc')->get();
        $district = District::orderBy('name', 'asc')->get();
        $village = Village::orderBy('name', 'asc')->get();

        $viewData = [
            'no_lp' => $no_lp,
            'penandatangan_surat' => $penandatangan_surat,
            'officer' => $officer,
            'sprindik' => $sprindik,
            'jenis_gp' => $jenis_gp,
            'identity_type' => $identity_type,
            'status_kawin' => $status_kawin,
            'ethnicity' => $ethnicity,
            'genders' => $genders,
            'job' => $job,
            'edu' => $edu,
            'religion' => $religion,
            'country' => $country,
            'provinces' => $provinces,
            'regency' => $regency,
            'district' => $district,
            'village' => $village,
            'accidentId' => $accidentId
        ];

        return view('produktivitas.surat-tugas.laporan-hasil-gelar-perkara.create',$viewData);
    }

    public function lhgp_store(Request $request){
        $accidentId = htmlspecialchars($request->accident_id_lhgp);

        // $this->validate($request,[
        //     'nomor_sprindik' => 'required',
        //     'jenis_lhgp' => 'required',
        //     'jenis_gelar_perkara' => 'required',
        //     'surat_undangan' => 'required',
        //     'tanggal_pelaksanaan' => 'required',
        //     'waktu_pelaksanaan' => 'required',
        //     'zona_waktu' => 'required',
        //     'tempat_pelaksanaan' => 'required',
        //     'pimpinan_gelar_perkara' => 'required',
        //     'pemapar' => 'required',
        //     'pejabat_penandatangan' => 'required',
        // ]);

        LHGP::updateOrCreate(['id'=>$request->lhgp_id],[
            'accident_id'=>$accidentId,
            'no_lp'=>$request->no_lp,
            'no_sprindik'=>$request->nomor_sprindik,
            'jenis_lhgp'=>$request->jenis_lhgp,
            'jenis_gelar_perkara'=>$request->jenis_gelar_perkara,
            'surat_undangan'=>$request->surat_undangan,
            'tanggal_pelaksanaan'=>Carbon::createFromFormat('d-m-Y',$request->tanggal_pelaksanaan)->format('Y-m-d'),
            'waktu_pelaksanaan' => $request->waktu_pelaksanaan ? Carbon::createFromFormat('H:i:s', $request->waktu_pelaksanaan)->format('H:i') : null,
            'zona_waktu'=>$request->zona_waktu,
            'tempat_pelaksanaan'=>$request->tempat_pelaksanaan,
            'pimpinan_gelar_perkara'=>$request->pimpinan_gelar,
            'pemapar'=>$request->pemapar,
            'Pembahasan'=>$request->pembahasan,
            'Kesimpulan'=>$request->kesimpulan,
            'Penutup'=>$request->penutup,
            'pejabat_penandatangan'=>$request->pejabat_penandatangan
        ]);

        Accident::where('id', $request->accident_id_lhgp)
        ->update([
            'last_update' => Carbon::now(),
            'category' =>'D010111',
            'tipe_update' => 'MEMBUAT'
        ]);

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function lhgp_edit(Request $request){
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        // Get data from database
        $accident = Accident::find($accidentId);

        $lhgp = LHGP::where('accident_id', $accidentId)->get();

        $penandatangan_surat = AuthorizedSignatory::select('*', DB::raw("CONCAT(first_title, ' ', first_name, ' ', last_name, ', ', last_title) AS full_name"))
        ->where('polres_id', $accident->polres_id)
        ->orderBy('first_name')
        ->get();
        $officer = Officer::where('polres_id',$accident->polres_id)->get();
        $sprindik = InvestigationOrderLetter::where('accident_id',$accident->id)->first();
        $jenis_gp = JenisGelarPerkara::select('id','nama_permasalahan')->get();
        $identity_type =DB::table('identity_types')->select('id', 'name')->get();
        $status_kawin =DB::table('marital_statuses')->select('id', 'name')->get();
        $ethnicity =DB::table('suku')->select('id', 'name')->get();
        $genders =DB::table('genders')->select('id', 'name')->get();
        $job =DB::table('jobs')->select('id', 'name')->get();
        $edu =DB::table('educations')->select('id', 'name')->get();
        $religion =DB::table('religions')->select('id', 'name')->get();
        $country = Country::orderBy('name', 'asc')->get();
        $provinces = Province::orderBy('name', 'asc')->get();
        $regency = Regency::orderBy('name', 'asc')->get();
        $district = District::orderBy('name', 'asc')->get();
        $village = Village::orderBy('name', 'asc')->get();

        $viewData = [
            'penandatangan_surat' => $penandatangan_surat,
            'officer' => $officer,
            'sprindik' => $sprindik,
            'jenis_gp' => $jenis_gp,
            'identity_type' => $identity_type,
            'status_kawin' => $status_kawin,
            'ethnicity' => $ethnicity,
            'genders' => $genders,
            'job' => $job,
            'edu' => $edu,
            'religion' => $religion,
            'country' => $country,
            'provinces' => $provinces,
            'regency' => $regency,
            'district' => $district,
            'village' => $village,
            'accidentId' => $accidentId,
            'lhgp'=>$lhgp
        ];

        return view('produktivitas.surat-tugas.laporan-hasil-gelar-perkara.edit', $viewData);
    }

    public function lhgp_update(Request $request){
        $accidentId = htmlspecialchars($request->accident_id_lhgp);

        // $this->validate($request,[
        //     'nomor_sprindik' => 'required',
        //     'jenis_lhgp' => 'required',
        //     'jenis_gelar_perkara' => 'required',
        //     'surat_undangan' => 'required',
        //     'tanggal_pelaksanaan' => 'required',
        //     'waktu_pelaksanaan' => 'required',
        //     'zona_waktu' => 'required',
        //     'tempat_pelaksanaan' => 'required',
        //     'pimpinan_gelar_perkara' => 'required',
        //     'pemapar' => 'required',
        //     'pejabat_penandatangan' => 'required',
        // ]);
        $lhgp = LHGP::where('id',$request->lhgp_id)->first();
        $lhgp->update([
            'accident_id'=>$accidentId,
            'no_lp'=>$request->no_lp,
            'no_sprindik'=>$request->nomor_sprindik,
            'jenis_lhgp'=>$request->jenis_lhgp,
            'jenis_gelar_perkara'=>$request->jenis_gelar_perkara,
            'surat_undangan'=>$request->surat_undangan,
            'tanggal_pelaksanaan'=>Carbon::createFromFormat('d-m-Y',$request->tanggal_pelaksanaan)->format('Y-m-d'),
            'waktu_pelaksanaan' => $request->waktu_pelaksanaan ? Carbon::createFromFormat('H:i:s', $request->waktu_pelaksanaan)->format('H:i') : null,
            'zona_waktu'=>$request->zona_waktu,
            'tempat_pelaksanaan'=>$request->tempat_pelaksanaan,
            'pimpinan_gelar_perkara'=>$request->pimpinan_gelar,
            'pemapar'=>$request->pemapar,
            'Pembahasan'=>$request->pembahasan,
            'Kesimpulan'=>$request->kesimpulan,
            'Penutup'=>$request->penutup,
            'pejabat_penandatangan'=>$request->pejabat_penandatangan
        ]);

        // dd($lhgp);

        Accident::where('id', $request->accident_id_lhgp)
        ->update([
            'last_update' => Carbon::now(),
            'category' =>'D010111',
            'tipe_update' => 'MENGUBAH'
        ]);

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function lhgp_view(Request $request){
        $accident_id=$request->input('accident_id');

        $accident =DB::select('select * from accidents where id= \''.$accident_id.'\' ');
        $lhgp =DB::select('select * from lhgp where accident_id= \''.$accident_id.'\' ');
        setlocale(LC_TIME, 'id_ID.utf8');
        $tanggal_pelaksanaan = Carbon::parse($lhgp[0]->tanggal_pelaksanaan)->isoFormat('dddd, D MMMM YYYY');;
        $waktu_pelaksanaan = Carbon::parse($lhgp[0]->waktu_pelaksanaan)->format('H:i');

        $data['accident']=$accident[0];
        $data['no_lp']=$lhgp[0]->no_lp;
        $data['no_sprindik']=$lhgp[0]->no_sprindik;
        $data['surat_undangan']=$lhgp[0]->surat_undangan;
        $data['tanggal_pelaksanaan']=$tanggal_pelaksanaan;
        $data['waktu_pelaksanaan']=$waktu_pelaksanaan;
        $data['tempat_pelaksanaan']=$lhgp[0]->tempat_pelaksanaan;
        $data['pemapar']=$lhgp[0]->pemapar;
        $data['pimpinan_gelar']=$lhgp[0]->pimpinan_gelar_perkara;
        $data['pembahasan']=$lhgp[0]->Pembahasan;
        $data['kesimpulan']=$lhgp[0]->Kesimpulan;
        $data['penutup']=$lhgp[0]->Penutup;

        return view('produktivitas.surat-tugas.laporan-hasil-gelar-perkara.cetak-lhgp',$data);
    }

    public function lhgp_delete(){
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        // Delete from database
        $lhgp = LHGP::where('accident_id', $accidentId)->first();
        $lhgp->delete();

        // Redirect with param accident_id
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function sddl_index()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $sddl = SuratKetetapanPenetapanTersangka::with('officers', 'signatoryOfficer')
            ->where('accident_id', $accidentId)
            ->first();

        return $sddl;
    }

    public function sddl_create(){
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        // $lhgpId = htmlspecialchars(request()->query('id'));

        // $lhgp = LHGP::find($lhgpId);

        $accident = Accident::find($accidentId);
        $no_lp = $accident->no_lp;
        // $penandatangan_surat = Officer::where('polres_id',$accident->polres_id)
        // ->where('sebagai_kepala','!=','-')->get();
        $authorizedSignatories = AuthorizedSignatory::select('*', DB::raw("CONCAT(first_title, ' ', first_name, ' ', last_name, ', ', last_title) AS full_name"))
            ->where('polres_id', $accident->polres_id)
            ->orderBy('first_name')
            ->get();
        $officer = Officer::where('polres_id',$accident->polres_id)->get();
        $sprindik = InvestigationOrderLetter::where('accident_id',$accident->id)->first();
        // $kejaksaan =DB::table('prosecutors')->select('id', 'name')->get();
        $identity_type =DB::table('identity_types')->select('id', 'name')->get();
        $status_kawin =DB::table('marital_statuses')->select('id', 'name')->get();
        $genders =DB::table('genders')->select('id', 'name')->get();
        $job =DB::table('jobs')->select('id', 'name')->get();
        $edu =DB::table('educations')->select('id', 'name')->get();
        $religion =DB::table('religions')->select('id', 'name')->get();
        $country = Country::orderBy('name', 'asc')->get();
        $provinces = Province::orderBy('name', 'asc')->get();
        $regency = Regency::orderBy('name', 'asc')->get();
        $district = District::orderBy('name', 'asc')->get();
        $village = Village::orderBy('name', 'asc')->get();
        $suku = DB::table('suku')->select('id','name')->get();
        $suspects = DB::table('suspects')->where('accident_id', $accidentId)->first();
        $lhgp = DB::table('lhgp')->where('accident_id', $accidentId)->first();

        $polresjaksa = Polres::where('id',$accident->polres_id)->first();
        $jaksa = Prosecutor::where('polda_id', $polresjaksa->polda_id)->get();

        $viewData = [
            'authorizedSignatories' => $authorizedSignatories,
            'officer' => $officer,
            'sprindik' => $sprindik,
            'accidentId' => $accidentId,
            'no_lp' => $no_lp,
            'letter_number' => $sprindik,
            'name'=>$jaksa,
            'id'=>$identity_type,
            'merried'=>$status_kawin,
            'gender'=>$genders,
            'jobs'=>$job,
            'educate'=>$edu,
            'religion'=>$religion,
            'country'=>$country,
            'provinces' => $provinces,
            'regency' => $regency,
            'district' => $district,
            'village' => $village,
            'suku' => $suku,

            'suspectName'=>$suspects->name,
            'id_types'=>$suspects->identity_type,
            'id_number'=>$suspects->identity_number,
            'mart_status'=>$suspects->marital_status,
            'phone_no'=>$suspects->phone_number,
            'sex'=>$suspects->gender,
            'occupation'=>$suspects->occupation,
            'email_address'=>$suspects->email_address,
            'birth_place'=>$suspects->birth_place,
            'birth_date'=>$suspects->birth_date,
            'education'=>$suspects->education,
            'relig'=>$suspects->religion,
            'countrys'=>$suspects->country,
            'prov' =>$suspects->province,
            'mother_name' =>$suspects->mother_name,
            'father_name' =>$suspects->father_name,
            'city' => $suspects->city,
            'districts' => $suspects->district,
            'sub_district' => $suspects->sub_district,
            'address' => $suspects->address,
            'ethnicity' => $suspects->ethnicity,
            'tanggal_pelaksanaan' => $lhgp->tanggal_pelaksanaan,
            'lhgpId' => $lhgp->id,
            'sprindikId' => $sprindik->id
        ];

        return view('produktivitas.surat-tugas.surat-penetapan-tersangka.create',$viewData);
    }

    public function sddl_store(Request $request){
        $accidentId = htmlspecialchars($request->accident_id_sddl);

        SuratKetetapanPenetapanTersangka::create([
            'accident_id'=>$accidentId,
            'letter_number'=>$request->letter_number,
            'letter_date'=>Carbon::parse($request->letter_date_ketetapan)->format('Y-m-d'),
            'no_lp'=>$request->no_lp,
            'no_sprindik'=>$request->no_sprindik,
            'prosecutor_office_id'=>$request->kejaksaan,
            'signing_officials'=>$request->letter_signature,
            'suspect_source'=>$request->sumber,
            'investigation_warrant_id'=>$request->accident_id_lhgp,
            'investigation_report_id'=>$request->sprindik_id_sddl,
            'tgl_gelar'=>$request->tgl_gelar
        ]);

        Accident::where('id', $request->accident_id_sddl)
        ->update([
            'last_update' => Carbon::now(),
            'category' =>'D010113',
            'tipe_update' => 'MEMBUAT'
        ]);

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);

    }

    public function sddl_edit(){
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        
        $accident = Accident::find($accidentId);
        $sddl = SuratKetetapanPenetapanTersangka::where('accident_id', $accidentId)->first();
        
        $authorizedSignatories = AuthorizedSignatory::select('*', DB::raw("CONCAT(first_title, ' ', first_name, ' ', last_name, ', ', last_title) AS full_name"))
            ->where('polres_id', $accident->polres_id)
            ->orderBy('first_name')
            ->get();
        $officer = Officer::where('polres_id',$accident->polres_id)->get();
        $sprindik = InvestigationOrderLetter::where('accident_id',$accident->id)->first();
        // $kejaksaan =DB::table('prosecutors')->select('id', 'name')->get();
        $identity_type =DB::table('identity_types')->select('id', 'name')->get();
        $status_kawin =DB::table('marital_statuses')->select('id', 'name')->get();
        $genders =DB::table('genders')->select('id', 'name')->get();
        $job =DB::table('jobs')->select('id', 'name')->get();
        $edu =DB::table('educations')->select('id', 'name')->get();
        $religion =DB::table('religions')->select('id', 'name')->get();
        $country = Country::orderBy('name', 'asc')->get();
        $provinces = Province::orderBy('name', 'asc')->get();
        $regency = Regency::orderBy('name', 'asc')->get();
        $district = District::orderBy('name', 'asc')->get();
        $village = Village::orderBy('name', 'asc')->get();
        $suku = DB::table('suku')->select('id','name')->get();
        $suspects = DB::table('suspects')->where('accident_id', $accidentId)->first();
        $lhgp = DB::table('lhgp')->where('accident_id', $accidentId)->first();

        $polresjaksa = Polres::where('id',$accident->polres_id)->first();
        $jaksa = Prosecutor::where('polda_id', $polresjaksa->polda_id)->get();
        // dd($jaksa);

        $viewData = [
            'accidentId' => $accidentId,
            'lhgpId' => $lhgp->id,
            'sprindikId' => $sprindik->id,
            'letter_number' => $sprindik->letter_number,
            'sddl' => $sddl,
            'name'=>$jaksa,
            'authorizedSignatories' => $authorizedSignatories,
            'officer' => $officer,
            'suspectName'=>$suspects->name,

            'id'=>$identity_type,
            'merried'=>$status_kawin,
            'gender'=>$genders,
            'jobs'=>$job,
            'educate'=>$edu,
            'religion'=>$religion,
            'country'=>$country,
            'provinces' => $provinces,
            'regency' => $regency,
            'district' => $district,
            'village' => $village,
            'suku' => $suku,
            'id_types'=>$suspects->identity_type,
            'id_number'=>$suspects->identity_number,
            'mart_status'=>$suspects->marital_status,
            'phone_no'=>$suspects->phone_number,
            'sex'=>$suspects->gender,
            'occupation'=>$suspects->occupation,
            'email_address'=>$suspects->email_address,
            'birth_place'=>$suspects->birth_place,
            'birth_date'=>$suspects->birth_date,
            'education'=>$suspects->education,
            'relig'=>$suspects->religion,
            'countrys'=>$suspects->country,
            'prov' =>$suspects->province,
            'mother_name' =>$suspects->mother_name,
            'father_name' =>$suspects->father_name,
            'city' => $suspects->city,
            'districts' => $suspects->district,
            'sub_district' => $suspects->sub_district,
            'address' => $suspects->address,
            'ethnicity' => $suspects->ethnicity,
            'tanggal_pelaksanaan' => $lhgp->tanggal_pelaksanaan,            
        ];

        return view('produktivitas.surat-tugas.surat-penetapan-tersangka.edit',$viewData);
    }

    public function sddl_update(Request $request)
    {
        $request->validate([
            'letter_number' => 'required',
            'letter_date_ketetapan' => 'required',
            'kejaksaan' => 'required',
            'letter_signature' => 'required',
            'sumber' => 'required',
            'tgl_gelar' => 'required'
        ]);

        $accidentId = htmlspecialchars($request->accident_id_sddl);
        $letter_number = htmlspecialchars($request->letter_number);
        $letter_date = Carbon::parse(htmlspecialchars($request->letter_date_ketetapan))->format('Y-m-d');
        $kejaksaan = htmlspecialchars($request->kejaksaan);
        $letter_signature = htmlspecialchars($request->letter_signature);
        $sumber = htmlspecialchars($request->sumber);
        $tgl_gelar = htmlspecialchars($request->tgl_gelar);


        $sddl = SuratKetetapanPenetapanTersangka::where('accident_id', $accidentId)->first();
        $sddl ->update([
            'letter_number' => $letter_number,
            'letter_date' => $letter_date,
            'prosecutor_office_id' => $kejaksaan,
            'signing_officials' => $letter_signature,
            'suspect_source' => $sumber,
            'tgl_gelar' => $tgl_gelar
        ]);

        
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function sddl_delete(){
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $Sddl = SuratKetetapanPenetapanTersangka::where('accident_id', $accidentId)->first();
        $Sddl->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function sddl_view(Request $request){
        $accident_id=$request->input('accident_id');

        $accident =DB::select('select * from accidents where id= \''.$accident_id.'\' ');
        $sddl =DB::select('select * from suspect_determination_decision_letters where accident_id= \''.$accident_id.'\' ');
        $spdik =DB::select('select * from legacy.investigation_order_letters where accident_id= \''.$accident_id.'\' ');
        $prosecutor = Prosecutor::where('id', $sddl[0]->prosecutor_office_id)->first();
        $kejaksaan = $prosecutor->name;
        $tanggal_pelaksanaan = Carbon::parse($sddl[0]->tgl_gelar)->format('d F Y');

        $suspects = DB::select('select polda.full_name as polda_name, polres.full_name as polres_name, polda.address as polda_alamat, polres.address as polres_alamat,
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
        where accidents.id = \''.$accident_id.'\' ');

        $data['accident']=$accident[0];
        $data['polres_name']=$suspects[0]->polres_name;
        $data['polda_name']=$suspects[0]->polda_name;
        $data['polda_name']=$suspects[0]->polda_name;
        $data['polres_alamat']=$suspects[0]->polres_alamat;
        $data['polres_district']=$suspects[0]->polres_kecamatan;
        $data['polres_zipcode']=$suspects[0]->polres_kodepos;
        $data['no_lp']=$sddl[0]->no_lp;
        $data['kejaksaan']=$kejaksaan;
        $data['no_sprindik']=$sddl[0]->no_sprindik;
        $data['issued_date']=$spdik[0]->issued_date;
        $data['letter_number']=$sddl[0]->letter_number;
        $data['letter_date']=$sddl[0]->letter_date;
        $data['signing_officials']=$sddl[0]->signing_officials;
        $data['tanggal_pelaksanaan']=$tanggal_pelaksanaan;
        $data['name']=$suspects[0]->nama_tersangka;
        $data['identity_number']=$suspects[0]->nomor_identitas;
        $data['country']=$suspects[0]->kewarganegaraan;
        $data['gender']=$suspects[0]->jenis_kelamin;
        $data['birth_place']=$suspects[0]->tempat_lahir;
        $data['birth_date']=$suspects[0]->tanggal_lahir;
        $data['occupation']=$suspects[0]->pekerjaan;
        $data['religion']=$suspects[0]->agama;
        $data['address']=$suspects[0]->alamat;

        return view('produktivitas.surat-tugas.surat-penetapan-tersangka.cetak-sddl',$data);
    }

}


