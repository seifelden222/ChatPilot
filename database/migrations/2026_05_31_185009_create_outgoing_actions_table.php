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
        Schema::create('outgoing_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('automation_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('interaction_id')->constrained()->cascadeOnDelete();
            $table->string('action_type');
            $table->json('payload')->nullable();
            $table->string('status')->default('pending');
            $table->json('provider_response')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['workspace_id', 'status']);
            $table->index(['channel_id', 'status']);
            $table->index('scheduled_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outgoing_actions');
    }
};
