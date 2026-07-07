<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('snippets', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->index()->after('owner_token');

            $table->index(['owner_token', 'user_id']);
            $table->index(['user_id', 'expires_at', 'created_at']);
            $table->index(['owner_token', 'expires_at', 'created_at']);

            $table->dropIndex(['expires_at']);
        });
    }

    public function down(): void
    {
        Schema::table('snippets', function (Blueprint $table) {
            $table->dropIndex(['owner_token', 'user_id']);
            $table->dropIndex(['user_id', 'expires_at', 'created_at']);
            $table->dropIndex(['owner_token', 'expires_at', 'created_at']);

            $table->index(['expires_at']);

            $table->dropColumn('ip_address');
        });
    }
};
