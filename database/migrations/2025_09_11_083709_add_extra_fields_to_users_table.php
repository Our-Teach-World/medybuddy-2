<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('first_name');
        $table->string('last_name');
        $table->string('phone');
        $table->string('address');
        $table->enum('role', ['patient','doctor']);
        $table->string('specialization')->nullable();
        $table->string('license')->nullable();
        $table->string('experience')->nullable();
        $table->date('dob')->nullable();
        $table->enum('gender', ['male','female','other','prefer-not-to-say'])->nullable();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
