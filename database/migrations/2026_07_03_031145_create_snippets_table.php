<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('snippets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('owner_token', 64)->nullable()->index();
            $table->string('alias', 50)->unique();
            $table->string('title')->nullable();
            $table->longText('content');
            $table->enum('content_type', ['text', 'url'])->default('text');
            $table->string('language', 50)->nullable();
            $table->boolean('is_public')->default(true);
            $table->string('password')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('snippets');
    }
};
