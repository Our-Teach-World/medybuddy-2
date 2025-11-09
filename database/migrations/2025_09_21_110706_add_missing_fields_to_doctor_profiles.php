<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->string('linkedin')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->text('consultation_hours')->nullable(); // optional, if you want to store general hours
            $table->string('default_mode')->nullable(); // optional, if you want default consultation mode
        });
    }

    public function down()
    {
        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->dropColumn(['linkedin', 'facebook', 'twitter', 'instagram', 'consultation_hours', 'default_mode']);
        });
    }
};