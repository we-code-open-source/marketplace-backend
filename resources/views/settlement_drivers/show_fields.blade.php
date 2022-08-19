<!-- Id Field -->
<div class="form-group row col-6">
  {!! Form::label('id', 'Id:', ['class' => 'col-4 control-label text-right']) !!}
  <div class="col-8">
    <p>{!! $settlementDriver->id !!}</p>
  </div>
</div>

<!-- Driver Id Field -->
<div class="form-group row col-6">
  {!! Form::label('driver_id', 'Driver Id:', ['class' => 'col-4 control-label text-right']) !!}
  <div class="col-8">
    <p>{!! $settlementDriver->driver->name !!}</p>
  </div>
</div>

<!-- Count orders Field -->
<div class="form-group row col-6">
  {!! Form::label('count', 'Count orders:', ['class' => 'col-4 control-label text-right']) !!}
  <div class="col-8">
    <p>{!! $settlementDriver->count !!}</p>
  </div>
</div>

<!-- Count delivery coupons Field -->
<div class="form-group row col-6">
  {!! Form::label('count', 'Count delivery coupons:', ['class' => 'col-4 control-label text-right']) !!}
  <div class="col-8">
    <p>{!! $settlementDriver->count_delivery_coupons !!}</p>
  </div>
</div>

<!-- Count restaurant coupons Field -->
<div class="form-group row col-6">
  {!! Form::label('count', 'Count restaurant coupons:', ['class' => 'col-4 control-label text-right']) !!}
  <div class="col-8">
    <p>{!! $settlementDriver->count_restaurant_coupons !!}</p>
  </div>
</div>

<!-- fee Field -->
<div class="form-group row col-6">
  {!! Form::label('fee', 'Fee:', ['class' => 'col-4 control-label text-right']) !!}
  <div class="col-8">
    <p>{!! $settlementDriver->fee !!}%</p>
  </div>
</div>

<!-- Amount orders Field -->
<div class="form-group row col-6">
  {!! Form::label('amount', 'Amount orders:', ['class' => 'col-4 control-label text-right']) !!}
  <div class="col-8">
    <p>{!! $settlementDriver->amount !!}</p>
  </div>
</div>

<!-- Amount delivery coupons Field -->
<div class="form-group row col-6">
  {!! Form::label('amount_delivery_coupons', 'Amount delivery coupons:', ['class' => 'col-4 control-label text-right']) !!}
  <div class="col-8">
    <p>{!! $settlementDriver->amount_delivery_coupons !!}</p>
  </div>
</div>

<!-- Amount restaurant coupons Field -->
<div class="form-group row col-6">
  {!! Form::label('amount_restaurant_coupons', 'Amount restaurant coupons:', ['class' => 'col-4 control-label text-right']) !!}
  <div class="col-8">
    <p>{!! $settlementDriver->amount_restaurant_coupons !!}</p>
  </div>
</div>

<!-- Amount total Field -->
<div class="form-group row col-6">
  {!! Form::label('total_amount', 'Total amount:', ['class' => 'col-4 control-label text-right']) !!}
  <div class="col-8">
    <p>{!! $settlementDriver->total_amount  !!}</p>
  </div>
</div>

<!-- Note Field -->
<div class="form-group row col-6">
  {!! Form::label('note', 'Note:', ['class' => 'col-4 control-label text-right']) !!}
  <div class="col-8">
    <p>{!! $settlementDriver->note !!}</p>
  </div>
</div>

<!-- Creator Id Field -->
<div class="form-group row col-6">
  {!! Form::label('creator_id', 'Creator Id:', ['class' => 'col-4 control-label text-right']) !!}
  <div class="col-8">
    <p>{!! $settlementDriver->creator->name !!}</p>
  </div>
</div>

<!-- Created At Field -->
<div class="form-group row col-6">
  {!! Form::label('created_at', 'Created At:', ['class' => 'col-4 control-label text-right']) !!}
  <div class="col-8">
    <p>{!! $settlementDriver->created_at !!}</p>
  </div>
</div>

<!-- Updated At Field -->
<div class="form-group row col-6">
  {!! Form::label('updated_at', 'Updated At:', ['class' => 'col-4 control-label text-right']) !!}
  <div class="col-8">
    <p>{!! $settlementDriver->updated_at !!}</p>
  </div>
</div>

