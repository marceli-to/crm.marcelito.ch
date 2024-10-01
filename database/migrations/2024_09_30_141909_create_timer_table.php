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
    Schema::create('timer', function (Blueprint $table) {
      $table->id();
      $table->string('description');
      $table->date('date');
      $table->time('time_start');
      $table->time('time_end');
      $table->boolean('is_billable')->default(true);
      $table->foreignId('project_id')->constrained('projects');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('timers');
  }
};
