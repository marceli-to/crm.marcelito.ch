<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use \Sushi\Sushi;

class Vat extends Model
{
  use Sushi;

  protected $rows = [
    ['id' => 1, 'value' => '7.7', 'label' => '7.7%'],
    ['id' => 2, 'value' => '8.1', 'label' => '8.1%'],
  ];
}