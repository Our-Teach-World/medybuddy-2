<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // सिर्फ तभी add करो जब column मौजूद न हो
        if (! Schema::hasColumn('patient_profiles', 'height')) {
            Schema::table('patient_profiles', function (Blueprint $table) {
                $table->string('height', 10)->nullable()->after('blood_group');
            });
        }

        if (! Schema::hasColumn('patient_profiles', 'emergency_contact')) {
            Schema::table('patient_profiles', function (Blueprint $table) {
                $table->string('emergency_contact', 20)->nullable()->after('weight');
            });
        }
    }

    public function down(): void
    {
        // rollback में safe तरीके से drop करो (अगर मौजूद हों)
        if (Schema::hasColumn('patient_profiles', 'height') || Schema::hasColumn('patient_profiles', 'emergency_contact')) {
            Schema::table('patient_profiles', function (Blueprint $table) {
                if (Schema::hasColumn('patient_profiles', 'height')) {
                    $table->dropColumn('height');
                }
                if (Schema::hasColumn('patient_profiles', 'emergency_contact')) {
                    $table->dropColumn('emergency_contact');
                }
            });
        }
    }
};
