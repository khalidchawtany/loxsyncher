@isset($landscape)
    <img style="width:100%;" src="{{ asset('img/lox_home_office_letterhead_landscape.png') }}">
@else
    <img style="width:100%;" src="{{ asset('img/lox_home_office_letterhead.png') }}">
@endisset

@isset($in_header_info)
    <div style="  position: absolute;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  top: 5px;
                  right: 15px;
                  width: 45%;
                  height: 150px;
                  margin: auto; ">
        {!! $in_header_info !!}
    </div>
@endisset
