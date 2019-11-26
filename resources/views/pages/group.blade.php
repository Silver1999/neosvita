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

<div class="group">
	<div class="wrapper">
		<h2 class="page__head">ІНФОРМАЦІЯ ПРО СТУДЕНТІВ ТА ЇХ КЕРІВНИКІВ</h2>
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

		<div class="controlBlock searchBar">
			<input type="text" class="inputText searchBar__group" placeholder="Шифр групи">
			<div class="select searchBar__institution">
				<div class="select__wrapper">
					<div class="select__item select__item-first" data-id="0">Заклад освіти</div>
					@foreach ($institutions as $key => $institution)
						<div class="select__item" data-id="{{$key}}">{{$institution}}</div>
					@endforeach
				</div>
			</div>
			<button class="searchBar__submit group__searchSubmit">ЗНАЙТИ</button>
		</div>

		<div id="groupContent">
			@if ($sessionUser->position < 6)
				@include('templates.group')
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
			};
			getGroup(data);
		});
		$(".group__searchSubmit").click(function () {
			var form = $(".searchBar");

			checkInput(form.find(".searchBar__group"));
			checkSelect(form.find(".searchBar__institution"));

			if ($("#content .error").length < 1) {
				var data = {
					group : form.find(".searchBar__group").val(),
					institution : form.find(".searchBar__institution .select__item-first").data("id")
				};
				getGroup(data);
			}
		});
		$("#currentGroupCode.getSwitch").click(function(){
			$(this).parent().siblings(".groupSwitcher").toggle();
		});
	});

	function getGroup(data){
		$.post("getGroup", JSON.stringify(data))
			.done(function (response) {
				if(response != 'err'){
					var notFinded = $("#groupContent").html(response)
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
			}).fail(function () { alert("Виникла помилка."); });
	}
</script>