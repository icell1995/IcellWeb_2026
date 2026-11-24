<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CmsSpdpPusiknasValidationTest extends TestCase
{
    private const string ROUTE_PREFIX = 'cms.case-document-validation.module.surat-pemberitahuan-dimulainya-penyidikan-pusiknas-document.validation';

    private const string DOCUMENT_TABLE = 'doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_documents';

    protected function setUp(): void
    {
        parent::setUp();

        $this->createValidationFixture();
        config(['app.mode' => 'PRODUCTION', 'services.whatsapp.api_url' => 'https://saungwa.test/']);
        Http::fake(['saungwa.test/*' => Http::response(['message_status' => 'success'])]);
        Http::preventStrayRequests();

        $admin = new User;
        $admin->id = 1;
        $admin->role_id = 1;
        $this->actingAs($admin);
    }

    public function test_approval_saves_without_sending_saungwa_notifications(): void
    {
        $this->postJson(route(self::ROUTE_PREFIX.'.approve', ['id' => 'test-spdp']), [
            'isApproved' => true,
            'isLegacy' => false,
        ])->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseHas(self::DOCUMENT_TABLE, ['id' => 'test-spdp', 'status_id' => '9']);
        $this->assertDatabaseHas('log_case_document_validations', [
            'document_id' => 'test-spdp', 'updated_status_id' => '9', 'approved_by_id' => 1,
        ]);
        Http::assertNothingSent();
    }

    public function test_rejection_saves_without_sending_saungwa_notifications(): void
    {
        $this->postJson(route(self::ROUTE_PREFIX.'.reject', ['id' => 'test-spdp']), [
            'isRejected' => true,
            'rejectStatusOption' => '4',
            'rejectReason' => 'Perlu revisi',
        ])->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseHas(self::DOCUMENT_TABLE, ['id' => 'test-spdp', 'status_id' => '4']);
        $this->assertDatabaseHas('log_case_document_validations', [
            'document_id' => 'test-spdp', 'updated_status_id' => '4', 'rejected_by_id' => 1, 'reject_reason' => 'Perlu revisi',
        ]);
        Http::assertNothingSent();
    }

    private function createValidationFixture(): void
    {
        config([
            'database.default' => 'spdp_validation_test',
            'database.connections.spdp_validation_test' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
        ]);

        DB::unprepared(<<<'SQL'
            ATTACH DATABASE ':memory:' AS doc;
            ATTACH DATABASE ':memory:' AS lib;
            ATTACH DATABASE ':memory:' AS opt;
            ATTACH DATABASE ':memory:' AS public;

            CREATE TABLE doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_documents (
                id TEXT PRIMARY KEY, accident_id TEXT, document_category_id TEXT, document_number TEXT,
                status_id TEXT, is_legacy INTEGER, messages TEXT, released_at TEXT, approved_at TEXT, rejected_at TEXT,
                updated_at TEXT, deleted_at TEXT
            );
            CREATE TABLE log_case_document_validations (
                id INTEGER PRIMARY KEY, document_id TEXT, accident_id TEXT, document_category_id TEXT, document_number TEXT,
                updated_status_id TEXT, updated_status_name TEXT, accident_number TEXT, document_category_name TEXT,
                model_class TEXT, approved_by_id INTEGER, approved_by_name TEXT, approved_at TEXT,
                rejected_by_id INTEGER, rejected_by_name TEXT, rejected_at TEXT, reject_reason TEXT, created_at TEXT, updated_at TEXT
            );
            CREATE TABLE accidents (id TEXT PRIMARY KEY, police_id TEXT, no_lp TEXT);
            CREATE TABLE lib.document_categories (id TEXT PRIMARY KEY, name TEXT);
            CREATE TABLE opt.statuses (id TEXT PRIMARY KEY, name TEXT, deleted_at TEXT);

            CREATE TABLE public.officers (
                id TEXT PRIMARY KEY, police_id TEXT, is_active INTEGER, flag TEXT, register_number TEXT, phone_number TEXT
            );
            CREATE TABLE lib.polices (id TEXT PRIMARY KEY, parent_id TEXT, full_name TEXT, deleted_at TEXT);
            CREATE TABLE lib.positions (id TEXT PRIMARY KEY, name TEXT, deleted_at TEXT);
            CREATE TABLE doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_officers (
                id TEXT PRIMARY KEY, surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_id TEXT,
                class TEXT, register_number TEXT, position_id TEXT
            );

            INSERT INTO doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_documents
                (id, accident_id, document_category_id, document_number, status_id, is_legacy)
                VALUES ('test-spdp', 'test-accident', '0204', 'SPDP-1', '12', 1);
            INSERT INTO accidents VALUES ('test-accident', 'test-police', 'LP-1');
            INSERT INTO lib.document_categories VALUES ('0204', 'SPDP Pusiknas');
            INSERT INTO opt.statuses (id, name) VALUES ('12', 'Validasi'), ('9', 'Siap ditandatangani'), ('4', 'Dikembalikan');
            INSERT INTO public.officers VALUES ('test-officer', 'test-police', 1, 'ADMIN', '9000', '6280000000000');
            INSERT INTO lib.polices (id, full_name) VALUES ('test-police', 'Satker Uji');
            INSERT INTO lib.positions (id, name) VALUES ('test-position', 'Penanda tangan');
            INSERT INTO doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_officers
                VALUES ('test-signatory', 'test-spdp', 'SIGNATORY', '9000', 'test-position');
            SQL);
    }
}
