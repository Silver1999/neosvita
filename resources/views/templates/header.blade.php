<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Neosvita</title>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="shortcut icon" href="{{ URL::asset('img/favicon.ico') }}" type="image/png">
	<link rel="stylesheet" href="{{ URL::asset('css/index.min.css') }}">

	@if (env("APP_DEBUG"))
		<script src="{{ URL::asset('js/jquery-3.4.1.min.js') }}"></script>
	@else
		<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	@endif
</head>

<body>
	<noscript id="noscript">
		Для корректной работы сайта браузер должен поддерживать JavaScript.
	</noscript>

	<header class="header">

		@if (empty($logged))
			@include('templates.authForm')
			@include('templates.regForm')
			@include('templates.forgotForm')
		@else
			@include('templates.navbar')
			<h1 class="header__h1">Neosvita&nbsp;-</h1>
			<h2 class="header__h2">ЕЛЕКТРОННА СИСТЕМА ВЕДЕННЯ ВІДОМОСТЕЙ ВІДВІДУВАННЯ</h2>
		@endif

	</header>

	<section id="content">
