@extends('layouts/default')

@section('content')

<div class="row">
    <div class="col-md-12">
        <ol class="breadcrumb">
            <li><a href="{{ URL::to('dashboard') }}">Dashboard</a></li>
            <li><a href="javascript:;">Reports</a></li>
            <li><a href="javascript:;">Report Category A</a></li>
            <li class="active">{{ $name }}</li>
        </ol>

        <div class="page-header">
            <h1>{{ $name }}
                <small>{{ date('d F Y') }}</small>
            </h1>
        </div>

    </div>
</div>

@stop