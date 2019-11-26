@php
	//remember me
	if(empty(session()->get('remember'))){
		config(['session.expire_on_close' => true]);
	}

	$logged = session()->get('logged');
@endphp

@include('templates.header')
@include('templates.footer')