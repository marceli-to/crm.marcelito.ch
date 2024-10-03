<style>
.qr {
  border-top: .15mm dashed #ddd;
  top: 172mm;
  left: 0;
  font-weight: 400;
  color: #000000;
  height: 105mm;
  position: absolute;
  width: 210mm;
}

.qr * {
  font-family: Arial, Helvetica, sans-serif !important;
}

.qr-item {
  float: left;
  height: 95mm;
  padding: 5mm;
}

.qr-item.qr-item--receipt {
  height: 95mm;
  width: 52mm;
}

.qr-item.qr-item--payment {
  height: 95mm;
  width: 138mm;
}

.qr-item h1 {
  font-size: 11pt;
  font-weight: 700;
  height: 7mm;
}

.qr-item.qr-item--receipt h2 {
  font-size: 6pt;
  font-weight: 700;
  line-height: 9pt;
}

.qr-item.qr-item--receipt p {
  font-size: 8pt;
  font-weight: 400;
  line-height: 9pt;
  margin-bottom: 9pt;
}

.qr-item--receipt__information {
  height: 63mm;
}

.qr-item--receipt__information > div {
  margin-bottom: 9pt;
}

.qr-item--receipt__amount {
  height: 14mm;
  vertical-align: top;
}

.qr-item--receipt__amount .currency {
  display: inline-block;
  vertical-align: top;
  width: 12mm;
}

.qr-item--receipt__amount .amount {
  display: inline-block;
  vertical-align: top;
  width: 40mm;
}

.qr-item--receipt__acceptance {
  font-size: 6pt;
  font-weight: 700 !important;
  line-height: 8pt;
  text-align: right;
}

.qr-item--payment__left {
  float: left;
  width: 51mm;
}

.qr-item--payment__right {
  float: right;
  width: 87mm;
}

.qr-item--payment h1 {
  margin-bottom: 5mm;
}

.qr-item--payment__code {
  height: 46mm;
  width: 46mm;
}

.qr-item--payment__code img {
  display: block;
  height: 46mm; 
  width: 46mm; 
}

.qr-item--payment__amount {
  margin-top: 7.4mm;
}

.qr-item--payment__amount .currency {
  display: inline-block;
  vertical-align: top;
  width: 16mm;
}

.qr-item--payment__amount .amount {
  display: inline-block;
  vertical-align: top;
  width: 30mm;
}

.qr-item--payment__amount h2 {
  font-size: 8pt;
  line-height: 11pt;
  font-weight: bold;
}

.qr-item--payment__amount p {
  font-size: 10pt;
  line-height: 13pt;
}

.qr-item--payment__info > div {
  margin-bottom: 9pt;
}

.qr-item--payment__info h2 {
  font-weight: bold;
  font-size: 8pt;
  line-height: 11pt;
}

.qr-item--payment__info p {
  font-size: 10pt;
  line-height: 11pt;
}

.page-break {
  page-break-after: always;
}

</style>

<div class="page-break"></div>
<div class="qr cf">
  <div class="qr-item qr-item--receipt">
    <div class="qr-item--receipt__information">
      <h1>Empfangsschein</h1>
      <div>
        <h2>Konto / Zahlbar an</h2>
        <p>
          {{config('invoice.classic_iban')}}<br>
          {{config('invoice.beneficiary_name')}}<br>
          {{config('invoice.beneficiary_street')}}<br>
          {{config('invoice.beneficiary_city')}}<br>
        </p>
      </div>
      {{-- <div>
        <h2>Referenz</h2>
        <p>00 00000 00000 00000 00000 00011</p>
      </div> --}}
      <div>
        <h2>Zahlbar durch</h2>
        <p>
          {{$data->client->name}}<br>
          @if ($data->client->byline) {{$data->client->byline}}<br>@endif
          @if ($data->client->street) {{$data->client->street}}<br>@endif
          {{$data->client->zip}} {{$data->client->city}}
        </p>
      </div>
    </div>
    <div class="qr-item--receipt__amount">
      <div class="currency">
        <h2>Währung</h2>
        <p>CHF</p>
      </div>
      <div class="amount">
        <h2>Betrag</h2>
        <p>{{ number_format($data->grandtotal, 2, '.', ' ') }}</p>
      </div>
    </div>
    <div class="qr-item--receipt__acceptance">
      <p>Annahmestelle</p>
    </div>
  </div>
  <div class="qr-item qr-item--payment cf">
    <div class="qr-item--payment__left">
      <h1>Zahlteil</h1>
      <div class="qr-item--payment__code">
        <img src="{{$qr}}" height="100" width="100" style="">
      </div>
      <div class="qr-item--payment__amount">
        <div class="currency">
          <h2>Währung</h2>
          <p>CHF</p>
        </div>
        <div class="amount">
          <h2>Betrag</h2>
          <p>{{ number_format($data->grandtotal, 2, '.', ' ') }}</p>
        </div>
      </div>
    </div>
    <div class="qr-item--payment__right">
      <div class="qr-item--payment__info">
        <div>
          <h2>Konto / Zahlbar an</h2>
          <p>
            {{config('invoice.classic_iban')}}<br>
            {{config('invoice.beneficiary_name')}}<br>
            {{config('invoice.beneficiary_street')}}<br>
            {{config('invoice.beneficiary_city')}}<br>
          </p>
        </div>
        {{-- <div>
          <h2>Referenz<h2>
          <p>00 00000 00000 00000 00000 0001</p>
        </div> --}}
        <div>
          <h2>Zahlbar durch</h2>
          <p>
            {{$data->client->name}}<br>
            @if ($data->client->byline) {{$data->client->byline}}<br>@endif
            @if ($data->client->street) {{$data->client->street}}<br>@endif
            {{$data->client->zip}} {{$data->client->city}}
          </p> 
        </div>
      </div>
    </div>
  </div>
</div>