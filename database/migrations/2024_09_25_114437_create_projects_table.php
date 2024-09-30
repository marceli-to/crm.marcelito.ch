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
    Schema::create('projects', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->decimal('budget', 10, 2)->nullable();
      $table->boolean('is_collection')->default(false);
      $table->foreignId('company_id')->constrained();
      $table->foreignId('principal_id')->constrained('companies');
      $table->timestamp('archived_at')->nullable();
      $table->softDeletes();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('projects');
  }
};
