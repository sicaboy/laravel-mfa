@extends('laravel-mfa::emails.base')

@section('content')
    <p class="lead">
        Hi{{ !empty($user->first_name) ? ' ' . $user->first_name : '' }},
    </p>
    <p>
        Here is your login Authentication Code.
    </p>
    <p>
        Login Authentication Code: <strong>{{ $code }}</strong><br/>
    </p>
    <p>
        The code is valid within {{ $minutes }} minutes.
    </p>
@stop
