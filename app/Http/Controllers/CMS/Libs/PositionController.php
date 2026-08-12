<?php

namespace App\Http\Controllers\CMS\Libs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Lib\Position;
use App\Models\Lib\Police;
use App\Models\Opt\PositionCluster;

class PositionController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $positions = Position::withRelated()
                ->orderBy('police_id')
                ->get();
   
            return DataTables::of($positions)->make();
        }

        $viewData = [
        ];

        return view('cms.libs.position.index', $viewData);
    }

    public function create()
    {
        $polices = Police::active()
            ->orderBy('id')
            ->get();

        $positionClusters = PositionCluster::active()
            ->orderBy('id')
            ->get();

        $positionMaxId = Position::select(DB::raw('MAX(CAST(id AS INTEGER)) as max_id'))
            ->first();

        $createPositionId = $positionMaxId->max_id + 1;

        $viewData = [
            'polices' => $polices,
            'positionClusters' => $positionClusters,
            'createPositionId' => $createPositionId,
        ];

        return view('cms.libs.position.create', $viewData);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => [
                'required',
                'numeric',
            ],
            'empId' => [
                'nullable',
                'numeric',
            ],
            'name' => [
                'required',
                'max:255',
            ],
            'code' => [
                'max:255',
            ],
            'employmentType' => [
                'required',
            ],
            'positionCluster' => [
                'required',
            ],
            'police' => [
                'required',
            ],
            'sort' => [
                'required',
                'numeric',
            ],
        ]);

        $id = htmlspecialchars($request->id);
        $empId = htmlspecialchars($request->empId);
        $name = htmlspecialchars($request->name);
        $code = htmlspecialchars($request->code);
        $employmentType = htmlspecialchars($request->employmentType);
        $positionCluster = htmlspecialchars($request->positionCluster);
        $police = htmlspecialchars($request->police);
        $sort = htmlspecialchars($request->sort);
        $isActive = htmlspecialchars($request->isActive);
        $isCanSignatory = htmlspecialchars($request->isCanSignatory);
        
        //unique check
        $positionId = Position::where('id', $id)->count();
        if ($positionId > 0) {
            return redirect()->back()->with('error', 'ID sudah ada.');
        }
        $positionEmpId = Position::where('emp_id', $empId)->count();
        if ($positionEmpId > 0 && !empty($empId)) {
            return redirect()->back()->with('error', 'EMP ID sudah ada.');
        }
        $positionCode = Position::where('code', $code)->count();
        if ($positionCode > 0 && !empty($code)) {
            return redirect()->back()->with('error', 'Kode sudah ada.');
        }

        DB::beginTransaction();
        try {
            Position::create([
                'id' => $id,
                'emp_id' => $empId,
                'name' => $name,
                'code' => $code,
                'employment_type_id' => $employmentType,
                'position_cluster_id' => $positionCluster,
                'police_id' => $police,
                'sort' => $sort,
                'is_active' => filter_var($isActive, FILTER_VALIDATE_BOOLEAN),
                'is_can_signatory' => filter_var($isCanSignatory, FILTER_VALIDATE_BOOLEAN),
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('cms.libs.position.index')->with('error', 'Data gagal ditambahkan.');
        }

        return redirect()->route('cms.libs.position.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $position = Position::withRelated()->find($id);
        $polices = Police::active()->get();
        $positionClusters = PositionCluster::active()->get();

        $viewData = [
            'position' => $position,
            'polices' => $polices,
            'positionClusters' => $positionClusters,
        ];

        return view('cms.libs.position.edit', $viewData);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id' => [
                'required',
                'numeric',
            ],
            'empId' => [
                'nullable',
                'numeric',
            ],
            'name' => [
                'required',
                'max:255',
            ],
            'code' => [
                'max:255',
            ],
            'employmentType' => [
                'required',
            ],
            'positionCluster' => [
                'required',
            ],
            'police' => [
                'required',
            ],
            'sort' => [
                'required',
                'numeric',
            ],
        ]);

        $oldId = htmlspecialchars($request->oldId);
        $id = htmlspecialchars($request->id);
        $empId = htmlspecialchars($request->empId);
        $name = htmlspecialchars($request->name);
        $code = htmlspecialchars($request->code);
        $employmentType = htmlspecialchars($request->employmentType);
        $positionCluster = htmlspecialchars($request->positionCluster);
        $police = htmlspecialchars($request->police);
        $sort = htmlspecialchars($request->sort);
        $isActive = htmlspecialchars($request->isActive);
        $isCanSignatory = htmlspecialchars($request->isCanSignatory);
        
        //unique check
        $positionId = Position::where('id', $id)->count();
        if ($positionId > 0 && $oldId != $id) {
            return redirect()->back()->with('error', 'ID sudah ada.');
        }
        $positionEmpId = Position::where('id', '!=', $oldId)->where('emp_id', $empId)->count();
        if ($positionEmpId > 0 && !empty($empId)) {
            return redirect()->back()->with('error', 'EMP ID sudah ada.');
        }
        $positionCode = Position::where('id', '!=', $oldId)->where('code', $code)->count();
        if ($positionCode > 0 && !empty($code)) {
            return redirect()->back()->with('error', 'Kode sudah ada.');
        }

        DB::beginTransaction();
        try {
            Position::where('id', $oldId)->update([
                'id' => $id,
                'emp_id' => $empId,
                'name' => $name,
                'code' => $code,
                'employment_type_id' => $employmentType,
                'position_cluster_id' => $positionCluster,
                'police_id' => $police,
                'sort' => $sort,
                'is_active' => filter_var($isActive, FILTER_VALIDATE_BOOLEAN),
                'is_can_signatory' => filter_var($isCanSignatory, FILTER_VALIDATE_BOOLEAN),
            ]);
            
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('cms.libs.position.index')->with('error', 'Data gagal diubah.');
        }

        return redirect()->route('cms.libs.position.index')->with('success', 'Data berhasil diubah.');
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $position = Position::find($id);
            
            $position->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            // return redirect()->route('cms.libs.position.index')->with('error', 'Data gagal dihapus.');
            return response()->json(false, 500);
        }

        // return redirect()->route('cms.libs.position.index')->with('success', 'Data berhasil dihapus.');
        return response()->json(true);
    }
}
