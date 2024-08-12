@php
    $section_seperator = '30px';

    $from = null;
    $to = null;

    $paymentDate = getFilterRule('swabs.created_at');
    if (str_contains($paymentDate, ',')) {
        $from = str_before($paymentDate, ',');
        $to   = str_after($paymentDate, ',');
    }


    $show_header = request()->has('show_header');

@endphp

@extends('layouts.print')

@push('styles')

    @page { size: A4 }

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
@endpush

@section('body-attributes') class="A4" style="font-family:Droid Arabic Naskh; font-size: 14px;" @endsection


@section('content')

    <section class="sheet" style="padding:15px;">
        @if($show_header)
            @include('layouts.print_header')
            <hr/>
        @endif

        <table style="width: 100%;">
            <tr>
                <td style="text-align: left; width: 33%;">
                    <table class="has_border" style="width: auto; float:right;">
                        <tr>
                            <td style="min-width: 100px;"> {{ $from }} </td>
                            <th style="width: 50px;">From </th>
                        </tr>
                        <tr>
                            <td>{{ $to }}</td>
                            <th>To</th>
                        </tr>
                    </table>
                </td>

                <td style="direction: ltr; width: 34%;">
                    <h1 style="text-align:center;">
                        SARS COV-2 Report
                    </h1>
                </td>


                <td>
                    <table class="has_border" style="width: auto; float:left; direction:ltr;">
                        <tr>
                            <th style="width: 100px;">Total Amount</th>
                            <td style="min-width: 100px;"> {{ number_format($info['total_amount'], 2)}} </td>
                        </tr>
                        <tr>
                            <th>Patient Count</th>
                            <td>{{ $info['total_patient_count']}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="has_border" style="width:100%; direction: ltr;">
            <tr>
                <th>id</th>
                <th>Name</th>
                <th>Passport</th>
                <th>Payment Date</th>
                <th>Result Date</th>
                <th>Result</th>
                <th>Paid Amount</th>
            </tr>
            @foreach ($swabs as $swab)
                @php
                    $result = '<span style="color: lightgray;">Pending</span>';
                    if ($swab->result == '1') {
                        $result = '<span style="font-weight: bold;">Positive</span>';
                    } else if ($swab->result == '0') {
                        $result = '<span style="color: gray;">Negative</span>';
                    }
                @endphp
                <tr>
                    <td>{{ $swab->id}}</td>
                    <td style="text-align: left;">{{ $swab->person_name}}</td>
                    <td>{{ $swab->passport_number}}</td>
                    <td>{{ $swab->payment_date}}</td>
                    <td>{{ $swab->result_date}}</td>
                    <td>{!! $result !!}</td>
                    <td>{{ number_format($swab->paid_amount, 2) }}</td>
                </tr>
            @endforeach
        </table>
    </section>
@endsection
