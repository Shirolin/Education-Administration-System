<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubCoursesTable extends Migration
{
    public function up(): void
    {
        Schema::create('sub_courses', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('course_id');
            $table->integer('year');
            $table->integer('month');
            $table->decimal('fee', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_courses');
    }
}
