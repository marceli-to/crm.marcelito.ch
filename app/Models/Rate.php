<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use \Sushi\Sushi;

class Rate extends Model
{
  use Sushi;

  protected $rows = [
    ['id' => 1, 'label' => '125.00', 'description' => 'Low (old)'],
    ['id' => 2, 'label' => '135.00', 'description' => 'Low'],
    ['id' => 3, 'label' => '150.00', 'description' => 'High'],
  ];
}