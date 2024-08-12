@php
  //da($rows->first());
    $from = null;
    $to = null;

    $filterRules = collect(json_decode(request('filterRules')));

    $filterRules->each( function($filterRule) use(&$from, &$to) {

        if($filterRule->field == 'checks.created_at'
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

    @page { size: A4 }

    body{
        padding: 0px; margin:0px; }

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
        padding: 0px 0px;
        font-size: 11px;
        line-height: 12.5px;
        width: 100%;
    }
@endpush
@include('styles')
@section('body-attributes') class="A4" style="font-family:Droid Arabic Naskh; font-size: 14px;" @endsection
@section('content')
    <section class="sheet" style="padding:15px;">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: right; width: 100px;">
                    <table class="has_border">
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

                <td>
                    <h1 style="text-align:center; direction:ltr;">
                      Test Changes Report
                    </h1>
                </td>


            </tr>
        </table>
        <table class="has_border small ltr" style="width:100%;">
            <tr>
                <th>Id</th>
                <th>Status</th>
                <th>Date</th>
                <th>Update Count</th>
                <th>Product Name</th>
                <th>Product Type</th>
                <th>User</th>
                <th>Lab</th>
                <th>Test</th>
            </tr>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->check_status }}</td>
                    <td>{{ $row->check_date }}</td>
                    <td>{{ $row->update_count }}</td>
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->product_type }}</td>
                    <td>{{ $row->user_name }}</td>
                    <td>{{ $row->lab }}</td>
                    <td>{{ $row->test }}</td>
                </tr>
                @if($row->activities)
                  <tr>
                    <th colspan="5">Old Values</th>
                    <th colspan="5">New Values</th>
                  </tr>
                @endif
                @foreach($row->activities as $activity)
                  @php
                    $causer_name = $causers->where('id',$activity['causer_id'])->first()->kurdish_name;
                    //dd($activity->properties['attributes']);
                    $props = json_decode($activity->properties);
                    $old = $props->old;
                    $new = $props->attributes;
                  @endphp

                  <tr>
                    <td colspan="5">
                      User: {{ $causer_name }}
                    </td>
                    <td colspan="5">
                      Date/Time: {{ $activity->created_at }}
                    </td>
                  </tr>
                  <tr>
                    <td  class="w-50p" style="padding:0px;" colspan="5">
                      <table class="small">
                        @foreach($new as $key => $field)
                        <tr>
                          <td class="w-50p"> {{ $key }}: </td>
                          <td> {{ $field }} </td>
                        </tr>
                        @endforeach
                      </table>
                    </td>
                    <td  class="w-50p" style="padding:0px;" colspan="5">
                      <table class="small">
                        @foreach($old as $key => $field)
                        <tr>
                          <td class="w-50p"> {{ $key }}: </td>
                          <td> {{ $field }} </td>
                        </tr>
                        @endforeach
                      </table>
                    </td>
                  </tr>
                @endforeach
            @endforeach
        </table>
    </section>
@endsection
