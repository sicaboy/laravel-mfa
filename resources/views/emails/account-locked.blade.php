@extends('laravel-security.emails.base')

@section('content')
    <p class="lead">
        Hi{{ !empty($user->first_name) ? ' ' . $user->first_name : '' }},
    </p>
    <p>
        Your account has been locked due to {{ $days }} days of no activity.
    </p>
    <p>
        If you have any questions, please do not hesitate to contact admin.
    </p>
@stop
