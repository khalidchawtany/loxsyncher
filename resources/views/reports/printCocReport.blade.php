@php
    $section_seperator = '30px';

    $from = null;
    $to = null;

    $filterRules = collect(json_decode(request('filterRules')));

    $filterRules->each( function($filterRule) use(&$from, &$to) {

        if($filterRule->field == 'transactions.date_time'
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
        padding: 5px;
        text-align:center;
    }
@endpush

@section('body-attributes') class="A4 landscape" style="font-family:Droid Arabic Naskh; font-size: 14px;" @endsection


@section('content')

    <section class="sheet" style="padding:15px;">

        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;">
                    <table class="has_border" style="width: auto; float:right;">
                        <tr>
                            <td style="min-width: 100px;"> {{ $from }} </td>
                            <th style="width: 100px;">From </th>
                        </tr>
                        <tr>
                            <td>{{ $to }}</td>
                            <th>To</th>
                        </tr>
                    </table>
                </td>

                <td style="direction: ltr;">
                    <h1 style="text-align:center;">Coc Report - {!! \App\Models\AppSetting::get('site_name') !!}</h1>
                </td>


                <td>
                    <table class="has_border" style="width: auto; float:left; direction:ltr;">
                        <tr>
                            <th style="width: 150px;">Total Amount</th>
                            <td style="min-width: 150px;"> {{ number_format($info['amount_paid_sum']) . ' IQD'}} </td>
                        </tr>
                        <tr>
                            <th>Total Inspected Trucks</th>
                            <td>{{ $info['transaction_count']}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="has_border" style="width:100%; direction: ltr;">
            <tr>
                <th>Inspection Date</th>
                <th>RD#</th>
                <th>Truck Plate</th>
                <th>Goods Description</th>
                <th>Quantity</th>
                <th>Type of Package</th>
                <th>Invoice Number</th>
                <th>Invoice Date</th>
                <th>Invoice Amount (IQD)</th>
            </tr>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y')}}</td>
                    <td>{{ $transaction->transaction_id }}</td>
                    <td>{{ $transaction->truck_plate }}</td>
                    <td>{{ $transaction->product_name }}</td>
                    <td>{{ number_format($transaction->amount) }}</td>
                    <td>{{ $transaction->unit }}</td>
                    <td>{{ $transaction->payment_id }}</td>
                    <td>{{ \Carbon\Carbon::parse( $transaction->payment_date_time )->format('d/m/Y')}}</td>
                    <td>{{ number_format($transaction->paid_amount) }}</td>
                </tr>
            @endforeach
        </table>
    </section>
@endsection
