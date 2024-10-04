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

  public function execute($number, $receipt)
  {
    // Set the extension
    $extension = $receipt['extension'];

    // Convert PDF to JPG
    if ($receipt['extension'] == 'pdf')
    {
      $pdf = new \Spatie\PdfToImage\Pdf($receipt['path']);
      $pdf->save(str_replace('.pdf', '.jpg', $receipt['path']));

      // Override the extension
      $extension = 'jpg';
    }

    // Set the filename
    $filename = $number . '.' . $extension;

    // Store the image
    Storage::disk('public')->putFileAs('expenses', new File($receipt['path']), $filename);

    // Resize the image to a maximum of 2000x2000 pixels
    $image = Image::load(Storage::disk('public')
      ->path('expenses/' . $filename))
      ->fit(Fit::Max, $this->maxSize, $this->maxSize)
      ->save();
    
    return $filename;
  }
}