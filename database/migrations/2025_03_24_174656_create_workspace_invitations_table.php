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
        Schema::create('workspace_invitations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workspace_id');
            $table->unsignedBigInteger('invited_by');
            $table->string('email');
            $table->enum('role', ['guest', 'member', 'manager']);
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->unique(['workspace_id', 'email']);

            // Relaciones
            // Unique constraint for workspace_id and email
            $table->unique(['workspace_id', 'email']);
            // Foreign key for workspace_id
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            // Foreign key for invited_by
            $table->foreign('invited_by')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('workspace_invitations');
    }
};
