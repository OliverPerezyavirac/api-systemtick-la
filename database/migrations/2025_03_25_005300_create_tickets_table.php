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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('workspace_id');
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('assignee_id')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['new', 'open', 'in_progress', 'resolved', 'closed'])->default('new');
            $table->enum('category', ['bug', 'feature', 'support', 'other']);
            $table->dateTime('closed_at')->nullable();

            // Relaciones
            // Foreign key for workspace_id
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            // Foreign key for creator_id
            $table->foreign('creator_id')->references('id')->on('users');
            // Foreign key for assignee_id
            $table->foreign('assignee_id')->references('id')->on('users')->onDelete('set null');
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
        Schema::dropIfExists('tickets');
    }
};
