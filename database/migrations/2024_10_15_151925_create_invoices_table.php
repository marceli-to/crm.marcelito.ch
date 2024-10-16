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
    Schema::create('invoices', function (Blueprint $table) {
      $table->id();
      $table->string('number')->nullable();
      $table->string('title');
      $table->text('text')->nullable();
      $table->decimal('total', 10, 2);
      $table->decimal('vat', 10, 2);
      $table->decimal('grand_total', 10, 2);
      $table->string('cancellation_reason')->nullable();
      $table->foreignId('company_id');
      $table->foreignId('project_id')->nullable();
      $table->foreignId('vat_id')->default(1);
      $table->foreignId('status_id')->default(1);
      $table->date('due_at')->nullable();
      $table->date('paid_at')->nullable();
      $table->softDeletes();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('invoices');
  }
};
