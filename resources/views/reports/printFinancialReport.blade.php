
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
                <td>
                    <table class="has_border" style="width: auto; ">
                        <tr>
                            <th style="width: 100px;">بڕی پارە:</th>
                            <td style="min-width: 100px; direction:ltr;"> {{ number_format($info['paid_amount'])}} IQD </td>
                        </tr>
                        <tr>
                            <th>ژ. مامەڵە:</th>
                            <td>{{ $info['transaction_count']}}</td>
                        </tr>
                    </table>
                </td>

                <td>
                    <h1 style="text-align:center; direction:ltr; margin-bottom: -10px; padding-bottom:0px;"> Financial Report (Transactions) </h1>

                    <div style="text-align:center; direction:ltr;">
												Date Printed: {{ now()->format('d/m/Y h:m:s A') }} -   {{ $departmentName }} ({{ user()->kurdish_name }})
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
                <th>Result</th>
                <th>Paid</th>
                <th>Office</th>
                <th>Type</th>
                <th>Product</th>
                {{-- <th>T. Date</th> --}}
                <th>Date</th>
                <th>Transaction</th>
                <th>Payment</th>
                <th>Invoice Number</th>
            </tr>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->result }}</td>
                    <td>{{ number_format($transaction->paid_amount) }}</td>
                    <td>{{ $transaction->office_name }}</td>
                    <td>{{ str_limit($transaction->product_type, 60) }}</td>
                    <td>{{ $transaction->product_name }}</td>
                    {{-- <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</td> --}}
                    <td>{{ \Carbon\Carbon::parse($transaction->payment_date)->format('d/m/Y') }}</td>
                    <td>{{ $transaction->transaction_id }}</td>
                    <td>{{ $transaction->payment_id }}</td>
                    <td>{{ $transaction->invoice_number }}</td>
                </tr>
            @endforeach
        </table>
    </section>
@endsection
