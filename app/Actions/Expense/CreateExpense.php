<?php
namespace App\Actions\Expense;
use App\Models\Expense;
use App\Actions\UploadedReceipt;

class CreateExpense
{
  public function execute(array $data)
  {
    // Create expense
    $expense = Expense::create([
      'date' => date('Y-m-d'),
      'title' => $data['title'],
      'description' => $data['description'],
      'amount' => $data['amount'],
      'currency_id' => $data['currency_id'],
    ]);

    $expense->number = date('y', time()) . '.' . str_pad($expense->id, 4, "0", STR_PAD_LEFT);
    $expense->save();
    return $expense;
  }
}
