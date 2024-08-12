@php
    $section_seperator = '30px';

    $from = null;
    $to = null;

    $transactionDate = getFilterRule('transactions.date_time');
    if (str_contains($transactionDate, ',')) {
        $from = str_before($transactionDate, ',');
        $to   = str_after($transactionDate, ',');
    }

    $departmentId = getFilterRule('department_id');
    $departmentName = $departmentId == null
        ? null
        : \App\Models\Department::find($departmentId)->name;

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
                        {{ $departmentName != null? $departmentName : 'Sectors '}}
                        Goods
                    </h1>
                </td>


                <td>
                    <table class="has_border" style="width: auto; float:left; direction:ltr;">
                        <tr>
                            <th style="width: 100px;">Total Amount</th>
                            <td style="min-width: 100px;"> {{ number_format($info['total_product_amount'], 2)}} </td>
                        </tr>
                        <tr>
                            <th>Truck Count</th>
                            <td>{{ $info['total_product_count']}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="has_border" style="width:100%; direction: ltr;">
            <tr>
                {!! $departmentName == null ? '<th>Department</th>' : '' !!}
                <th>Product</th>
                <th>Truck Count</th>
                <th>Amount</th>
                <th>Unit</th>
            </tr>
            @foreach ($goodsSums as $goodsSum)
                <tr>
                    {!! $departmentName == null ? '<td>' . $goodsSum->department_name . '</td>': '' !!}
                    <td>{{ $goodsSum->product_name}}</td>
                    <td>{{ number_format($goodsSum->product_count) }}</td>
                    <td>{{ number_format($goodsSum->amount_sum,2) }}</td>
                    <td>{{ $goodsSum->amount_unit }}</td>
                </tr>
            @endforeach
        </table>
    </section>
@endsection
