<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Timer extends Model
{
  protected $table = 'timer';

  protected $fillable = [
    'task',
    'date',
    'time_start',
    'time_end',
    'duration',
    'is_billable',
    'project_id',
  ];

  protected $casts = [
    'date' => 'date',
    'time_start' => 'datetime',
    'time_end' => 'datetime',
  ];

  public function project()
  {
    return $this->belongsTo(Project::class);
  }
}
