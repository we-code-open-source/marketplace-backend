<!-- Id Field -->
<div class="form-group row col-6">
  {!! Form::label('restaurant_id', 'restaurant Id:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementManager->restaurant->id !!}</p>
  </div>
</div>

<!-- Restaurant Id Field -->
<div class="form-group row col-6">
  {!! Form::label('restaurant', 'Restaurant:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementManager->restaurant->name !!}</p>
  </div>
</div>

<!-- Count Field -->
<div class="form-group row col-6">
  {!! Form::label('count', 'Count:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementManager->count !!}</p>
  </div>
</div>

<!-- fee Field -->
<div class="form-group row col-6">
  {!! Form::label('fee', 'Fee:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementManager->fee !!}%</p>
  </div>
</div>

<!-- Amount Field -->
<div class="form-group row col-6">
  {!! Form::label('amount', 'Amount:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementManager->amount !!}</p>
  </div>
</div>

<!-- Sales Amount Field -->
<div class="form-group row col-6">
  {!! Form::label('sales_mount', 'Sales mount:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementManager->sales_amount !!}</p>
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
          <th>قيمة الطلبية</th>
          <th>التاريخ</th>
        </tr>
      </thead>
      <tbody>
            @foreach ($settlementManager->orders as $order)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $order->id }}</td>
                    <td>{{ round(($settlementManager->fee/100) * $order->amount,3) }}</td>
                    <td>{{ $order->amount }}</td> 
                    <td><bdi>{{ $order->created_at->format('Y-m-d g:ia') }}</bdi></td>
                </tr>
            @endforeach
      </tbody>
  </table>
</div> 