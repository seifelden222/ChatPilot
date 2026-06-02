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
        Schema::create('content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->string('content_key');
            $table->string('content_type')->default('message');
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['workspace_id', 'channel_id', 'content_key']);
            $table->index(['workspace_id', 'content_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_items');
    }
};
