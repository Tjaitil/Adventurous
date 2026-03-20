<table>
  <thead>
    <tr>
      <th>@lang('Level')</th>
      <th colspan="2">@lang('Crop')</th>
      <th>@lang('Location')</th>
      <th>@lang('Growth Time')</th>
      <th>@lang('Experience')</th>
      <th>@lang('Seeds Required')</th>
      <th>@lang('Min Yield')</th>
      <th>@lang('Max Yield')</th>
    </tr>
  </thead>
  <tbody>
    @foreach($crops as $crop)
      <tr>
        <td>{{ $crop['farmer_level'] }}</td>
        <td>
            <img class="w-9 h-9 max-w-fit" src="{{ $crop['image_url'] }}" alt="{{ $crop['crop_type'] }}" />
        </td>
        <td>
          {{ ucfirst($crop['crop_type']) }}
        </td>
        <td>{{ ucwords($crop['location']) }}</td>
        <td>{{ $crop['time'] }} @lang('min')</td>
        <td>{{ $crop['experience'] }}</td>
        <td>{{ $crop['seed_required'] }}</td>
        <td>{{ $crop['min_crop_count'] }}</td>
        <td>{{ $crop['max_crop_count'] }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
