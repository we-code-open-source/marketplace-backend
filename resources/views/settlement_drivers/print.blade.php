
@extends('layouts.invoice')


@section('title')
تسوية حساب شريك
@endsection


@section('headers')
    <ul>
        <li><b>رقم الفاتورة : </b><bdi>{{ $settlement->id }}</bdi></li>
        @if($settlement->created_at)
            <li><b>تاريخ العملية: </b><bdi>{{ $settlement->created_at->format('Y/m/d') }}</bdi></li>
        @endif
    </ul>
    <ul>
        <li><b>كود الشريك : </b><bdi>{{ $settlement->driver_id }}</bdi></li>
        <li><b>اسم الشريك : </b><bdi>{{ $settlement->driver->name }}</bdi></li>
    </ul>
@endsection


@section('content')

    <table class="table" style="margin-top: 20px;">
        <thead>
            <tr style="background-color: #bbb;">
                <th>#</th>
                <th>رقم الطلبية</th>
                <th>عمولة الشركة</th>
                <th>عمولة المندوب</th>
                <th>قيمة الطلبية</th>
                <th>التاريخ</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($settlement->orders as $order)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $order->id }}</td>
                    <td>{{ round(($settlement->fee/100) * $order->delivery_fee,3) }}</td>
                    <td>{{ $order->delivery_fee }}</td>
                    <td>{{ $order->payment->price }}</td>
                    <td><bdi>{{ $order->created_at->format('Y-m-d g:ia') }}</bdi></td>
                </tr>
            @endforeach

        </tbody>
    </table>

    {{-- Start Coupons --}}
    <table class="table" style="margin-top: 20px;">
        <thead>
            <tr style="background-color: #bbb;">
                <th>عدد كوبونات التوصيل</th>
                <th>عدد كوبونات المطاعم</th>
                <th>قيمة كوبونات التوصيل</th>
                <th>قيمة كوبونات المطاعم</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $settlement->count_delivery_coupons }}</td>
                <td>{{ $settlement->count_restaurant_coupons }}</td>
                <td>{{ $settlement->amount_delivery_coupons }}<bdi>د.ل</bdi></td>
                <td>{{ $settlement->amount_restaurant_coupons }}<bdi>د.ل</bdi></td>
            </tr>
        </tbody>
    </table>
    {{-- End Coupons --}}

    <table class="table" style="margin-top: 20px;">
        <thead>
            <tr style="background-color: #bbb;">
                <th>العمولة</th>
                <th>العدد</th>
                <th>القيمة</th>
                <th>الإجمالي</th>
                <th>الموظف</th>
                <th>التاريخ</th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td>{{ $settlement->fee }}<bdi>%</bdi></td>
                <td>{{ $settlement->count }}</td>
                <td>{{ $settlement->amount }} <bdi>د.ل</bdi></td>
                <td>{{ $settlement->total_amount }} <bdi>د.ل</bdi></td>
                <td><bdi>{{ Auth::user()->name }}</bdi></td>
                <td><bdi>{{ date('Y-m-d g:ia') }}</bdi></td>
            </tr>

        </tbody>
    </table>
            
@endsection