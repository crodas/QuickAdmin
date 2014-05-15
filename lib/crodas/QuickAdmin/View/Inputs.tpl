@set($shown, [])
@foreach ($inputs as $input)
    @if (empty($shown[$input->getId()]))
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
        @set($shown[$input->getId()], true)
    @end
@end
