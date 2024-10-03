@include('pdf.partials.header')
<div class="invoice-address">
  <strong>{{$data->client->name}}</strong><br>
  @if ($data->client->byline)
    {!! $data->client->byline!!}<br>
  @endif
  {{$data->client->street}}<br>
  {{$data->client->zip}} {{$data->client->city}}<br>
</div>
<header class="invoice-header cf">
  <h1>Rechnung</h1>
  <table cellspacing="0" cellpadding="0">
    <tr>
      <td>Rechnungs-Nr.:</td>
      <td>{{$data->number}}</td>
    </tr>
    <tr>
      <td>Datum:</td>
      <td>{{ date('d.m.Y', strtotime($data->date))}}</td>
    </tr>
    <tr>
      <td>MWST-Nr.:</td>
      <td>CHE-398.845.092 MWST</td>
    </tr>
    <tr>
      <td class="bold">Bezahlen bis:</td>
      <td class="bold">{{ date('d.m.Y', strtotime($data->date_due)) }}</td>
    </tr>
  </table>
</header>
<main class="invoice-body">
  <p>{{$data->title}}</p>
  @if ($data->positions)
    <table class="invoice-positions" cellspacing="0" cellpadding="0">
      <thead>
        <tr>
          <th class="position-periode">
            Periode
          </th>
          <th class="position-description">
            Beschreibung
          </th>
          <th class="position-cost">
            Aufwand
          </th>
          <th class="position-amount align-right">
            Betrag CHF
          </th>
        </tr>
      </thead>
      <tbody>
        @if (!isset($data['journal']))
          @foreach($data->positions as $position)
            <tr class="position">
              <td>{{ $position->periode }}</td>
              <td>{{ $position->description }}</td>
              @if ($position->is_flat)
                <td>Pauschal</td>
              @else
                <td>{{ $position->hours }} Std. à {{ $position->rate }}</td>
              @endif
              <td class="align-right">{{ number_format($position->amount, 2, '.', '\'') }}</td>
            </tr>
          @endforeach
        @else
          <tr class="position">
            <td>{{$data['journal']['periode']}}</td>
            <td>{{$data['journal']['description']}}</td>
            <td>{{$data['journal']['totalHours']}} Std.</td>
            <td class="align-right">{{ number_format($data->total, 2, '.', '\'') }}</td>
          </tr>
        @endif
        <tr class="position-footer">
          <td>Subtotal</td>
          <td></td>
          <td></td>
          <td class="position-total align-right">{{ number_format($data->total, 2, '.', '\'') }}</td>
        </tr>
        <tr class="position-footer">
          <td>MWST {{ $data->vat_rate }}%</td>
          <td></td>
          <td></td>
          <td class="position-total align-right">{{ number_format($data->vat, 2, '.', '\'') }}</td>
        </tr>
        <tr class="position-footer position-footer--grandtotal">
          <td>Total</td>
          <td></td>
          <td></td>
          <td class="position-total align-right">{{ number_format($data->grandtotal, 2, '.', '\'') }}</td>
        </tr>
      </tbody>
    </table>
  @endif

  @if ($data->text)
    <div class="invoice-remarks"><strong>Bemerkungen:</strong><br>{!! nl2br($data->text) !!}</div>
  @endif

</main>


@if (isset($data['journal']))
  <style>
    .page-break {
      page-break-after: always;
    }
  </style>
  <div class="page-break"></div>
  <div class="invoice-journal">
    <header class="invoice-header is-journal cf">
      <h1>Journal</h1>
      <table cellspacing="0" cellpadding="0">
        <tr>
          <td>Rechnungs-Nr.:</td>
          <td>{{$data->number}}</td>
        </tr>
        <tr>
          <td>Datum:</td>
          <td>{{ date('d.m.Y', strtotime($data->date))}}</td>
        </tr>
        <tr>
          <td>MWST-Nr.:</td>
          <td>CHE-398.845.092 MWST</td>
        </tr>
        <tr>
          <td class="bold">Bezahlen bis:</td>
          <td class="bold">{{ date('d.m.Y', strtotime($data->date_due)) }}</td>
        </tr>
      </table>
    </header>
    <table class="invoice-positions is-journal" cellspacing="0" cellpadding="0">
      <thead>
        <tr>
          <th class="position-periode">
            Periode
          </th>
          <th class="position-description">
            Beschreibung
          </th>
          <th class="position-amount align-right">
            Aufwand (Std.)
          </th>
        </tr>
      </thead>
      <tbody>
          
        @foreach($data->positions as $position)
          <tr class="position">
            <td>{{ $position->periode }}</td>
            <td>{{ $position->description }}</td>
            @if ($position->is_flat)
              <td>Pauschal</td>
            @else
              <td class="align-right">{{ $position->hours }}</td>
            @endif
          </tr>
        @endforeach

        <tr class="position-footer position-footer--grandtotal">
          <td>Total</td>
          <td></td>
          <td class="position-total align-right">{{$data['journal']['totalHours']}}</td>
        </tr>
      </tbody>
    </table>
  </div>
@endif

{{-- @include('pdf.partials.qr') --}}

@include('pdf.partials.footer')