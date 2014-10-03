@extends('layouts/email')

@section('content')
<h1>Hi {{ $full_name }},</h1>

<p>You have requested a new password for the website.</p>

<p>If you didn't update your password, someone else did. You may reset your password at the following address:</p>

<table>
	<tr>
		<td class="padding">
			<p>{{ HTML::link($link, 'Reset Password', array('class' => 'btn-primary')) }}</p>
		</td>
	</tr>
</table>

<p>If you did update your password, please ignore this email.</p>

<p>Best Regards</p>
@stop