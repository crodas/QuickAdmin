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
