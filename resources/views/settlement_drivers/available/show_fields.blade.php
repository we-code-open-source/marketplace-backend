<!-- Id Field -->
<div class="form-group row col-6">
  {!! Form::label('driver_id', 'Driver Id:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementDriver->driver->id !!}</p>
  </div>
</div>

<!-- Driver Id Field -->
<div class="form-group row col-6">
  {!! Form::label('driver_id', 'Driver:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementDriver->driver->name !!}</p>
  </div>
</div>

<!-- Count Field -->
<div class="form-group row col-6">
  {!! Form::label('count', 'Count:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementDriver->count !!}</p>
  </div>
</div>

<!-- fee Field -->
<div class="form-group row col-6">
  {!! Form::label('fee', 'Fee:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementDriver->fee !!}</p>
  </div>
</div>

<!-- Amount Field -->
<div class="form-group row col-6">
  {!! Form::label('amount', 'Amount:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementDriver->amount !!}</p>
  </div>
</div>

<div class="form-group row col-12"> 
  <!-- Table -->
  <table class="table table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th>رقم الطلبية</th>
          <th>عمولة الشركة</th>
          <th>عمولة المندوب</th>
          <th>قيمة الطلبية</th>
          <th>التاريخ</th>
        </tr>
      </thead>
      <tbody>
            @foreach ($settlementDriver->orders as $order)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $order->id }}</td>
                    <td>{{ round(($settlementDriver->fee/100) * $order->delivery_fee,3) }}</td>
                    <td>{{ $order->delivery_fee }}</td>
                    <td>{{ $order->payment->price }}</td>
                    <td><bdi>{{ $order->created_at->format('Y-m-d g:ia') }}</bdi></td>
                </tr>
            @endforeach
      </tbody>
  </table>
</div> 