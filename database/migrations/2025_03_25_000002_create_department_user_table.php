<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('department_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('role', ['admin', 'manager', 'member'])->default('member');
            $table->timestamps();

            // Foreign key for department_id
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            // Foreign key for user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Unique constraint for department_id and user_id
            $table->unique(['department_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('department_user');
    }
}; 