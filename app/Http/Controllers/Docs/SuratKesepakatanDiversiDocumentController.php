<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\Services\Doc\DocService;
use App\Traits\DocsOfficersTraits;

use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;

use App\Models\Lib\Gender;
use App\Models\Lib\Religion;
use App\Models\Lib\Job;
use App\Models\Lib\Nationality;
use App\Models\Lib\IdentityType;

class SuratKesepakatanDiversiDocumentController extends Controller
{
    protected $docService;

    use DocsOfficersTraits;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function index(Request $request)
    {
        $accidentId = htmlspecialchars($request->query('accident_id'));
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::where('id', $accidentId)->first();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        // Penyidik Pembantu (MEMBER) - untuk opsi Fasilitator Diversi
        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->member()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        // Tersangka pada perkara ini
        $suspects = Suspect::withRelated()
            ->where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->get();

        // Master data
        $identityTypes = IdentityType::active()->get();
        $genders = Gender::where('is_active', true)->get();
        $religions = Religion::where('is_active', true)->get();
        $jobs = Job::where('is_active', true)->orderBy('name')->get();
        $nationalities = Nationality::active()->orderBy('sort')->get();

        $viewData = [
            'accidentId'            => $accidentId,
            'accident'              => $accident,
            'authorizedSignatories' => $authorizedSignatories,
            'officers'              => $officers,
            'suspects'              => $suspects,
            'identityTypes'         => $identityTypes,
            'genders'               => $genders,
            'religions'             => $religions,
            'jobs'                  => $jobs,
            'nationalities'         => $nationalities,
        ];

        return view('docs.surat-kesepakatan-diversi-document.create', $viewData);
    }

    public function store(Request $request)
    {
        $accidentId = $request->input('accidentId');
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Formulir Surat Kesepakatan Diversi berhasil disimpan.');
    }
}
