@php
$section_seperator = "30px";

$totalPaymentCount = 0;
$totalPaidAmount = 0 ;

@endphp

@extends("layouts.print")

@push("styles")

  @page { size: A4}

  body{
  padding: 0px;
  margin:0px;
  }

  table {
  border-collapse: collapse;
  width: 100%;
  }

  th{
  solid;background-color:#ddd
  }

  section.sheet {
  font-size: 11px;
  page-break-inside: initial;
  }

  tr {
  page-break-inside: avoid;
  }

  table.has_border, table.has_border th, table.has_border td {
  border: 1px solid grey;
  padding: 0px 2px;
  text-align:center;
  line-height: 17px;
  }
  table.header_table{
  height: auto !important;
  }

  table.header_table td {
  font-size: 14px;
  line-height: 14px;
  }
  table.header_table td img {
  height: 80px;
  }
@endpush

@section("body-attributes") class="A4" style="font-family:Droid Arabic Naskh; font-size: 14px;" @endsection

@section("content")

  <section class="sheet" style=" padding: 20px 30px 10px 30px;">
    @include ('layouts.print_header')

    <table style="width: 100%;">
      <tr>
        <td style="text-align: left;">
          <table class="has_border" style="width: 150px; float:right;">
            <tr>
              <td>{{ $dateTo }}</td>
              <th>To</th>
            </tr>
          </table>
        </td>

        <td style="direction: ltr;">
          <h2 style="text-align:center; margin: 0px;">
            SARS Cov 2 Speed Report
          </h2>
        </td>

        <td>
          <table class="has_border" style="width: 150px; float:left; direction:ltr;">
            <tr>
              <th style="width: 50px;">From </th>
              <td style="min-width: 100px;"> {{ $dateFrom }} </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

    <table class="has_border" style="width:100%; direction: rtl;">
      <tr>

        <th rowspan="2">بەروار</th>

        <th colspan="2">کۆی گشتی</th>

      </tr>
      <tr>
        <th> پشکنین </th>
        <th> دینار </th>
      </tr>
      @foreach ($data as $rowData)
        <tr>
          <td> {{ $rowData[0]['payment_date'] }} </td>
          @foreach ($rowData as $colData)
              @php
                  $totalPaymentCount += $colData['payment_count'];
                  $totalPaidAmount += $colData['paid_amount'];
              @endphp

            <td>
              {{ $colData['payment_count'] }}
            </td>
            <td>
              {{ number_format($colData['paid_amount']) }}
            </td>
          @endforeach
        </tr>
      @endforeach
      <tr>
        <td> کۆی گشتی </td>
        <td> {{ $totalPaymentCount }} </td>
        <td> {{ number_format($totalPaidAmount) }} </td>
      </tr>
    </table>
  </section>
@endsection
