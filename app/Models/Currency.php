<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use \Sushi\Sushi;

class Currency extends Model
{
  use Sushi;

  protected $rows = [
    ['id' => 1, 'label' => 'CHF', 'description' => 'Swiss Franc'],
    ['id' => 2, 'label' => 'USD', 'description' => 'United States Dollar'],
    ['id' => 3, 'label' => 'EUR', 'description' => 'Euro'],
  ];
}