@if (empty($groupID))
	@if (empty($rType))
		@include('templates.groupNotFound')
	@endif
@else
	<input id="institutionName" type="hidden" value="{{$institutionName}}">
	@if (!empty($schedule_sorted))
		<div class="schedule__label" data-group="{{$groupID}}">РОЗКЛАД НАВЧАЛЬНИХ ПРЕДМЕТІВ</div>
		<div class="schedule__cards table-disc">
			@php
				$daysNames = [ '',
					"ПОНЕДІЛОК", "ВІВТОРОК", "СЕРЕДА",
					"ЧЕТВЕР", "П’ЯТНИЦЯ", "СУБОТА",
				];
			@endphp

			@if ($permission > 4)
				@foreach ($schedule_sorted as $day)
					<div class="scheduleCard scheduleDayCard {{"col-" . $loop->iteration}} {{$loop->iteration == 1 ? "visible" : ""}}" data-id="{{$day->id}}">
						<div class="scheduleCard__head">{{$daysNames[$day->DofW]}}</div>
						@php
							$content = empty($day->content) ? '' : unserialize($day->content);
						@endphp
						@if (empty($content))
							@for ($j = 0; $j < 8; $j++)
								<div class="scheduleCard__item">
									<div class="scheduleCard__disciplineIndex">{{$j+1}}.</div>
									<div class="scheduleCard__discipline" data-id="{{$j}}">
										<div class="scheduleCard__disciplineName"></div>
										<input type="text" class="inputText scheduleDiscipline__edit" placeholder="Назва навчальної дисципліни">
										<div class="scheduleCard__disciplineData"></div>
										<input type="text" class="inputText scheduleLocation__edit" placeholder="ПІП викладача - Аудиторія">
									</div>
								</div>
							@endfor
						@else
							@for ($j = 0; $j < 8; $j++)
								<div class="scheduleCard__item">
									<div class="scheduleCard__disciplineIndex">{{$j+1}}.</div>
									<div class="scheduleCard__discipline" data-id="{{$j}}">
										<div class="scheduleCard__disciplineName">{{$content[$j][0]}}</div>
										<input type="text" class="inputText scheduleDiscipline__edit" placeholder="Назва навчальної дисципліни">
										<div class="scheduleCard__disciplineData">{{$content[$j][1]}}</div>
										<input type="text" class="inputText scheduleLocation__edit" placeholder="ПІП викладача - Аудиторія">
									</div>
								</div>
							@endfor
						@endif
					</div>
				@endforeach
			@else
				@foreach ($schedule_sorted as $day)
					<div class="scheduleCard scheduleDayCard {{"col-" . $loop->iteration}} {{$loop->iteration == 1 ? "visible" : ""}}" data-id="{{$day->id}}">
						<div class="scheduleCard__head">{{$daysNames[$day->DofW]}}</div>
						@php
							$content = empty($day->content) ? '' : unserialize($day->content);
						@endphp
						@if (empty($content))
							@for ($j = 0; $j < 8; $j++)
							<div class="scheduleCard__item">
								<div class="scheduleCard__disciplineIndex">{{$j+1}}.</div>
								<div class="scheduleCard__discipline" data-id="{{$j}}">
									<div class="scheduleCard__disciplineName"></div>
									<div class="scheduleCard__disciplineData"></div>
								</div>
							</div>
							@endfor
						@else
							@for ($j = 0; $j < 8; $j++)
								<div class="scheduleCard__item">
									<div class="scheduleCard__disciplineIndex">{{$j+1}}.</div>
									<div class="scheduleCard__discipline" data-id="{{$j}}">
										<div class="scheduleCard__disciplineName">{{$content[$j][0]}}</div>
										<div class="scheduleCard__disciplineData">{{$content[$j][1]}}</div>
									</div>
								</div>
							@endfor
						@endif
					</div>
				@endforeach
			@endif

			<div class="switcher schedule__switch" data-table="table-disc">
				<div class="switcherCase schedule__switchCase active" data-col="col-1"></div>
				<div class="switcherCase schedule__switchCase" data-col="col-2"></div>
				<div class="switcherCase schedule__switchCase" data-col="col-3"></div>
				<div class="switcherCase schedule__switchCase" data-col="col-4"></div>
				<div class="switcherCase schedule__switchCase" data-col="col-5"></div>
				<div class="switcherCase schedule__switchCase" data-col="col-6"></div>
			</div>
		</div>
	@endif
@endif

@include('templates.scheduleBottom')