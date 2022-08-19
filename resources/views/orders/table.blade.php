@push('css_lib')
@include('layouts.datatables_css')
@endpush

@php
    $searchFields = [
        ["name" => "restaurant","data-column" => 1, "title" => trans('lang.restaurant')],
        ["name" => "client","data-column" => 2, "title" => trans('lang.order_user_id')],
        ["name" => "driver","data-column" => 3, "title" => trans('lang.order_driver_id')],
        ["name" => "order status","data-column" => 4, "title" => trans('lang.order_order_status_id')],
        /* ["name" => "delivery fee","data-column" => 5, "title" => trans('lang.order_delivery_fee')], */
    ]
@endphp

{{-- Start customer search fields --}}
<form id="myCustomeSearchForm" novalidate>
    <div class="form-row">
        @foreach ($searchFields as $f)            
            <div class="col-md-3">
                <label for="validationCustom{{$f['name']}}">{{ $f['title'] }}</label>
                <input type="text" class="form-control searchDTFields" data-column="{{ $f['data-column'] }}" id="validationCustom{{$f['name']}}">
            </div>
        @endforeach
        {{-- <div class="col-auto align-self-end">
            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
        </div> --}}
    </div>
</form>
{{-- End customer search fields --}}

<hr/>

{!! $dataTable->table(['width' => '100%'],true) !!}

@push('scripts_lib')
@include('layouts.datatables_js')
{!! $dataTable->scripts() !!}

<script> 
   /*  $('#myCustomeSearchForm').submit(function(e){
        e.preventDefault();
        LaravelDataTables["dataTableBuilder"].columns($(this).data('column'))
        .search($(this).val())
        .draw();
    }); */
     $(".searchDTFields").keyup(function(){
        LaravelDataTables["dataTableBuilder"].columns($(this).data('column'))
        .search($(this).val())
        .draw();
    });
</script>
@endpush