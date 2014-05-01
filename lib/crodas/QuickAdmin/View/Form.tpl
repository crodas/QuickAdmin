{{ $form->open($action, 'POST', ['class' => 'form-horizontal']) }}

@if (!empty($error))
    <div class="alert alert-danger">{{{$error}}}</div>
@end

{{ $self->inputsView(compact('form', 'inputs')) }}

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default">{{{$create}}}</button>
    </div>
</div>

</form>
{{ $form->close() }}
