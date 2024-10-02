<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
  use SoftDeletes;
  protected $fillable = [
    'number',
    'date',
    'title',
    'description',
    'currency_id',
    'amount',
    'receipt',
  ];

  protected $casts = [
    'date' => 'date',
  ];

  public function currency()
  {
    return $this->belongsTo(Currency::class);
  }
}
