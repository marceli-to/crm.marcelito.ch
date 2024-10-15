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
  ];

  protected $casts = [
    'date' => 'date',
    'due_at' => 'date',
    'paid_at' => 'date',
    'cancelled_at' => 'date',
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
}
