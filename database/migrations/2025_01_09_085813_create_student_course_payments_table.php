<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentCoursePaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('student_course_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('sub_course_id');
            $table->integer('invoice_item_id');
            $table->tinyInteger('status')->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'sub_course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_course_payments');
    }
}
