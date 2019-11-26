<div class="controlBlock list__filter">

	<div class="select list__filterMode">
		<div class="select__wrapper">
			<div class="select__item select__item-first" data-id="0" data-type="0" data-default="Спосіб фільтру">Спосіб фільтру</div>
			<div class="select__item" data-id="1">За найменуванням</div>
			<div class="select__item" data-id="2">За певним значенням</div>
			<div class="select__item clearSelect" data-id="111">Очистити</div>
		</div>
	</div>

	<div class="list__filterBlock list__filterBlockFIO">
		<div class="select list__filterData">
			<div class="select__wrapper">
				<div class="select__item select__item-first" data-id="0"  data-default="Дані для фільтру">Дані для фільтру</div>
				<div class="select__item" data-id="1">Прізвище та ініціали</div>
			</div>
		</div>
		<button class="list__filterCancel button-cancel button-nosave"></button>
		<input type="text" class="inputText list__filterVal" placeholder="Найменування...">
	</div>

	<div class="list__filterBlock list__filterBlockSkips">
		<div class="select list__filterData">
			<div class="select__wrapper">
				<div class="select__item select__item-first" data-id="0"  data-default="Дані для фільтру">Дані для фільтру</div>
				<div class="select__item" data-id="1">Загальна кількість</div>
			</div>
		</div>
		<button class="list__filterCancel button-cancel button-nosave"></button>
		<div class="select list__filterSign">
			<div class="select__wrapper">
				<div class="select__item select__item-first" data-id="0">0</div>
				<div class="select__item" data-id="1">&gt;</div>
				<div class="select__item" data-id="2">&lt;</div>
				<div class="select__item" data-id="3">=</div>
				<div class="select__item" data-id="4">&ge;</div>
				<div class="select__item" data-id="5">&le;</div>
			</div>
		</div>
		<input type="text" class="inputText list__filterVal" placeholder="Число...">	</div>

	{{-- отключено. для недельной ведомости код в tableWeek --}}
	{{-- <div class="list__filterBlock list__filterBlockDay">
		<div class="select list__filterData">
			<div class="select__wrapper">
				<div class="select__item select__item-first" data-id="0">Дані для фільтрування</div>
				<div class="select__item" data-id="1">Дні тижня</div>
			</div>
		</div>
		<input type="text" class="inputText list__filterVal" placeholder="ДД.ММ.РР">
	</div> --}}

	<button class="list__submit button-ok list__filterSubmit button-filter">ФІЛЬТРУВАТИ</button>
</div>
<script src="{{ URL::asset('js/jquery.maskedinput.min.js') }}"></script>