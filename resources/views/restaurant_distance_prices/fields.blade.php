<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

    <!-- Restaurants Field -->
    <div class="form-group row ">
        {!! Form::label('restaurant_id', trans("lang.restaurant_distance_price_restaurant"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('restaurant_id', $restaurants, null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <!-- Price Field -->
    <div class="form-group row ">
        {!! Form::label('from', trans("lang.restaurant_distance_price_from"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::number('from', null,  ['class' => 'form-control','step'=>'any','placeholder'=>  trans("lang.restaurant_distance_price_from_placeholder")]) !!}
        </div>
    </div>

    <!-- Price Field -->
    <div class="form-group row ">
        {!! Form::label('to', trans("lang.restaurant_distance_price_to"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::number('to', null,  ['class' => 'form-control','step'=>'any','placeholder'=>  trans("lang.restaurant_distance_price_to_placeholder")]) !!}
        </div>
    </div>

    <!-- Price Field -->
    <div class="form-group row ">
        {!! Form::label('price', trans("lang.restaurant_distance_price"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::number('price', null,  ['class' => 'form-control','step'=>'any','placeholder'=>  trans("lang.restaurant_distance_price_price_placeholder")]) !!}
        </div>
    </div>

    <!-- 'Boolean is_available Field' -->
    <div class="form-group row ">
        {!! Form::label('is_available', trans("lang.restaurant_distance_price_is_available"),['class' => 'col-3 control-label text-right']) !!}
        <div class="checkbox icheck">
            <label class="col-9 ml-2 form-check-inline">
                {!! Form::hidden('is_available', 0) !!}
                {!! Form::checkbox('is_available', 1, null) !!}
            </label>
        </div>
    </div>

</div>


<!-- Submit Field -->
<div class="form-group col-12 text-right">
    <button type="submit" class="btn btn-{{setting('theme_color')}}"><i
                class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.restaurant_distance_price')}}</button>
    <a href="{!! route('restaurantDistancePrices.index') !!}" class="btn btn-default"><i
                class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
