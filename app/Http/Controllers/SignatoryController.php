<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Peoples\AuthorizedSignatory;
use App\Models\Polda;
use App\Models\Polres;

class SignatoryController extends Controller
{
    public $ranks;
    public $positions;

    public function __construct(){
        $rankList = collect([    
            ['id' => 'BRIPTU', 'name' => 'Brigadir Polisi Satu'],
            ['id' => 'AIPTU', 'name' => 'Ajun Inspektur Polisi Satu'],
            ['id' => 'BRIGADIR', 'name' => 'Brigadir'],
            ['id' => 'AIPDA', 'name' => 'Ajun Inspektur Polisi Dua'],
            ['id' => 'BRIPDA', 'name' => 'Brigadir Polisi Dua'],
            ['id' => 'BRIPKA', 'name' => 'Brigadir Polisi Kepala'],
            ['id' => 'IPDA', 'name' => 'Inspektur Polisi Dua'],
            ['id' => 'AKP', 'name' => 'Ajun Komisaris Polisi'],
            ['id' => 'AKBP', 'name' => 'Ajun Komisaris Besar Polisi'],
            ['id' => 'IPTU', 'name' => 'Inspektur Polisi Satu'],
            ['id' => 'KOMBESPOL', 'name' => 'Komisaris Besar Polisi'],
            ['id' => 'KOMJENPOL', 'name' => 'Komisaris Jendral'],
            ['id' => 'KOMPOL', 'name' => 'Komisaris Polisi'],
            ['id' => 'BRIGPOL', 'name' => 'Brigadir Polisi'],
        ]);
       
        $positionList = collect([    
            ['id' => 'KASAT LANTAS', 'name' => 'Kasat Lantas'],
            ['id' => 'PS. KASAT LANTAS', 'name' => 'PS. Kasat Lantas'],
            ['id' => 'PLT. KASAT LANTAS', 'name' => 'PLT. Kasat Lantas'],
            ['id' => 'KAPOLRES', 'name' => 'Kapolres'],
            ['id' => 'WAKAPOLRES', 'name' => 'Wakapolres'],
            ['id' => 'KASUBDITGAKKUM', 'name' => 'Kasubditgakkum'],
            ['id' => 'PS. KASUBDITGAKKUM', 'name' => 'PS. Kasubditgakkum'],
        ]);

        $this->ranks = $rankList;
        $this->positions = $positionList;
    }

    public function index()
    {
        $signatories = AuthorizedSignatory::with('polres', 'polres.polda')
                        ->orderBy('polres_id', 'asc')                
                        ->get();

        $viewData = [
            'signatories' => $signatories,
        ];

        return view('signatories.index', $viewData);
    }

    public function create()
    {
        $regionalPolices = Polda::with('polres')
                ->orderBy('id', 'asc')
                ->get();

        $viewData = [
            'ranks' => $this->ranks->all(),
            'positions' => $this->positions->all(),
            'regionalPolices' => $regionalPolices,
        ];

        return view('signatories.create', $viewData);
    }

    public function store(Request $request)
    {
        $request->validate([
            'districtPoliceId' => 'required',

            'firstTitle' => 'max:255',
            'firstName' => 'max:255',
            'lastName' => 'max:255',
            'lastTitle' => 'max:255',

            'rank' => 'max:255',
            'registerNumber' => 'max:8',
            'position' => 'max:255',
            'identityNumber' => 'max:16',
            'email' => 'max:255',
            'phone' => 'max:16',
        ]);
    
        $districtPoliceId = htmlspecialchars($request->districtPoliceId);
        $firstTitle = htmlspecialchars($request->firstTitle);
        $firstName = htmlspecialchars($request->firstName);
        $lastName = htmlspecialchars($request->lastName);
        $lastTitle = htmlspecialchars($request->lastTitle);
        $rankId = htmlspecialchars($request->rankId);
        $rankName = ($this->ranks->where('id', $rankId)->first()) ? $this->ranks->where('id', $rankId)->first()['name'] : null;
        $registerNumber = htmlspecialchars($request->registerNumber);
        $positionId = htmlspecialchars($request->positionId);
        $positionName = ($this->positions->where('id', $positionId)->first()) ? $this->positions->where('id', $positionId)->first()['name'] : null;
        $identityNumber = htmlspecialchars($request->identityNumber);
        $email = htmlspecialchars($request->email);
        $phone = htmlspecialchars($request->phone);
        $isValid = ($request->isValid == 'on') ? true : false;
        $isOpenUserForm = ($request->isOpenUserForm == 'on') ? false : true; 

        DB::beginTransaction();
        try{
            $signatory = AuthorizedSignatory::create([
                'polres_id' => $districtPoliceId,
                'first_title' => $firstTitle,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'last_title' => $lastTitle,
                'rank_id' => $rankId,
                'rank' => $rankName,
                'register_number' => $registerNumber,
                'position_id' => $positionId,
                'position' => $positionName,
                'identity_number' => $identityNumber,
                'email' => $email,
                'phone' => $phone,
                'valid' => $isValid,
            ]);

            if($isOpenUserForm == true){
                $signatory->update([
                    'noted' => false,
                ]);
            }

            Polres::where('id', $districtPoliceId)
                ->update([
                    'is_complete' => $isOpenUserForm,
                ]);

            DB::Commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menyimpan data.');
        }

        return redirect()->route('signatories')
            ->with('success', 'Signatory created successfully.');
    }

    public function edit($id)
    {
        $signatory = AuthorizedSignatory::find($id);

        $regionalPolices = Polda::with('polres')
                ->orderBy('id', 'asc')
                ->get();

        $viewData = [
            'signatory' => $signatory,
            'ranks' => $this->ranks->all(),
            'positions' => $this->positions->all(),
            'regionalPolices' => $regionalPolices,
        ];

        return view('signatories.edit', $viewData);
    }

    public function update(Request $request, $id)
    {    
        $request->validate([
            'districtPoliceId' => 'required',

            'firstTitle' => 'max:255',
            'firstName' => 'max:255',
            'lastName' => 'max:255',
            'lastTitle' => 'max:255',
            
            'rank' => 'max:255',
            'registerNumber' => 'max:8',
            'position' => 'max:255',
            'identityNumber' => 'max:16',
            'email' => 'max:255',
            'phone' => 'max:16',
        ]);
        
        $districtPoliceId = htmlspecialchars($request->districtPoliceId);
        $firstTitle = htmlspecialchars($request->firstTitle);
        $firstName = htmlspecialchars($request->firstName);
        $lastName = htmlspecialchars($request->lastName);
        $lastTitle = htmlspecialchars($request->lastTitle);
        $rankId = htmlspecialchars($request->rankId);
        $rankName = ($this->ranks->where('id', $rankId)->first()) ? $this->ranks->where('id', $rankId)->first()['name'] : null;
        $registerNumber = htmlspecialchars($request->registerNumber);
        $positionId = htmlspecialchars($request->positionId);
        $positionName = ($this->positions->where('id', $positionId)->first()) ? $this->positions->where('id', $positionId)->first()['name'] : null;
        $identityNumber = htmlspecialchars($request->identityNumber);
        $email = htmlspecialchars($request->email);
        $phone = htmlspecialchars($request->phone);
        $isValid = ($request->isValid == 'on') ? true : false;
        $isOpenUserForm = ($request->isOpenUserForm == 'on') ? false : true; 
        
        DB::beginTransaction();
        try{
            $signatory = AuthorizedSignatory::find($id);
            $signatory->update([
                'polres_id' => $districtPoliceId,
                'first_title' => $firstTitle,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'last_title' => $lastTitle,
                'rank_id' => $rankId,
                'rank' => $rankName,
                'register_number' => $registerNumber,
                'position_id' => $positionId,
                'position' => $positionName,
                'identity_number' => $identityNumber,
                'email' => $email,
                'phone' => $phone,
                'valid' => $isValid,
            ]);

            if($isOpenUserForm == true){
                $signatory->update([
                    'noted' => false,
                ]);
            }

            Polres::where('id', $districtPoliceId)
                ->update([
                    'is_complete' => $isOpenUserForm,
                ]);

            DB::Commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menyimpan data.');
        }

        return redirect()->route('signatories')
            ->with('success', 'Signatory updated successfully.');
    }

    public function destroy($id){
        $signatory = AuthorizedSignatory::find($id);

        DB::beginTransaction();
        try{
            $signatory->update([
                'valid' => false,
            ]);

            $signatory->delete();

            DB::Commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menghapus data.');
        }

        return redirect()->route('signatories')
            ->with('success', 'Signatory deleted successfully');
    }
}
