<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('automation_run_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('automation_step_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('step_order');
            $table->string('status')->default('pending');
            $table->json('result')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();

            $table->unique(['automation_run_id', 'step_order']);
            $table->index(['automation_run_id', 'status']);
            $table->index('executed_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_run_steps');
    }
};
