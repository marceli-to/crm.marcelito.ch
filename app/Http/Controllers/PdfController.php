<?php
namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Expense;

class PdfController extends Controller
{
  public function expense(Expense $expense)
  {
    $expense = Expense::findOrFail($expense->id);
    $pdf = PDF::loadView('pdf.expense', array('expense' => $expense));
    $pdf->setOption(['isRemoteEnabled' => true]);
    return $pdf->stream($this->_getFileName($expense));
  }

  private function _getFileName(Expense $expense)
  {
    return 'marceli.to-' . $expense->number . '-' . Str::slug($expense->title) . '-' . date('d.m.Y', strtotime($expense->date)) . '.pdf';
  }
}

