<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['unpaid', 'active', 'inactive', 'expired']);
            $table->timestamp('ad_start_date');
            $table->timestamp('ad_end_date');
            $table->string('file_name', 255);
            $table->enum('file_type', ['img', 'video']);
            $table->string('url', 255);
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
