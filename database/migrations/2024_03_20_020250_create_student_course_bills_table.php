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
        Schema::create('student_course_bills', function (Blueprint $table) {
            $table->comment('学生课程账单表');
            $table->commonColumns();
            $table->integer('course_bill_id')->nullable()->comment('课程账单ID');
            $table->integer('student_id')->nullable();
            $table->integer('course_id')->nullable();
            $table->string('card_number')->nullable();
            $table->tinyInteger('pay_status')->default(1)->comment('支付状态');
            $table->timestamp('pay_time')->nullable()->comment('支付时间');
            $table->bigInteger('bill_fees')->nullable()->comment('课程费用(单位：日元)');
            $table->bigInteger('paid_fees')->nullable()->comment('已付费用(单位：日元)');

            $table->json('extra_data')->nullable()->comment('额外字段');
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
