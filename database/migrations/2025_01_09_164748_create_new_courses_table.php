<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id(); // 创建自增的 BIGINT 主键 (等价于 $table->bigIncrements('id');)
            $table->unsignedInteger('teacher_id'); // 无符号整数，对应 int4
            $table->string('teacher_nickname');
            $table->string('name');
            $table->decimal('unit_fee', 10, 2);
            $table->tinyInteger('status'); // 对应 tinyint2
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('teachers'); // 添加外键约束
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
