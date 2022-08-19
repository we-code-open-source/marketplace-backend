<div class='btn-group btn-group-sm'>
  @can('settlementDrivers.show')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('settlementDrivers.show', $id) }}" class='btn btn-link'>
    <i class="fa fa-eye"></i>
  </a>
  @endcan

  @can('settlementDrivers.edit')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.settlement_driver_edit')}}" href="{{ route('settlementDrivers.edit', $id) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a>
  @endcan

  @can('settlementDrivers.destroy')
{!! Form::open(['route' => ['settlementDrivers.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fa fa-trash"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-danger',
  'onclick' => "return confirm('Are you sure?')"
  ]) !!}
{!! Form::close() !!}
  @endcan
</div>
