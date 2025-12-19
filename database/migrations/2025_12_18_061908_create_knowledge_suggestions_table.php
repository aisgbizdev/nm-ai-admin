<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_suggestions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title', 255);
            $table->longText('answer');

            $table->string('source', 255)->default('manual');

            // sesuai screenshot: default 1
            $table->boolean('is_published')->default(true);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            // sesuai screenshot: created_at & updated_at nullable
            $table->timestamps();

            // Kalau mau kolomnya benar-benar nullable (match screenshot):
            // $table->timestamp('created_at')->nullable();
            // $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_suggestions');
    }
};
