<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('app_events', function (Blueprint $table) {
            $table->id();
            $table->string('user_id',50)->nullable();
            $table->enum('event_type', [
                'ad_view',
                'level_start',
                'level_complete',
                'level_fail',
                'purchase',
                'hint_used',
                'iap_purchase',
                'app_launch',
                'app_close'
            ]);
            $table->json('event_data')->nullable();
            $table->json('device_info')->nullable();
            $table->timestamps();
            $table->index('event_type');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('app_events');
    }
};
