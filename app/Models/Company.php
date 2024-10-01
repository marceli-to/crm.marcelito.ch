<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
  protected $fillable = [
    'name',
    'acronym',
    'byline',
    'street',
    'zip',
    'city',
  ];

  public function projects()
  {
    return $this->hasMany(Project::class);
  }

  // relationship for active projects
  public function activeProjects()
  {
    return $this->hasMany(Project::class)->whereNull('archived_at');
  }
}
