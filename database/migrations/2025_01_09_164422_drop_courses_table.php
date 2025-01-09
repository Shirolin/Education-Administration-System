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
        Schema::dropIfExists('course_student');
        Schema::dropIfExists('sub_courses');
        Schema::dropIfExists('courses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('teacher_id');
            $table->string('teacher_nickname');
            $table->string('name');
            $table->decimal('unit_fee', 10, 2);
            $table->integer('sub_courses_count');
            $table->tinyInteger('status');
            $table->timestamps();
        });

        Schema::create('sub_courses', function (Blueprint $table) {
            $table->id();
            $table->integer('course_id');
            $table->integer('year');
            $table->integer('month');
            $table->string('fee');
            $table->timestamps();
        });

        Schema::create('course_student', function (Blueprint $table) {
            $table->id();
            $table->integer('course_id');
            $table->integer('student_id');
            $table->timestamps();
        });
    }
};
