<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public const TABLENAME = 'users_target_products';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable(self::TABLENAME)) {
            return;
        }

        Schema::create(self::TABLENAME, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('target_product_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('target_product_id')
                ->references('id')
                ->on('target_product')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLENAME);
    }
};
