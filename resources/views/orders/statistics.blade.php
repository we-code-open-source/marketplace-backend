@extends('layouts.app')

@push('css_lib')
<!-- iCheck -->
<link rel="stylesheet" href="{{asset('plugins/iCheck/flat/blue.css')}}">
<!-- select2 -->
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endpush

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">{{trans('lang.order_plural')}}<small class="ml-3 mr-3">|</small><small>{{trans('lang.order_desc')}}</small></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="fa fa-dashboard"></i> {{trans('lang.dashboard')}}</a></li>
          <li class="breadcrumb-item"><a href="{!! route('orders.index') !!}">{{trans('lang.order_plural')}}</a>
          </li>
          <li class="breadcrumb-item active">{{trans('lang.order')}}</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<div class="content">
  <div class="card">
    <div class="card-header d-print-none">
      <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
        <li class="nav-item">
          <a class="nav-link" href="{!! route('orders.index') !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.order_table')}}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.statistics')}}</a>
        </li>
      </ul>
    </div>
    <div class="card-body pb-5">        
      
      {!! Form::open(['url' => 'orders/statistics', 'method' => 'get', 'id' => 'search-form']) !!}
      
        <div class="row">
            <!-- Order Status Id Field -->
            <div class="col-md-4">
              <div class="form-group row ">
                  {!! Form::label('order_status_id', trans("lang.order_order_status_id"),['class' => 'col-4 control-label text-right']) !!}
                  <div class="col-8">
                      {!! Form::select('order_status_id', $orderStatus, request('order_status_id'), ['class' => 'select2 form-control not-required']) !!}
                      <div class="form-text text-muted">{{ trans("lang.order_order_status_id_help") }}</div>
                  </div>
              </div>
            </div>
            
            <!-- From date Field -->
            <div class="form-group row ">
              {!! Form::label('from_date', "From date", ['class' => 'col-4 control-label text-right']) !!}
              <div class="col-8">
                  {!! Form::text('from_date', request('from_date'),  ['class' => 'form-control datepicker','autocomplete'=>'off','placeholder'=>  "From date"  ]) !!}
              </div>
            </div>

            <!-- To date Field -->
            <div class="form-group row ">
              {!! Form::label('to_date', "To date", ['class' => 'col-4 control-label text-right']) !!}
              <div class="col-8">
                  {!! Form::text('to_date', request('to_date'),  ['class' => 'form-control datepicker','autocomplete'=>'off','placeholder'=>  "To date"  ]) !!}
              </div>
            </div>

            <div class="col-auto">
              <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-search"></i> {{trans('lang.search')}}</button>
              <button type="button" class="btn btn-secondary" onclick="clearSearchOptions()">
                <i class="fa fa-rest"></i>{{trans('lang.reset')}}
              </button>
            </div>
        </div>
          {{-- @include('orders.fields') --}}

        {!! Form::close() !!}
      
        <hr/>
          
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">date</th>
              <th scope="col">Status</th>
              <th scope="col">Delivery fee</th>
              <th scope="col">count</th>
            </tr>
          </thead>
          <tbody>
              @foreach ($statistics as $s)              
                <tr>
                  <td>{{ $s->date }}</td>
                  <td>{{ $s->order_status }}</td>
                  <td>{{ $s->delivery_fee}}</td>
                  <td>{{ $s->count }}</td>
                </tr>
              @endforeach
                <tr>
                  <td colspan="10" class="text-center">
                    <h4 class="p-2">Total</h4>
                  </td>
                </tr>
                <tr>
                  <td>Items : {{  $statistics->count() }}</td>
                  <td>-------</td>
                  <td>{{ $statistics->sum('delivery_fee') }}</td>
                  <td>{{ $statistics->sum('count') }}</td>
                </tr>
          </tbody>
        </table>



      <div class="clearfix"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts_lib')
<!-- iCheck -->
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<!-- select2 -->
<script src="{{asset('plugins/select2/select2.min.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>

<script>

  function clearSearchOptions(){
    $('#search-form input , #search-form select ').val('').trigger('change');
  }

</script>
@endpush