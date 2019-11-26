<div class="navbar">
	<div class="wrapper">
		<a href="{{ url('/') }}">
			<img src="{{ URL::asset('img/logo.png') }}" alt="logo" class="logo navbar__logo">
		</a>

		<nav class="navbar__nav">
			@if (empty($pageView))
				<a class="navbar__link link-home navbar__link-active" data-url="/">ГОЛОВНА</a>
				<a class="navbar__link link-schedule" data-url="schedule">РОЗКЛАД</a>
				<a class="navbar__link link-list" data-url="timetable">ВІДОМІСТЬ</a>
				<a class="navbar__link link-group" data-url="group">ГРУПА</a>
			@else
				<a class="navbar__link link-home" data-url="/">ГОЛОВНА</a>
				<a class="navbar__link link-schedule {{$pageView == 'schedule' ? 'navbar__link-active':''}}" data-url="schedule">РОЗКЛАД</a>
				<a class="navbar__link link-list {{$pageView == 'list' ? 'navbar__link-active':''}}" data-url="timetable">ВІДОМІСТЬ</a>
				<a class="navbar__link link-group {{$pageView == 'group' ? 'navbar__link-active':''}}" data-url="group">ГРУПА</a>
			@endif
		</nav>

		<div class="navbar__lobby">
			@if (!empty($logged))
				<img src="{{ URL::asset('img/avatar.png') }}" alt="avatar" class="navbar__avatar">
				<div class="navbar__lobbyData">
					<div class="navbar__name">{{$logged}}</div>
					<button class="navbar__logout">ВИЙТИ</button>
				</div>
			@endif
		</div>

		<button class="hamburger hamburger--squeeze" type="button">
			<span class="hamburger-box">
				<span class="hamburger-inner"></span>
			</span>
		</button>
	</div>
	
	<nav class="navbar__navMob">
			@if (!empty($logged))
				<div class="navbar__nameMob">{{$logged}}</div>
			@endif
			
			<a class="navbar__linkMob link-home navbar__link-active" data-url="/">ГОЛОВНА</a>
			<a class="navbar__linkMob link-schedule" data-url="schedule">РОЗКЛАД</a>
			<a class="navbar__linkMob link-list" data-url="timetable">ВІДОМІСТЬ</a>
			<a class="navbar__linkMob link-group" data-url="group">ГРУПА</a>

			@if (!empty($logged))
				<div class="navbar__logoutMob">ВИЙТИ</div>
			@endif
	</nav>
</div>
