<table>
  <thead>
    <tr>
      <th>@lang('Efficiency')</th>
      <th>@lang('Required Farmer Level')</th>
      <th>@lang('Upgrade Cost')</th>
    </tr>
  </thead>
  <tbody>
    @foreach($upgrades as $upgrade)
      <tr>
        <td>{{ $upgrade['id'] }}</td>
        <td>{{ $upgrade['level'] }}</td>
        <td>
          @if($upgrade['price'] === 0)
            @lang('Starting')
          @else
            {{ $upgrade['price'] }} @lang('Gold')
          @endif
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
