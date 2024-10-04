<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Invoice</title>
<style>
@font-face {
  font-family: 'Poppins-Bold';
  src: url('{{ url("/") }}/pdf/css/fonts/Poppins-Bold.ttf') format("truetype");
  font-weight: normal;
  font-style: normal; 
}

@font-face {
  font-family: 'Poppins-Medium';
  src: url('{{ url("/") }}/pdf/css/fonts/Poppins-Medium.ttf') format("truetype");
  font-weight: normal;
  font-style: normal; 
}

@font-face {
  font-family: 'Poppins-Light';
  src: url('{{ url("/") }}/pdf/css/fonts/Poppins-Light.ttf') format("truetype");
  font-weight: normal;
  font-style: normal; 
}

@page {
  size: A4;
  margin: 0;
}

@media print {
  html, body {
    width: 210mm;
    height: 297mm;
  }
}

html {
  margin: 0;
  padding: 0;
}

body {
  background-color: white;
  padding: 15mm 20mm 20mm 20mm;
}

* {
  font-family: 'Poppins-Light', sans-serif !important;
  font-style: normal;
  font-stretch: normal;
  font-weight: normal;
  text-rendering: optimizeLegibility;
  color: #111111;
  margin: 0;
  padding: 0;
}

b, strong, .bold {
  font-family: 'Poppins-Medium', sans-serif !important;
}

h1 {
  color: #e94364;
}

p {
  margin: 0;
}

table td {
  vertical-align: top;
}

.cf::after {
  content: "";
  clear: both;
  display: table;
}

header.page-header {
  position: fixed;
  top: 10mm;
  left: 0;
  text-align: center;
  width: 100%;
}

footer.page-footer {
  font-size: 8pt;
  color: #777777;
  position: fixed;
  bottom: 10mm;
  left: 20mm;
  position: fixed;
  text-align: left;
  width: 100%;
}

/* Meta data */
.logo {
  display: inline-block;
  height: 20mm;
  position: fixed;
  top: 15mm;
  left: 20mm;
  width: 50mm;
  z-index: 100;
}

.logo img {
  display: block;
  height: auto;
  width: 100%;
}

.address {
  font-size: 10pt;
  line-height: 9pt;
  position: relative;
  top: 30mm;
}

/* Generic elements */
h1 {
  font-family: 'Poppins-Bold', sans-serif !important;
  font-size: 16pt;
  line-height: 1;
}

h2 {
  font-family: 'Poppins-Bold', sans-serif !important;
  font-size: 16pt;
  line-height: 1;
}

/* Invoice data */
.invoice-address {
  font-size: 10pt;
  line-height: 1;
  margin-top: 20mm;
}

header.invoice-header {
  margin-top: 50mm;
}

header.invoice-header h1 {
  float: left;
  margin: -1mm 0 0 0;
  width: 65%;
}

header.invoice-header table {
  float: left;
  font-size: 10pt;
  line-height: 1;
  margin: 0;
  padding: 0;
  width: 35%;
}

header.invoice-header table td:nth-child(2n+2) {
  text-align: right;
}

main.invoice-body {
  font-size: 10pt;
  line-height: 15pt;
  margin-top: 5mm;
  text-align: left;
}

table.invoice-positions,
table.invoice-positions.is-journal {
  font-size: 10pt;
  line-height: 1;
  margin-top: 5mm;
  width: 100%;
}


table.invoice-positions.is-journal {
  margin-top: 10mm;
}

table.invoice-positions td,
table.invoice-positions th {
  padding: 0;
  vertical-align: middle;
}

table.invoice-positions thead {
  border-bottom: .1mm solid #444444;
  line-height: 1;
}

table.invoice-positions thead th,
table.invoice-positions tr.position td,
table.invoice-positions tr.position-footer td {
  padding: 1.5mm 0 1.75mm 0;
}

table.invoice-positions thead th {
  font-family: 'Poppins-Bold', sans-serif !important;
}

table.invoice-positions tr.position td {
  border-bottom: .03mm solid #444444;
  vertical-align: top;
}

table.invoice-positions tr.position-footer td {
  border-bottom: .03mm solid #444444;
}

table.invoice-positions tr.position-footer--grandtotal td {
  font-family: 'Poppins-Medium', sans-serif !important;
  border-bottom: .3mm solid #444444;
}

.invoice-remarks {
  line-height: 1; 
  margin-top: 20px;
}

.position-periode {
  width: 12%;
}

.position-cost {
  width: 17%;
}

.position-description {
  width: 56%;
}

.position-amount {
  width: 15%;
}

.align-right {
  text-align: right;
}

.invoice-journal {
  margin-top: 30mm;
  margin-bottom: 0mm;
}

.invoice-vat-info {
  font-size: 9pt;
  line-height: 1;
  text-align: right;
  margin-top: 1mm;
}

.payment-info-box {
  border: .3mm solid #000;
  bottom: -20mm;
  font-size: 10pt;
  line-height: 0.9;
  left: 20mm;
  padding: 1mm;
  position: absolute;
  width: 70mm;
}

.payment-info-box table {
  width: 100%;
}

.payment-info-box td {
  padding: 1mm;
}

ul {
  margin-left: 16px
}

li {
  display: list-item;
  list-style-type: circle;
  line-height: 10pt;
  margin-bottom: 1mm;
}
</style>
</head>
<body>
<header class="page-header">
  <span class="logo">
    <img src="{{ asset('pdf/img/logo-marcelito.svg') }}" height="100" width="100">
  </span>
</header>
<div class="address">
  <strong>marceli.to</strong><br>Marcel Stadelmann<br>Letzigraben 149<br>8047 Zürich<br><br>m@marceli.to<br>078 749 74 09
</div>
<div class="payment-info-box">
  <table>
    <tr>
      <td>Bank</td>
      <td>Raiffeisenbank Weinland</td>
    </tr>
    <tr>
      <td>IBAN</td>
      <td>CH22 8080 8003 1865 2284 6</td>
    </tr>
    <tr>
      <td>Zugunsten</td>
      <td>Marcel Stadelmann<br>Letzigraben 149<br>8047 Zürich</td>
    </tr>
  </table>
</div>
<footer class="page-footer">
 Marcel Stadelmann &bull; Letzigraben 149 &bull; 8047 Zürich &bull; 078 749 74 09 &bull; m@marceli.to
</footer>