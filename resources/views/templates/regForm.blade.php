<form id="reg" class="form reg" method="post" novalidate>
	@csrf
	<div class="form__head">РЕЄСТРАЦІЯ</div>
	<div class="reg__stage reg__stage1 reg__stage-active">
		<input type="text" class="inputText reg__name" name="name" placeholder="Ім’я">
		<input type="text" class="inputText reg__surname" name="surname" placeholder="Прізвище">
		<input type="text" class="inputText reg__patronymic" name="patronymic" placeholder="По-батькові">
		<input type="text" class="inputText reg__dob" name="dob" placeholder="Дата народження">

		<div class="select">
			<div class="select__wrapper">
				<input class="select__input" type="hidden" name="sex" value="0">
				<div class="select__item select__item-first" data-id="0" data-default="Стать">Стать</div>
				<div class="select__item" data-id="1">Чоловіча</div>
				<div class="select__item" data-id="2">Жіноча</div>
			</div>
		</div>
	</div>
	<div class="reg__stage reg__stage2">
		<div class="select">
			<div class="select__wrapper">
				<input class="select__input" type="hidden" name="country" value="0">
				<div class="select__item select__item-first" data-id="0" data-default="Країна">Країна</div>
				<div class="select__item" data-id="1">Україна</div>
			</div>
		</div>
		<div class="select">
			<div class="select__wrapper">
				<input class="select__input" type="hidden" name="city" value="0">
				<div class="select__item select__item-first" data-id="0" data-default="Місто">Місто</div>
				<div class="select__item" data-id="1">Івано-Франківськ</div>
			</div>
		</div>
		<div class="select">
			<div class="select__wrapper">
				<input class="select__input" type="hidden" name="institution" value="0">
				<div class="select__item select__item-first" data-id="0" data-default="Заклад освіти">Заклад
					освіти</div>
				<div class="select__item" data-id="1">КЕП ІФНТУНГ</div>
			</div>
		</div>
		<div class="select">
			<div class="select__wrapper">
				<input class="select__input" type="hidden" name="role" value="0">
				<div class="select__item select__item-first" data-id="0" data-default="Посада">Посада</div>
				<div class="select__item" data-id="1">Студент</div>
				<div class="select__item" data-id="2">Заступник старости</div>
				<div class="select__item" data-id="3">Староста</div>
				<div class="select__item" data-id="4">Куратор</div>
				<div class="select__item" data-id="5">Завідуючий відділенням</div>
				<div class="select__item" data-id="6">Директор</div>
			</div>
		</div>
		<input type="text" class="inputText reg__group" name="group" placeholder="Шифр групи">
	</div>
	<div class="reg__stage reg__stage3">
		<input type="email" class="inputText reg__mail" name="email" placeholder="E-mail">
		<input type="password" class="inputText reg__pass" name="pass" placeholder="Пароль">
		<input type="password" class="inputText reg__repass" placeholder="Повторно пароль">
		<input type="text" class="inputText reg__code" name="code" placeholder="Код допуску">
		<div class="reg__confidentBlock">
			<label class="checkbox reg__confident">
				<input type="checkbox" class="checkbox__native">
				<img src="{{ URL::asset('img/checkmark.png') }}" alt="&#10003;" class="checkbox__check">
			</label>
			<div class="reg__confidentText">
				Я згоден (-на) з <a href="#" class="reg__confidentLink">умовами конфіденційності</a>
				і дозволяю використовувати мої особисті дані на законних засадах
			</div>
		</div>
	</div>
	<div class="reg__stage reg__stage4">
		<div class="reg__mess"></div>
	</div>
	<div class="form__buttons reg__buttons" data-stage="1">
		<button class="button button-cancel reg__prev">НАЗАД</button>
		<button class="button button-ok reg__next">ДАЛІ</button>
	</div>
	<div class="form__footer">
		<div class="form__forgot reg__close">Вже зареєстровані?</div>
		<div class="form__close reg__close">Увійти</div>
	</div>
</form>