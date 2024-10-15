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
    Schema::create('invoice_items', function (Blueprint $table) {
      $table->id();
      $table->string('periode')->nullable();
      $table->date('date')->nullable();
      $table->text('description');
      $table->decimal('rate', 10, 2)->nullable();
      $table->decimal('time', 10, 2)->nullable();
      $table->boolean('flatrate')->default(false);
      $table->decimal('amount', 10, 2);
      $table->foreignId('invoice_id');
      $table->softDeletes();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('invoice_items');
  }
};
