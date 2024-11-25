<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public const TABLENAME = 'prices_log';

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
            $table->decimal('price', places: 2)->nullable();
            $table->enum('result', ['success', 'repeat', 'unavailable'])->default('repeat');
            $table->text('message')->nullable();
            $table->timestamps();
            
            $table->foreign('target_product_id')
                ->references('id')
                ->on('target_product')
                ->onUpdate('cascade')
                ->restrictOnDelete();
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
