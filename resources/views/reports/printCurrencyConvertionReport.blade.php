
@php
    $departmentName = getFilterRule('departments.name');
    $section_seperator = '30px';

    $from = null;
    $to = null;

    $filterRules = collect(json_decode(request('filterRules')));

    $filterRules->each( function($filterRule) use(&$from, &$to) {

        if($filterRule->field == 'payments.date_time'
            && isset($filterRule->value)) {

            $value = $filterRule->value;
            $from = $value;

            if (str_contains($value, ',')) {
                $from = str_before($value, ',');
                $to   = str_after($value, ',');
            }
        }
    });
@endphp

@extends('layouts.print')

@push('styles')

    @page { size: A4 landscape }

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
        page-break-inside: initial;
    }

    tr {
        page-break-inside: avoid;
    }

    table.has_border, table.has_border th, table.has_border td {
        border: 1px solid grey;
        padding: 0px 5px;
        text-align:center;
    }

    table.has_border.is_minimal , table.has_border.is_minimal th, table.has_border.is_minimal td {
        padding: 0px 3px;
        font-size: 11px;
        line-height: 18.5px;
    }
@endpush

@section('body-attributes') class="A4 landscape" style="font-family:Droid Arabic Naskh; font-size: 14px;" @endsection


@section('content')

    <section class="sheet" style="padding:15px;">

        <table style="width: 100%;">
            <tr>
                <td style="width:225px;">
                </td>

                <td>
                    <h1 style="text-align:center; direction:ltr; margin-bottom: -10px; padding-bottom:0px;"> Currency Convertion Report </h1>

                    <div style="text-align:center; direction:ltr; font-size: 11px;">
                      Printed by <span style="font-weight: bold;">{{ user()->kurdish_name }}</span>,
                      at: <span style="font-weight: bold;">{{ now()->format('d/m/Y h:m:s A') }}</span>
                      - {{  config('app.site_name') }}
										</div>
                </td>


                <td style="text-align: left;">
                    <table class="has_border" style="width: auto; float:left;">
                        <tr>
                            <th style="width: 100px;">لە: </th>
                            <td style="min-width: 100px;"> {{ $from }} </td>
                        </tr>
                        <tr>
                            <th>بۆ:</th>
                            <td>{{ $to }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="has_border is_minimal" style="width:100%;">
            <tr>
                <th>Department</th>
                <th>Office</th>
                <th>Product</th>
                <th>Type</th>
                <th>Date</th>
                <th># Transaction</th>
                <th># Payment</th>
                <th># Invoice</th>
                <th>USD/IQD</th>
                <th>Paid (IQD)</th>
                <th>Diff IQD</th>
                <th>Paid (USD)</th>
                <th>Diff USD</th>
            </tr>
            @foreach ($transactions as $transaction)
                <tr>
                  <td style="text-align:right;">{{ $transaction->department_name }}</td>
                    <td style="text-align:right;">{{ $transaction->office_name }}</td>
                    <td style="text-align:right;">{{ $transaction->product_name }}</td>
                    <td style="text-align:right;">{{ str_limit($transaction->product_type, 60) }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->payment_date)->format('d/m/Y') }}</td>
                    <td>{{ $transaction->transaction_id }}</td>
                    <td>{{ $transaction->payment_id }}</td>
                    <td>{{ $transaction->invoice_number }}</td>
                    <td>{{ number_format($transaction->currency_convertion_ratio) }}</td>
                    <td>{{ number_format($transaction->paid_amount) }}</td>
                    <td>{{ number_format($transaction->diff_iqd) }}</td>
                    <td>{{ number_format($transaction->paid_amount_usd, 2) }}</td>
                    <td>{{ number_format($transaction->diff_usd, 2) }}</td>
                </tr>
            @endforeach
            <tr>
              <th  colspan="2">ژ. مامەڵە: {{ $info['transaction_count']}} </th>
              <th colspan="7">کۆی گشتی</th>
              <th>  {{ number_format($info['sum_paid_amount'])}} </th>
              <th>{{ number_format($info['sum_diff_iqd'])}}</th> 
              <th> {{ number_format($info['sum_paid_amount_usd'], 2)}} </th>
              <th> {{ number_format($info['sum_diff_usd'], 2)}} </th>
            </tr>
        </table>

    </section>
@endsection
