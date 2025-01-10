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
        Schema::dropIfExists('sub_courses');
        Schema::dropIfExists('course_student');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('students');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('student_purchased_courses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id()->comment('教师ID');
            $table->string('nickname', 255)->unique()->comment('教师昵称');
            $table->integer('admin_id')->unique()->comment('管理员ID');
            $table->timestamps();
            $table->comment('教师表');
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id()->comment('学生ID');
            $table->string('nickname', 255)->unique()->comment('学生昵称');
            $table->timestamps();
            $table->comment('学生表');
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id()->comment('课程ID');
            $table->integer('teacher_id')->comment('教师ID');
            $table->string('teacher_nickname', 255)->comment('教师昵称');
            $table->string('name', 255)->comment('课程名称');
            $table->decimal('unit_fee', 10, 2)->comment('单价');
            $table->smallInteger('status')->comment('状态');
            $table->timestamps();
            $table->unique(['teacher_id', 'name']);
            $table->index('teacher_id');
            $table->comment('课程表');
        });

        Schema::create('sub_courses', function (Blueprint $table) {
            $table->id()->comment('子课程ID');
            $table->integer('course_id')->comment('课程ID');
            $table->integer('year')->comment('年份');
            $table->integer('month')->comment('月份');
            $table->decimal('fee', 10, 2)->comment('费用');
            $table->timestamps();
            $table->unique(['course_id', 'year', 'month']);
            $table->index('course_id');
            $table->comment('子课程表');
        });

        Schema::create('course_student', function (Blueprint $table) {
            $table->integer('course_id')->comment('课程ID');
            $table->integer('student_id')->comment('学生ID');
            $table->primary(['course_id', 'student_id']);
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->comment('课程表与学生表的关联表');
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id()->comment('账单ID');
            $table->string('invoice_no', 20)->unique()->comment('账单编号');
            $table->integer('course_id')->comment('课程ID');
            $table->integer('student_id')->comment('学生ID');
            $table->integer('creator_id')->comment('创建者ID');
            $table->decimal('total_amount', 10, 2)->comment('总金额');
            $table->string('currency', 10)->default('CNY')->comment('货币');
            $table->smallInteger('status')->comment('状态');
            $table->timestamps();
            $table->index('course_id');
            $table->index('student_id');
            $table->index('creator_id');
            $table->comment('账单表');
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id()->comment('账单明细ID');
            $table->integer('invoice_id')->comment('账单ID');
            $table->integer('sub_course_id')->comment('子课程ID');
            $table->decimal('amount', 10, 2)->comment('金额');
            $table->timestamps();
            $table->comment('账单明细表');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id()->comment('支付ID');
            $table->integer('invoice_id')->comment('账单ID');
            $table->string('omise_id', 255)->comment('Omise ID');
            $table->decimal('amount', 10, 2)->comment('金额');
            $table->string('currency', 10)->default('CNY')->comment('货币');
            $table->string('card_id', 255)->comment('卡ID');
            $table->string('charge_id', 255)->comment('收费ID');
            $table->string('failure_code', 255)->nullable()->comment('失败代码');
            $table->string('failure_message', 255)->nullable()->comment('失败信息');
            $table->boolean('authorized')->default(false)->comment('是否授权');
            $table->boolean('paid')->default(false)->comment('是否支付');
            $table->string('transaction_id', 255)->nullable()->comment('交易ID');
            $table->smallInteger('status')->default(0)->comment('状态');
            $table->timestamp('paid_at')->nullable()->comment('支付时间');
            $table->timestamps();
            $table->index('invoice_id');
            $table->comment('支付表');
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id()->comment('退款ID');
            $table->integer('payment_id')->comment('支付ID');
            $table->string('omise_id', 255)->comment('Omise ID');
            $table->decimal('amount', 10, 2)->comment('金额');
            $table->string('currency', 10)->default('CNY')->comment('货币');
            $table->smallInteger('status')->default(0)->comment('状态');
            $table->string('reason', 255)->nullable()->comment('原因');
            $table->timestamp('refunded_at')->nullable()->comment('退款时间');
            $table->timestamps();
            $table->index('payment_id');
            $table->comment('退款表');
        });

        Schema::create('student_purchased_courses', function (Blueprint $table) {
            $table->id()->comment('已购买课程ID');
            $table->integer('invoice_id')->comment('账单ID');
            $table->integer('student_id')->comment('学生ID');
            $table->integer('sub_course_id')->comment('子课程ID');
            $table->timestamp('purchase_date')->comment('购买日期');
            $table->timestamps();
            $table->unique(['student_id', 'sub_course_id']);
            $table->index('invoice_id');
            $table->comment('学生已购买课程表');
        });
    }
};
