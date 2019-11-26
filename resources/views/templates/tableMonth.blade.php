@php
	$weekNames = ["I", "II", "III", "IV", "V", "VI"];
	$dayNames = ["", "ПОНЕДІЛОК", "ВІВТОРОК", "СЕРЕДА", "ЧЕТВЕР", "П’ЯТНИЦЯ", "СУБОТА"];
	$monthNames = [
		[9, 'Вересень'],
		[10, 'Жовтень'],
		[11, 'Листопад'],
		[12, 'Грудень'],
		[1, 'Січень'],
		[2, 'Лютий'],
		[3, 'Березень'],
		[4, 'Квітень'],
		[5, 'Травень'],
		[6, 'Червень'],
	];
@endphp

<h3 class="list__periodHead">ВІДОМІСТЬ ВІДВІДУВАННЯ ЗА МІСЯЦЬ</h3>

@include('templates.tableFilter')

<div id="sheet__container">
	<div id="sheet" class="sheet">
		@include('templates.tableSortRow')
		<div class="sheet__content">
			<div class="sheet__headRow">
				<div class="sheet__cell cell-n color-primary">№</div>
				<div class="sheet__cell cell-name color-primary">ПРІЗВИЩЕ ТА ІНІЦІАЛИ СТУДЕНТА</div>
				<div class="sheet__days">
					@foreach ($days_sorted as $week)
						<div class="cell-day {{"col-". $loop->iteration}} {{$loop->first ? 'visible' : ''}}">
							<div class="sheet__dayName color-primary">{{$weekNames[$loop->index]}} ТИЖДЕНЬ</div>
							<div class="sheet__disciples">
								@if ($loop->first)
									@for ($i = 0; $i < $dayShift[0]; $i++)
										<div class="sheet__cell sheet__disciple color-secondary">
											<span class="rotated center"><span class="discipleNameText"></span></span>
										</div>
									@endfor
								@endif
								@foreach ($week as $day)
									<div class="sheet__cell sheet__disciple color-secondary">
										<span class="rotated center">
											<span class="discipleNameText">{{$dayNames[$day->DofW]}} {{sprintf('%02d.%02d.%d', $day->day, $day->month, $day->year)}}</span>
										</span>
									</div>
								@endforeach
								@if ($loop->last)
									@for ($i = 0; $i < $dayShift[1]; $i++)
										<div class="sheet__cell sheet__disciple color-secondary">
											<span class="rotated center"><span class="discipleNameText"></span></span>
										</div>
									@endfor
								@endif
							</div>
						</div>
					@endforeach
				</div>
				<div class="sheet__cell cell-skips color-primary col-999">
					<div class="rotated">
						ПРОПУСКИ ПО <br> ПОВАЖНІЙ ПРИЧИНІ
					</div>
				</div>
				<div class="sheet__cell cell-skips color-primary col-999">
					<div class="rotated">
						ПРОПУСКИ БЕЗ <br> ПОВАЖНОЇ ПРИЧИНИ
					</div>
				</div>
				<div class="sheet__cell cell-skips color-primary col-999">
					<div class="rotated">
						ЗАГАЛЬНА КІЛЬКІСТЬ <br> ПРОПУСКІВ
					</div>
				</div>
			</div>
	
			@if (!$users->isEmpty())
				@foreach ($users as $user)
					<div class="sheet__itemRow">
						<div class="sheet__cell cell-n color-secondary">{{$loop->iteration}}</div>
						<div class="sheet__cell cell-name color-secondary">
							@php 
								$initials = mb_substr($user->name, 0 , 1). '.';
								$initials .= mb_substr($user->patronymic, 0 , 1). '.';
							@endphp
							{{$user->surname. '. ' . $initials}}
						</div>
						<div style="display:flex;">
							@php
								$pp = 0;
								$bp = 0;
							@endphp
							@foreach ($days_sorted as $week)
								<div class="sheet__itemDaySkips {{"col-". $loop->iteration}} {{$loop->first ? 'visible' : ''}}">
									@if ($loop->first)
										@for ($i = 0; $i < $dayShift[0]; $i++)
											<div class="sheet__cell cell-skip"></div>
										@endfor
									@endif
									@foreach ($week as $day)
										@php
											$userSkip = $skips_sorted[$user->id][$day->id] ?? '';
											if(empty($userSkip)){
												$userSkip = '';
											} else {
												$pp += $userSkip->pp;
												$bp += $userSkip->bp;
												$userSkip = $userSkip->pp + $userSkip->bp == 0 ? '' : $userSkip->pp + $userSkip->bp;
											}
										@endphp
										<div class="sheet__cell cell-skip">{{$userSkip}}</div>
									@endforeach
									@if ($loop->last)
										@for ($i = 0; $i < $dayShift[1]; $i++)
											<div class="sheet__cell cell-skip"></div>
										@endfor
									@endif
								</div>
							@endforeach
						</div>
						<div class="sheet__cell cell-skips ppCount col-999">{{$pp}}</div>
						<div class="sheet__cell cell-skips bpCount col-999">{{$bp}}</div>
						@php
							$skipSum = $pp+$bp;
							$skipClass = '';
							if($skipSum >= 30){
								$skipClass = 'text-danger';
							} else if($skipSum >= 20){
								$skipClass = 'text-warning';
							}
						@endphp
						<div class="sheet__cell cell-skips sumCount col-999 {{$skipClass}}">{{$skipSum}}</div>
					</div>
				@endforeach
			@endif
		</div>
			</div>
</div>

<div class="switcher list__switch" data-table="table-1">
	@for ($i = 0; $i < count($days_sorted); $i++)
		<div class="switcherCase list__switchCase {{$i == 0 ? 'active' :''}}" data-col="col-{{$i+1}}"></div>
	@endfor
	<div class="switcherCase list__switchCase" data-col="col-999"></div>
</div>

<div class="sheetSwitch">
	<div class="sheetSwitch__button" data-val="week"><span class="mob-no">ВІДОМІСТЬ</span> ЗА ТИЖДЕНЬ</div>
	<div class="sheetSwitch__button sheetSwitch__button-active" data-val="month"><span class="mob-no">ВІДОМІСТЬ</span> ЗА МІСЯЦЬ</div>
	<div class="sheetSwitch__button" data-val="semester"><span class="mob-no">ВІДОМІСТЬ</span> ЗА СЕМЕСТР</div>
</div>

<div class="controlBlock list__edit">
	<div class="select list__period" data-type="month">
		<div class="select__wrapper">
			@php
				$period = 'Виберіть місяць';
				if(!empty($arr['period'])){
					foreach ($monthNames as $month) {
						if($month[0] == $arr['period']){
							$period = $month[1];
							break;
						}
					}
				}
			@endphp
			<div class="select__item select__item-first {{empty($arr['period']) ? '' : 'select__item-active'}}" data-id="{{date('n')}}">{{$period}}</div>
			@foreach ($monthNames as $month)
				<div class="select__item" data-id="{{$month[0]}}">{{$month[1]}}</div>
			@endforeach
		</div>
	</div>

	<div class="m-auto"></div>

	@if ($permission > 3)
		<button class="list__submit button-cancel list__sendSubmit button-mail">РОЗІСЛАТИ</button>
	@endif
	<button class="list__submit button-ok list__exelExport button-download">ЗАВАНТАЖИТИ EXCEL</button>
</div>

@include('templates.listPageOverlay')

<script src="{{ URL::asset('js/list.min.js') }}"></script>