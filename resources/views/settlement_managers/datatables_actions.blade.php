<div class='btn-group btn-group-sm'>
  @can('settlementManagers.show')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('settlementManagers.show', $id) }}" class='btn btn-link'>
    <i class="fa fa-eye"></i>
  </a>
  @endcan

  @can('settlementManagers.edit')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.settlement_manager_edit')}}" href="{{ route('settlementManagers.edit', $id) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a>
  @endcan

  @can('settlementManagers.destroy')
{!! Form::open(['route' => ['settlementManagers.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fa fa-trash"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-danger',
  'onclick' => "return confirm('Are you sure?')"
  ]) !!}
{!! Form::close() !!}
  @endcan
</div>
