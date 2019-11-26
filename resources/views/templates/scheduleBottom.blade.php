@php
	$arr_time = [80, 60, 45];
	$weekNames = [
		'Перший тиждень', 'Другий тиждень', 'Третій тиждень', 
		'Четвертий тиждень', "П'ятий тиждень", 'Шостий тиждень'
	];
@endphp

@if (!$timeSchedule->isEmpty())
	<div class="schedule__label">РОЗКЛАД ПРОВЕДЕННЯ ПРЕДМЕТІВ</div>
	<div class="schedule__cards table-time">
		@if ($permission > 4)
			@for ($i = 0; $i < 3; $i++)
				<div class="scheduleCard scheduleTimeCard {{"col-" . ($i + 1)}} {{$i == 0 ? "visible" : ""}}">
					<div class="scheduleCard__head">ПО {{$arr_time[$i]}} ХВИЛИН</div>
						<?php for ($j = 0; $j < 8; $j++) {
							$lesson = $timeSchedule[$i*8 + $j];
						?>
						<div class="scheduleCard__item">
							<div class="scheduleCard__disciplineIndex">{{$lesson->lesson}}.</div>
							<div class="scheduleCard__discipline scheduleTimeCard__item">
								<div class="scheduleCard__disciplineName scheduleTimeCard__time">{{$lesson->time}}</div>
								<input type="text" class="inputText scheduleTimeCard__edit" data-id="{{$lesson->id}}"
								tabindex="{{$i*8 + $j}}" placeholder="Початок пари - Кінець пари">
							</div>
						</div>
					<?php } ?>
				</div>
			@endfor
		@else
			@for ($i = 0; $i < 3; $i++)
				<div class="scheduleCard scheduleTimeCard {{"col-" . ($i + 1)}} {{$i == 0 ? "visible" : ""}}">
					<div class="scheduleCard__head">ПО {{$arr_time[$i]}} ХВИЛИН</div>
						<?php for ($j = 0; $j < 8; $j++) {
							$lesson = $timeSchedule[$i*8 + $j];
						?>
						<div class="scheduleCard__item">
							<div class="scheduleCard__disciplineIndex">{{$lesson->lesson}}.</div>
							<div class="scheduleCard__discipline scheduleTimeCard__item">
								<div class="scheduleCard__disciplineName scheduleTimeCard__time">{{$lesson->time}}</div>
							</div>
						</div>
					<?php } ?>
				</div>
			@endfor
		@endif


		<div class="switcher schedule__switch" data-table="table-time">
			<div class="switcherCase schedule__switchCase active" data-col="col-1"></div>
			<div class="switcherCase schedule__switchCase" data-col="col-2"></div>
			<div class="switcherCase schedule__switchCase" data-col="col-3"></div>
		</div>
	</div>
@endif

<div class="controlBlock schedule__edit">
<div class="select schedule__week">
	<div class="select__wrapper">
		@if (empty($schedule_sorted))
			<div class="select__item select__item-first" data-id="{{$currentWeek}}">Виберіть тиждень</div>
		@else
			@php
				$period = 'Виберіть тиждень';
				if($currentWeek > 0){
					for ($i = 0; $i < $maxWeek - $minWeek + 1; $i++) {
						if($minWeek + $i == $currentWeek){
							$period = $weekNames[$i];
							break;
						}
					}
				}
			@endphp
			<div class="select__item select__item-first select__item-active" data-id="{{$currentWeek}}">{{$period}}</div>
		@endif
		
		<?php for ($i = 0; $i < $maxWeek - $minWeek + 1; $i++) { ?>
			<div class="select__item" data-id="{{$minWeek + $i}}">{{$weekNames[$i]}}</div>
		<?php } ?>
	</div>
</div>

@if ($permission > 4)
	<button class="schedule__submit schedule__editCancel button-nosave">ВІДМІНИТИ ЗМІНИ</button>
	<button class="schedule__submit schedule__editSubmit button-save">ЗБЕРЕГТИ ЗМІНИ</button>
	<button class="schedule__submit schedule__editEdit button-edit">РЕДАГУВАТИ ДАНІ</button>
@endif

</div>

<script src="{{ URL::asset('js/jquery.maskedinput.min.js') }}"></script>
<script src="{{ URL::asset('js/schedule.min.js') }}"></script>