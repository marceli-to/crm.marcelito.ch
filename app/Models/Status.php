<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use \Sushi\Sushi;

class Status extends Model
{
  use Sushi;

  protected $rows = [
    ['id' => 1, 'label' => 'open'],
    ['id' => 2, 'label' => 'pending'],
    ['id' => 3, 'label' => 'closed'],
  ];
}