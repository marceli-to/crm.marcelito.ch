<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
  use SoftDeletes;

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

  public function activeProjects()
  {
    return $this->hasMany(Project::class)->whereNull('archived_at');
  }
}
