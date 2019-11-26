<form id="authForm" class="form auth" novalidate>
	<div class="form__head">АВТОРИЗАЦІЯ</div>
	<div class="form__body">
		<input type="email" class="inputText auth__mail" name="email" placeholder="E-mail">
		<input type="password" class="inputText auth__pass" name="pass" placeholder="Пароль">
		<div class="auth__rememberBlock">
			<label class="checkbox auth__remember">
				<input type="checkbox" class="checkbox__native" name="remember">
				<img src="{{ URL::asset('img/checkmark.png') }}" alt="&#10003;" class="checkbox__check">
			</label>
			<div class="auth__rememberText">Запам’ятати мене</div>
		</div>
	</div>
	<div class="form__buttons">
		<button class="button button-ok auth__next">УВІЙТИ</button>
	</div>
	<div class="form__footer">
		<div class="form__reg">Реєстрація</div>
		<div class="form__forgot auth__forgot">Забули пароль?</div>
	</div>
</form>
