@extends('layouts/email')

@section('content')
<h1>Hi {{ $full_name }},</h1>

<p>You have requested to reset your password. Your password won't change until you click on the link below.</p>

<table>
	<tr>
		<td class="padding">
			<p>{{ HTML::link($link, 'Reset Your Password', array('class' => 'btn-primary')) }}</p>
		</td>
	</tr>
</table>

<p>Once your new password is confirmed, you can log in at {{ HTML::link(URL::to('signin'), URL::to('signin')) }} using your email address {{ $email }} and the new password.</p>

<p>If you didn't make this password reset request, please ignore this email.</p>

<p>Best Regards</p>
@stop