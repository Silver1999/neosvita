<div class="group__row userRow editable" data-id="{{$user->id}}">
	<div class="group__td group__tdID">{{$loop->iteration}}</div>
	<div class="group__td group__tdName">
		@php 
			$initials = mb_substr($user->name, 0 , 1). '.' . mb_substr($user->patronymic, 0 , 1). '.';
		@endphp
		<div class="userName">{{$user->surname . ' ' . $initials}}</div>
		<div class="group__delete" data-id="{{$user->id}}"></div>
	</div>
	<div class="group__td col-1 visible">
		@php
			$dob = new DateTime($user->dayOfBirth);
		@endphp
		<div class="group__text">{{$dob->format('d-m-Y')}}</div>
		<input type="text" class="inputText group__input userDofB">
	</div>
	<div class="group__td col-2">
		<div class="group__text">{{$user->phone}}</div>
		<input type="text" class="inputText group__input userPhone">
	</div>
	<div class="group__td col-3">
		<div class="group__text">{{$user->email}}</div>
		<input type="text" class="inputText group__input userEmail">
	</div>
	<div class="group__td col-4">
		<div class="group__text">{{$user->grant == 0 ? 'НІ' : 'ТАК'}}</div>
		<div class="group__grant">
			<div class="group__radios">
				<div class="group__radio"><input class="radioNO" type="radio" name="grant{{$user->id}}" value="0" data-val="НІ">НІ</div>
				<div class="group__radio"><input class="radioYES" type="radio" name="grant{{$user->id}}" value="1" data-val="ТАК">ТАК</div>
			</div>
		</div>
	</div>
	<div class="group__td col-5">
		<div class="group__text">{{$user->addressOfResidence}}</div>
		<input type="text" class="inputText group__input userAddressOfResidence">
	</div>
	<div class="group__td col-6">
		<div class="group__text">{{$user->addressOfRegistration}}</div>
		<input type="text" class="inputText group__input userAddressOfRegistration">
	</div>
	<div class="group__td col-7">
		<div class="group__text">{{$user->parentName1}}</div>
		<input type="text" class="inputText group__input userParentName1">
	</div>
	<div class="group__td col-8">
		<div class="group__text">{{$user->parentName2}}</div>
		<input type="text" class="inputText group__input userParentName2">
	</div>
	<div class="group__td col-9">
		<div class="group__text">{{$user->parentPhone}}</div>
		<input type="text" class="inputText group__input userParentPhone">
	</div>

</div>