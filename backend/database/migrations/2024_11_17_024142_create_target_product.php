<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public const TABLENAME = 'target_product';

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
            $table->text('link');
            $table->bool('is_active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique('link');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->nullOnDelete();
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
