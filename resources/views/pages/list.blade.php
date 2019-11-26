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

<div class="list">
	<div class="wrapper">
		<h2 class="page__head">ВІДОМІСТЬ ВІДВІДУВАННЯ НАВЧАЛЬНИХ ДИСЦИПЛІН</h2>
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
		
		<div class="controlBlock list__search">
			@if ($sessionUser->position < 5)
				<input type="text" class="inputText list__group" placeholder="Шифр групи" value="{{$arr['group']}}">
				<div class="select list__institution">
					<div class="select__wrapper">
						<div class="select__item select__item-first select__item-active" data-id="{{$arr['institution']}}">Заклад освіти</div>
						@foreach ($institutions as $key => $institution)
							<div class="select__item" data-id="{{$key}}">{{$institution}}</div>
						@endforeach
					</div>
				</div>
			@else
				<input type="text" class="inputText list__group" placeholder="Шифр групи">
				<div class="select list__institution">
					<div class="select__wrapper">
						<div class="select__item select__item-first" data-id="0">Заклад освіти</div>
						@foreach ($institutions as $key => $institution)
							<div class="select__item" data-id="{{$key}}">{{$institution}}</div>
						@endforeach
					</div>
				</div>
			@endif

			<button class="searchBar__submit list__searchSubmit">ЗНАЙТИ</button>
		</div>

		<div id="listContent">
			@if ($sessionUser->position < 6)
				@include('templates.tableWeek')
			@endif
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {
		$(".groupSwitcher").click(function(e){
			var target = $(e.target);
			if (!target.is(".select__item") || target.is(".select__item-first")) return;
			var data = { 
				group: target.text(),
				institution: target.data("id"),
				period : '-1'
			};
			getList(data);
		});
		$(".list__searchSubmit").click(function () {
			var form = $(".list__search");

			checkInput(form.find(".list__group"));
			checkSelect(form.find(".list__institution"));

			if (form.find(".error").length < 1) {
				var data = {
					group : form.find(".list__group").val(),
					institution : form.find(".list__institution .select__item-first").data("id"),
					period : '-1'
				};
				getList(data);
			}
		});
		$("#currentGroupCode.getSwitch").click(function(){
			$(this).parent().siblings(".groupSwitcher").toggle();
		});
	});

	function getList(data){
		$.post("getWeekTable", JSON.stringify(data))
			.done(function (response) {
				if(response != 'err'){
					var notFinded = $("#listContent").html(response)
						.find('.groupNotFound').length;
					
					if(notFinded){
						var code = $("#currentGroupCode");
						if(!code.is(".getSwitch")){
							code.html(data.group);
						}
					} else {
						var code = $("#currentGroupCode");
							if(code.is(".getSwitch")){
								var groupSwitcher = code.parent().siblings(".groupSwitcher");
								if(groupSwitcher.length){
									if(findInSelect(groupSwitcher, data.group)){
										code.html(data.group);
										groupSwitcher.find('.select__item-first').html(data.group).addClass('select__item-active');
									}
								}
							} else {
								code.html(data.group);
							}
						code.siblings(".institutionName").html($("#institutionName").val());
					}
				}
			}).fail(function () {alert("Виникла помилка.");});
	}
</script>