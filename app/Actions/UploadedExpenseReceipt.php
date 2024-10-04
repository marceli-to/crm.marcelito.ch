<?php
namespace App\Actions;
use App\Models\Expense;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Image;
use Spatie\Image\Enums\Fit;

class UploadedExpenseReceipt
{
  protected $maxSize = 2000;

  public function execute(Expense $expense, $receipt)
  {
    if ($receipt['extension'] == 'pdf')
    {
      $pdf = new \Spatie\PdfToImage\Pdf($receipt['path']);
      $pdf->save(str_replace('.pdf', '.jpg', $receipt['path']));
      $filename = $expense->number . '.jpg';
    }
    else
    {
      $filename = $expense->number . '.' . $receipt['extension'];
    }

    // Store the file
    Storage::disk('public')->putFileAs('expenses', new File($receipt['path']), $filename);

    // Resize the image
    $image = Image::load(Storage::disk('public')
      ->path('expenses/' . $filename))
      ->fit(Fit::Max, $this->maxSize, $this->maxSize)
      ->save();
    
    return $filename;
  }
}