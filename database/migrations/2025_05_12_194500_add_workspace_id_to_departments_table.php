<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'workspace_id')) {
                // Foreign key for workspace_id
                $table->foreignId('workspace_id')->after('id')->nullable()->constrained()->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'workspace_id')) {
                $table->dropForeign(['workspace_id']);
                $table->dropColumn('workspace_id');
            }
        });
    }
}; 