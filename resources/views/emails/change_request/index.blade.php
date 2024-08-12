@php
  //dd( $change_request->user->kurdish_name  );
  //da($change_request);
@endphp
Hello,

<p>
{{ $change_request->user->kurdish_name  }} requested a change with the following detail:
</p>

<p>
  <b>Title:</b>
  {{ $change_request->title }}
</p>

<p>
  <b>Description:</b>
  {{ $change_request->description }}
</p>

<p>
  <b>Note:</b>
  {{ $change_request->note }}
</p>

<p>
  <b>Date:</b>
  {{ $change_request->created_at }}
</p>


