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
        Schema::create('cards', function (Blueprint $table) {
            $table->comment('信用卡表');
            $table->commonColumns();
            $table->string('brand_name')->nullable()->comment('品牌名称');
            $table->string('number')->nullable()->comment('卡号');
            $table->date('expiration_date')->nullable()->comment('过期时间');

            $table->integer('card_table_id')->nullable()->comment('关联表ID');
            $table->string('card_table_type')->nullable()->comment('关联表类型');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
