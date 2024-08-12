@php
  //dd( $permission_request->user->kurdish_name  );
  //da($permission_request);
@endphp
Hello,

<p>
{{ $permission_request->user->kurdish_name  }} requested a permission with the following detail:
</p>

<p>
  <b>Requested For (User):</b>
  {{ $permission_request->for_user->kurdish_name }}
</p>

<p>
  <b>Perm/Role:</b>
  {{ $permission_request->permission_name }}
</p>

<p>
  <b>Description:</b>
  {{ $permission_request->description }}
</p>

<p>
  <b>Note:</b>
  {{ $permission_request->note }}
</p>

<p>
  <b>Date:</b>
  {{ $permission_request->created_at }}
</p>


