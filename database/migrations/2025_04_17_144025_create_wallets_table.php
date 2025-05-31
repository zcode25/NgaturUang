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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->enum('type', ['cash', 'bank', 'ewallet', 'other']);
            $table->enum('currency', ['IDR', 'USD'])->default('IDR');
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active','inactive'])->default('active');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
