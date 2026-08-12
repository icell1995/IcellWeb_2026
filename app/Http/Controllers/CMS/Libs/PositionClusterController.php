<?php

namespace App\Http\Controllers\CMS\Libs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Opt\PositionCluster;

class PositionClusterController extends Controller
{
    public function index()
    {
        $positionClusters = PositionCluster::orderBy('id')
            ->get();

        $viewData = [
            'positionClusters' => $positionClusters,
        ];

        return view('cms.libs.position-cluster.index', $viewData);
    }

    public function create()
    {
        $positionClusterMaxId = PositionCluster::select(DB::raw('MAX(CAST(id AS INTEGER)) as max_id'))
            ->first();

        $createPositionClusterId = $positionClusterMaxId->max_id + 1;

        $viewData = [
            'createPositionClusterId' => $createPositionClusterId,
        ];

        return view('cms.libs.position-cluster.create', $viewData);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => [
                'required',
                'numeric',
            ],
            'name' => [
                'required',
                'max:255',
            ],
            'code' => [
                'max:255',
            ],
            'sort' => [
                'required',
                'numeric',
            ],
        ]);

        $id = htmlspecialchars($request->id);
        $name = htmlspecialchars($request->name);
        $code = htmlspecialchars($request->code);
        $sort = htmlspecialchars($request->sort);
        $isActive = htmlspecialchars($request->isActive);
        $isCanSignatory = htmlspecialchars($request->isCanSignatory);
        
        //unique check
        $positionClusterId = PositionCluster::where('id', $id)->count();
        if ($positionClusterId > 0) {
            return redirect()->back()->with('error', 'ID sudah ada.');
        }
       
        $positionClusterCode = PositionCluster::where('code', $code)->count();
        if ($positionClusterCode > 0 && !empty($code)) {
            return redirect()->back()->with('error', 'Kode sudah ada.');
        }

        DB::beginTransaction();
        try {
            PositionCluster::create([
                'id' => $id,
                'name' => $name,
                'code' => $code,
                'sort' => $sort,
                'is_active' => filter_var($isActive, FILTER_VALIDATE_BOOLEAN),
                'is_can_signatory' => filter_var($isCanSignatory, FILTER_VALIDATE_BOOLEAN),
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('cms.libs.position-cluster.index')->with('error', 'Data gagal ditambahkan.');
        }

        return redirect()->route('cms.libs.position-cluster.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $positionCluster = PositionCluster::where('id', $id)
            ->first();

        $viewData = [
            'id' => $id,
            'positionCluster' => $positionCluster,
        ];

        return view('cms.libs.position-cluster.edit', $viewData);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'max:255',
            ],
            'code' => [
                'max:255',
            ],
            'sort' => [
                'required',
                'numeric',
            ],
        ]);

        $oldId = $request->oldId;
        $id = htmlspecialchars($request->id);
        $name = htmlspecialchars($request->name);
        $code = htmlspecialchars($request->code);
        $sort = htmlspecialchars($request->sort);
        $isActive = htmlspecialchars($request->isActive);
        $isCanSignatory = htmlspecialchars($request->isCanSignatory);
        
        //unique check
        $positionClusterCode = PositionCluster::where('id', '!=', $oldId)->where('code', $code)->count();
        if ($positionClusterCode > 0 && !empty($code)) {
            return redirect()->back()->with('error', 'Kode sudah ada.');
        }

        DB::beginTransaction();
        try {
            PositionCluster::where('id', $oldId)
                ->update([
                    'id' => $id,
                    'name' => $name,
                    'code' => $code,
                    'sort' => $sort,
                    'is_active' => filter_var($isActive, FILTER_VALIDATE_BOOLEAN),
                    'is_can_signatory' => filter_var($isCanSignatory, FILTER_VALIDATE_BOOLEAN),
                ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('cms.libs.position-cluster.index')->with('error', 'Data gagal diubah.');
        }

        return redirect()->route('cms.libs.position-cluster.index')->with('success', 'Data berhasil diubah.');
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            PositionCluster::where('id', $id)
                ->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('cms.libs.position-cluster.index')->with('error', 'Data gagal dihapus.');
        }

        return redirect()->route('cms.libs.position-cluster.index')->with('success', 'Data berhasil dihapus.');
    }
}
