<div class='btn-group btn-group-sm'>
  @can('driverReviews.show')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('driverReviews.show', $id) }}" class='btn btn-link'>
    <i class="fa fa-eye"></i>
  </a>
  @endcan

  @can('driverReviews.edit')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.driver_review_edit')}}" href="{{ route('driverReviews.edit', $id) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a>
  @endcan

  @can('driverReviews.destroy')
{!! Form::open(['route' => ['driverReviews.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fa fa-trash"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-danger',
  'onclick' => "return confirm('Are you sure?')"
  ]) !!}
{!! Form::close() !!}
  @endcan
</div>
