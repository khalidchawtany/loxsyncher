@php
$section_seperator = '30px';

$from = null;
$to = null;

$filterRules = collect(json_decode(request('filterRules')));

$filterRules->each(function ($filterRule) use (&$from, &$to) {
    if ($filterRule->field == 'payments.date_time' && isset($filterRule->value)) {
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
    @page { size: landscape }

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

    .office_class {
    max-width: 100px;
    overflow: hidden;
    white-space: nowrap;
    }

    .plate_class {
    {{-- max-width: 60px; --}}
    }

    .product_name_class {
    max-width: 150px;
    overflow: hidden;
    white-space: nowrap;
    }

    .product_type_class {
    max-width: 150px;
    overflow: hidden;
    white-space: nowrap;
    }
@endpush

@section('body-attributes')
    class="landscape" style="font-family:Droid Arabic Naskh; font-size: 14px;"
@endsection


@section('content')
    @php
		$chunks = $transactions->chunk(20);
		$i = 1;
		$pageIndex = 0;
		$indexPageRowIndex = 0;
    @endphp

    {{-- Header of index page --}}
    <section class="sheet" style="padding:15px;">
        <table style="width: 99%; margin: 0 auto;">
            <tr>
                <td style="text-align: right; ">
                        <p> {!! \App\Models\AppSetting::get('site_name_formal_kurdish') !!} </p>
                    <p> ڕاپۆرتی داهاتی ڕۆژانەی پشکنین</p>
                    <p> بەشی ژمێریاری</p>
                </td>
                <td style="width: 200px; text-align: left; ">
                        <img style="height: 100px;" src="{{ App\Utils\AssetsHelper::img('lox_letterhead.png') }}" >
                </td>
            </tr>
        </table>

        {{-- Header of index page table --}}
        <table style="text-align: right; width: 99%;">
            <tr>
                <td style="width:33%;"> بەروار: {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }}</td>
                <td> </td>
                <td style="width:33%;"> </td>
            </tr>
        </table>

        {{-- Header of page table --}}
        <table class="has_border" style="text-align: right;">
            <tr>
                <th>ز.</th>
                <th>ژ. لاپەڕە</th>
                <th>هەژماری پسوڵە</th>
                <th>کۆی داهات</th>
                <th>پشکی وەزارەتی دارایی و ئابووری ٪٣٨</th>
                <th>پشکی کۆمپانیای لۆکس ٪٦٢</th>
            </tr>

            @foreach ($chunks as $chunk)
                @php
                    ++$indexPageRowIndex;
                @endphp

                <tr>
                    <td>{{ $indexPageRowIndex }}</td>
                    <td>{{ $indexPageRowIndex }}</td>
                    <td>{{ number_format($chunks[$indexPageRowIndex - 1]->count()) }}</td>
                    <td>{{ number_format($chunks[$indexPageRowIndex - 1]->sum('paid_amount')) }}</td>
                    <td>{{ number_format($chunks[$indexPageRowIndex - 1]->sum('paid_amount') * 0.38) }}</td>
                    <td>{{ number_format($chunks[$indexPageRowIndex - 1]->sum('paid_amount') * 0.62) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2">کۆ</td>
                <td>{{ number_format($transactions->count()) }}</td>
                <td>{{ number_format($transactions->sum('paid_amount')) }}</td>
                <td>{{ number_format($transactions->sum('paid_amount') * 0.38) }}</td>
                <td>{{ number_format($transactions->sum('paid_amount') * 0.62) }}</td>
            </tr>
        </table>
        <div style="page-break-inside: avoid;">

            <table style="margin-top: 40px; border-bottom:1px solid black;">
                <tr>
                    <td>
                        سەرپەرشتیاری نوسینگەی {{ \App\Models\AppSetting::get('site_name') }} / کۆمپانیای لۆکس
                    </td>
                    <td style="width: 30%;">
                        ناو و ئیمزا: {{ \App\Models\AppSetting::get('site_manager_name') }}
                    </td>
                    <td style="width: 20%;">
                        بەروار:
                    </td>
                </tr>

            </table>

            <table style="margin-top:10px;">
                <tr>
                    <td style="width: 40%;">
                        ژمێریاری نوسینگەی {{ \App\Models\AppSetting::get('site_name') }} / کۆمپانیای لۆکس
                    </td>
                  <td style="width: 30%;">
                    {{ \App\Models\AppSetting::get('site_customs_accountant') }}
                    </td>
                  <td style="width: 30%;">
                    {{ \App\Models\AppSetting::get('site_customs_inspector') }}
                    </td>
                </tr>
                <tr>
                    <td>
                        ناو و ئیمزا:
                    </td>
                    <td>
                        ناو و ئیمزا:
                    </td>
                    <td>
                        ناو و ئیمزا:
                    </td>
                </tr>
                <tr>
                    <td>
                        بەروار:
                    </td>
                    <td>
                        بەروار:
                    </td>
                    <td>
                        بەروار:
                    </td>
                </tr>

            </table>
        </div>
    </section>


    @foreach ($chunks as $chunk)
        @php
            $pageIndex++;
        @endphp
        <section class="sheet" style="padding:15px;">

            <table style="width: 99%; margin: 0 auto;">
                <tr>
                    <td style="width: 33%; text-align: right; ">
                        بەروار: {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }}
                    </td>
                    <td style="width: 33%; text-align: center; ">
                        Page {{ $pageIndex }} of {{ $chunks->count() }}
                    </td>
                    <td style="width: 33%; text-align: left; ">
                <img style="height: 75px;" src="{{ App\Utils\AssetsHelper::img('lox_letterhead.png') }}" >
                    </td>
                </tr>
            </table>

            <table class="has_border" style="width:100%;">
                <tr>
                    <th>ز.</th>
                    <th>پسوڵە</th>
                    <th>بڕ</th>
                    <th>ژ. بارهەڵگر</th>
                    <th>کاڵا</th>
                    <th>جۆری کاڵا</th>
                    <th>نوسینگە</th>
                </tr>
                @foreach ($chunk as $transactionTruck)
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $transactionTruck->invoice_number }}</td>
                        <td class="office_class">{{ number_format($transactionTruck->paid_amount) }}</td>
                        <td class="plate_class">{{ $transactionTruck->plate }}</td>
                        <td class="product_name_class">{{ $transactionTruck->product_name }}</td>
                        <td class="product_type_class">{{ $transactionTruck->product_type }}</td>
                        <td>{{ $transactionTruck->office_name }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="1">کۆ</td>
                    <td>{{ number_format($chunk->count()) }}</td>
                    <td>{{ number_format($chunk->sum('paid_amount')) }}</td>
                    <td colspan="4" style="border-color: white; "></td>
                </tr>
            </table>
        </section>
    @endforeach
@endsection
