@foreach ($inputs as $input)
    @if (empty($input->shown))
    <div class="form-group">
        <label for="{{{$input->getId()}}}" class="col-sm-2 control-label">
            {{{$input->getLabel()}}}
            @if ($input->isRequired())
                (*)
            @end
        </label>
        <div class="col-sm-10">
            {{ $input->getHtml($form) }}
        </div>
    </div>
        @set($input->shown, true)
    @end
@end
