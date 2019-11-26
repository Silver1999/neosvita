@if (empty($groupID))
	@if (empty($rType))
		@include('templates.groupNotFound')
	@endif
@else
	@php
		$dayNames = ["ПОНЕДІЛОК", "ВІВТОРОК", "СЕРЕДА", "ЧЕТВЕР", "П’ЯТНИЦЯ", "СУБОТА"];
		$weekNames = [
			'Перший тиждень', 'Другий тиждень', 'Третій тиждень', 
			'Четвертий тиждень', "П'ятий тиждень", 'Шостий тиждень'
		];
	@endphp

	<input id="institutionName" type="hidden" value="{{$institutionName}}">

	<h3 class="list__periodHead">ВІДОМІСТЬ ВІДВІДУВАННЯ ЗА ТИЖДЕНЬ</h3>

	@include('templates.tableFilterWeek')

	<script src="{{ URL::asset('js/jquery.maskedinput.min.js') }}"></script>

	<div id="sheet__container">
		<div id="sheet" class="sheet">
			<div id="cellEdit">
				<div class="cellEditWrap">
					<div class="cellEditCell" data-val="-1">
						<div class="cellEditFirstCell"></div>
					</div>
					<div class="cellEditCell" data-val="0"></div>
					<div class="cellEditCell" data-val="1">СП</div>
					<div class="cellEditCell" data-val="2">ПП</div>
					<div class="cellEditCell" data-val="3">БП</div>
				</div>
			</div>

			@include('templates.tableSortRow')

			<div class="sheet__content">
				<div class="sheet__headRow">
					<div class="sheet__cell cell-n color-primary">№</div>
					<div class="sheet__cell cell-name color-primary">ПРІЗВИЩЕ ТА ІНІЦІАЛИ СТУДЕНТА</div>
					<div class="sheet__days">
						@foreach ($days as $day)
							<div class="cell-day {{"col-". $loop->iteration}} {{$loop->first ? 'visible' : ''}}" data-id="{{$day->id}}" data-group="{{$groupID}}">
								<div class="sheet__dayName color-primary">
									{{$dayNames[$loop->index]}}&nbsp;
									<span class="dayDate">
										{{sprintf('%02d.%02d.%d', $day->day, $day->month, $day->year)}}
									</span>
								</div>
								<div class="sheet__disciples">
									@php
										$dayDisciplines = unserialize($day->content);
									@endphp
									@if (!empty($dayDisciplines))
										@foreach ($dayDisciplines as $discipline)
											<div class="sheet__cell sheet__disciple color-secondary">
												<span class="rotated">
													<span class="discipleNameIndex">{{$loop->iteration}}. </span>
													<span class="discipleNameText">{{$discipline[0]}}</span>
												</span>
											</div>
										@endforeach
									@else
										@for ($i = 1; $i < 9; $i++)
											<div class="sheet__cell sheet__disciple color-secondary">
												<span class="rotated">
													<span class="discipleNameIndex">{{$i}}. </span>
													<span class="discipleNameText"></span>
												</span>
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
								@foreach ($days as $day)
									@if (empty($skips_sorted[$user->id][$day->id]))
										<div class="sheet__itemDaySkips {{"col-". $loop->iteration}} {{$loop->first ? 'visible' : ''}}" data-w="{{$currentWeek}}" data-day="{{$day->id}}" data-user="{{$user->id}}">
											@for ($i = 0; $i < 8; $i++)
												<div class="sheet__cell cell-skip" data-idx="{{$i}}" data-val="0"></div>
											@endfor
										</div>
									@else
										@php
											$skips = unserialize($skips_sorted[$user->id][$day->id]);
										@endphp
										<div class="sheet__itemDaySkips {{"col-". $loop->iteration}} {{$loop->first ? 'visible' : ''}}" data-w="{{$currentWeek}}" data-day="{{$day->id}}" data-user="{{$user->id}}">
											@foreach ($skips as $skip)
												@php
													$status = '';
													switch($skip){
														case 1: $status = 'sp'; break;
														case 2: $status = 'pp'; $pp++; break;
														case 3: $status = 'bp'; $bp++; break;
													}
												@endphp
		
												<div class="sheet__cell cell-skip {{$status}}" data-idx="{{$loop->index}}" data-val="{{$skip}}"></div>
											@endforeach
										</div>
									@endif
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
		@for ($i = 0; $i < count($days); $i++)
			<div class="switcherCase list__switchCase {{$i == 0 ? 'active' :''}}" data-col="col-{{$i+1}}"></div>
		@endfor
		<div class="switcherCase list__switchCase" data-col="col-999"></div>
	</div>

	<div class="sheetSwitch">
		<div class="sheetSwitch__button sheetSwitch__button-active" data-val="week"><span class="mob-no">ВІДОМІСТЬ</span> ЗА ТИЖДЕНЬ</div>
		<div class="sheetSwitch__button" data-val="month"><span class="mob-no">ВІДОМІСТЬ</span> ЗА МІСЯЦЬ</div>
		<div class="sheetSwitch__button" data-val="semester"><span class="mob-no">ВІДОМІСТЬ</span> ЗА СЕМЕСТР</div>
	</div>

	<div class="controlBlock list__edit">
		<div class="select list__period" data-type="week">
			<div class="select__wrapper">
				@php
					$period = 'Виберіть тиждень';
					if($arr['period'] > 0){
						for ($i = 0; $i < $maxWeek - $minWeek + 1; $i++) {
							if($minWeek + $i == $arr['period']){
								$period = $weekNames[$i];
								break;
							}
						}
					}
				@endphp
				<div class="select__item select__item-first {{$arr['period'] > 0 ? 'select__item-active' : ''}}" data-id="{{$currentWeek}}">{{$period}}</div>
				<?php for ($i = 0; $i < $maxWeek - $minWeek + 1; $i++) { ?>
					<div class="select__item" data-id="{{$minWeek + $i}}">{{$weekNames[$i]}}</div>
				<?php } ?>
			</div>
		</div>

		<div class="m-auto"></div>

		@if ($permission > 1)
			<button class="list__submit button-cancel list__editCancel button-nosave">ВІДМІНИТИ ЗМІНИ</button>
			<button class="list__submit button-ok list__editSubmit button-save">ЗБЕРЕГТИ ЗМІНИ</button>
			<button class="list__submit button-cancel list__editEdit button-edit">РЕДАГУВАТИ</button>
		@endif

		@if ($permission > 3)
			<button class="list__submit button-cancel list__sendSubmit button-mail">РОЗІСЛАТИ</button>
		@endif
		
		<button class="list__submit button-ok list__exelExport button-download button-download">ЗАВАНТАЖИТИ EXCEL</button>
		

	</div>

	@include('templates.listPageOverlay')
		
	<script src="{{ URL::asset('js/list.min.js') }}"></script>
@endif

