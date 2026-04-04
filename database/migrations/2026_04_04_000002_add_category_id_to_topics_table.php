<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('description');
        });

        $defaultCategoryId = DB::table('categories')->insertGetId([
            'name' => 'Assuntos gerais',
            'description' => 'Categoria padrão para tópicos.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('topics')->update(['category_id' => $defaultCategoryId]);
    }

    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
};
