<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="modal_edit">Update {{ $name }}</h4>
</div>
<div class="modal-body">
	{{ Form::model($record, array('id' => 'formUpdate', 'enctype' =>  "$enctype", 'class' => 'form-horizontal', 'url' => URL::to('admin/'.$routeName.'/edit', $record->id))) }}
	<?php $customView = 'admin.'.$routeName.'.rowFormEdit'; ?>
	@if(View::exists($customView))
	@include($customView)
	@else
	@include('avelca::rowFormEdit')
	@endif

	<?php $customView = 'admin.'.$routeName.'.additional.edit'; ?>
	@if(View::exists($customView))
	@include($customView)
	@endif
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<button type="submit" onclick="document.getElementById('formUpdate').submit();" class="btn theme-color"><i class="fa fa-save fa-fw"></i> Save changes</button>
</div>
{{ Form::close() }}

<script type="text/javascript">
$(document).ready(function(){
	<?php $customView = 'admin.'.$routeName.'.js.initializeComponents'; ?>
	@if( ! View::exists($customView))
	<?php $customView = 'avelca::js.initializeComponents'; ?>
	@endif
	@include($customView)
});
</script>


<?php $customView = 'admin.'.$routeName.'.js.masterDetail'; ?>
@if(View::exists($customView))
@include($customView)
@else
@include('avelca::js.masterDetail')
@endif
