<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_bills', function (Blueprint $table) {
            $table->comment('课程账单表');
            $table->commonColumns();
            $table->integer('teacher_id')->nullable()->comment('教师id');
            $table->tinyInteger('status')->nullable()->comment('发送状态');
            $table->json('course_ids')->nullable()->comment('课程ids');

            $table->index(['teacher_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_bills');
    }
};
