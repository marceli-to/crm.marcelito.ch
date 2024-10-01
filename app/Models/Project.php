<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
  protected $fillable = [
    'name',
    'budget',
    'is_collection',
    'company_id',
    'principal_id',
    'archived_at',
    'created_at',
  ];

  protected $casts = [
    'archived_at' => 'datetime',
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function principal()
  {
    return $this->belongsTo(Company::class);
  }

  public function scopeActive($query)
  {
    return $query->whereNull('archived_at');
  }
}
