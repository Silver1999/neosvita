<div class="group__row userRow" data-id="{{$user->id}}">
	<div class="group__td group__tdID">{{$loop->iteration}}</div>
	<div class="group__td group__tdName">
		@php 
			$initials = mb_substr($user->name, 0 , 1). '.' . mb_substr($user->patronymic, 0 , 1). '.';
		@endphp
		<div class="userName">{{$user->surname . ' ' . $initials}}</div>
	</div>
	<div class="group__td col-1 visible">
		@php
			$dob = new DateTime($user->dayOfBirth);
		@endphp
		<div class="group__text">{{$dob->format('d-m-Y')}}</div>
	</div>
	<div class="group__td col-2">
		<div class="group__text">{{$user->phone}}</div>
	</div>
	<div class="group__td col-3">
		<div class="group__text">{{$user->email}}</div>
	</div>
	<div class="group__td col-4">
		<div class="group__text">{{$user->grant == 0 ? 'НІ' : 'ТАК'}}</div>
	</div>
	<div class="group__td col-5">
		<div class="group__text">{{$user->addressOfResidence}}</div>
	</div>
	<div class="group__td col-6">
		<div class="group__text">{{$user->addressOfRegistration}}</div>
	</div>
	<div class="group__td col-7">
		<div class="group__text">{{$user->parentName1}}</div>
	</div>
	<div class="group__td col-8">
		<div class="group__text">{{$user->parentName2}}</div>
	</div>
	<div class="group__td col-9">
		<div class="group__text">{{$user->parentPhone}}</div>
	</div>
</div>