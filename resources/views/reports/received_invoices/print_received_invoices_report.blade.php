@php
$section_seperator = '30px';

$from = null;
$to = null;

$filterRules = collect(json_decode(request('filterRules')));

$filterRules->each(function ($filterRule) use (&$from, &$to) {
    if ($filterRule->field == 'received_invoices.received_at' && isset($filterRule->value)) {
        $value = $filterRule->value;
        $from = $value;

        if (str_contains($value, ',')) {
            $from = str_before($value, ',');
            $to = str_after($value, ',');
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

@section('body-attributes') class="A4 landscape" style="font-family:Droid Arabic Naskh; font-size:
14px;" @endsection


@section('content')

    <section class="sheet" style="padding:15px;">

        <table style="width: 100%;">
            <tr>
                <td>
                    <table class="has_border" style="width: auto; ">
                        <tr>
                            <th style="width: 100px;">بڕی پارە:</th>
                            <td style="min-width: 100px; direction:ltr;">
                                {{ number_format($info['received_invoices_amount']) }} IQD </td>
                        </tr>
                        <tr>
                            <th>عددی پسوڵە:</th>
                            <td>{{ $info['received_invoices_count'] }}</td>
                        </tr>
                    </table>
                </td>

                <td>
                    <h1
                        style="text-align:center; direction:ltr; margin-bottom: -10px; padding-bottom:0px;">
                        Received Invoices Report </h1>

                    <div style="text-align:center; direction:ltr;">
                        Date Printed: {{ now()->format('d/m/Y h:m:s A') }}
                        ({{ user()->kurdish_name }})
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
                <th>ژ. پسوڵە</th>
                <th>بڕ</th>
                <th>نووسینگە</th>
                <th>وەرگر</th>
                <th>کاتی وەرگرتن</th>
            </tr>
            @foreach ($received_invoices as $received_invoice)
                <tr>
                    <td>{{ $received_invoice->payment_id }}</td>
                    <td>{{ number_format($received_invoice->payment_amount) }}</td>
                    <td>{{ $received_invoice->office_name }}</td>
                    <td>{{ $received_invoice->received_by_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($received_invoice->received_at)->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </table>
    </section>
@endsection
