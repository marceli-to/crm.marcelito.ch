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
    'duration',
    'is_billable',
    'project_id',
  ];

  protected $casts = [
    'date' => 'date',
    'time_start' => 'datetime',
    'time_end' => 'datetime',
  ];

  protected $appends = [
    'humanized_duration',
  ];

  public function getHumanizedDurationAttribute()
  {
    // duration is in minutes. if lower than 60, return the duration in minutes.
    // if it's more than 60, return the duration in hours and minutes.
    if ($this->duration < 60)
    {
      return $this->duration . 'm';
    }
    else {
      // show minutes only if its more than 0
      if ($this->duration % 60 > 0)
      {
        return floor($this->duration / 60) . 'h ' . ($this->duration % 60) . 'm';
      }
      return floor($this->duration / 60) . 'h';
    }

    // todo: get difference between time_end and time_start in minutes.
    // if its more than 60, return the difference in hours and minutes.
    // if its less than 60, return the difference in minutes.
    // if its 0, return 0.
    // $start = \Carbon\Carbon::parse($this->time_start);
    // $end = \Carbon\Carbon::parse($this->time_end);
    // $diff = $start->diffInMinutes($end);
    // if ($diff > 60)
    // {
    //   // get hours and minutes
    //   $hours = floor($diff / 60);
    //   $minutes = $diff % 60;

    //   // show minutes only if its more than 0
    //   if ($minutes > 0)
    //   {
    //     return $hours . 'h ' . $minutes . 'm';
    //   }
    //   return $hours . 'h';
    // } 
    // else
    // {
    //   return $diff . 'm';
    // }
  }

  public function project()
  {
    return $this->belongsTo(Project::class);
  }
}
