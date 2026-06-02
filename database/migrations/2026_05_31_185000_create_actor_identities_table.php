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
        Schema::create('actor_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->constrained()->cascadeOnDelete();
            $table->string('identity_key');
            $table->json('identity_data')->nullable();
            $table->timestamps();

            $table->unique(['workspace_id', 'channel_id', 'identity_key']);
            $table->index(['workspace_id', 'actor_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actor_identities');
    }
};
