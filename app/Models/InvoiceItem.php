<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'periode',
    'date',
    'description',
    'rate',
    'time',
    'flatrate',
    'amount',
    'invoice_id',
  ];

  protected $casts = [
    'date' => 'date',
  ];

  public function invoice()
  {
    return $this->belongsTo(Invoice::class);
  }
}
