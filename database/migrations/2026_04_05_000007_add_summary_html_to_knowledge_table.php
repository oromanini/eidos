<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('knowledge', function (Blueprint $table): void {
            $table->longText('summary_html')->nullable()->after('summary_doc_embed_url');
        });
    }

    public function down(): void
    {
        Schema::table('knowledge', function (Blueprint $table): void {
            $table->dropColumn('summary_html');
        });
    }
};
