<?php

namespace App\Http\Controllers\Letters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\Letters\InvestigationWarrant\InvestigationWarrant;
use App\Models\Letters\InvestigationWarrant\InvestigationWarrantLaw;
use App\Models\Peoples\AuthorizedSignatory;
use App\Models\Officer;
use App\Models\Accident;
use App\Models\Meta\Legals\Law;
use App\Models\Polres;

class InvestigationWarrantController extends Controller
{
    public function index()
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        
        $investigationWarrants = InvestigationWarrant::with('officers', 'authorizedSignatories')
            ->where('accident_id', $accidentId)
            ->first();
    
        return $investigationWarrants;
    }

    public function create()
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $laws = Law::orderBy('sort')->get();

        $accident = Accident::find($accidentId);
        $authorizedSignatories = AuthorizedSignatory::select('*', DB::raw("CONCAT(first_title, ' ', first_name, ' ', last_name, ', ', last_title) AS full_name"))
                ->where('polres_id', $accident->polres_id)
                ->where('valid', true)
                ->orderBy('first_name')
                ->get();

        $personnelLeaders = Officer::select('*', DB::raw("CONCAT(first_name, ' ', last_name) AS full_name"))
                ->where('polres_id', $accident->polres_id)
                ->where('sebagai_kepala' ,'!=', '-')
                ->where('state' ,'=', '1')
                ->whereIn('sebagai_kepala', ["KANIT LAKA", "BANIT LAKA", "PENYIDIK"])
                ->orderBy('first_name')
                ->get();

        $officers = Officer::select('*', DB::raw("CONCAT(first_name, ' ', last_name) AS full_name"))
                ->where('polres_id', $accident->polres_id)
                ->where('sebagai_kepala' , '=', '-')
                ->where('state' ,'=', '1')
                ->orderBy('first_name')
                ->get();
     
        $viewData =[
            'authorizedSignatories' => $authorizedSignatories,
            'personnelLeaders' => $personnelLeaders,
            'officers' => $officers,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'laws' => $laws,
        ];

        return view('letters.investigation-warrant.create', $viewData);
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'letter_number' => 'required',
            'start_date' => 'required',
            'end_date' => 'required_if:is_finished,false|nullable|date',
            'is_finished' => 'nullable|boolean',
            'authorized_signatory' => 'required',
            'personnel' => 'required',
            'personnel_leader' => 'required',
        ]);

        // Get URL Parameter
        $accidentId = htmlspecialchars($request->accident_id);

        // Define & Sanitize Text Input
        $user = Auth::user();
        $letterNumber = htmlspecialchars($request->letter_number);
        $issuedDate = Carbon::now()->format('Y-m-d');
        $startDate = Carbon::parse(htmlspecialchars($request->start_date))->format('Y-m-d');
        $endDate = ($request->is_finished != 1) ? Carbon::parse(htmlspecialchars($request->end_date))->format('Y-m-d') : null;
        $createdBy = $user->first_name . ' ' . $user->last_name;
        $authorizedSignatory = htmlspecialchars($request->authorized_signatory);
        $personnelLeader = htmlspecialchars($request->personnel_leader);

        DB::beginTransaction();
        try{
            // Store to database
            $investigationWarrant = InvestigationWarrant::create([
                'accident_id' => $accidentId,
                'letter_number' => $letterNumber,
                'issued_date' => $issuedDate,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'created_by' => $createdBy,
            ]);

            // Sync authorized signatories
            $investigationWarrant->authorizedSignatories()->sync($authorizedSignatory);

            // Sync personnel leader
            $investigationWarrant->leaderOfficers()->attach($personnelLeader);

            // Sync laws
            foreach ($request->laws as $law) {
                $investigationWarrant->laws()->attach($law);
            }

            // Sync officers
            foreach ($request->personnel as $personnel) {
                $investigationWarrant->officers()->attach($personnel);
            }

            $accident=Accident::find($accidentId);
            $accident->officer_surat_penyelidikan()->attach($personnelLeader);
            foreach ($request->personnel as $personnel) {
                $accident->officer_surat_penyelidikan()->attach($personnel);
            }

            $accident->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D010102',
                    'tipe_update' => 'MEMBUAT',
                ]);

            DB::Commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menyimpan data.');
        }

        // Redirect
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function show()
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $investigationWarrantId = htmlspecialchars(request()->query('id'));

        // Get data from database
        $investigationWarrant = InvestigationWarrant::with(['authorizedSignatories', 'officers', 'officers.rank', 'leaderOfficers', 'leaderOfficers.rank', 'laws'])
            ->where('id', $investigationWarrantId)
            ->first();

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        $viewData =[
            'accidentId' => $accidentId,
            'accident' => $accident,
            'investigationWarrantId' => $investigationWarrantId,
            'investigationWarrant' => $investigationWarrant,
        ];

        return view('letters.investigation-warrant.print', $viewData);
    }

    public function edit()
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $investigationWarrantId = htmlspecialchars(request()->query('id'));
        
        $laws = Law::orderBy('sort')->get();

        // Get data from database
        $accident = Accident::find($accidentId);
        $authorizedSignatories = AuthorizedSignatory::select('*', DB::raw("CONCAT(first_title, ' ', first_name, ' ', last_name, ', ', last_title) AS full_name"))
            ->where('polres_id', $accident->polres_id)
            ->where('valid', true)
            ->orderBy('first_name')
            ->get();

        $personnelLeaders = Officer::select('*', DB::raw("CONCAT(first_name, ' ', last_name) AS full_name"))
            ->where('polres_id', $accident->polres_id)
            ->where('sebagai_kepala' ,'!=', '-')
            ->where('state' ,'=', '1')
            ->whereIn('sebagai_kepala', ["KANIT LAKA", "BANIT LAKA", "PENYIDIK"])
            ->orderBy('first_name')
            ->get();

        $officers = Officer::select('*', DB::raw("CONCAT(first_name, ' ', last_name) AS full_name"))
            ->where('polres_id', $accident->polres_id)
            ->where('sebagai_kepala' , '=', '-')
            ->where('state' ,'=', '1')
            ->orderBy('first_name')
            ->get();

        $investigationWarrant = InvestigationWarrant::with(['authorizedSignatories', 'officers', 'leaderOfficers', 'laws'])
            ->where('id', $investigationWarrantId)
            ->first();
        
        $viewData =[
            'authorizedSignatories' => $authorizedSignatories,
            'personnelLeaders' => $personnelLeaders,
            'officers' => $officers,
            'investigationWarrantId' => $investigationWarrantId,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'investigationWarrant' => $investigationWarrant,
            'investigationWarrantAuthorizedSignatory' => $investigationWarrant->authorizedSignatories->first(),
            'laws' => $laws,
        ]; 

        return view('letters.investigation-warrant.edit', $viewData);
    }

    public function update(Request $request)
    {
         // Validation
         $request->validate([
            'letter_number' => 'required',
            'start_date' => 'required',
            'end_date' => 'required_if:is_finished,false|nullable|date',
            'is_finished' => 'nullable|boolean',
            'authorized_signatory' => 'required',
            'personnel_leader' => 'required',
            'personnel' => 'required',
        ]);

        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $investigationWarrantId = htmlspecialchars(request()->query('id'));

        // Define & Sanitize Text Input
        $user = Auth::user();
        $letterNumber = htmlspecialchars($request->letter_number);
        $issuedDate = Carbon::now()->format('Y-m-d');
        $startDate = Carbon::parse(htmlspecialchars($request->start_date))->format('Y-m-d');
        $endDate = ($request->is_finished != 1) ? Carbon::parse(htmlspecialchars($request->end_date))->format('Y-m-d') : null;
        $updatedBy = $user->first_name . ' ' . $user->last_name;
        $authorizedSignatory = htmlspecialchars($request->authorized_signatory);
        $personnelLeader = htmlspecialchars($request->personnel_leader);
        
        DB::beginTransaction();
        try{
            $investigationWarrant = InvestigationWarrant::where('id', $investigationWarrantId)->first();
            // Update to database
            $investigationWarrant->update([
                'letter_number' => $letterNumber,
                'issued_date' => $issuedDate,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'updated_by' => $updatedBy,
            ]);

            // Sync signatory officer
            $investigationWarrant->authorizedSignatories()->sync($authorizedSignatory);

            // Sync personnel leader
            $investigationWarrant->leaderOfficers()->sync($personnelLeader);

            // detach all officers
            $investigationWarrant->officers()->detach();
            // Sync officers
            foreach ($request->personnel as $personnel) {
                $investigationWarrant->officers()->attach($personnel);
            }

            // detach all laws
            $investigationWarrant->laws()->detach();
            // Sync laws
            foreach ($request->laws as $law) {
                $investigationWarrant->laws()->attach($law);
            }

            $accident=Accident::find($accidentId);
            $accident->officer_surat_penyelidikan()->detach();
            $accident->officer_surat_penyelidikan()->attach($personnelLeader);
            foreach ($request->personnel as $personnel) {
                $accident->officer_surat_penyelidikan()->attach($personnel);
            }

            $accident->update([
                'last_update' => Carbon::now(),
                'category' => 'D010102',
                'tipe_update' => 'MENGUBAH',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data');
        }

        // Redirect with param accident_id
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function delete()
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $investigationWarrantId = htmlspecialchars(request()->query('id'));

        DB::beginTransaction();
        try{
            // Delete from database
            $investigationWarrant = InvestigationWarrant::where('id', $investigationWarrantId)->first();
            $investigationWarrant->delete();

            $accident = Accident::find($accidentId);
            $accident->update([
                'last_update' => Carbon::now(),
                'category' => 'D010102',
                'tipe_update' => 'MENGHAPUS',
            ]);

            DB::Commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menghapus data.');
        }

        // Redirect with param accident_id
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function print()
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $investigationWarrantId = htmlspecialchars(request()->query('id'));

        // Get data from database
        $investigationWarrant = InvestigationWarrant::with(['authorizedSignatories', 'officers', 'officers.rank', 'leaderOfficers', 'leaderOfficers.rank', 'laws'])
            ->where('id', $investigationWarrantId)
            ->first();

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        $no = 1;
        $blockOfficers = [];
        foreach($investigationWarrant->leaderOfficers as $leaderOfficer) {
            $blockOfficers[] = [
                'number' => $no,
                'first_name'   => $leaderOfficer->first_name,
                'last_name'  => $leaderOfficer->last_name,
                'rank_id' => $leaderOfficer->rank_short_name,
                'officer_id' => $leaderOfficer->id,
                'position' => $leaderOfficer->position_short_name,
            ];
            $no++;
        }
        foreach ($investigationWarrant->officers as $officer) {
            $blockOfficers[] = [
                'number' => $no,
                'first_name'   => $officer->first_name,
                'last_name'  => $officer->last_name,
                'rank_id' => $officer->rank_short_name,
                'officer_id' => $officer->id,
                'position' => $officer->position_short_name,
            ];
            $no++;
        }

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/surat_perintah_penyelidikan.docx');

        $signatureTitleText = [
            'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name,
            'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name . '</w:t><w:p/><w:t>' . $investigationWarrant->authorizedSignatories->first()->position_id,
            'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . $accident->polres->polda->full_name . '</w:t><w:p/><w:t>' . $investigationWarrant->authorizedSignatories->first()->position_id,
        ];
        
        if($investigationWarrant->authorizedSignatories->first()->position_id == 'KAPOLRES'){
            $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['KAPOLRES']);
        }else if($investigationWarrant->authorizedSignatories->first()->position_id == 'KASUBDITGAKKUM' || $investigationWarrant->authorizedSignatories->first()->position_id == 'PS. KASUBDITGAKKUM'){
            $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_DIRLANTAS']);
        }else{
            $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_KAPOLRES']);
        }

        $templateProcessor->cloneBlock('block_officers', 2, true, false, $blockOfficers);
        $templateProcessor->setValue('letter_number', $investigationWarrant->letter_number);
        $templateProcessor->setValue('issued_date', Carbon::parse($investigationWarrant->issued_date)->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('letter_end_date', ($investigationWarrant->end_date != NULL) ? Carbon::parse($investigationWarrant->end_date)->locale('id')->translatedFormat('d F Y') : 'Selesai');
        $templateProcessor->setValue('accident_day', Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l'));
        $templateProcessor->setValue('accident_date', Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('accident_time', Carbon::parse($accident->accident_time)->locale('id')->translatedFormat('H:i'));
        $templateProcessor->setValue('polda_full_name', $accident->polres->polda->full_name);
        $templateProcessor->setValue('polda_name', $accident->polres->polda->name);
        $templateProcessor->setValue('polres_name', $accident->polres->name);
        $templateProcessor->setValue('polres_alamat',ucwords(strtolower($accident->polres->address . ', ' . $accident->polres->polres_district . ', ' . $accident->polres->polres_zipcode)));
        $templateProcessor->setValue('road_name', $accident->road_name);
        $templateProcessor->setValue('no_lp', $accident->no_lp);
        $templateProcessor->setValue('officer_signature_sebagai_kepala', strtoupper($investigationWarrant->authorizedSignatories->first()->position_id));
        $templateProcessor->setValue('officer_signature_rank', strtoupper($investigationWarrant->authorizedSignatories->first()->rank_id));
        $templateProcessor->setValue('officer_signature_nrp', $investigationWarrant->authorizedSignatories->first()->register_number);
        $templateProcessor->setValue('officer_signature_name', $investigationWarrant->authorizedSignatories->first()->first_title . ' ' . $investigationWarrant->authorizedSignatories->first()->first_name . ' ' . $investigationWarrant->authorizedSignatories->first()->last_name . ', ' . $investigationWarrant->authorizedSignatories->first()->last_title);
        $templateProcessor->setValue('officer_assign_rank', strtoupper($investigationWarrant->leaderOfficers->first()->rank_short_name));
        $templateProcessor->setValue('officer_assign_nrp', $investigationWarrant->leaderOfficers->first()->id);
        $templateProcessor->setValue('officer_assign_name', $investigationWarrant->leaderOfficers->first()->first_name . ' ' . $investigationWarrant->leaderOfficers->first()->last_name);
        $templateProcessor->setValue('location_created', ucwords(strtolower($accident->polres->polres_district)));

        $filename = 'Surat Perintah Penyelidikan - Resor ' . $accident->polres->full_name;
        $templateProcessor->saveAs($filename.'.docx');
        return response()->download($filename.'.docx')->deleteFileAfterSend(true);
    }
}
