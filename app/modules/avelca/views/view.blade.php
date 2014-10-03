<?php unset($fields['id']); ?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="modal_edit">View {{ $name }}</h4>
</div>
<div class="modal-body">
	{{ Form::model($record, array('class' => 'form-horizontal')) }}
	<?php $customView = 'admin.'.$routeName.'.rowView'; ?>
	@if(View::exists($customView))
	@include($customView)
	@else
	@include('avelca::rowView')
	@endif

	<?php $customView = 'admin.'.$routeName.'.additional.view'; ?>
	@if(View::exists($customView))
	@include($customView)
	@endif
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
{{ Form::close() }}