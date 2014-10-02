<div class="modal fade text-left" id="edit_modal-{{ $client->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Edit Client</h4>
			</div>
			<div class="modal-body">


				<form class="form-horizontal" role="form" action="{{ URL::to('admin/api/client/edit') }}" method="post">
					<input type="hidden" name="id" value="{{ $client->id }}">

					<div class="form-group">
						<label class="col-md-2 control-label">Name</label>
						<div class="col-md-10">
							{{ Form::text("name", $client->name, array("class" => "form-control")) }}
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get("general.close") }}</button>
					<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{ Lang::get("general.confirm") }}</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog modal-sm -->
</div>