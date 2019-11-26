$(document).ready(function () {
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$(".hamburger").click(function () {
		$(this).toggleClass("is-active");
		$(".navbar__navMob").slideToggle("fast");
	});

	$('body').on('click', '.select', function (e) {
		$(this).siblings(".select-active").removeClass("select-active");
		$(this).toggleClass("select-active");
		var target = $(e.target);

		target.siblings('.select__item-first').addClass("select__item-active")
			.html(target.text()).data('id', target.data('id'))
			.siblings(".select__input").val(target.data('id'));
	});

	$(".reg__buttons").click(function (e) {
		var oldStage = $(this).data('stage');
		var stage = oldStage;
		var sender = $(e.target);

		if (sender.hasClass('reg__next')) {
			var form = $(this).siblings(".reg__stage-active");
			checkInput(form.find(".inputText"));
			checkSelect(form.find(".select"));
			if(stage == 1){
				checkDate(form.find(".reg__dob"));
			}
			if (stage == 3) {
				checkMail(form.find(".reg__mail"));
				checkCheckBox(form.find(".checkbox"));
				passCompare(form.find(".reg__pass"), form.find(".reg__repass"));
			}
			if (form.find(".error").length > 0) return;

			if (stage < 5) stage++;
		} else if (sender.hasClass('reg__prev')) {
			if (stage > 1) stage--;
		} else {
			return false;
		}

		$("#reg").find(".reg__stage" + oldStage).removeClass("reg__stage-active")
			.end().find(".reg__stage" + stage).addClass("reg__stage-active");

		switch (stage) {
			case 1:
				$(this).find(".reg__prev").hide();
				$(this).find(".reg__next").html('ДАЛІ');
				break;
			case 2:
				$(this).find(".reg__prev").show();
				$(this).find(".reg__next").html('ДАЛІ');
				break;
			case 3:
				$(this).find(".reg__prev, .reg__next").show();
				$(this).find(".reg__next").html('ЗАВЕРШИТИ');
				$("#reg .form__footer").css("display", "flex");
				break;
			case 4:
				var form = $(".reg__stage-active");
				var btnsBar = $(this);
				form.find(".reg__mess").html('');
				btnsBar.find(".reg__prev").hide();

				$.post("/register", $("#reg").serialize())
					.done(function (response) {
						form.find(".reg__mess").html(response.message);
						if (response.status == 'ok') {
							
							btnsBar.find(".reg__next").html('НА ГОЛОВНУ');
						} else {
							btnsBar.find(".reg__prev").show();
							btnsBar.find(".reg__next").hide();
						}

					}).fail(function (response) {
						form.find(".reg__mess").html("Помилка зв'язку з сервером.<br>Будь ласка, спробуйте пізніше.");
						btnsBar.find(".reg__next").hide();
						btnsBar.find(".reg__prev").show();
					});
				break;
			case 5:
				$("#reg").hide();
				$("#authForm").show();
				break;
		}

		$(this).data('stage', stage);
	});

	$(".forgot__buttons").click(function (e) {
		var oldStage = $(this).data('stage');
		var stage = oldStage;
		var sender = $(e.target);

		if (sender.hasClass('forgot__next')) {
			var form = $(this).siblings(".forgot__stage-active");
			switch (stage) {
				case 1:
					checkMail(form.find(".forgot__mail"));
					break;
				case 2:
					checkInput(form.find(".forgot__code"));
					break;
				case 3:
					passCompare(form.find(".forgot__pass"), form.find(".forgot__repass"));
					break;
			}
			if (form.find(".error").length > 0) return;

			if (stage < 5) stage++;
		} else if (sender.hasClass('forgot__prev')) {
			if (stage > 1) stage--;
		} else {
			return false;
		}

		$("#forgotForm")
			.find(".forgot__stage" + oldStage).removeClass("forgot__stage-active")
			.end()
			.find(".forgot__stage" + stage).addClass("forgot__stage-active");

		switch (stage) {
			case 1:
				$(this).find(".forgot__prev").hide();
				$(this).find(".forgot__next").html('ДАЛІ').show();
				break;
			case 2:
				$(this).find(".forgot__prev").show();
				var nestBtn = $(this).find(".forgot__next").html('ДАЛІ')
				
				if(oldStage == 1){
					nestBtn.hide();
					$.post("sendRestore", {email: form.find('.forgot__mail').val()})
						.done(function () {
							nestBtn.show();
						}).fail(function () {});
				}

				break;
			case 3:
				$(this)
					.find(".forgot__prev, .forgot__next").show()
					.filter(".forgot__next").html('ДАЛІ');
				break;
			case 4:
				var form = $("#forgotForm");
				$(this).find(".forgot__next").html('ЗАВЕРШИТИ');
				var btnsBar = $(this);
				btnsBar.find(".forgot__prev").hide();
				form.find(".forgot__mess").html('');

				$.post("restore", form.serialize())
					.done(function (response) {
						form.find(".forgot__mess").html(response.message);
						if (response.status == 'err') {
							btnsBar.find(".forgot__next").hide();
							btnsBar.find(".forgot__prev").show();
						}

					}).fail(function () {});
				break;
			case 5:
				$("#forgotForm").hide();
				$("#authForm").show();
				break;
		}

		$(this).data('stage', stage);
	});

	//вызов auth
	$(".reg__close, .forgot__auth").click(function () {
		$("#reg, #forgotForm").hide();
		var form = $("#authForm");
		form.find(".auth__mail, .auth__pass").val('').removeClass("error");
		form.show();
	});

	//вызов forgot
	$(".reg__forgot, .auth__forgot").click(function () {
		$("#reg, #authForm").hide();

		var form = $("#forgotForm");
		form.find(".forgot__mail, .forgot__code, .forgot__pass, .forgot__repass")
			.removeClass('error').val('')
		form.find(".forgot__buttons").data("stage", 1)
			.siblings(".forgot__stage-active").removeClass("forgot__stage-active")
			.end().siblings(".forgot__stage1").addClass("forgot__stage-active");
		form.find(".forgot__prev").hide().siblings(".forgot__next").html("ДАЛІ").show();
		form.show();
	});

	//вызов рег
	$(".form__reg, .forgot__reg").click(function () {
		$("#authForm, #forgotForm").hide();

		var regForm = $("#reg");
		regForm.find(".reg__stage-active").removeClass("reg__stage-active");
		regForm.find(".reg__stage1").addClass("reg__stage-active");
		regForm.find(".reg__buttons").data("stage", 1);
		regForm.find(".reg__prev").hide().siblings(".reg__next").html("ДАЛІ").show();
		resetForm(regForm);
		regForm.show();
	});

	$(".navbar__link, .navbar__linkMob").click(function (e) {
		e.preventDefault();

		if($(this).is(".navbar__link")){
			$(this).siblings().removeClass("navbar__link-active").end().addClass("navbar__link-active");
		} else {
			$(".hamburger").removeClass("is-active");
			$(this).parent().hide();
		}

		if ($(this).is(".link-home")) {
			$('#content').empty();
			return false;
		}

		var url = $(this).data(url);
		$.post(url, function (data) {
			$("#content").html(data);

			var state = {};
			var title = 'Neosvita';

			history.pushState(state, title, url.url);

			$([document.documentElement, document.body]).animate({
				scrollTop: $("#content").offset().top
			}, 1000);
		});
	});

	$(".auth__next").click(function (e) {
		var form = $("#authForm");
		checkInput(form.find(".inputText"));
		checkMail(form.find(".auth__mail"));
		if (form.find(".error").length > 0) return;

		$.post("/login", form.serialize())
			.done(function (response) {
				if (response.status == "ok") {
					window.location.href = "/";
				} else if (response.status == "err") {
					if (!response.email) form.find(".auth__mail").addClass("error");
					if (!response.pass) form.find(".auth__pass").addClass("error");
				}
			}).fail(function () {
				window.location.href = "/";
			});
	});

	$("#reg, #authForm, #forgotForm").submit(function (e) {
		e.preventDefault();
	});

	$(".navbar__logout, .navbar__logoutMob").click(function (e) {
		$.post("/logout")
			.done(function (response) {
				if (response == "ok") {
					window.location.href = "/";
				}
			});
	});

	//-------------------------------------------------------------------------------

	if ($(".reg__dob").length > 0) {
		var cleave = new Cleave('.reg__dob', {
			date: true,
			delimiter: '-',
			datePattern: ['d', 'm', 'Y']
		});
	};
});
//------------------------------------ FUNCTIONS ------------------------------
function checkInput(inputs) {
	inputs.each(function () {
		$(this).val($(this).val().trim());
		if ($(this).val().length < 3) {
			$(this).addClass("error");
		} else {
			$(this).removeClass("error");
		}
	});
};

function checkSelect(selects) {
	selects.each(function () {
		if ($(this).find(".select__item-first").data("id")) {
			$(this).removeClass("error");
		} else {
			$(this).addClass("error");
		}
	});
};

function checkCheckBox(checkBox) {
	checkBox.each(function () {
		if ($(this).find(".checkbox__native").is(':checked')) {
			$(this).removeClass("error");
		} else {
			$(this).addClass("error");
		}
	});
};

function checkMail(inputs) {
	inputs.each(function () {
		if (isValidEmailAddress($(this).val())) {
			$(this).removeClass("error");
		} else {
			$(this).addClass("error");
		}
	});
};

function checkDate(inputs) {
	var pattern = /^(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}$/i;
	inputs.each(function () {
		if (pattern.test($(this).val())) {
			$(this).removeClass("error");
		} else {
			$(this).addClass("error");
		}
	});
};

function passCompare(input1, input2) {
	var pass1 = input1.val().trim();
	var pass2 = input2.val().trim();
	if (pass1.length < 6 || pass2.length < 6 || pass1 != pass2) {
		input1.addClass("error");
		input2.addClass("error");
	} else {
		input1.removeClass("error");
		input2.removeClass("error");
	}
};

function resetForm(form) {
	form.find(".inputText").each(function () {
		$(this).val("").removeClass("error");
	});
	form.find(".select").each(function () {
		var firstItem = $(this).find(".select__item-first");
		firstItem.removeClass("select__item-active").data("id", 0).html(firstItem.data("default"));
		firstItem.siblings(".select__input").val("");
		$(this).removeClass("error");
	});
	form.find(".checkbox").each(function () {
		$(this).removeClass("error").find(".checkbox__native").prop("checked", false);
	});
};

function isValidEmailAddress(emailAddress) {
	var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
	return pattern.test(emailAddress);
};

function findInSelect(select, text){
	text = text.trim();
	if(!select.length || !text.length) return false ;
	var exists = false;

	select.find(".select__item").each(function(){
		if(text == $(this).text().trim()) {
			exists = true;
		}
	});

	return exists;
};

function resetSelect(select){
	select.removeClass("select-active error");
	var activeItem = select.find(".select__item-first");
	activeItem.data("id", 0);
	activeItem.html(activeItem.data("default"));
	activeItem.removeClass("select__item-active");

}