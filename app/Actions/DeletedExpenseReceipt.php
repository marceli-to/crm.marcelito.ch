<?php
namespace App\Actions;
use App\Models\Expense;
use Illuminate\Support\Facades\Storage;

class DeletedExpenseReceipt
{
  public function execute(Expense $expense)
  {
    if ($expense->receipt)
    {
      if (Storage::disk('public')->exists('expenses/' . $expense->receipt))
      {
        Storage::disk('public')->delete('expenses/' . $expense->receipt);
      }
    }
  }
}