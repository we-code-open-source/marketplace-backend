<div class='btn-group btn-group-sm'>
  @can('driverTypes.show')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('driverTypes.show', $id) }}" class='btn btn-link'>
    <i class="fa fa-eye"></i>
  </a>
  @endcan

  @can('driverTypes.edit')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.driver_type_edit')}}" href="{{ route('driverTypes.edit', $id) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a>
  @endcan

  @can('driverTypes.destroy')
{!! Form::open(['route' => ['driverTypes.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fa fa-trash"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-danger',
  'onclick' => "return confirm('Are you sure?')"
  ]) !!}
{!! Form::close() !!}
  @endcan
</div>
