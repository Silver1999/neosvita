@php
	$sessionUser = session()->get('user');

	$institutions = [
		1 => "КЕП ІФНТУНГ",
	];

	$grName = '';
	
	if($sessionUser->position == 5) {
		if(count($supGroups)){
			reset($supGroups);
			$grName = key($supGroups);
		}
	} else if($sessionUser->position < 5){
		$grName = $sessionUser->groupName;
	}

@endphp

<div class="schedule">
	<div class="wrapper">
		<h2 class="page__head">РОЗКЛАД НАВЧАЛЬНИХ ПРЕДМЕТІВ І ЇХ ПРОВЕДЕННЯ</h2>
		<h3 class="page__subhead">
			<span class="institutionName">{{$institutionName}}</span>  
			<span id="currentGroupCode" class="{{count($supGroups) > 1 ? 'getSwitch' : ''}}">{{$grName}}</span>
		</h3>

		@if (count($supGroups) > 1)
			<div class="select groupSwitcher">
				<div class="select__wrapper">
					@foreach ($supGroups as $key => $supGroup)
						@if ($loop->first)
							<div class="select__item select__item-first select__item-active" data-id="{{$sessionUser->institution}}">{{$key}}</div>
						@endif
						<div class="select__item" data-id="{{$sessionUser->institution}}">{{$key}}</div>
					@endforeach
				</div>
			</div>
		@endif

		<div class="controlBlock searchBar schedule__search">
			@if ($sessionUser->position < 5)
				<input type="text" class="inputText searchBar__group" placeholder="Шифр групи" value="{{$sessionUser->groupName}}">
				<div class="select searchBar__institution">
					<div class="select__wrapper">
						<div class="select__item select__item-first select__item-active" data-id="{{$sessionUser->institution}}">Заклад освіти</div>
						@foreach ($institutions as $key => $institution)
							<div class="select__item" data-id="{{$key}}">{{$institution}}</div>
						@endforeach
					</div>
				</div>
			@else
				<input type="text" class="inputText searchBar__group" placeholder="Шифр групи">
				<div class="select searchBar__institution">
					<div class="select__wrapper">
						<div class="select__item select__item-first" data-id="0">Заклад освіти</div>
						@foreach ($institutions as $key => $institution)
							<div class="select__item" data-id="{{$key}}">{{$institution}}</div>
						@endforeach
					</div>
				</div>
			@endif

			<button class="searchBar__submit schedule__searchSubmit">ЗНАЙТИ</button>
		</div>

		<script>
			$(document).ready(function () {
				$(".groupSwitcher").click(function(e){
					groupSwitcherSchedule(e);
				});
				$(".schedule__searchSubmit").click(getGroupSchedule);
				$("#currentGroupCode.getSwitch").click(function(){
					$(this).parent().siblings(".groupSwitcher").toggle();
				});
			});
		</script>
		
		<div id="groupSchedule">
			@if ($sessionUser->position < 6)
				@include('templates.schedule')
			@else
				@include('templates.scheduleBottom')
			@endif
		</div>
	</div>
</div>