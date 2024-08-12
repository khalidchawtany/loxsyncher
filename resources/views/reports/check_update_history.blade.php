<style type="text/css" media="screen">
    table.has_border {
        border-collapse: collapse;
        width: 100%;
        font-size: 14px;
    }

    table.has_border th{
        solid;background-color:#ddd;
        padding: 5px;
    }


    table.has_border tr {
        page-break-inside: avoid;
    }

    table.has_border, table.has_border th, table.has_border td {
        border: 1px solid grey;
        text-align:center;
    }
</style>

<div class="">
  Check id: {{ $history->first()->check_id }}
</div>

<div class="">
  Batch id: {{ $history->first()->batch_id }}
</div>

<div class="">
  Transaction id: {{ $history->first()->transaction_id }}
</div>


<table class="has_border">
    <tr>
        <th>Date</th>
        <th>User</th>
        <th>Old</th>
        <th>New</th>
    </tr>

    @foreach ($history as $row)
        @php
            $props = json_decode($row->props);
            $old = trim ( prettyPrint ( json_encode($props->old)) , '{}\t\n\r\0\x0B"') ;
            //dd($old);
            $new = trim ( prettyPrint ( json_encode($props->attributes)) , '{}\t\n\r\0\x0B"');
        @endphp
        <tr>
            <td>{{ $row->date}}</td>
            <td>{{ $row->user_name}}</td>
            <td style="text-align: left; padding: 3px">

                    {!! str_replace(",", "<br/>", $old)  !!}
            </td>
            <td style="text-align: left; padding: 3px">
                    {!! str_replace(",", "<br/>", $new)  !!}


              <div style="background-color: #DCFF66;">
                {{ optional($props->attributes)->reason }}
              </div>
            </td>

        </tr>
    @endforeach

</table>
