@php
	//remember me
	if(empty(session()->get('remember'))){
		config(['session.expire_on_close' => true]);
	}

	$logged = session()->get('logged');
@endphp

@include('templates.header')

	@if (!empty($pageView))
		@include('pages.' . $pageView)
	@endif

@include('templates.footer')
