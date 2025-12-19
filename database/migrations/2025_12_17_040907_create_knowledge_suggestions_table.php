<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('question')->nullable();
            $table->longText('answer')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->unsignedBigInteger('suggested_by')->nullable(); // user id (optional)
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('reject_reason')->nullable();

            $table->json('meta')->nullable(); // { chat_message_id, confidence, tags, etc }

            $table->timestamps();

            $table->index(['status', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_suggestions');
    }
};
