<form id="forgotForm" class="form forgot" novalidate>
	<div class="form__head">ВІДНОВЛЕННЯ ПАРОЛЮ</div>
	<div class="forgot__stage forgot__stage1 forgot__stage-active">
		<input type="email" class="inputText forgot__mail" name="email" placeholder="E-mail">
	</div>
	<div class="forgot__stage forgot__stage2">
		<input type="text" class="inputText forgot__code" name="code" placeholder="Код">
	</div>
	<div class="forgot__stage forgot__stage3">
		<input type="password" class="inputText forgot__pass" name="pass" placeholder="Пароль">
		<input type="password" class="inputText forgot__repass" placeholder="Повторно пароль">
	</div>
	<div class="forgot__stage forgot__stage4">
		<div class="forgot__mess"></div>
	</div>
	<div class="forgot__buttons" data-stage="1">
		<button class="button button-cancel forgot__prev">НАЗАД</button>
		<button class="button button-ok forgot__next">ДАЛІ</button>
	</div>
	
	<div class="form__footer">
		<div class="forgot__auth">Увійти</div>
		<div class="forgot__reg">Реєстрація</div>
	</div>
</form>
