{{ $form->open($action) }}

@if (!empty($error))
    <div class="alert alert-danger">{{{$error}}}</div>
@end

@foreach ($inputs as $input)
    <div class="form-group">
        <label for="{{$input['id']}}" class="col-sm-2 control-label">
            {{{$input['label']}}}
            @if ($input['required'])
                (*)
            @end
        </label>
        <div class="col-sm-10">
            {{ $input['html'] }}
        </div>
    </div>
@end

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default">{{{$create}}}</button>
    </div>
</div>

</form>
{{ $form->close() }}
