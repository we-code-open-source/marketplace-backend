@extends('layouts.app')

@section('content')


@push('css_lib')
@include('layouts.datatables_css')
@endpush


<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">{{trans('lang.order_plural')}}
                    <small class="ml-3 mr-3">|</small>
                    <small>{{trans('lang.order_waitting_drivers_plural')}}</small>
                </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{url('/')}}">
                            <i class="fa fa-dashboard"></i>
                            {{trans('lang.dashboard')}}
                        </a>
                    </li>
                    <li class="breadcrumb-itema ctive">
                        <a href="{!! route('orders.index') !!}">{{trans('lang.order_plural')}}</a>
                    </li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<!-- /.content-header -->
<div class="content">

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ request()->url() }}"><i class="fa fa-list mr-2"></i>Orders</a>
                </li>
                <div class="ml-auto d-inline-flex">
                    <li class="nav-item">
                        <a class="nav-link pt-1" id="refreshDatatable" href="#">
                            <i class="fa fa-refresh"></i>
                            Refresh
                        </a>
                    </li>
                </div>
            </ul>
        </div>
        <div class="card-body">
            <div class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                <table id="dataTableOrders" class="table table-striped table-bordered" style="width:100%">
                </table>
            </div>

            <div class="clearfix"></div>
        </div>
    </div>

</div>


<!-- Start model confirm set driver for order -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Set driver for order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <bdi>
                    هل أنت متأكد أنك تريد إعطاء الطلب
                    #<span id="dataOrdersId" class="font-weight-bold"></span>
                    للسائق
                    <span id="dataOrdersDriver" class="font-weight-bold"></span>
                    ؟
                </bdi>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a target="_blank" id="dataOrdersSetDriverLink" class="btn btn-primary"
                    onclick="close_confirm_set_driver_modal()">Select</a>
            </div>
        </div>
    </div>
</div>
<!-- End model confirm set driver for order -->



<!--Firestore Libraries-->
{{--
<script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-firestore.js"></script> --}}

{{-- @include('vendor.notifications.init_firebase') --}}

@section('extra-js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" type="text/javascript"></script>
@include('layouts.datatables_js')

<script>

    /*  drivers data from back-end , to use them in get names of drivers */
    var drivers = JSON.parse(`{!! json_encode($drivers) !!}`);

    var db_orders = []; /* to save firestore drivers */
    var db = firebase.firestore();

    var dt_orders = null; /* datatable object */

    function set_data_for_datatable(dataSet) {
        if (dt_orders) {
            dt_orders.destroy();
        }

        $(document).ready(function () {
            dt_orders = $('#dataTableOrders').DataTable({
                data: dataSet,
                columns: [
                    {
                        data: 'id',
                        title: "Order ID",
                        "render": function (data, type, row, meta) {
                            return `<a href="/orders/${data}">${data}</a>`;
                        }
                    },
                    {
                        data: 'restaurant.id',
                        title: "Restaurant",
                        "render": function (data, type, row, meta) {
                            return `<a href="/restaurants/${data}/edit">${row.restaurant.name}</a>`;
                        }
                    },
                    {
                        data: 'drivers',
                        title: "Drivers",
                        'render': function (data, type, row, meta) {
                            r = '';
                            data && data.map(function (e) {
                                r += `
                                    <div class="py-1">
                                        <a href="/users/${e}/edit">${drivers[e] ?? e}</a>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="prepare_set_driver_for_order(${row.id},${e},'${drivers[e] ?? e}')">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </div>
                                `;
                                return e;
                            });
                            return r;
                        }
                    },
                    {
                        data: 'created_at',
                        title: "Created at",
                        'render': function (data) {
                            return moment(data * 1000).format("YYYY-MM-DD hh:mmA");
                        }
                    },
                    {
                        data: 'created_at_since',
                        title: "created at since",
                        'render': function (data, type, row, meta) {
                            return moment(row.created_at * 1000).fromNow()
                        }
                    },
                ]
            });
        });
    }

    function getOrdersFromFirestore() {
        const observer = db.collection('orders').onSnapshot(snapshot => {
            db_orders = [];
            snapshot.forEach((doc) => {
                db_orders.push(doc.data());
            });
            set_data_for_datatable(db_orders || []);
        }, e => {
            console.log(e);
            alert(e.message);
        })
    }

    getOrdersFromFirestore();

    function prepare_set_driver_for_order(order_id, driver_id, driver_name) {
        let link = "{{ url('/orders/set-driver') }}";
        document.getElementById('dataOrdersId').innerHTML = order_id;
        document.getElementById('dataOrdersDriver').innerHTML = driver_name;
        document.getElementById('dataOrdersSetDriverLink').setAttribute("href", `${link}/${order_id}/${driver_id}`);
        $('#exampleModal').modal('show');
    }


    function close_confirm_set_driver_modal() {
        $('#exampleModal').modal('hide');
    }

</script>

@endsection


@endsection