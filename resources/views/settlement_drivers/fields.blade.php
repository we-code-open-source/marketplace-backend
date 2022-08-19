@if($customFields)
<h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

<!-- Driver Id Field -->
<div class="form-group row ">
    {!! Form::label('driver_id', trans("lang.settlement_driver_driver_id"),['class' => 'col-3 control-label text-right']) !!}
    <div class="col-9">
        {!! Form::select('driver_id', $drivers, null, ['data-empty'=>trans("lang.settlement_driver_driver_id_placeholder"),'class' => 'select2 not-required form-control']) !!}
        <div class="form-text text-muted">{{ trans("lang.settlement_driver_driver_id_help") }}</div>
    </div>
</div> 

<!-- Note Field -->
<div class="form-group row ">
  {!! Form::label('note', trans("lang.settlement_driver_note"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {!! Form::textarea('note', null, ['class' => 'form-control','placeholder'=>
     trans("lang.settlement_driver_note_placeholder")  ]) !!}
    <div class="form-text text-muted">{{ trans("lang.settlement_driver_note_help") }}</div>
  </div>
</div>
</div>
@if($customFields)
<div class="clearfix"></div>
<div class="col-12 custom-field-container">
  <h5 class="col-12 pb-4">{!! trans('lang.custom_field_plural') !!}</h5>
  {!! $customFields !!}
</div>
@endif
<!-- Submit Field -->
<div class="form-group col-12 text-right">
  <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.settlement_driver')}}</button>
  <a href="{!! route('settlementDrivers.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
