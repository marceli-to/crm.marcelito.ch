<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Timer extends Model
{
  protected $table = 'timer';

  protected $fillable = [
    'description',
    'date',
    'time_start',
    'time_end',
    'is_billable',
    'project_id',
  ];

  protected $appends = [
    'duration',
  ];

  public function getDurationAttribute()
  {
    return Carbon::parse($this->time_end)->diffInSeconds(Carbon::parse($this->time_start));
  }

  public function project()
  {
    return $this->belongsTo(Project::class);
  }
}
