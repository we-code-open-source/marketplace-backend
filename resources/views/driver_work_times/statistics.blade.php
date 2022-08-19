@extends('layouts.app')

@push('css_lib')
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('plugins/iCheck/flat/blue.css') }}">
    <!-- select2 -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
@endpush

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ trans('lang.order_plural') }}<small
                            class="ml-3 mr-3">|</small><small>{{ trans('lang.order_desc') }}</small></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fa fa-dashboard"></i>
                                {{ trans('lang.dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{!! route('drivers.index') !!}">{{ trans('lang.order_plural') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('lang.order') }}</li>
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
                        <a class="nav-link" href="{!! route('drivers.index') !!}"><i
                                class="fa fa-list mr-2"></i>{{ trans('lang.order_table') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{!! url()->current() !!}"><i
                                class="fa fa-plus mr-2"></i>{{ trans('lang.statistics') }}</a>
                    </li>
                </ul>
            </div>
            <div class="card-body pb-5">

                {!! Form::open(['url' => 'driverWorkTimes/statistics', 'method' => 'get', 'id' => 'search-form']) !!}

                <div class="row">
                    <!-- Order Status Id Field -->
                    <div class="col-md-4">
                        <div class="form-group row ">
                            {!! Form::label('driver_id', trans('lang.order_driver_id'), ['class' => 'col-4 control-label text-right']) !!}
                            <div class="col-8">
                                {!! Form::select('driver', $drivers, request('driver'), ['class' => 'select2 form-control not-required']) !!}
                                <div class="form-text text-muted">{{ trans('lang.order_driver_id') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- From date Field -->
                    <div class="form-group row ">
                        {!! Form::label('from_date', 'From date', ['class' => 'col-4 control-label text-right']) !!}
                        <div class="col-8">
                            {!! Form::text('from_date', request('from_date'), ['class' => 'form-control datepicker', 'autocomplete' => 'off', 'placeholder' => 'From date']) !!}
                        </div>
                    </div>

                    <!-- To date Field -->
                    <div class="form-group row ">
                        {!! Form::label('to_date', 'To date', ['class' => 'col-4 control-label text-right']) !!}
                        <div class="col-8">
                            {!! Form::text('to_date', request('to_date'), ['class' => 'form-control datepicker', 'autocomplete' => 'off', 'placeholder' => 'To date']) !!}
                        </div>
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-{{ setting('theme_color') }}"><i class="fa fa-search"></i>
                            {{ trans('lang.search') }}</button>
                        <button type="button" class="btn btn-secondary" onclick="clearSearchOptions()">
                            <i class="fa fa-rest"></i>{{ trans('lang.reset') }}
                        </button>
                    </div>
                </div>
                {{-- @include('orders.fields') --}}

                {!! Form::close() !!}

                <hr />

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Driver</th>
                            <th scope="col">from</th>
                            <th scope="col">to</th>
                            <th scope="col">hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($driverWorkTimes as $s)
                            <tr>
                                <td>{{ $s->user->name }}</td>
                                <td>{{ $s->from_time }}</td>
                                <td>{{ $s->to_time }}</td>
                                <td>{{ time_to_human_hours_or_minutes($s->time) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="10" class="text-center">
                                <h4 class="p-2">Total</h4>
                            </td>
                        </tr>
                        <tr>
                            <td>Items : {{ $driverWorkTimes->count() }}</td>
                            <td>-------</td>
                            <td>-------</td>
                            <td>{{ time_to_human_hours_or_minutes($driverWorkTimes->sum('time')) }}</td>
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
    <script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
    <!-- select2 -->
    <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>

    <script>
        function clearSearchOptions() {
            $('#search-form input , #search-form select ').val('').trigger('change');
        }
    </script>
@endpush
