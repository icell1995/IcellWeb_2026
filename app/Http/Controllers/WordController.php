<?php

namespace App\Http\Controllers;

use App\Models\LHGP;
use App\Models\Officer;
use App\Models\SuratKetetapanPenetapanTersangka;
use App\Models\Suspect;
use App\Models\Accident;
use App\Models\Letters\InvestigationOrderLetter\InvestigationOrderLetter;
use App\Models\Meta\Institutions\Prosecutor;
use App\Models\Peoples\AuthorizedSignatory;
use DB;
use Carbon\Carbon;
use PhpOffice\PhpWord\Element\TextRun;

use Illuminate\Http\Request;

use App\Models\SprintGas;

class WordController extends Controller
{
    // public function createWordDocx()
    // {
    //     $wordTest = new \PhpOffice\PhpWord\PhpWord();

    //     $newSection = $wordTest->addSection();

    //     $desc1 = "test1";
    //     $desc2 = "test2";
    //     $desc3 = "test3";

    //     $newSection->addText($desc1);
    //     $newSection->addText($desc2);

    //     $objectWriter = \PhpOffice\PhpWord\IOFactory::createWriter($wordTest,"Word2007");

    //     try{
    //         $objectWriter->save(storage_path('test.docx'));
    //     }catch(Exception $e){

    //     }

    //     return response()->download(storage_path('test.docx'));
    // }

    public function createword_surat_tugas($id)
    {
        $surat_perintah_tugas = DB::select('select * from surat_tugas left join officers on surat_tugas.officer_id = officers.id where accident_id = \''.$id.'\' ');
        // dd($surat_perintah_tugas);
        $get_accident = DB::select('select polda.name as polda_name, polres.name as polres_name, polda.address as polda_alamat, polres.address as polres_alamat from accidents
         left join polres on accidents.polres_id = polres.id
         left join polda on polda.id = polres.polda_id
         where accidents.id = \''.$id.'\' ');
        $get_data = [];
        $i=1;
        foreach ($surat_perintah_tugas as $i => $surat_tugas) {
          $i++;
          $get_data[] = [
            'nomor' => $i,
            'first_name'   => $surat_tugas->first_name,
            'last_name'  => $surat_tugas->last_name,
            'rank_id' => $surat_tugas->rank_short_name,
            'officer_id' => $surat_tugas->officer_id,
            'position' => $surat_tugas->position,
          ];
        }
            // dd($get_data);
        $accident =DB::select('select * from accidents where id= \''.$id.'\' ');



        $data['surat_perintah_tugas']=$surat_perintah_tugas;
        // dd($data['surat_perintah_tugas']);
        $data['accident']=$accident;
        $data['accident_date'] = Carbon::parse($accident[0]->accident_date)->format('d F Y');
        $data['accident_time'] = Carbon::parse($accident[0]->accident_time)->format('H:m');
        $data['road_name']=$accident[0]->road_name;
        $data['no_lp']=$accident[0]->no_lp;
        $data['polda_name']=$get_accident[0]->polda_name;
        $data['polres_alamat']=$get_accident[0]->polres_alamat;

        // $templateProcessor->cloneRowAndSetValues('userId', $values);

      $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/surat_tugas.docx');

    //     $title = new TextRun();
    //     $title->addText('This title has been set ', array('bold' => true, 'italic' => true, 'color' => 'blue'));
    //     $title->addText('dynamically', array('bold' => true, 'italic' => true, 'color' => 'red', 'underline' => 'single'));
    //     $templateProcessor->setComplexBlock('title', $title);

    //     $inline = new TextRun();
    //     $inline->addText('by a red italic text', array('italic' => true, 'color' => 'red'));
    //     $templateProcessor->setComplexValue('inline', $inline);


      $templateProcessor->cloneBlock('block_name', 2, true, false, $get_data);
      $templateProcessor->setValue('accident_date',$data['accident_date']);
      $templateProcessor->setValue('no_lp',$data['no_lp']);
      $templateProcessor->setValue('accident_time',$data['accident_time']);
      $templateProcessor->setValue('road_name',$data['road_name']);
      $templateProcessor->setValue('polda_name',$data['polda_name']);
      $templateProcessor->setValue('polres_alamat',$data['polres_alamat']);


      $filename = 'Surat Perintah Tugas';
      $templateProcessor->saveAs($filename.'.docx');
      return response()->download($filename.'.docx')->deleteFileAfterSend(true);
    }
    public function createword_surat_penyitaan($id)
    {
        $surat_perintah_penyitaan = DB::select('select * from surat_penyitaan left join officers on surat_penyitaan.officer_id = officers.id where accident_id = \''.$id.'\' ');
        // dd($surat_perintah_penyitaan);
        $get_accident = DB::select('select polda.name as polda_name, polres.name as polres_name, polda.address as polda_alamat, polres.address as polres_alamat from accidents
         left join polres on accidents.polres_id = polres.id
         left join polda on polda.id = polres.polda_id
         where accidents.id = \''.$id.'\' ');
        $get_data = [];
        $i=1;
        foreach ($surat_perintah_penyitaan as $i => $surat_penyitaan) {
          $i++;
          $get_data[] = [
            'nomor' => $i,
            'first_name'   => $surat_penyitaan->first_name,
            'last_name'  => $surat_penyitaan->last_name,
            'rank_id' => $surat_penyitaan->rank_short_name,
            'officer_id' => $surat_penyitaan->officer_id,
            'position' => $surat_penyitaan->position,
          ];
        }
            // dd($get_data);
        $accident =DB::select('select * from accidents where id= \''.$id.'\' ');



        $data['surat_perintah_penyitaan']=$surat_perintah_penyitaan;
        $data['accident']=$accident;
        $data['accident_day'] = Carbon::parse($accident[0]->accident_date)->locale('id')->translatedFormat('l');
        $data['accident_date'] = Carbon::parse($accident[0]->accident_date)->format('d F Y');
        $data['accident_time'] = Carbon::parse($accident[0]->accident_time)->format('H:m');
        $data['road_name']=$accident[0]->road_name;
        $data['no_lp']=$accident[0]->no_lp;
        $data['polda_name']=$get_accident[0]->polda_name;
        $data['polres_name']=$get_accident[0]->polres_name;
        $data['polres_alamat']=$get_accident[0]->polres_alamat;

      $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/surat_perintah_penyitaan.docx');


      $templateProcessor->cloneBlock('block_name', 2, true, false, $get_data);
      $templateProcessor->setValue('accident_day',$data['accident_day']);
      $templateProcessor->setValue('accident_date',$data['accident_date']);
      $templateProcessor->setValue('no_lp',$data['no_lp']);
      $templateProcessor->setValue('accident_time',$data['accident_time']);
      $templateProcessor->setValue('road_name',$data['road_name']);
      $templateProcessor->setValue('polda_name',$data['polda_name']);
      $templateProcessor->setValue('polres_name',$data['polres_name']);
      $templateProcessor->setValue('polres_alamat',$data['polres_alamat']);


      $filename = 'Surat Perintah Penyitaan';
      $templateProcessor->saveAs($filename.'.docx');
      return response()->download($filename.'.docx')->deleteFileAfterSend(true);
    }

    public function createword_springas($id)
    {
        $accident =DB::select('select * from accidents where id= \''.$id.'\' ');
        $springas = SprintGas::where('accident_id',$id)->first();
        $pejabat = AuthorizedSignatory::where('id', $springas->pejabat_penandatangan)->first();
        $sprindik = InvestigationOrderLetter::where('accident_id',$id)->first();
        // $sprindik =DB::select('select * from legacy.investigation_order_letters where accident_id= \''.$id.'\' ');
        $officer_springas =DB::select('select * from legacy.officer_springas left join legacy.springas on legacy.officer_springas.sprint_gas_id = legacy.springas.id left join officers on legacy.officer_springas.officer_id = officers.id where accident_id= \''.$id.'\' ');
        $leader = Officer::where('id',$springas->ketua_tim)->first();
        $get_accident = DB::select('select polda.full_name as polda_name, polres.full_name as polres_name, polda.address as polda_alamat, polres.address as polres_alamat, polres.polres_district as polres_district, polres.polres_zipcode as polres_zipcode from accidents
        left join polres on accidents.polres_id = polres.id
        left join polda on polda.id = polres.polda_id
        where accidents.id = \''.$id.'\' ');
        $get_data = [];
        // $count_officer_springas = count($officer_springas);
        $i=2;
        var_dump($i);
        foreach($officer_springas as $officers){
            $get_data[] = [
                'nomor' => $i,
                'first_name' => $officers->first_name,
                'last_name' => $officers->last_name,
                'rank_id' => $officers->rank_short_name,
                'officer_id' => $officers->officer_id,
                'position' => $officers->position,
            ];
            $i++;
        }
        var_dump($i);
        // dd($count_officer_springas);

        $data['springas']=$springas;
        $data['accident']=$accident;
        $data['polres_name']=$get_accident[0]->polres_name;
        $data['polda_name']=$get_accident[0]->polda_name;
        $data['polres_alamat']=$get_accident[0]->polres_alamat .', '. $get_accident[0]->polres_district .', '. $get_accident[0]->polres_zipcode;
        $data['polres_district']=$get_accident[0]->polres_district;
        $data['no_lp']=$springas->no_lp;
        $data['no_surat']=$springas->no_surat;
        $data['no_sprindik']=$sprindik->letter_number;
        $data['pejabat_id']=$pejabat->register_number;
        $data['pejabat_rank']=$pejabat->rank_id;
        $data['pejabat_sebagai']=$pejabat->position_id;
        $data['pejabat_name']=$pejabat->first_title .' '. $pejabat->first_name . ' ' . $pejabat->last_name .', '. $pejabat->last_title;
        $data['leader_name']=$leader->first_name .' '. $leader->last_name;
        $data['leader_rank']=$leader->rank_short_name;
        $data['leader_nrp']=$leader->id;
        $data['leader_jabatan']=$leader->sebagai_kepala;
        setlocale(LC_TIME, 'id_ID.utf8');
        $data['accident_date']=Carbon::parse($accident[0]->accident_date)->isoFormat('D MMMM YYYY');
        $data['tanggal_sprindik']=Carbon::parse($sprindik->issued_date)->isoFormat('D MMMM YYYY');
        $data['tanggal_dimulai']=Carbon::parse($springas->tanggal_dimulai)->isoFormat('D MMMM YYYY');

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/springas.docx');

        $signatureTitleText = [
            'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . $data['polres_name'],
            'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . $data['polres_name'] . '</w:t><w:p/><w:t>' . $data['pejabat_sebagai'],
            'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . $data['polres_name'] . '</w:t><w:p/><w:t>' . $data['pejabat_sebagai'],
        ];

        if($data['pejabat_sebagai'] == 'KAPOLRES'){
            $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['KAPOLRES']);
        }else if($data['pejabat_sebagai'] == 'KASUBDITGAKKUM' || $data['pejabat_sebagai'] == 'PS. KASUBDITGAKKUM'){
            $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_DIRLANTAS']);
        }else{
            $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_KAPOLRES']);
        }

        $templateProcessor->cloneBlock('block_name', $i, true, false, $get_data);
        $templateProcessor->setValue('polres_name',$data['polres_name']);
        $templateProcessor->setValue('polda_name',$data['polda_name']);
        $templateProcessor->setValue('polres_alamat',ucwords(strtolower($data['polres_alamat'])));
        $templateProcessor->setValue('polres_district',ucwords(strtolower($data['polres_district'])));
        $templateProcessor->setValue('no_lp',$data['no_lp']);
        $templateProcessor->setValue('accident_date',$data['accident_date']);
        $templateProcessor->setValue('no_surat',$data['no_surat']);
        $templateProcessor->setValue('no_sprindik',$data['no_sprindik']);
        $templateProcessor->setValue('tanggal_sprindik',$data['tanggal_sprindik']);
        $templateProcessor->setValue('tanggal_dimulai',$data['tanggal_dimulai']);
        $templateProcessor->setValue('leader_name',$data['leader_name']);
        $templateProcessor->setValue('leader_rank',$data['leader_rank']);
        $templateProcessor->setValue('leader_nrp',$data['leader_nrp']);
        $templateProcessor->setValue('leader_jabatan',$data['leader_jabatan']);
        $templateProcessor->setValue('pejabat_name',$data['pejabat_name']);
        $templateProcessor->setValue('pejabat_sebagai',$data['pejabat_sebagai']);
        $templateProcessor->setValue('pejabat_rank',$data['pejabat_rank']);
        $templateProcessor->setValue('pejabat_id',$data['pejabat_id']);

        $filename = 'Surat Perintah Tugas';
        $templateProcessor->saveAs($filename.'.docx');
        return response()->download($filename.'.docx')->deleteFileAfterSend(true);

    }

    public function createword_lhgp($id)
    {
        $accident =DB::select('select * from accidents where id= \''.$id.'\' ');
        $lhgp = LHGP::where('accident_id',$id)->first();
        $pejabat = AuthorizedSignatory::where('id', $lhgp->pejabat_penandatangan)->first();
        // $pejabat = Officer::with('rank')->where('id',$lhgp->pejabat_penandatangan)->first();
        // $sprindik =DB::select('select * from legacy.investigation_order_letters where accident_id= \''.$id.'\' ');
        $get_accident = DB::select('select polda.full_name as polda_name, polres.full_name as polres_name, polda.address as polda_alamat, polres.address as polres_alamat, polres.polres_province as polres_province from accidents
        left join polres on accidents.polres_id = polres.id
        left join polda on polda.id = polres.polda_id
        where accidents.id = \''.$id.'\' ');

        $data['lhgp']=$lhgp;
        $data['accident']=$accident;
        $data['polres_name']=$get_accident[0]->polres_name;
        $data['polda_name']=$get_accident[0]->polda_name;
        $data['polda_name']=$get_accident[0]->polda_name;
        $data['polres_alamat']=$get_accident[0]->polres_alamat;
        $data['no_lp']=$lhgp->no_lp;
        $data['no_sprindik']=$lhgp->no_sprindik;
        $data['surat_undangan']=$lhgp->surat_undangan;
        setlocale(LC_TIME, 'id_ID.utf8');
        $data['tanggal_pelaksanaan'] = Carbon::parse($lhgp->tanggal_pelaksanaan)->isoFormat('dddd, D MMMM YYYY');
        $data['waktu_pelaksanaan']=Carbon::parse($lhgp->waktu_pelaksanaan)->format('H:i');
        $data['zona_waktu']=$lhgp->zona_waktu;
        $data['tempat_pelaksanaan']=$lhgp->tempat_pelaksanaan;
        $data['pemapar']=$lhgp->pemapar;
        $data['pimpinan_gelar_perkara']=$lhgp->pimpinan_gelar_perkara;
        $data['pembahasan']=$lhgp->Pembahasan;
        $data['kesimpulan']=$lhgp->Kesimpulan;
        $data['penutup']=$lhgp->Penutup;
        $data['pejabat_id']=$pejabat->register_number;
        $data['pejabat_rank']=$pejabat->rank_id;
        $data['pejabat_sebagai']=$pejabat->position_id;
        $data['pejabat_name']=$pejabat->first_title .' '. $pejabat->first_name . ' ' . $pejabat->last_name .', '. $pejabat->last_title;

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/lhgp.docx');

        $signatureTitleText = [
            'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . $data['polres_name'],
            'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . $data['polres_name'] . '</w:t><w:p/><w:t>' . $data['pejabat_sebagai'],
            'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . $data['polres_name'] . '</w:t><w:p/><w:t>' . $data['pejabat_sebagai'],
        ];

        if($data['pejabat_sebagai'] == 'KAPOLRES'){
            $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['KAPOLRES']);
        }else if($data['pejabat_sebagai'] == 'KASUBDITGAKKUM' || $data['pejabat_sebagai'] == 'PS. KASUBDITGAKKUM'){
            $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_DIRLANTAS']);
        }else{
            $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_KAPOLRES']);
        }

        $templateProcessor->setValue('polres_name',$data['polres_name']);
        $templateProcessor->setValue('polda_name',$data['polda_name']);
        $templateProcessor->setValue('polres_alamat',$data['polres_alamat']);
        $templateProcessor->setValue('no_lp',$data['no_lp']);
        $templateProcessor->setValue('no_sprindik',$data['no_sprindik']);
        $templateProcessor->setValue('surat_undangan',$data['surat_undangan']);
        $templateProcessor->setValue('tanggal_pelaksanaan',$data['tanggal_pelaksanaan']);
        $templateProcessor->setValue('waktu_pelaksanaan',$data['waktu_pelaksanaan']);
        $templateProcessor->setValue('zona_waktu',$data['zona_waktu']);
        $templateProcessor->setValue('tempat_pelaksanaan',$data['tempat_pelaksanaan']);
        $templateProcessor->setValue('pemapar',$data['pemapar']);
        $templateProcessor->setValue('pimpinan_gelar_perkara',$data['pimpinan_gelar_perkara']);
        $templateProcessor->setValue('pembahasan',$data['pembahasan']);
        $templateProcessor->setValue('kesimpulan',$data['kesimpulan']);
        $templateProcessor->setValue('penutup',$data['penutup']);
        $templateProcessor->setValue('pejabat_name',$data['pejabat_name']);
        $templateProcessor->setValue('pejabat_sebagai',$data['pejabat_sebagai']);
        $templateProcessor->setValue('pejabat_rank',$data['pejabat_rank']);
        $templateProcessor->setValue('pejabat_id',$data['pejabat_id']);

        $filename = 'Laporan Hasil Gelar Perkara';
        $templateProcessor->saveAs($filename.'.docx');
        return response()->download($filename.'.docx')->deleteFileAfterSend(true);
    }

    public function createword_sddl($id)
    {
      $accident =DB::select('select * from accidents where id= \''.$id.'\' ');
      $sddl = SuratKetetapanPenetapanTersangka::where('accident_id',$id)->first();
      $kejaksaan = Prosecutor::where('id',$sddl->prosecutor_office_id)->first();
      $spdik = InvestigationOrderLetter::where('accident_id',$id)->first();
      // $pejabat = Officer::with('rank')->where('id',$sddl->signing_officials)->first();
      $pejabat = AuthorizedSignatory::where('id', $sddl->signing_officials)->first();
      // $sprindik =DB::select('select * from legacy.investigation_order_letters where accident_id= \''.$id.'\' ');
      $get_accident = DB::select('select polda.full_name as polda_name, polres.full_name as polres_name, polda.address as polda_alamat, polres.address as polres_alamat,
          polres.polres_district as polres_kecamatan, polres.polres_zipcode as polres_kodepos,
          suspects.name as nama_tersangka, suspects.identity_number as nomor_identitas, country.name as kewarganegaraan,
          genders.name as jenis_kelamin, suspects.birth_place as tempat_lahir, suspects.birth_date as tanggal_lahir,
          jobs.name as pekerjaan, religions.name as agama, suspects.address as alamat, authorized_signatories.position_id as posisi, accidents.accident_date as accident_date
      from accidents
          left join polres on accidents.polres_id = polres.id
          left join polda on polda.id = polres.polda_id
          left join suspects on accidents.id = suspects.accident_id
          left join genders on suspects.gender = genders.id
          left join jobs on suspects.occupation = jobs.id
          left join religions on suspects.religion = religions.id
          left join country on suspects.country = country.id
          left join authorized_signatories on accidents.polres_id = authorized_signatories.polres_id
      where accidents.id = \''.$id.'\' ');

      $data['sddl']=$sddl;
      $data['accident']=$accident;
      $data['polres_name']=$get_accident[0]->polres_name;
      $data['polda_name']=$get_accident[0]->polda_name;
      $data['polda_name']=$get_accident[0]->polda_name;
      $data['polres_alamat']=$get_accident[0]->polres_alamat;
      $data['polres_kecamatan']=$get_accident[0]->polres_kecamatan;
      $data['accident_date']=Carbon::parse($get_accident[0]->accident_date)->translatedFormat('d F Y');
      $data['no_lp']=$sddl->no_lp;
      $data['no_sprindik']=$sddl->no_sprindik;
      $data['letter_number']=$sddl->letter_number;
      $data['letter_date']=Carbon::parse($sddl->letter_date)->translatedFormat('d F Y');
      $data['issued_date']=Carbon::parse($spdik->issued_date)->translatedFormat('d F Y');
      $data['tgl_gelar'] = Carbon::parse($sddl->tgl_gelar)->translatedFormat('d F Y');

      // data tersangka
      $data['nama_tersangka']=$get_accident[0]->nama_tersangka;
      $data['nomor_identitas']=$get_accident[0]->nomor_identitas;
      $data['kewarganegaraan']=$get_accident[0]->kewarganegaraan;
      $data['jenis_kelamin']=$get_accident[0]->jenis_kelamin;
      $data['tempat_lahir']=$get_accident[0]->tempat_lahir;
      $data['tanggal_lahir']=$get_accident[0]->tanggal_lahir;
      $data['pekerjaan']=$get_accident[0]->pekerjaan;
      $data['agama']=$get_accident[0]->agama;
      $data['alamat']=$get_accident[0]->alamat;
      $data['name']=$kejaksaan->name;

      $data['pejabat_name']=$pejabat->first_title .' '. $pejabat->first_name . ' ' . $pejabat->last_name .', '. $pejabat->last_title;
      $data['pejabat_rank']=$pejabat->rank_id;
      $data['pejabat_nrp']=$pejabat->register_number;
      // $data['pejabat_sebagai']=$pejabat->sebagai_kepala;

      $signatureTitleText = [
        'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . $get_accident[0]->polres_name,
        'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . $get_accident[0]->polres_name . '</w:t><w:p/><w:t>' . $pejabat->position_id,
        'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . $get_accident[0]->polda_name . '</w:t><w:p/><w:t>' . $pejabat->position_id,
    ];
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/surat_ketetapan_penetapan_tersangka.docx');

    if($pejabat->position_id == 'KAPOLRES'){
        $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['KAPOLRES']);
    }else if($pejabat->position_id == 'KASUBDITGAKKUM' || $pejabat->position_id == 'PS. KASUBDITGAKKUM'){
        $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_DIRLANTAS']);
    }else{
        $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_KAPOLRES']);
    }

      $templateProcessor->setValue('polres_name',$data['polres_name']);
      $templateProcessor->setValue('polda_name',$data['polda_name']);
      $templateProcessor->setValue('polres_alamat',$data['polres_alamat']);
      $templateProcessor->setValue('no_lp',$data['no_lp']);
      $templateProcessor->setValue('no_sprindik',$data['no_sprindik']);
      $templateProcessor->setValue('letter_number',$data['letter_number']);
      $templateProcessor->setValue('tgl_gelar',$data['tgl_gelar']);
      $templateProcessor->setValue('issued_date',$data['issued_date']);
      $templateProcessor->setValue('accident_date',$data['accident_date']);
      $templateProcessor->setValue('letter_date',$data['letter_date']);
      // data tersangka
      $templateProcessor->setValue('nama_tersangka',$data['nama_tersangka']);
      $templateProcessor->setValue('nomor_identitas',$data['nomor_identitas']);
      $templateProcessor->setValue('kewarganegaraan',$data['kewarganegaraan']);
      $templateProcessor->setValue('jenis_kelamin',$data['jenis_kelamin']);
      $templateProcessor->setValue('tempat_lahir',$data['tempat_lahir']);
      $templateProcessor->setValue('tanggal_lahir',$data['tanggal_lahir']);
      $templateProcessor->setValue('pekerjaan',$data['pekerjaan']);
      $templateProcessor->setValue('agama',$data['agama']);
      $templateProcessor->setValue('alamat',$data['alamat']);
      $templateProcessor->setValue('name',$data['name']);
      $templateProcessor->setValue('polres_kecamatan',$data['polres_kecamatan']);
      $templateProcessor->setValue('pejabat_name',$data['pejabat_name']);
      $templateProcessor->setValue('pejabat_rank',$data['pejabat_rank']);
      $templateProcessor->setValue('pejabat_nrp',$data['pejabat_nrp']);
      // $templateProcessor->setValue('pejabat_id',$data['pejabat_id']);

      $filename = 'Surat Ketetapan Penetapan Tersangka';
      $templateProcessor->saveAs($filename.'.docx');
      return response()->download($filename.'.docx')->deleteFileAfterSend(true);
    }

    public function createword_surat_penyelidikan($id)
    {
        $surat_perintah_penyelidikan = DB::select('select * from surat_penyelidikan left join officers on surat_penyelidikan.officer_id = officers.id where accident_id = \''.$id.'\' ');
        // dd($surat_perintah_tugas);
        $get_accident = DB::select('select polda.name as polda_name, polres.name as polres_name, polda.address as polda_alamat, polres.address as polres_alamat from accidents
         left join polres on accidents.polres_id = polres.id
         left join polda on polda.id = polres.polda_id
         where accidents.id = \''.$id.'\' ');
        $get_data = [];
        $i=1;
        foreach ($surat_perintah_penyelidikan as $i => $surat_penyelidikan) {
            $i++;
                $get_data[] = [
                'nomor' => $i,
                'first_name'   => $surat_penyelidikan->first_name,
                'last_name'  => $surat_penyelidikan->last_name,
                'rank_id' => $surat_penyelidikan->rank_short_name,
                'officer_id' => $surat_penyelidikan->officer_id,
                'position' => $surat_penyelidikan->position,
                ];
            }
            // dd($get_data);
        $accident =DB::select('select * from accidents where id= \''.$id.'\' ');



        $data['surat_perintah_penyelidikan']=$surat_penyelidikan;
        // dd($data['surat_perintah_tugas']);
        $data['accident']=$accident;
        $data['accident_date'] = Carbon::parse($accident[0]->accident_date)->format('d F Y');
        $data['accident_time'] = Carbon::parse($accident[0]->accident_time)->format('H:m');
        $data['road_name']=$accident[0]->road_name;
        $data['no_lp']=$accident[0]->no_lp;
        $data['polda_name']=$get_accident[0]->polda_name;
        $data['polres_alamat']=$get_accident[0]->polres_alamat;
        // $templateProcessor->cloneRowAndSetValues('userId', $values);

      $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/surat_perintah_penyelidikan.docx');

    //     $title = new TextRun();
    //     $title->addText('This title has been set ', array('bold' => true, 'italic' => true, 'color' => 'blue'));
    //     $title->addText('dynamically', array('bold' => true, 'italic' => true, 'color' => 'red', 'underline' => 'single'));
    //     $templateProcessor->setComplexBlock('title', $title);

    //     $inline = new TextRun();
    //     $inline->addText('by a red italic text', array('italic' => true, 'color' => 'red'));
    //     $templateProcessor->setComplexValue('inline', $inline);


      $templateProcessor->cloneBlock('block_name', 2, true, false, $get_data);
      $templateProcessor->setValue('accident_date',$data['accident_date']);
      $templateProcessor->setValue('no_lp',$data['no_lp']);
      $templateProcessor->setValue('accident_time',$data['accident_time']);
      $templateProcessor->setValue('road_name',$data['road_name']);
      $templateProcessor->setValue('polda_name',$data['polda_name']);
      $templateProcessor->setValue('polres_alamat',$data['polres_alamat']);


      $filename = 'Surat Perintah Penyelidikan';
      $templateProcessor->saveAs($filename.'.docx');
      return response()->download($filename.'.docx')->deleteFileAfterSend(true);
    }


    public function createword_surat_penyidikan($id)
    {
        $surat_perintah_penyidikan = DB::select('select * from surat_penyidikan left join officers on surat_penyidikan.officer_id = officers.id where accident_id = \''.$id.'\' ');
        // dd($surat_perintah_tugas);
        $get_accident = DB::select('select polda.name as polda_name, polres.name as polres_name, polda.address as polda_alamat, polres.address as polres_alamat from accidents
        left join polres on accidents.polres_id = polres.id
        left join polda on polda.id = polres.polda_id
        where accidents.id = \''.$id.'\' ');
        $get_data = [];
        $i=1;
        foreach ($surat_perintah_penyidikan as $i => $surat_penyidikan) {
            $i++;
                $get_data[] = [
                'nomor' => $i,
                'first_name'   => $surat_penyidikan->first_name,
                'last_name'  => $surat_penyidikan->last_name,
                'rank_id' => $surat_penyidikan->rank_short_name,
                'officer_id' => $surat_penyidikan->officer_id,
                'position' => $surat_penyidikan->position,
                ];
            }
            // dd($get_data);
        $accident =DB::select('select * from accidents where id= \''.$id.'\' ');



        $data['surat_perintah_penyidikan']=$surat_penyidikan;
        // dd($data['surat_perintah_tugas']);
        $data['accident']=$accident;
        $data['accident_date'] = Carbon::parse($accident[0]->accident_date)->format('d F Y');
        $data['accident_time'] = Carbon::parse($accident[0]->accident_time)->format('H:m');
        $data['road_name']=$accident[0]->road_name;
        $data['no_lp']=$accident[0]->no_lp;
        $data['polda_name']=$get_accident[0]->polda_name;
        $data['polres_alamat']=$get_accident[0]->polres_alamat;
        // $templateProcessor->cloneRowAndSetValues('userId', $values);

      $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/surat_perintah_penyidikan.docx');

    //     $title = new TextRun();
    //     $title->addText('This title has been set ', array('bold' => true, 'italic' => true, 'color' => 'blue'));
    //     $title->addText('dynamically', array('bold' => true, 'italic' => true, 'color' => 'red', 'underline' => 'single'));
    //     $templateProcessor->setComplexBlock('title', $title);

    //     $inline = new TextRun();
    //     $inline->addText('by a red italic text', array('italic' => true, 'color' => 'red'));
    //     $templateProcessor->setComplexValue('inline', $inline);


      $templateProcessor->cloneBlock('block_name', 2, true, false, $get_data);
      $templateProcessor->setValue('accident_date',$data['accident_date']);
      $templateProcessor->setValue('no_lp',$data['no_lp']);
      $templateProcessor->setValue('accident_time',$data['accident_time']);
      $templateProcessor->setValue('road_name',$data['road_name']);
      $templateProcessor->setValue('polda_name',$data['polda_name']);
      $templateProcessor->setValue('polres_alamat',$data['polres_alamat']);


      $filename = 'Surat Perintah Penyidikan';
      $templateProcessor->saveAs($filename.'.docx');
      return response()->download($filename.'.docx')->deleteFileAfterSend(true);
    }


    public function createword_surat_spdp($id)
    {
        $surat_spdp = DB::select('select * from spdpp where accident_id = \''.$id.'\' ');
        $kejaksaan = DB::table('prosecutors')
        ->join('spdpp', 'prosecutors.id', '=', 'spdpp.kejaksaan_id')
        ->select('prosecutors.*')
        ->where('spdpp.accident_id', '=', $id)
        ->get();
        $pejabat = DB::table('authorized_signatories')
        ->join('spdpp', 'authorized_signatories.id', '=', 'spdpp.latter_signature')
        ->select('authorized_signatories.*')
        ->where('spdpp.accident_id', '=', $id)
        ->first();

        // $pejabat = AuthorizedSignatory::where('id', $surat_spdp->latter_signature)->first();
        $get_accident = DB::select('select polda.full_name as polda_name, polres.full_name as polres_name, polda.address as polda_alamat, polres.address as polres_alamat,
          polres.polres_district as polres_kecamatan, polres.polres_zipcode as polres_kodepos,
          suspects.name as nama_tersangka, suspects.identity_number as nomor_identitas, country.name as kewarganegaraan,
          genders.name as jenis_kelamin, suspects.birth_place as tempat_lahir, suspects.birth_date as tanggal_lahir,
          jobs.name as pekerjaan, religions.name as agama, suspects.address as alamat, authorized_signatories.position_id as posisi, accidents.accident_date as accident_date
      from accidents
          left join polres on accidents.polres_id = polres.id
          left join polda on polda.id = polres.polda_id
          left join suspects on accidents.id = suspects.accident_id
          left join genders on suspects.gender = genders.id
          left join jobs on suspects.occupation = jobs.id
          left join religions on suspects.religion = religions.id
          left join country on suspects.country = country.id
          left join authorized_signatories on accidents.polres_id = authorized_signatories.polres_id
      where accidents.id = \''.$id.'\' ');
        $get_data = [];
        $i=1;
        foreach ($get_accident as $i => $spdp) {
            $i++;
                $get_data[] = [
                'nomor' => $i,
                'nama_tersangka'   => $spdp->nama_tersangka,
                'jenis_kelamin'  => $spdp->jenis_kelamin,
                'tempat_lahir' => $spdp->tempat_lahir,
                'tanggal_lahir' => Carbon::parse($spdp->tanggal_lahir)->translatedFormat('d F Y'). " / ". Carbon::parse($spdp->tanggal_lahir)->age,
                'pekerjaan' => $spdp->pekerjaan,
                'agama' => $spdp->agama,
                'alamat' => $spdp->alamat,
                ];
            }
        $accident =DB::select('select * from accidents where id= \''.$id.'\' ');

        $data['klasifikasi']=$surat_spdp[0]->klasifikasi;
        $data['lampiran']=$surat_spdp[0]->lampiran;
        $data['pengadilan']=$surat_spdp[0]->pengadilan;
        $data['nama_kejaksaan']=$kejaksaan[0]->name;
        $data['regency']=$kejaksaan[0]->regency;
        $data['surat_spdp']=$surat_spdp;
        $data['accident']=$accident;
        $data['accident_date'] = Carbon::parse($accident[0]->accident_date)->translatedFormat('d F Y');
        $data['accident_day'] = Carbon::parse($accident[0]->accident_date)->locale('id')->translatedFormat('l');
        $data['accident_time'] = Carbon::parse($accident[0]->accident_time)->format('H:m');
        $data['spdp_date'] = Carbon::parse($surat_spdp[0]->spdp_date)->translatedFormat('d F Y');
        $data['road_name']=$accident[0]->road_name;
        $data['no_lp']=$accident[0]->no_lp;
        $data['description']=$accident[0]->damage_lose_desc;
        $data['no_spdp']=$surat_spdp[0]->no_spdp;
        $data['no_sprindik']=$surat_spdp[0]->no_sprindik;
        $data['sprindik_date']=Carbon::parse($surat_spdp[0]->sprindik_date)->translatedFormat('d F Y');
        $data['polres_name']=$get_accident[0]->polres_name;
        $data['polda_name']=$get_accident[0]->polda_name;
        $data['polres_alamat']=$get_accident[0]->polres_alamat;
        $data['polres_kecamatan']=$get_accident[0]->polres_kecamatan;
        $data['polres_kodepos']=$get_accident[0]->polres_kodepos;

        $data['pejabat_name']=$pejabat->first_title .' '. $pejabat->first_name . ' ' . $pejabat->last_name .', '. $pejabat->last_title;
        $data['pejabat_rank']=$pejabat->rank_id;
        $data['pejabat_nrp']=$pejabat->register_number;

        $signatureTitleText = [
          'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . $get_accident[0]->polres_name,
          'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . $get_accident[0]->polres_name . '</w:t><w:p/><w:t>' . $pejabat->position_id,
          'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . $get_accident[0]->polda_name . '</w:t><w:p/><w:t>' . $pejabat->position_id,
        ];

      $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/spdp.docx');

      if($pejabat->position_id == 'KAPOLRES'){
        $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['KAPOLRES']);
      }else if($pejabat->position_id == 'KASUBDITGAKKUM' || $pejabat->position_id == 'PS. KASUBDITGAKKUM'){
          $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_DIRLANTAS']);
      }else{
          $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_KAPOLRES']);
      }

      $templateProcessor->setValue('accident_date',$data['accident_date']);
      $templateProcessor->setValue('no_lp',$data['no_lp']);
      $templateProcessor->setValue('accident_time',$data['accident_time']);
      $templateProcessor->setValue('road_name',$data['road_name']);
      if($data['surat_spdp'] == null){
        $templateProcessor->setValue('klasifikasi','-');
      }else{
        $templateProcessor->setValue('klasifikasi',$data['klasifikasi']);
      }
      $templateProcessor->cloneBlock('blockName', $i, true, false, $get_data);
      $templateProcessor->setValue('no_spdp',$data['no_spdp']);
      $templateProcessor->setValue('lampiran',$data['lampiran']);
      $templateProcessor->setValue('nama_kejaksaan',$data['nama_kejaksaan']);
      $templateProcessor->setValue('regency',$data['regency']);
      $templateProcessor->setValue('pengadilan',$data['pengadilan']);
      $templateProcessor->setValue('description',$data['description']);
      $templateProcessor->setValue('spdp_date',$data['spdp_date']);
      $templateProcessor->setValue('no_sprindik',$data['no_sprindik']);
      $templateProcessor->setValue('sprindik_date',$data['sprindik_date']);
      $templateProcessor->setValue('polres_name',$data['polres_name']);
      $templateProcessor->setValue('polda_name',$data['polda_name']);
      $templateProcessor->setValue('polres_alamat',$data['polres_alamat']. ', '. $data['polres_kecamatan']. ', '. $data['polres_kodepos'] );
      $templateProcessor->setValue('polres_kecamatan',$data['polres_kecamatan']);
      $templateProcessor->setValue('polres_kodepos',$data['polres_kodepos']);
      $templateProcessor->setValue('pejabat_name',$data['pejabat_name']);
      $templateProcessor->setValue('pejabat_rank',$data['pejabat_rank']);
      $templateProcessor->setValue('pejabat_nrp',$data['pejabat_nrp']);
      $templateProcessor->setValue('accident_day',$data['accident_day']);

      $filename = 'SPDP';
      $templateProcessor->saveAs($filename.'.docx');
      return response()->download($filename.'.docx')->deleteFileAfterSend(true);
    }

    public function createword_surat_p21_tahap_1($id)
    {
      $surat_p21_tahap_1 = DB::table('surat_p21_tahap_1')->where('accident_id',$id)->first();
      $accident = DB::table('accidents')->where('id',$id)->first();

      $data['surat_p21_tahap_1']=$surat_p21_tahap_1;
      $data['accident_date'] = Carbon::parse($accident->accident_date)->format('d F Y');
      $data['accident_time'] = Carbon::parse($accident->accident_time)->format('H:m');
      $data['accident']=$accident;
      $data['road_name']=$accident->road_name;

      $data['province_name'] = $surat_p21_tahap_1->province_name;
      $data['polres_name'] = $surat_p21_tahap_1->polres_name;
      $data['polres_address'] = $surat_p21_tahap_1->polres_address;
      $data['no_p21'] = $surat_p21_tahap_1->no_p21;
      $data['p21_date'] = $surat_p21_tahap_1->p21_date;
      $data['p21_location'] = $surat_p21_tahap_1->p21_location;
      $data['classification'] = $surat_p21_tahap_1->classification;
      $data['attachment'] = $surat_p21_tahap_1->attachment;
      $data['subject'] = $surat_p21_tahap_1->subject;
      $data['letter_recipient'] = $surat_p21_tahap_1->letter_recipient;
      $data['recipient_location'] = $surat_p21_tahap_1->recipient_location;
      $data['no_spdp'] = $surat_p21_tahap_1->no_spdp;
      $data['spdp_date'] = $surat_p21_tahap_1->spdp_date;
      $data['no_lp'] = $accident->no_lp;
      $data['suspects'] = $surat_p21_tahap_1->suspects;
      $data['description'] = $surat_p21_tahap_1->description;
      $data['cc'] = $surat_p21_tahap_1->cc;
      $data['offense_articles'] = $surat_p21_tahap_1->offense_articles;
      $data['penyidik_name'] = $surat_p21_tahap_1->penyidik_name;
      $data['penyidik_position'] = $surat_p21_tahap_1->penyidik_position;
      $data['penyidik_nrp'] = $surat_p21_tahap_1->penyidik_nrp;


      $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/p21-tahap-1.docx');
      $templateProcessor->setValue('accident_date', Carbon::parse($accident->accident_date)->format('d F Y'));
      $templateProcessor->setValue('no_lp',$data['no_lp']);
      $templateProcessor->setValue('accident_time',$data['accident_time']);
      $templateProcessor->setValue('road_name',$data['road_name']);
      if($data['surat_p21_tahap_1'] == null){
        $templateProcessor->cloneBlock('block_name_suspects', 0, true, false);
        $templateProcessor->cloneBlock('block_name_cc', 0, true, false);
        $templateProcessor->setValue('province_name','-');
        $templateProcessor->setValue('polres_name','-');
        $templateProcessor->setValue('polres_address','-');
        $templateProcessor->setValue('no_p21','-');
        $templateProcessor->setValue('p21_date','-');
        $templateProcessor->setValue('p21_location','-');
        $templateProcessor->setValue('classification','-');
        $templateProcessor->setValue('attachment','-');
        $templateProcessor->setValue('subject','-');
        $templateProcessor->setValue('letter_recipient','-');
        $templateProcessor->setValue('recipient_location','-');
        $templateProcessor->setValue('no_spdp','-');
        $templateProcessor->setValue('spdp_date','-');
        $templateProcessor->setValue('suspects','-');
        $templateProcessor->setValue('description','-');
        $templateProcessor->setValue('cc','-');
        $templateProcessor->setValue('offense_articles','-');
        $templateProcessor->setValue('penyidik_name','-');
        $templateProcessor->setValue('penyidik_position','-');
        $templateProcessor->setValue('penyidik_nrp','-');
      }else{
        // Loop $data['suspects']
        $get_data_suspects = [];
        $loop = 0;
        foreach (json_decode($data['suspects'], true) as $suspectId) {
          $suspectRow = DB::table('daftar_tersangka')->where('id', $suspectId)->first();
          $suspectRow = (array) $suspectRow;
          $get_data_suspects[] = [
            'suspect_name' => ucwords(strtolower($suspectRow['name'])),
            'suspect_gender' => ucwords(strtolower(DB::table('ref')->where('id', $suspectRow['gender'])->first()->name ?? '-')),
            'suspect_birthplace' => ucwords(strtolower($suspectRow['city'])),
            'suspect_birthday' => Carbon::parse($suspectRow['birth_date'])->format('d-m-Y'),
            'suspect_age' => Carbon::parse($suspectRow['birth_date'])->age,
            'suspect_citizen' => ucwords(strtolower($suspectRow['citizen'])),
            'suspect_religion' => ucwords(strtolower(DB::table('ref')->where('id', $suspectRow['religion'])->first()->name ?? '-')),
            'suspect_job' => ucwords(strtolower($suspectRow['job'])),
            'suspect_address' => ucwords(strtolower($suspectRow['address'])),
          ];
          $loop++;
        }

        // Loop $data['cc']
        $get_data_cc = [];
        $iteration = 0;
        foreach (json_decode($data['cc'], true) as $ccRow) {
          $get_data_cc[] = [
            'cc_name' => ucwords(strtolower($ccRow)),
          ];
          $iteration++;
        }

        $templateProcessor->cloneBlock('block_name_suspects', $loop, true, false, $get_data_suspects);
        $templateProcessor->cloneBlock('block_name_cc', $iteration, true, false, $get_data_cc);
        $templateProcessor->setValue('province_name',$data['province_name']);
        $templateProcessor->setValue('polres_name',$data['polres_name']);
        $templateProcessor->setValue('polres_address',$data['polres_address']);
        $templateProcessor->setValue('no_p21',$data['no_p21']);
        $templateProcessor->setValue('p21_date', Carbon::parse($data['p21_date'])->format('d F Y'));
        $templateProcessor->setValue('p21_location',$data['p21_location']);
        $templateProcessor->setValue('classification',$data['classification']);
        $templateProcessor->setValue('attachment',$data['attachment']);
        $templateProcessor->setValue('subject',$data['subject']);
        $templateProcessor->setValue('letter_recipient',$data['letter_recipient']);
        $templateProcessor->setValue('recipient_location',$data['recipient_location']);
        $templateProcessor->setValue('no_spdp',$data['no_spdp']);
        $templateProcessor->setValue('spdp_date', Carbon::parse($data['spdp_date'])->format('d F Y'));
        $templateProcessor->setValue('description',$data['description']);
        $templateProcessor->setValue('offense_articles',$data['offense_articles']);
        $templateProcessor->setValue('penyidik_name',$data['penyidik_name']);
        $templateProcessor->setValue('penyidik_position',$data['penyidik_position']);
        $templateProcessor->setValue('penyidik_nrp',$data['penyidik_nrp']);
      }

      $filename = 'Surat P21 Tahap 1';
      $templateProcessor->saveAs($filename.'.docx');
      return response()->download($filename.'.docx')->deleteFileAfterSend(true);
    }

    public function createword_surat_p21_tahap_2($id)
    {
      $surat_p21_tahap_2 = DB::table('surat_p21_tahap_2')->where('accident_id', $id)->first();
      $accident = DB::table('accidents')->where('id', $id)->first();

      $data['surat_p21_tahap_2']=$surat_p21_tahap_2;
      $data['accident_date'] = Carbon::parse($accident->accident_date)->format('d F Y');
      $data['accident_time'] = Carbon::parse($accident->accident_time)->format('H:m');
      $data['accident'] = $accident;
      $data['road_name'] = $accident->road_name;

      $data['province_name'] = $surat_p21_tahap_2->province_name;
      $data['polres_name'] = $surat_p21_tahap_2->polres_name;
      $data['polres_address'] = $surat_p21_tahap_2->polres_address;
      $data['no_p21'] = $surat_p21_tahap_2->no_p21;
      $data['p21_date'] = Carbon::parse($surat_p21_tahap_2->p21_date)->format('d F Y');
      $data['p21_start_date'] = Carbon::parse($surat_p21_tahap_2->p21_start_date)->format('d F Y');
      $data['p21_location'] = $surat_p21_tahap_2->p21_location;
      $data['classification']=$surat_p21_tahap_2->classification;
      $data['attachment']=$surat_p21_tahap_2->attachment;
      $data['subject'] = $surat_p21_tahap_2->subject;
      $data['letter_recipient'] = $surat_p21_tahap_2->letter_recipient;
      $data['recipient_location'] = $surat_p21_tahap_2->recipient_location;
      $data['no_lp']=$surat_p21_tahap_2->no_lp;
      $data['suspects']=$surat_p21_tahap_2->suspects;
      $data['description']=$surat_p21_tahap_2->description;
      $data['evidences']=$surat_p21_tahap_2->evidences;
      $data['cc']=$surat_p21_tahap_2->cc;
      $data['offense_articles']=$surat_p21_tahap_2->offense_articles;
      $data['penyidik_name']=$surat_p21_tahap_2->penyidik_name;
      $data['penyidik_position']=$surat_p21_tahap_2->penyidik_position;
      $data['penyidik_nrp']=$surat_p21_tahap_2->penyidik_nrp;
      $data['current_month_year'] = Carbon::now()->format('F Y');

      $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/p21-tahap-2.docx');
      $templateProcessor->setValue('accident_date', Carbon::parse($data['accident_date'])->format('d F Y'));
      $templateProcessor->setValue('no_lp',$data['no_lp']);
      $templateProcessor->setValue('accident_time',$data['accident_time']);
      $templateProcessor->setValue('road_name',$data['road_name']);

      if($data['surat_p21_tahap_2'] == null){
        $templateProcessor->cloneBlock('block_name_suspects', 0, true, false);
        $templateProcessor->cloneBlock('block_name_evidences', 0, true, false);
        $templateProcessor->cloneBlock('block_name_cc', 0, true, false);
        $templateProcessor->setValue('province_name','-');
        $templateProcessor->setValue('polres_name','-');
        $templateProcessor->setValue('polres_address','-');
        $templateProcessor->setValue('no_p21','-');
        $templateProcessor->setValue('p21_date','-');
        $templateProcessor->setValue('p21_start_date','-');
        $templateProcessor->setValue('p21_location','-');
        $templateProcessor->setValue('classification','-');
        $templateProcessor->setValue('attachment','-');
        $templateProcessor->setValue('subject','-');
        $templateProcessor->setValue('letter_recipient','-');
        $templateProcessor->setValue('recipient_location','-');
        $templateProcessor->setValue('description','-');
        $templateProcessor->setValue('offense_articles','-');
        $templateProcessor->setValue('penyidik_name','-');
        $templateProcessor->setValue('penyidik_position','-');
        $templateProcessor->setValue('penyidik_nrp','-');
        $templateProcessor->setValue('current_month_year', Carbon::now()->format('F Y'));
      }else{
        // Loop $data['suspects']
        $get_data_suspects = [];
        $loopSuspects = 0;
        foreach (json_decode($data['suspects'], true) as $suspectId) {
          $suspectRow = DB::table('daftar_tersangka')->where('id', $suspectId)->first();
          $suspectRow = (array) $suspectRow;
          $get_data_suspects[] = [
            'suspect_name' => ucwords(strtolower($suspectRow['name'])),
            'suspect_gender' => ucwords(strtolower(DB::table('ref')->where('id', $suspectRow['gender'])->first()->name ?? '-')),
            'suspect_birthplace' => ucwords(strtolower($suspectRow['city'])),
            'suspect_birthday' => Carbon::parse($suspectRow['birth_date'])->format('d-m-Y'),
            'suspect_age' => Carbon::parse($suspectRow['birth_date'])->age,
            'suspect_citizen' => ucwords(strtolower($suspectRow['citizen'])),
            'suspect_religion' => ucwords(strtolower(DB::table('ref')->where('id', $suspectRow['religion'])->first()->name ?? '-')),
            'suspect_job' => ucwords(strtolower($suspectRow['job'])),
            'suspect_address' => ucwords(strtolower($suspectRow['address'])),
          ];
          $loopSuspects++;
        }

        // Loop $data['cc]
        $get_data_cc = [];
        $loopCc = 0;
        foreach (json_decode($data['cc'], true) as $ccRow) {
          $get_data_cc[] = [
            'cc_name' => ucwords(strtolower($ccRow)),
          ];
          $loopCc++;
        }

        // Loop $data['evidences']
        $get_data_evidences = [];
        $loopEvidences = 0;
        foreach (json_decode($data['evidences'], true) as $evidenceRow) {
          $evidenceRow = DB::table('daftar_barang_bukti')->where('id', $evidenceRow)->first();
          $evidenceRow = (array) $evidenceRow;
          $get_data_evidences[] = [
            'evidence_name' => ucwords(strtolower($evidenceRow['nama_barang'])),
            'evidence_amount' => $evidenceRow['jumlah_barang'],
          ];
          $loopEvidences++;
        }

        $templateProcessor->cloneBlock('block_name_suspects', $loopSuspects, true, false, $get_data_suspects);
        $templateProcessor->cloneBlock('block_name_cc', $loopCc, true, false, $get_data_cc);
        $templateProcessor->cloneBlock('block_name_evidences', $loopEvidences, true, false, $get_data_evidences);
        $templateProcessor->setValue('province_name',$data['province_name']);
        $templateProcessor->setValue('polres_name',$data['polres_name']);
        $templateProcessor->setValue('polres_address',$data['polres_address']);
        $templateProcessor->setValue('no_p21',$data['no_p21']);
        $templateProcessor->setValue('p21_date', Carbon::parse($data['p21_date'])->format('d F Y'));
        $templateProcessor->setValue('p21_start_date', Carbon::parse($data['p21_start_date'])->format('d F Y'));
        $templateProcessor->setValue('p21_location',$data['p21_location']);
        $templateProcessor->setValue('classification',$data['classification']);
        $templateProcessor->setValue('attachment',$data['attachment']);
        $templateProcessor->setValue('subject',$data['subject']);
        $templateProcessor->setValue('letter_recipient',$data['letter_recipient']);
        $templateProcessor->setValue('recipient_location',$data['recipient_location']);
        $templateProcessor->setValue('description',$data['description']);
        $templateProcessor->setValue('offense_articles',$data['offense_articles']);
        $templateProcessor->setValue('penyidik_name',$data['penyidik_name']);
        $templateProcessor->setValue('penyidik_position',$data['penyidik_position']);
        $templateProcessor->setValue('penyidik_nrp',$data['penyidik_nrp']);
        $templateProcessor->setValue('current_month_year',$data['current_month_year']);
      }

      $filename = 'Surat P21 Tahap 2';
      $templateProcessor->saveAs($filename.'.docx');
      return response()->download($filename.'.docx')->deleteFileAfterSend(true);
    }

    public function createword_sp3($id){
        $sp3 = DB::select('select * from sp3 where accident_id = \''.$id.'\'');
        $accident =DB::select('select * from accidents where id= \''.$id.'\' ');
        $surat_perintah_tugas = DB::select('select * from surat_tugas left join officers on surat_tugas.officer_id = officers.id where accident_id = \''.$id.'\' ');
        $get_accident = DB::select('select polda.name as polda_name, polres.name as polres_name, polda.address as polda_alamat, polres.address as polres_alamat, polres.polres_province as polres_province from accidents
        left join polres on accidents.polres_id = polres.id
        left join polda on polda.id = polres.polda_id
        where accidents.id = \''.$id.'\' ');
        // dd($get_accident);
        $get_data = [];
        $i=1;
        foreach ($surat_perintah_tugas as $i => $surat_tugas) {
            $i++;
            $get_data[] = [
            'nomor' => $i,
            'first_name'   => $surat_tugas->first_name,
            'last_name'  => $surat_tugas->last_name,
            'rank_id' => $surat_tugas->rank_short_name,
            'officer_id' => $surat_tugas->officer_id,
            'position' => $surat_tugas->position,
            ];
        }

        $data['sp3']=$sp3;
        $data['accident']=$accident;
        $data['polres_name']=$get_accident[0]->polres_name;
        $data['polres_province']=$get_accident[0]->polres_province;
        $data['no_lp']=$sp3[0]->no_lp;
        $data['accident_date']=Carbon::parse($accident[0]->accident_date)->format('d F Y');
        $data['no_sp3']=$sp3[0]->no_sp3;
        $data['no_surat_perintah_penyidikan']=$sp3[0]->no_surat_perintah_penyidikan;
        $data['tanggal_sp_dik']=Carbon::parse($sp3[0]->tanggal_sp_dik)->format('d F Y');

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/sp3.docx');

        $templateProcessor->cloneBlock('block_name', $i, true, false, $get_data);
        $templateProcessor->setValue('polres_name',$data['polres_name']);
        if($data['polres_province'] == null){
            $templateProcessor->setValue('polres_province','-');
        }else{
            $templateProcessor->setValue('polres_province',$data['polres_province']);
        }
        $templateProcessor->setValue('no_lp',$data['no_lp']);
        $templateProcessor->setValue('accident_date',$data['accident_date']);
        $templateProcessor->setValue('no_sp3',$data['no_sp3']);
        $templateProcessor->setValue('no_surat_perintah_penyidikan',$data['no_surat_perintah_penyidikan']);
        $templateProcessor->setValue('tanggal_sp_dik',$data['tanggal_sp_dik']);

        $filename = 'Surat Perintah Penghentian Penyidikan';
        $templateProcessor->saveAs($filename.'.docx');
        return response()->download($filename.'.docx')->deleteFileAfterSend(true);
    }

    public function daftarSaksi($id){
        $daftarSaksi = DB::select('select daftar_saksi_gender.name as gender, daftar_saksi_religion.name as religion, daftar_saksi.name as name_saksi, daftar_saksi.job as job_saksi, daftar_saksi.city as city, address as address
                                from daftar_saksi
                                left join (select * from ref where grp_id =\'G01\') as daftar_saksi_gender on daftar_saksi.gender = daftar_saksi_gender.id
                                left join (select * from ref where grp_id =\'R01\') as daftar_saksi_religion on daftar_saksi.religion = daftar_saksi_religion.id
                                where accident_id = \''.$id. '\' ');
      $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/daftar_saksi.docx');
      $data= [];
        foreach($daftarSaksi as $saksi){
            $data[]= [
                'name_saksi'=> $saksi->name_saksi,
                'gender'=> $saksi->gender,
                'job_saksi'=> $saksi->job_saksi,
                'city'=> $saksi->city,
                'religion'=> $saksi->religion,
                'address'=> $saksi->address,
            ];
        }
    $templateProcessor->cloneRowAndSetValues('name_saksi', $data);

    $filename = 'Daftar Saksi';
    $templateProcessor->saveAs($filename.'.docx');
    return response()->download($filename.'.docx')->deleteFileAfterSend(true);
    }
    public function daftarTersangka($id){
        $daftarTersangka = DB::select('select daftar_tersangka_gender.name as gender, daftar_tersangka_religion.name as religion, daftar_tersangka.name as name_tersangka, daftar_tersangka.job as job_tersangka, daftar_tersangka.city as city, address as address
                                  from daftar_tersangka
                                  left join (select * from ref where grp_id =\'G01\') as daftar_tersangka_gender on daftar_tersangka.gender = daftar_tersangka_gender.id
                                  left join (select * from ref where grp_id =\'R01\') as daftar_tersangka_religion on daftar_tersangka.religion = daftar_tersangka_religion.id
                                  where accident_id = \''.$id. '\' ');
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/daftar_tersangka.docx');
        $data= [];
        foreach($daftarTersangka as $tersangka){
          $data[]= [
            'name_tersangka'=> $tersangka->name_tersangka,
            'gender'=> $tersangka->gender,
            'job_tersangka'=> $tersangka->job_tersangka,
            'city'=> $tersangka->city,
            'religion'=> $tersangka->religion,
            'address'=> $tersangka->address,
          ];

        }
        $templateProcessor->cloneRowAndSetValues('name_tersangka', $data);

        $filename = 'Daftar Tersangka';
        $templateProcessor->saveAs($filename.'.docx');
        return response()->download($filename.'.docx')->deleteFileAfterSend(true);
      }
}
