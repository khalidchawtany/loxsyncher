@php
$section_seperator = '30px';


$invoice_currency = strtolower(\App\Models\AppSetting::get('invoice_currency'));
$isUsd = request('isUsd') == 'true';

$totalPaymentCount = 0;
$totalPaidAmount = 0;
$totalPaidAmountUsd = 0;

$landscape = true;

$departmentTotalStats = [];
foreach ($departmentNames as $departmentName) {
    $departmentTotalStats[$departmentName]['payment_count'] = 0;
    $departmentTotalStats[$departmentName]['paid_amount'] = 0;
    $departmentTotalStats[$departmentName]['paid_amount_usd'] = 0;
}

@endphp

@extends("layouts.print")

@push('styles')

  @page { size: A4 landscape}

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

@section('body-attributes') class="A4 landscape" style="font-family:Droid Arabic Naskh; font-size:
14px;" @endsection

@section('content')

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
          <h2 style="text-align:center; margin: 0px; display:inline;">
            Sectors Report
          </h2>
          <div class="" style=" position: absolute;
                  color:#aaa;
                  left: -30px;
                  top: 127px;
                  width: 100%;
                  text-align: right;
									font-size:10px;
               ">
            ({{ now() }}) {{ user()->kurdish_name }}
          </div>
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

        <th rowspan="2">Date</th>

        @foreach ($departmentNames as $departmentName)
          <th colspan="2">
            {{ $departmentName }}
          </th>
        @endforeach

        <th colspan="2">کۆی گشتی</th>

      </tr>
      <tr>
        @foreach ($departmentNames as $departmentName)
          <th>
            بار
          </th>
          <th>
          @if($isUsd)
            دۆلار
          @else
            دینار
          @endif
          </th>
        @endforeach
        <th>
          بار
        </th>
        <th>
          @if($isUsd)
            دۆلار
          @else
            دینار
          @endif
        </th>
      </tr>
      @foreach ($data as $rowData)
        <tr>
          <td>
            {{ $rowData[0]['payment_date'] }}
          </td>
          @foreach ($rowData as $colData)
            @php

              if (!isset($colData->paid_amount_usd)) {
                $colData['paid_amount_usd'] = 0;
              }
              $departmentTotalStats[$colData['department_name']]['paid_amount'] += $colData['paid_amount'];
              $departmentTotalStats[$colData['department_name']]['paid_amount_usd'] += $colData['paid_amount_usd'];
              $departmentTotalStats[$colData['department_name']]['payment_count'] += $colData['payment_count'];
            @endphp
            <td>
              {{ $colData['payment_count'] }}
            </td>
            <td>
              @if($isUsd)
                {{ number_format($colData['paid_amount_usd']) }}
              @else
                {{ number_format($colData['paid_amount']) }}
              @endif
            </td>
          @endforeach
          <td>
            @php
              $rowPaymentCount = 0;

              foreach ($rowData as $colData) {
                  if ($colData['department_name'] != \App\Models\Incineration::$DEPARTMENT_NAME) {
                      $rowPaymentCount += $colData['payment_count'];
                  }
              }
              $totalPaymentCount += $rowPaymentCount;
            @endphp
            {{ $rowPaymentCount }}
          </td>
          <td>
            @php
              $rowPaidAmount = 0;
              $rowPaidAmountUsd = 0;
              foreach ($rowData as $colData) {

                  if (!isset($colData->paid_amount_usd)) {
                    $colData['paid_amount_usd'] = 0;
                  }
                  $rowPaidAmount += $colData['paid_amount'];
                  $rowPaidAmountUsd += $colData['paid_amount_usd'];
              }
              $totalPaidAmount += $rowPaidAmount;
              $totalPaidAmountUsd += $rowPaidAmountUsd;
            @endphp

            @if($isUsd)
              {{ number_format($rowPaidAmountUsd) }}
            @else
              {{ number_format($rowPaidAmount) }}
            @endif
          </td>

        </tr>
      @endforeach
      <tr>

        <td>
          کۆی گشتی
        </td>

        @foreach ($departmentNames as $departmentName)
          <td>
            {{ $departmentTotalStats[$departmentName]['payment_count'] }}
          </td>
          <td>

            @if($isUsd)
              {{ number_format($departmentTotalStats[$departmentName]['paid_amount_usd']) }}
            @else
              {{ number_format($departmentTotalStats[$departmentName]['paid_amount']) }}
            @endif
          </td>
        @endforeach
        <td>
          {{ $totalPaymentCount }}
        </td>
        <td>
          @if($isUsd)
            {{ number_format($totalPaidAmountUsd) }}
          @else
            {{ number_format($totalPaidAmount) }}
          @endif
        </td>

      </tr>
    </table>
  </section>
@endsection
