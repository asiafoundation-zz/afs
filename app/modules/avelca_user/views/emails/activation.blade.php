@extends('layouts/email')

@section('content')
<h1>Hi {{ $full_name }},</h1>

<p>Your account won't be activated until you click on the link below.</p>

<table>
	<tr>
		<td class="padding">
			<p>{{ HTML::link($link, 'Click to Activate', array('class' => 'btn-primary')) }}</p>
		</td>
	</tr>
</table>

<p>Once your account is activated, you can log in at {{ HTML::link(URL::to('signin'), URL::to('signin')) }} using your email address {{ $email }} and your password.</p>

<p>Best Regards</p>
@stop