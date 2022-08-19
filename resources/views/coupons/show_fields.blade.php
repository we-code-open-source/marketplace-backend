<!-- Id Field -->
<div class="form-group row col-6">
  {!! Form::label('id', 'Id:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->id !!}</p>
  </div>
</div>

<!-- Code Field -->
<div class="form-group row col-6">
  {!! Form::label('code', 'Code:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->code !!}</p>
  </div>
</div>

<!-- count Field -->
<div class="form-group row col-6">
  {!! Form::label('count', 'count:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->count !!}</p>
  </div>
</div>

<!-- count_used Field -->
<div class="form-group row col-6">
  {!! Form::label('count_used', 'count_used:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->count_used !!}</p>
  </div>
</div>

<!-- Discount Field -->
<div class="form-group row col-6">
  {!! Form::label('discount', 'Discount:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->discount !!}</p>
  </div>
</div>

<!-- Discount Type Field -->
<div class="form-group row col-6">
  {!! Form::label('discount_type', 'Discount Type:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->discount_type !!}</p>
  </div>
</div>

<!-- Description Field -->
<div class="form-group row col-6">
  {!! Form::label('description', 'Description:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->description !!}</p>
  </div>
</div>

<!-- Food Id Field -->
<div class="form-group row col-6">
  {!! Form::label('food_id', 'Food Id:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->food_id !!}</p>
  </div>
</div>

<!-- Restaurant Id Field -->
<div class="form-group row col-6">
  {!! Form::label('restaurant_id', 'Restaurant Id:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->restaurant_id !!}</p>
  </div>
</div>

<!-- Category Id Field -->
<div class="form-group row col-6">
  {!! Form::label('category_id', 'Category Id:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->category_id !!}</p>
  </div>
</div>

<!-- Expires At Field -->
<div class="form-group row col-6">
  {!! Form::label('expires_at', 'Expires At:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->expires_at !!}</p>
  </div>
</div>

<!-- Enabled Field -->
<div class="form-group row col-6">
  {!! Form::label('enabled', 'Enabled:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->enabled !!}</p>
  </div>
</div>

<!-- On delivery fee Field -->
<div class="form-group row col-6">
  {!! Form::label('on_delivery_fee', 'On delivery fee:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->on_delivery_fee !!}</p>
  </div>
</div>

<!-- Cost on Restaurant Field -->
<div class="form-group row col-6">
  {!! Form::label('cost_on_restaurant', 'Cost on Restaurant:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->cost_on_restaurant !!}</p>
  </div>
</div>

<!-- Created At Field -->
<div class="form-group row col-6">
  {!! Form::label('created_at', 'Created At:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->created_at !!}</p>
  </div>
</div>

<!-- Updated At Field -->
<div class="form-group row col-6">
  {!! Form::label('updated_at', 'Updated At:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $coupon->updated_at !!}</p>
  </div>
</div>

