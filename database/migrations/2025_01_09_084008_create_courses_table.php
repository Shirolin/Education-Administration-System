<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    public function up(): void
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
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
}
