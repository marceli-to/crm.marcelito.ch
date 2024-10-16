<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use \Sushi\Sushi;

class Status extends Model
{
  use Sushi;

  protected $rows = [
    ['id' => 1, 'label' => 'open', 'color' => 'blue'],
    ['id' => 2, 'label' => 'pending', 'color' => 'amber'],
    ['id' => 3, 'label' => 'paid', 'color' => 'green'],
    ['id' => 4, 'label' => 'overdue', 'color' => 'red'],
    ['id' => 5, 'label' => 'cancelled', 'color' => 'zinc'],
  ];
}