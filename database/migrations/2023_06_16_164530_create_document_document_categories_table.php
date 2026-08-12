<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentDocumentCategoriesTable extends Migration
{
    private $schema = 'document';
    private $table;

    public function __construct()
    {
        $this->table = $this->schema . '.document_categories';
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->string('id')->primary();
            
            $table->string('parent_id')->nullable();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('category')->nullable()->comment('STAGE, TYPE');
            $table->unsignedInteger('sort')->nullable()->default(0);
            $table->string('route')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->schema . '.document_categories');
    }
}
