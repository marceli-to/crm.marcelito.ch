<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
  use SoftDeletes;
  
  protected $fillable = [
    'name',
    'budget',
    'is_collection',
    'rate_id',
    'company_id',
    'principal_id',
    'archived_at',
    'created_at',
    'updated_at',
    'deleted_at',
  ];

  protected $casts = [
    'archived_at' => 'datetime',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
  ];
  
  public function rate()
  {
    return $this->belongsTo(Rate::class);
  }

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function principal()
  {
    return $this->belongsTo(Company::class);
  }

  public function timer()
  {
    return $this->hasMany(Timer::class);
  }

  public function scopeActive($query)
  {
    return $query->whereNull('archived_at');
  }
}
