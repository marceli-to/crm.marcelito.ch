<?php
namespace App\Actions\Expense;
use App\Models\Expense;
use Illuminate\Support\Facades\Storage;

class DeletedReceipt
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