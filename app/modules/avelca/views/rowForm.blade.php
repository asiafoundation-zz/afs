@foreach($createFields as $field => $structure)
<div class="form-group">
	{{ AvelcaController::label($field, $structure, $rules) }}
	{{ AvelcaController::field($field, $structure, $rules) }}
</div>
@endforeach

