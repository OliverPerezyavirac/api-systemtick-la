<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Crear un departamento por defecto
        $defaultDepartmentId = DB::table('departments')->insertGetId([
            'name' => 'Departamento General',
            'description' => 'Departamento por defecto para workspaces existentes',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Agregar la columna department_id con la restricción de clave foránea
        Schema::table('workspaces', function (Blueprint $table) use ($defaultDepartmentId) {
            // Agregar la columna department_id
            $table->unsignedBigInteger('department_id')->after('id')->default($defaultDepartmentId);
            // Agregar la restricción de clave foránea
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
}; 