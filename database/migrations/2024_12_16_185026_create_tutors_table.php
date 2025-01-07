<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tutors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 20);
            $table->string('secondary_phone', 45)->nullable();
            $table->string('email', 100);
            $table->string('address');
            $table->enum('dni_type', ['CC', 'CE', 'TI', 'RC']);
            $table->string('dni', 20)->unique('dni_unique');
            $table->foreignId('dni_expedition_city')->constrained('cities')->onDelete('cascade');
            $table->foreignId('residence_city')->constrained('cities')->onDelete('cascade');
            $table->string('ocupation', 45)->nullable();
            $table->timestamps();
            $table->primary(['id', 'dni_expedition_city', 'residence_city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutors');
    }
};
