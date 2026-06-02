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
        Schema::create('automation_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('step_order');
            $table->string('step_type');
            $table->json('configuration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['automation_id', 'step_order']);
            $table->index(['automation_id', 'is_active']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_steps');
    }
};
