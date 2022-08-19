
@extends('layouts.invoice')


@section('title')
تسوية حساب مطعم
@endsection


@section('headers')
    <ul>
        <li><b>رقم الفاتورة : </b><bdi>{{ $settlement->id }}</bdi></li>
        @if($settlement->created_at)
            <li><b>تاريخ العملية: </b><bdi>{{ $settlement->created_at->format('Y/m/d') }}</bdi></li>
        @endif
    </ul>
    <ul>
        <li><b>كود المطعم : </b><bdi>{{ $settlement->restaurant_id }}</bdi></li>
        <li><b>اسم المطعم : </b><bdi>{{ $settlement->restaurant->name }}</bdi></li>
    </ul>
@endsection


@section('content')

    <table class="table" style="margin-top: 20px;">
        <thead>
            <tr style="background-color: #bbb;">
                <th>#</th>
                <th>رقم الطلبية</th>
                <th>عمولة الشركة</th>
                <th>قيمة الطلبية</th>
                <th>التاريخ</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($settlement->orders as $order)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->fee }}</td>
                    <td>{{ $order->amount }}</td>
                    <td><bdi>{{ $order->created_at->format('Y-m-d g:ia') }}</bdi></td>
                </tr>
            @endforeach

        </tbody>
    </table>


    {{-- Start coupons summary --}}
    <table class="table" style="margin-top: 20px;">
        <thead>
            <tr style="background-color: #bbb;">
                <th>عدد كوبونات التوصيل</th>
                <th>عدد كوبونات الطلبيات</th>
                <th>قيمة كوبونات التوصيل+</th>
                <th>قيمة كوبونات الطلبيات</th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td>{{ $settlement->delivery_coupons_count }}</td>
                <td>{{ $settlement->restaurant_coupons_count }}</td>
                <td>{{ $settlement->delivery_coupons_amount }} <bdi>د.ل</bdi></td>
                <td>{{ $settlement->restaurant_coupons_amount }} <bdi>د.ل</bdi></td>
            </tr>

        </tbody>
    </table>
    {{-- End coupons summary --}}


    {{-- Start coupons summary --}}
    <table class="table" style="margin-top: 20px;">
        <thead>
            <tr style="background-color: #bbb;">
                <th>عدد كوبونات الشركة</th>
                <th>قيمة كوبونات الشركة-</th>
                <th>إجمالي العمولة</th> 
            </tr>
        </thead>
        <tbody>

            <tr>
                <td>{{ $settlement->restaurant_coupons_on_company_count }}</td>
                <td>{{ $settlement->restaurant_coupons_on_company_amount }} <bdi>د.ل</bdi></td>
                <td>{{ $settlement->amount }} <bdi>د.ل</bdi></td>
            </tr>

        </tbody>
    </table>
    {{-- End coupons summary --}}


    <table class="table" style="margin-top: 20px;">
        <thead>
            <tr style="background-color: #bbb;">
                <th>العمولة</th>
                <th>العدد</th>
                <th>الإجمالي</th>
                <th>الموظف</th>
                <th>التاريخ</th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td>{{ $settlement->fee }}<bdi>%</bdi></td>
                <td>{{ $settlement->count }}</td>
                <td>{{ $settlement->total  }} <bdi>د.ل</bdi></td>
                <td><bdi>{{ Auth::user()->name }}</bdi></td>
                <td><bdi>{{ date('Y-m-d g:ia') }}</bdi></td>
            </tr>

        </tbody>
    </table>
            
@endsection