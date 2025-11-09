<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->integer('consultation_fees')->nullable()->after('bio');
            $table->json('consultation_modes')->nullable()->after('consultation_fees');
        });
    }

    public function down(): void
    {
        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->dropColumn('consultation_fees');
            $table->dropColumn('consultation_modes');
        });
    }
};
