@include('pdf.partials.header')
<style>
.payment-info-box {
  display: none !important;
}
</style>
<header class="invoice-header cf">
  <h1>Ausgabe</h1>
</header>
<main class="invoice-body">
  <div>
    <strong>{{$expense->title}}</strong>
  </div>
  <table class="invoice-positions" cellspacing="0" cellpadding="0" style="margin-top: 2mm; border-top: .03mm solid #000000;">
    <tbody>
      <tr class="position">
        <td>{{$expense->description}}, {{ date('d.m.Y', strtotime($expense->date))}}</td>
        <td class="align-right">{{ number_format($expense->amount, 2, '.', '\'') }}</td>
      </tr>
      <tr class="position-footer position-footer--grandtotal">
        <td>Total {{$expense->currency->label }}</td>
        <td class="position-total align-right">{{ number_format($expense->amount, 2, '.', '\'') }}</td>
      </tr>
    </tbody>
  </table>
  <div>
  </div>
</main>
<style>
  .page-break {
    page-break-after: always;
  }
</style>
@if ($expense->receipt)
<div class="page-break"></div>
<div style="margin-top: 35mm; max-height: 220mm; max-width: 160mm; overflow: hidden; margin-left: auto; margin-right: auto">
  <img src="{{ storage_path('app/public/expenses/'. $expense->receipt) }}" width="100" style="width: auto; max-width: 100%; height: auto; display: block;">
</div>
@endif
@include('pdf.partials.footer')
