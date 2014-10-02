@extends('layouts/email')

@section('content')
<h1>Hi {{ $full_name }},</h1>

<p>Sorry you've been having trouble logging into your account.</p>

<p>{{ HTML::link($link, 'Go back to website now', array('class' => 'btn-primary')) }}</p>

<p>You can also get <a href="{{ URL::to('reset-password') }}">password help</a> or <a href="{{ URL::to('signin') }}">login</a> help on the <a href="{{ URL::to('/') }}">website</a>..</p>

<p>Best Regards</p>
@stop