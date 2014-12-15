<?php
$messages = Session::get('messages');
$status = Session::get('status');
$status_error = Session::get('status_error');
$status_info = Session::get('status_info');
$status_warning = Session::get('status_warning');
?>

<br>
@if( ! empty($status) )
<div class="alert alert-success">
 <button type="button" class="close" data-dismiss="alert">&times;</button>
 <strong>{{ Lang::get('general.message_success') }}!</strong> {{ $status }}
</div>
@endif

@if( ! empty($status_error) )
<div class="alert alert-danger">
 <button type="button" class="close" data-dismiss="alert">&times;</button>
 <strong>{{ Lang::get('general.message_error') }}!</strong> {{ $status_error }}
</div>
@endif

@if( ! empty($status_info) )
<div class="alert alert-info">
 <button type="button" class="close" data-dismiss="alert">&times;</button>
 <strong>{{ Lang::get('general.message_info') }}!</strong> {{ $status_info }}
</div>
@endif

@if( ! empty($status_warning) )
<div class="alert alert-warning">
 <button type="button" class="close" data-dismiss="alert">&times;</button>
 <strong>{{ Lang::get('general.message_warning') }}!</strong> {{ $status_warning }}
</div>
@endif

@if ( ! empty($messages))
@foreach ($messages->all() as $message)
<div class="alert alert-danger">
 <button type="button" class="close" data-dismiss="alert">&times;</button>
 <strong>Error!</strong> {{ $message }}.
</div>
@endforeach
@endif

@if( ! empty($errors))
@foreach ($errors->all() as $message)
<div class="alert alert-danger">
 <button type="button" class="close" data-dismiss="alert">&times;</button>
 <strong>{{ Lang::get('general.message_error') }}!</strong> {{ $message }}
</div>
@endforeach
@endif