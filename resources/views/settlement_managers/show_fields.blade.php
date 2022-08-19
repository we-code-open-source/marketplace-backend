<!-- Id Field -->
<div class="form-group row col-6">
  {!! Form::label('id', 'Id:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementManager->id !!}</p>
  </div>
</div>

<!-- Creator Id Field -->
<div class="form-group row col-6">
  {!! Form::label('creator_id', 'Creator Id:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementManager->creator->name !!}</p>
  </div>
</div>

<!-- Restaurant Id Field -->
<div class="form-group row col-6">
  {!! Form::label('restaurant_id', 'Restaurant Id:', ['class' => 'col-3 control-label text-right']) !!}
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

<!-- Amount Field -->
<div class="form-group row col-6">
  {!! Form::label('amount', 'Amount:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {{-- <p>{!! $settlementManager->amount !!}</p> --}}
    <p>{!! $settlementManager->total !!}</p>
  </div>
</div>

<!-- Note Field -->
<div class="form-group row col-6">
  {!! Form::label('note', 'Note:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementManager->note !!}</p>
  </div>
</div>

<!-- Fee Field -->
<div class="form-group row col-6">
  {!! Form::label('fee', 'Fee:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementManager->fee !!}</p>
  </div>
</div>

<!-- Created At Field -->
<div class="form-group row col-6">
  {!! Form::label('created_at', 'Created At:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementManager->created_at !!}</p>
  </div>
</div>

<!-- Updated At Field -->
<div class="form-group row col-6">
  {!! Form::label('updated_at', 'Updated At:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $settlementManager->updated_at !!}</p>
  </div>
</div>

