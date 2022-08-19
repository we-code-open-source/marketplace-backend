<div class='btn-group btn-group-sm'>
  @can('settlementManagers.showAvailable')
    <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('settlementManagers.showAvailable', $restaurant_id) }}" class='btn btn-link'>
      <i class="fa fa-eye"></i>
    </a>
  @endcan
</div>
