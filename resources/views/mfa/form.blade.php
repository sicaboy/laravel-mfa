@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Multi-factor Authentication') }}</div>

                    <div class="card-body">
                        <form method="POST" action="" aria-label="{{ __('Multi-factor Authentication') }}">
                            @csrf
                            <div class="alert alert-secondary" role="alert">
                                {{ __("The authentication code has been sent to your e-mail and the code will expire after :minutes minutes.", ['minutes' => $minutes]) }}
                            </div>
                            <div class="form-group row">
                                <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Auth Code') }}</label>

                                <div class="col-md-6">
                                    <input id="code" type="text" class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" value="{{ $code ?? old('code') }}" required autofocus>
                                    @if ($errors->has('code'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('code') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row mb-10" id="re_send_code_hint" style="visibility: hidden">
                                <div class="col-md-6 offset-md-4 col-md-offset-4">
                                    <span class="text-muted">
                                        {{ __("Haven't received the code? Try ") }}
                                    </span>
                                    <a id="re_send_code_link" href="{{route('mfa.generate', ['group' => $group])}}">
                                        {{ __('re-send auth code') }}
                                    </a>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Submit') }}
                                    </button>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4 col-md-offset-4">
                                    <a href="{{ route('logout') }}" class="btn btn-link">
                                        {{ __('Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
setTimeout(function() {
    document.getElementById('re_send_code_hint').style.visibility = 'unset';
    document.getElementById("re_send_code_link").addEventListener("click", function(){
        document.getElementById('re_send_code_hint').style.visibility = 'hidden';
    });
}, 2000);
</script>
@endsection
