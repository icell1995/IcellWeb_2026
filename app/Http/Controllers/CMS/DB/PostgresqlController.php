<?php

namespace App\Http\Controllers\CMS\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Dir\PostgresqlSavedQuery;

class PostgresqlController extends Controller
{
    public function queryIndex(Request $request)
    {
        return view('cms.db.postgresql.query.index', ['results' => []]);
    }

    public function queryExecute(Request $request)
    {
        $password = htmlspecialchars($request->password);
        $userId = Auth::id();
        
        $user = User::select('password')->find($userId);

        if (!$user || !Hash::check($password, $user->password)) {
            return view('cms.db.postgresql.query.index', [
                'results' => [],
                'message' => 'Invalid Password!'
            ]);
        }

        $queryText = $request->queryText;
        $connection = $request->connection;

        DB::beginTransaction();
        try{
            // Additional check to prevent manipulation queries
            $disallowedKeywords = ['INSERT', 'DELETE', 'ALTER', 'DROP', 'TRUNCATE', 'GRANT'];

            if (Str::contains(Str::upper($queryText), $disallowedKeywords)) {
                return view('cms.db.postgresql.query.index', [
                    'results' => [], 
                    'queryText' => $queryText, 
                    'message' => 'Not Allowed Query!'
                ]);
            }

            if($connection == 'icell'){
                $result = DB::select($queryText);
            }
            
            if(!isset($result) || empty($result)){
                $result = [];
            }

            DB::rollBack();
            return view('cms.db.postgresql.query.index', ['results' => $result, 'queryText' => $queryText, 'message' => 'Success']);
        }catch(\Exception $e){
            DB::rollBack();
            return view('cms.db.postgresql.query.index', ['results' => [], 'queryText' => $queryText, 'message' => $e->getMessage()]);
        }
    }

    public function retrieveSavedQuery(Request $request)
    {
        $id = $request->id;

        if(!empty($id)){
            $savedQuery = PostgresqlSavedQuery::find($id);
            return response()->json([
                "code" => 200,
                "status" => "OK",
                "message" => "Success",
                "data" => $savedQuery
            ]);
        }else{
            $savedQueries = PostgresqlSavedQuery::orderBy('updated_at', 'desc')->get();
            return response()->json([
                "code" => 200,
                "status" => "OK",
                "message" => "Success",
                "data" => $savedQueries
            ]);
        }
    }

    public function savingQuery(Request $request)
    {
        $savedId = $request->saveId;
        $saveName = $request->saveName;
        $queryText = str_replace("\r\n", "\n", $request->queryText);
        
        DB::beginTransaction();
        try{
            if(!empty($savedId)){
                $savedQuery = PostgresqlSavedQuery::find($savedId);
                $savedQuery->title = $saveName;
                $savedQuery->query = $queryText;
                $savedQuery->save();
            }else{
                $savedQuery = new PostgresqlSavedQuery();
                $savedQuery->title = $saveName;
                $savedQuery->query = $queryText;
                $savedQuery->save();
            }

            DB::commit();
            return response()->json([
                "code" => 200,
                "status" => "OK",
                "message" => "Success"
            ]);
        } catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                "code" => 500,
                "status" => "Error",
                "message" => $e->getMessage()
            ]);
        }
    }
}
