<div class="modal fade text-left" id="edit_modal-{{ $scope->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Edit Scope</h4>
			</div>
			<div class="modal-body">


				<form class="form-horizontal" role="form" action="{{ URL::to('admin/api/scope/edit') }}" method="post">
					<input type="hidden" name="id" value="{{ $scope->id }}">

					<div class="form-group">
						<label>Scope</label><br>
						{{ Form::text("scope", $scope->scope, array("class" => "form-control")) }}
					</div><br>

					<div class="form-group">
						<label>Name</label><br>
						{{ Form::text("name", $scope->name, array("class" => "form-control")) }}
					</div><br>

					<div class="form-group">
						<label>Description</label><br>
						{{ Form::text("description", $scope->description, array("class" => "form-control")) }}
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