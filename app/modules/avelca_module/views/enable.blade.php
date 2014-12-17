<div class="modal fade" id="enable_{{ $module_name }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Confirm</h4>
			</div>
			<div class="modal-body">
				<form action="{{ URL::to('admin/module') }}" method="post">
					<input type="hidden" name="name" value="{{ $module_name }}">
					<input type="hidden" name="enabled" value="1">
					<p>Do you wish to enable {{ ucwords(str_replace('_',' ',str_replace('-',' ',$module_name))) }}?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get("general.close") }}</button>
					<button class="btn theme-color" type="submit">{{ Lang::get("general.confirm") }}</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog modal-sm -->
</div>