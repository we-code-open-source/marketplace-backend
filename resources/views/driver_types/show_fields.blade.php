<!-- Id Field -->
<div class="form-group row col-6">
  {!! Form::label('id', 'Id:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $driverType->id !!}</p>
  </div>
</div>

<!-- Name Field -->
<div class="form-group row col-6">
  {!! Form::label('name', 'Name:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $driverType->name !!}</p>
  </div>
</div>

<!-- Range Field -->
<div class="form-group row col-6">
  {!! Form::label('range', 'Range:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $driverType->range !!}</p>
  </div>
</div>

<!-- Last Access Field -->
<div class="form-group row col-6">
  {!! Form::label('last_access', 'Last Access:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $driverType->last_access !!}</p>
  </div>
</div>

<!-- Created At Field -->
<div class="form-group row col-6">
  {!! Form::label('created_at', 'Created At:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $driverType->created_at !!}</p>
  </div>
</div>

<!-- Updated At Field -->
<div class="form-group row col-6">
  {!! Form::label('updated_at', 'Updated At:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $driverType->updated_at !!}</p>
  </div>
</div>

