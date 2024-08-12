@php
    $section_seperator = '30px';

    $from = null;
    $to = null;

    $paymentDate = getFilterRule('payment_date');
    if (str_contains($paymentDate, ',')) {
        $from = str_before($paymentDate, ',');
        $to   = str_after($paymentDate, ',');
    }

    $departmentId = getFilterRule('department_id');
    $departmentName = $departmentId == null
        ? null
        : \App\Models\Department::find($departmentId)->name;

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

        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;">
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

                <td style="direction: ltr;">
                    <h1 style="text-align:center;">
                        {{ $departmentName != null? $departmentName : 'Sectors '}}
                        Report
                    </h1>
                </td>


                <td>
                    <table class="has_border" style="width: auto; float:left; direction:ltr; font-size: 13px; line-height: 18px;">
                        <tr>
                            <th style="width: 120px;">Invoice Amount</th>
                            <td style="min-width: 150px;"> {{ number_format($info['total_paid_amount']) . ' IQD'}} </td>
                        </tr>
                        <tr>
                            <th>S. Invoice Amount</th>
                            <td> {{ number_format($info['total_paid_amount_8_percent']) . ' IQD'}} </td>
                        </tr>
                        <tr>
                            <th>Invoice Count</th>
                            <td>{{ $info['total_payment_count']}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="has_border" style="width:100%; direction: ltr;">
            <tr>
                {!! $departmentName == null ? '<th>Department</th>' : '' !!}
                <th>Invoice Date</th>
                <th>Invoice Amount (IQD)</th>
                <th>Invoice Count</th>
                <th>Amount (Ton)</th>
                <th>Sample Count</th>
            </tr>
            @foreach ($dailyPaymentSums as $dailyPaymentSum)
                <tr>
                    {!! $departmentName == null ? '<td>' . $dailyPaymentSum->department_name . '</td>': '' !!}
                    <td>{{ \Carbon\Carbon::parse($dailyPaymentSum->payment_date)->format('d/m/Y')}}</td>
                    <td>{{ number_format($dailyPaymentSum->paid_amount) }}</td>
                    <td>{{ $dailyPaymentSum->payment_count}}</td>
                    <td>{{ $dailyPaymentSum->amount_sum}}</td>
                    <td>{{ $dailyPaymentSum->batch_count}}</td>
                </tr>
            @endforeach
        </table>
    </section>
@endsection
