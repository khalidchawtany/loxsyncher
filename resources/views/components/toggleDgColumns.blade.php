<a href="javascript:void(0)" id="toggleDgColumnMenuButton" class="easyui-menubutton" style="width: 150px;"
  data-options="plain:false,menu:'#{{ $dg }}_toggle_columns',iconCls:'icon-more'">
  Toggle Columns
</a>
<div id="{{ $dg }}_toggle_columns" class="jeasyui-menu">
  @foreach ($columns as $key => $col)
    @php
      $title = $key;
      if (is_numeric($key)) {
          $title = ucfirst(str_replace('-', '', str_replace('_', ' ', $col)));
      }
      
      $icon = 'icon-mini-add';
      if (starts_with($col, '-')) {
          // column is hidden
          $icon = '';
      }
      
    @endphp
    <div data-options="iconCls:'{{ $icon }}',name:'{{ str_replace('-', '', $col) }}'"
      onclick="toggleDgColumn(this, '{{ $dg }}')">

      {{ $title }}

    </div>
  @endforeach
</div>
