<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('doctor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // users table must exist
            $table->string('specialization')->nullable();
            $table->string('license')->nullable();
            $table->string('experience')->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_image')->nullable(); // stored filename in storage/app/public/profile
            $table->decimal('in_person_fee', 8, 2)->nullable();
            $table->decimal('video_fee', 8, 2)->nullable();
            $table->float('rating', 3, 2)->default(4.50);
            $table->integer('reviews_count')->default(0);
            $table->string('hospital_name')->nullable();
            $table->text('hospital_address')->nullable();
            $table->json('languages')->nullable();
            $table->timestamps();

            $table->index('specialization');
        });
    }

    public function down()
    {
        Schema::dropIfExists('doctor_profiles');
    }
}

