
@php
    $departmentName = getFilterRule('departments.name');
    $section_seperator = '30px';

    $from = null;
    $to = null;

    $filterRules = collect(json_decode(request('filterRules')));

    $filterRules->each( function($filterRule) use(&$from, &$to) {

        if($filterRule->field == 'incineration_payments.date'
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
            <table style="width: 99%; margin: 0 auto;">
                <tr>
                    <td style="text-align: right; ">
                        <p> کۆمەڵگەی دەروازەی پەروێزخانی نێودەوڵەتی </p>
                        <p> ڕاپۆرتی داهاتی ڕۆژانەی سوتێنەر</p>
                        <p> بەشی ژمێریاری</p>
                    </td>
                    <td style="width: 200px; text-align: left; ">
                        <img style="height: 100px;" src="{{ App\Utils\AssetsHelper::img('lox_letterhead.png') }}" >
                    </td>
                </tr>
            </table>

        <table style="width: 100%; font-size:12px;">
            <tr>
                <td>
                    <table class="has_border" style="width: auto; ">
                        <tr>
                            <th style="width: 100px;">بڕی پارە:</th>
                            <td style="min-width: 100px; direction:ltr;"> {{ number_format($info['amount_sum'])}} IQD </td>
                        </tr>
                        <tr>
                            <th>ژ. سوتاندن:</th>
                            <td>{{ $info['incineration_count']}}</td>
                        </tr>
                    </table>
                </td>

                <td>
                    <h2 style="text-align:center; direction:ltr; margin-bottom: -10px; padding-bottom:0px;"> Financial Report (Incinerations) </h2>
                    <div style="text-align:center; direction:ltr;"> Date Printed: {{ now()->format('d/m/Y h:m:s A') }} </div>
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
                <th>Paid</th>
                <th>Office</th>
                <th>Type</th>
                <th>Product</th>
                {{-- <th>T. Date</th> --}}
                <th>Date</th>
                <th>Invoice Number</th>
            </tr>
            @foreach ($incinerations as $incineration)
                <tr>
                    <td>{{ number_format($incineration->paid_amount) }}</td>
                    <td>{{ $incineration->office_name }}</td>
                    <td>{{ str_limit($incineration->product_type, 60) }}</td>
                    <td>{{ $incineration->product_name }}</td>
                    {{-- <td>{{ \Carbon\Carbon::parse($incineration->transaction_date)->format('d/m/Y') }}</td> --}}
                    <td>{{ \Carbon\Carbon::parse($incineration->payment_date)->format('d/m/Y') }}</td>
                    <td>{{ $incineration->invoice_number }}</td>
                </tr>
            @endforeach
        </table>
    </section>
@endsection
