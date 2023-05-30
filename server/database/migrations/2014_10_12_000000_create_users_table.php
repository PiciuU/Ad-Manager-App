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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_role_id');
            $table->string('name', 255);
            $table->string('login', 32);
            $table->string('password', 72);
            $table->string('email', 255);
            $table->string('activation_key', 32)->nullable();
            $table->string('nip', 10)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('postal_code', 255)->nullable();
            $table->string('country', 255)->nullable();
            $table->string('company_email', 255)->nullable();
            $table->string('company_phone', 32)->nullable();
            $table->string('representative', 255)->nullable();
            $table->string('representative_phone', 32)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_banned')->default(0);
            $table->string('ban_reason', 255)->nullable();
            $table->timestamps();
            $table->timestamp('activated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users'); //zmiana nazwy
    }
};
