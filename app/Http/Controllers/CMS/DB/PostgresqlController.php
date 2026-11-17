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
        $authUser = Auth::user();
        if (!$authUser || (int) $authUser->role_id !== 1) {
            abort(403, 'Unauthorized');
        }

        $password = (string) $request->password;
        $userId = Auth::id();

        $user = User::select('password')->find($userId);

        if (!$user || !Hash::check($password, $user->password)) {
            return view('cms.db.postgresql.query.index', [
                'results' => [],
                'message' => 'Invalid Password!'
            ]);
        }

        $queryText = trim((string) $request->queryText);
        $connection = $request->connection;

        $normalized = rtrim($queryText, "; \t\n\r\0\x0B");
        if (str_contains($normalized, ';') || !preg_match('/^(SELECT|WITH|EXPLAIN)\b/i', $normalized)) {
            return view('cms.db.postgresql.query.index', [
                'results' => [],
                'queryText' => $queryText,
                'message' => 'Not Allowed Query! Only single read-only SELECT/WITH/EXPLAIN statements are permitted.'
            ]);
        }

        $disallowedKeywords = [
            'INSERT',
            'UPDATE',
            'DELETE',
            'ALTER',
            'DROP',
            'TRUNCATE',
            'GRANT',
            'REVOKE',
            'CREATE',
            'REPLACE',
            'COPY',
            'CALL',
            'DO',
            'EXECUTE',
            'PREPARE',
            'SET',
            'VACUUM',
            'ANALYZE',
            'LOCK',
            'COMMENT',
            'PG_READ_FILE',
            'PG_LS_DIR',
            'PG_STAT_FILE',
            'LO_IMPORT',
            'LO_EXPORT',
            'DBLINK'
        ];
        $blockedTables = [
            'PASSWORD_RESETS',
            'SSO_TOKENS',
            'PUSIKNAS_API_TOKENS',
            'PERSONAL_ACCESS_TOKENS'
        ];

        $upperQuery = Str::upper($normalized);
        foreach ($disallowedKeywords as $kw) {
            if (preg_match('/\b' . preg_quote($kw, '/') . '\b/', $upperQuery)) {
                return view('cms.db.postgresql.query.index', [
                    'results' => [],
                    'queryText' => $queryText,
                    'message' => 'Not Allowed Query!'
                ]);
            }
        }
        foreach ($blockedTables as $tbl) {
            if (preg_match('/\b' . preg_quote($tbl, '/') . '\b/', $upperQuery)) {
                return view('cms.db.postgresql.query.index', [
                    'results' => [],
                    'queryText' => $queryText,
                    'message' => 'Access to sensitive credential/token tables is prohibited!'
                ]);
            }
        }

        DB::beginTransaction();
        try {
            DB::statement('SET LOCAL TRANSACTION READ ONLY');
            DB::statement("SET LOCAL statement_timeout = '5000'");

            if ($connection == 'icell') {
                $result = DB::select($normalized);
            }

            if (!isset($result) || empty($result)) {
                $result = [];
            }

            DB::rollBack();
            return view('cms.db.postgresql.query.index', ['results' => $result, 'queryText' => $queryText, 'message' => 'Success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return view('cms.db.postgresql.query.index', ['results' => [], 'queryText' => $queryText, 'message' => $e->getMessage()]);
        }
    }

    public function retrieveSavedQuery(Request $request)
    {
        $id = $request->id;

        if (!empty($id)) {
            $savedQuery = PostgresqlSavedQuery::find($id);
            return response()->json([
                "code" => 200,
                "status" => "OK",
                "message" => "Success",
                "data" => $savedQuery
            ]);
        } else {
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
        try {
            if (!empty($savedId)) {
                $savedQuery = PostgresqlSavedQuery::find($savedId);
                $savedQuery->title = $saveName;
                $savedQuery->query = $queryText;
                $savedQuery->save();
            } else {
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
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "code" => 500,
                "status" => "Error",
                "message" => $e->getMessage()
            ]);
        }
    }
}
