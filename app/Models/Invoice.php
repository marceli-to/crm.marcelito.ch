<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'date',
    'number',
    'title',
    'text',
    'total',
    'vat',
    'grand_total',
    'cancellation_reason',
    'company_id',
    'project_id',
    'status_id',
    'due_at',
    'paid_at',
    'created_at',
    'updated_at',
    'deleted_at',
  ];

  protected $casts = [
    'date' => 'date',
    'due_at' => 'date',
    'paid_at' => 'date',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function project()
  {
    return $this->belongsTo(Project::class);
  }

  public function vat()
  {
    return $this->belongsTo(Vat::class);
  }

  public function status()
  {
    return $this->belongsTo(Status::class);
  }

  public function items()
  {
    return $this->hasMany(InvoiceItem::class);
  }

  public function scopeOpen($query)
  {
    return $query->where('status_id', 1);
  }

  public function scopePending($query)
  {
    return $query->where('status_id', 2);
  }

  public function scopePaid($query)
  {
    return $query->where('status_id', 3);
  }

  public function scopeOverdue($query)
  {
    return $query->where('status_id', 4);
  }

  public function scopeCancelled($query)
  {
    return $query->where('status_id', 5);
  }

  public function scopeNotCancelled($query)
  {
    return $query->where('status_id', '!=', 5);
  }
}
