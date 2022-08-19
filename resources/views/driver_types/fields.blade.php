@if($customFields)
<h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
<!-- Name Field -->
<div class="form-group row ">
  {!! Form::label('name', trans("lang.driver_type_name"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {!! Form::text('name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.driver_type_name_placeholder")]) !!}
    <div class="form-text text-muted">
      {{ trans("lang.driver_type_name_help") }}
    </div>
  </div>
</div>

<!-- Range Field -->
<div class="form-group row ">
  {!! Form::label('range', trans("lang.driver_type_range"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {!! Form::number('range', null,['step' => 'any','min' => '0.01','class' => 'form-control','placeholder'=>  trans("lang.driver_type_range_placeholder")]) !!}
    <div class="form-text text-muted">
      {{ trans("lang.driver_type_range_help") }}
    </div>
  </div>
</div>
</div>
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

<!-- Last Access Field -->
<div class="form-group row ">
  {!! Form::label('last_access', trans("lang.driver_type_last_access"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {!! Form::number('last_access', null, ['min' => '1','class' => 'form-control','placeholder'=>  trans("lang.driver_type_last_access_placeholder")]) !!}
    <div class="form-text text-muted">
      {{ trans("lang.driver_type_last_access_help") }}
    </div>
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
  <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.driver_type')}}</button>
  <a href="{!! route('driverTypes.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
