<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="modal_edit">Remove {{ $name }}</h4>
</div>
<div class="modal-body">
	{{ Form::open(array('id' => 'formRemove', 'class' => 'form-horizontal', 'url' => URL::to('admin/'.$routeName.'/delete', $record->id))) }}
	<p>Are you sure want to remove this {{ strtolower($name) }}? </p>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<button type="submit" onclick="document.getElementById('formRemove').submit();" class="btn theme-color"><i class="fa fa-check fa-fw"></i> Confirm</button>
</div>
{{ Form::close() }}