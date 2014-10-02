@extends('layouts/email')

@section('content')
<h1>Hi {{ $full_name }},</h1>

<p>Your password will expired in {{ $duration }} days.</p>

<p>{{ HTML::link($link, 'Change Your Password', array('class' => 'btn-primary')) }}</p>

<p>Plase change you password to prevent diactivation of your account.</p>

<p>Best Regards</p>
@stop