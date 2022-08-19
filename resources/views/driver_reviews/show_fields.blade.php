<!-- Id Field -->
<div class="form-group row col-6">
  {!! Form::label('id', 'Id:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $driverReview->id !!}</p>
  </div>
</div>

<!-- Review Field -->
<div class="form-group row col-6">
  {!! Form::label('review', 'Review:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $driverReview->review !!}</p>
  </div>
</div>

<!-- Rate Field -->
<div class="form-group row col-6">
  {!! Form::label('rate', 'Rate:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $driverReview->rate !!}</p>
  </div>
</div>

<!-- User Id Field -->
<div class="form-group row col-6">
  {!! Form::label('user_id', 'User Id:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $driverReview->user_id !!}</p>
  </div>
</div>

<!-- Driver Id Field -->
<div class="form-group row col-6">
  {!! Form::label('driver_id', 'Driver Id:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $driverReview->driver_id !!}</p>
  </div>
</div>

<!-- Created At Field -->
<div class="form-group row col-6">
  {!! Form::label('created_at', 'Created At:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $driverReview->created_at !!}</p>
  </div>
</div>

<!-- Updated At Field -->
<div class="form-group row col-6">
  {!! Form::label('updated_at', 'Updated At:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $driverReview->updated_at !!}</p>
  </div>
</div>

