<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Добавлен импорт DB

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id(); // Эквивалент INT(10) NOT NULL AUTO_INCREMENT (первичный ключ)
            $table->integer('level_id')->nullable(); // INT(10) NULL DEFAULT NULL
            $table->string('user_id',50)->nullable();
            $table->string('email', 128)->nullable()->collation('utf16_bin'); // VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf16_bin'
            $table->text('message')->nullable()->collation('utf16_bin'); // TEXT NULL DEFAULT NULL COLLATE 'utf16_bin'
            $table->timestamps(); // Создаст created_at и updated_at TIMESTAMP NULL DEFAULT NULL
            $table->index('user_id');
        });

        // Установка кодировки и движка таблицы
  //      DB::statement('ALTER TABLE feedback COLLATE utf16_bin');
   //     DB::statement('ALTER TABLE feedback ENGINE = InnoDB');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
