@if (empty($groupID))
	@if (empty($rType))
		@include('templates.groupNotFound')
	@endif
@else
	@if (!empty($users))
		<input id="institutionName" type="hidden" value="{{$institutionName}}">

		@if (array_key_exists('5', $users))
			<h4 class="group__tableDesc">ІНФОРМАЦІЯ ПРО ЗАВІДУЮЧОГО ВІДДІЛЕННЯМ</h4>
			<div class="group__table table-5">
				@include('templates.groupFieldsNames')
				
				@foreach ($users['5'] as $user)
					@if ($permission > 4)
						@include('templates.groupFieldsPermissed')
					@else
						@include('templates.groupFieldsPermissedNot')
					@endif
				@endforeach
			</div>

			<div class="switcher group__switch" data-table="table-5">
				<div class="switcherCase group__switchCase active" data-col="col-1"></div>
				@for ($i = 2; $i < 10; $i++)
					<div class="switcherCase group__switchCase" data-col="col-{{ $i }}"></div>
				@endfor
			</div>
		@endif

		@if (array_key_exists('4', $users))
			<h4 class="group__tableDesc">ІНФОРМАЦІЯ ПРО КУРАТОРА</h4>
			<div class="group__table table-4">
				@include('templates.groupFieldsNames')
				
				@foreach ($users['4'] as $user)
					@if ($permission > 3)
						@include('templates.groupFieldsPermissed')
					@else
						@include('templates.groupFieldsPermissedNot')
					@endif

				@endforeach
			</div>
			<div class="switcher group__switch" data-table="table-4">
				<div class="switcherCase group__switchCase active" data-col="col-1"></div>
				@for ($i = 2; $i < 10; $i++)
					<div class="switcherCase group__switchCase" data-col="col-{{ $i }}"></div>
				@endfor
			</div>
		@endif

		@if (array_key_exists('3', $users) || array_key_exists('2', $users))
			<h4 class="group__tableDesc">ІНФОРМАЦІЯ ПРО СТАРОСТУ ТА ЗАСТУПНИКА</h4>
			<div class="group__table table-23">
				@include('templates.groupFieldsNames')
				@if (array_key_exists('3', $users))
					@foreach ($users['3'] as $user)
						@if ($permission > 3)
							@include('templates.groupFieldsPermissed')
						@else
							@include('templates.groupFieldsPermissedNot')
						@endif

					@endforeach
				@endif
				@if (array_key_exists('2', $users))
					@foreach ($users['2'] as $user)
					@if ($permission > 3)
						@include('templates.groupFieldsPermissed')
					@else
						@include('templates.groupFieldsPermissedNot')
					@endif

				@endforeach
				@endif
			</div>
			<div class="switcher group__switch" data-table="table-23">
				<div class="switcherCase group__switchCase active" data-col="col-1"></div>
				@for ($i = 2; $i < 10; $i++)
					<div class="switcherCase group__switchCase" data-col="col-{{ $i }}"></div>
				@endfor
			</div>
		@endif

		@if (array_key_exists('1', $users))
			<h4 class="group__tableDesc">ІНФОРМАЦІЯ ПРО СТУДЕНТІВ</h4>
			<div class="group__table table-1">
				@include('templates.groupFieldsNames')
				
				@foreach ($users['1'] as $user)
					@if ($permission > 3)
						@include('templates.groupFieldsPermissed')
					@else
						@include('templates.groupFieldsPermissedNot')
					@endif
				@endforeach

			</div>
			<div class="switcher group__switch" data-table="table-1">
				<div class="switcherCase group__switchCase active" data-col="col-1"></div>
				@for ($i = 2; $i < 10; $i++)
					<div class="switcherCase group__switchCase" data-col="col-{{ $i }}"></div>
				@endfor
			</div>
		@endif

		@if ($permission > 3)
			<div class="controlBlock group__edit">
				@if ($permission > 4)
					<button class="group__editButton button-cancel group__removeGroup">ВИДАЛИТИ ГРУПУ</button>
					<button class="group__editButton button-cancel group__changeGrName">ЗМІНИТИ ШИФР</button>
				@endif
				<button class="group__editButton button-cancel group__editEdit button-edit">РЕДАГУВАТИ ДАНІ</button>
				<button class="group__editButton button-cancel group__editCancel button-nosave">ВІДМІНИТИ ЗМІНИ</button>
				<button class="group__editButton button-ok group__editSubmit button-save">ЗБЕРЕГТИ ЗМІНИ</button>
			</div>
		@endif

		@if ($permission > 4)
			<div id="overlay" class="overlay">
				<div class="container">
					<div class="modal modalRemoveUser">
						<div class="modal__header">ВИДАЛЕННЯ ОСОБИ</div>
						<div class="modal__text">
							Ви справді хочете видалити <span class="userName"></span> з групи?
						</div>
						<div class="modal__buttons">
							<button class="button modal__button button-cancel">НІ</button>
							<button class="button modal__button button-ok btnRemoveUser">ВИДАЛИТИ</button>
						</div>
					</div>

					<div class="modal modalRemoveGroup">
						<div class="modal__header">ВИДАЛЕННЯ ГРУПИ</div>
						<div class="modal__text">
							Ви справді хочете видалити безповоротно дану групу?
						</div>
						<div class="modal__buttons">
							<button class="button modal__button button-cancel">НІ</button>
							<button class="button modal__button button-ok btnRemoveGroup" data-id="{{$groupID}}">ВИДАЛИТИ</button>
						</div>
					</div>

					<div class="modal modalChangeGrName">
						<div class="modal__header">ЗМІНА ШИФРУ ГРУПИ</div>
						<div class="modal__text">
							Напишіть новий шифр групи:
						</div>
						<input type="text" name="newGrName" class="modal__newGrName">
						<div class="modal__buttons">
							<button class="button modal__button button-cancel">НІ</button>
							<button class="button modal__button button-ok btnChangeGrName" data-id="{{$groupID}}">ЗМІНИТИ</button>
						</div>
					</div>
				</div>
			</div>
		@endif

		<script src="{{ URL::asset('js/group.min.js') }}"></script>
	@endif
@endif