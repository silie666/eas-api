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
        Schema::create('courses', function (Blueprint $table) {
            $table->comment('课程表');
            $table->commonColumns();

            $table->integer('teacher_id')->nullable()->comment('教师ID');
            $table->string('name')->nullable()->comment('课程名称');
            $table->date('date')->nullable()->comment('年月');
            $table->text('content')->nullable()->comment('课程内容');
            $table->bigInteger('fees')->nullable()->comment('费用(单位：日元)');

            $table->json('student_ids')->nullable()->comment('学生IDs');

            $table->index(['teacher_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
