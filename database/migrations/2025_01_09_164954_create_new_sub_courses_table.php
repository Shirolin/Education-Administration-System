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
        Schema::create('sub_courses', function (Blueprint $table) {
            $table->id(); // 自增 BIGINT 主键
            $table->unsignedInteger('course_id'); // 无符号 int4
            $table->unsignedInteger('year'); // 无符号 int4
            $table->unsignedInteger('month'); // 无符号 int4
            $table->decimal('fee', 10, 2);
            $table->timestamps();

            $table->unique(['course_id', 'year', 'month'], 'sub_courses_course_id_year_month_unique'); // 唯一索引
            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade'); // 外键约束，级联删除
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_courses');
    }
};
