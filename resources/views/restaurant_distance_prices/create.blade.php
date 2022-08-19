@extends('layouts.app')
@push('css_lib')
    <!-- iCheck -->
    <link rel="stylesheet" href="{{asset('plugins/iCheck/flat/blue.css')}}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{trans('lang.restaurant_distance_price_plural')}}<small
                                class="ml-3 mr-3">|</small><small>{{trans('lang.restaurant_distance_price_desc')}}</small></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i
                                        class="fa fa-dashboard"></i> {{trans('lang.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a
                                    href="{!! route('restaurantDistancePrices.index') !!}">{{trans('lang.restaurant_distance_price_plural')}}</a>
                        </li>
                        <li class="breadcrumb-item active">{{trans('lang.restaurant_distance_price_create')}}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <div class="content">
        <div class="clearfix"></div>
        @include('flash::message')
        @include('adminlte-templates::common.errors')
        <div class="clearfix"></div>
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                    @can('restaurantDistancePrices.index')
                        <li class="nav-item">
                            <a class="nav-link" href="{!! route('restaurantDistancePrices.index') !!}"><i
                                        class="fa fa-list mr-2"></i>{{trans('lang.restaurant_distance_price_table')}}</a>
                        </li>
                    @endcan
                    <li class="nav-item">
                        <a class="nav-link active" href="{!! url()->current() !!}"><i
                                    class="fa fa-plus mr-2"></i>{{trans('lang.restaurant_distance_price_create')}}</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                {!! Form::open(['route' => 'restaurantDistancePrices.store']) !!}
                <div class="row">
                    @include('restaurant_distance_prices.fields')
                </div>
                {!! Form::close() !!}
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
@endsection
@push('scripts_lib')
    <!-- iCheck -->
    <script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
@endpush