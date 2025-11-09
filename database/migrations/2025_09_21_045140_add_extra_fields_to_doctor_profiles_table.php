<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->json('treatments')->nullable()->after('bio');
            $table->json('expertise')->nullable()->after('treatments');
            $table->text('awards')->nullable()->after('expertise');
            $table->text('publications')->nullable()->after('awards');
        });
    }

    public function down(): void
    {
        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->dropColumn(['treatments', 'expertise', 'awards', 'publications']);
        });
    }
};

