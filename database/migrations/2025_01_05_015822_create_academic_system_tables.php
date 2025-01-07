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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'CLOSED'])->default('ACTIVE');
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('grade_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Índice único para evitar duplicados
            $table->unique(['grade_id', 'subject_id']);
        });

        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('name', 45);
            $table->unsignedTinyInteger('number');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            // Índice único para evitar duplicados de períodos en el mismo año
            $table->unique(['academic_year_id', 'number']);
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('grade_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->date('enrollment_date');
            $table->enum('status', ['ACTIVE', 'WITHDRAWN', 'GRADUATED', 'SUSPENDED'])
                ->default('ACTIVE');
            $table->timestamps();

            // Índice único para evitar múltiples matrículas activas
            $table->unique(['student_id', 'academic_year_id']);
        });

        Schema::create('academic_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('period_id')->constrained()->onDelete('cascade');
            $table->decimal('grade', 3, 2); // Permite notas de 0.00 a 5.00
            $table->text('observations')->nullable();
            $table->timestamps();

            // Índice único para evitar notas duplicadas
            $table->unique(['enrollment_id', 'subject_id', 'period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_notes');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('periods');
        Schema::dropIfExists('grade_subjects');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('academic_years');
    }
};
