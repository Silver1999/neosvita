$(document).ready(function () {
	$(".schedule__switch").click(function (e) {
		var sender = $(e.target);

		if (sender.is(".schedule__switchCase")) {
			sender
				.siblings().removeClass("active")
				.end().addClass("active");

			var table = $("." + $(this).data("table"));
			table.find(".visible").removeClass("visible");
			table.find("." + sender.data("col")).addClass("visible");
		}
	});

	//-------------------------------------------------------------------------------

	$(".schedule__week").click(function(e){
		var target = $(e.target);
		if (!target.is(".select__item") || target.is(".select__item-first")) return;
		var form = $(".schedule__search");

		checkInput(form.find(".searchBar__group"));
		checkSelect(form.find(".searchBar__institution"));

		if(!form.find(".error").length) {
			var data = { 
				group: form.find(".searchBar__group").val().trim(),
				institution: form.find(".searchBar__institution .select__item-first").data("id"),
				week: target.data("id") 
			};
			getSchedule(data);
		}

		scrollTo(".schedule__search", 1000);
	});

	$(".schedule__editEdit").click(function () {
		$(this).hide()
			.siblings(".schedule__week").hide()
			.siblings(".schedule__editCancel, .schedule__editSubmit").show();

		$(".scheduleDayCard .scheduleCard__disciplineName").each(function () {
			$(this).hide()
				.siblings(".scheduleDiscipline__edit").val($(this).text()).show();
		});

		$(".scheduleCard__disciplineData").each(function () {
			$(this).hide()
				.siblings(".scheduleLocation__edit").val($(this).text()).show();
		});

		$(".scheduleTimeCard__time").each(function () {
			$(this).hide()
				.siblings(".scheduleTimeCard__edit")
				.val($(this).text()).show();
		});
	});

	$(".scheduleTimeCard__edit").focus(function () {
		$(this).addClass("edited");
		$(this).mask("99:99 - 99:99", {
			autoclear: false,
		});
	}).blur(function () {
		$(this).removeClass("edited");
	});

	$(".schedule__editCancel").click(function () {
		$(this).hide()
			.siblings(".schedule__week, .schedule__editEdit").show()
			.siblings(".schedule__editSubmit").hide();

		$(".scheduleTimeCard__edit").each(function () {
			$(this).hide()
				.siblings(".scheduleTimeCard__time").show();
		});

		$(".scheduleDayCard .scheduleCard__disciplineName").each(function () {
			$(this).show()
				.siblings(".scheduleDiscipline__edit").hide();
		});

		$(".scheduleCard__disciplineData").each(function () {
			$(this).show()
				.siblings(".scheduleLocation__edit").hide();
		});
	});

	$(".schedule__editSubmit").click(function () {
		var daySchedule = new Array();
		$(".scheduleDayCard").each(function () {
			var arr = new Array();
			var isChanged = false;

			$(this).find(".scheduleCard__discipline").each(function () {
				var newText1 = $(this).find(".scheduleDiscipline__edit").val().trim();
				var newText2 = $(this).find(".scheduleLocation__edit").val().trim();

				if (!isChanged)
					if ($(this).find(".scheduleCard__disciplineName").html() != newText1 ||
						$(this).find(".scheduleCard__disciplineData").html() != newText2) {
						isChanged = true;
					}
				arr[$(this).data("id")] = [newText1, newText2];
			});

			if (isChanged) {
				var obj = {};
				obj.id = $(this).data("id");
				obj.data = arr;
				daySchedule.push(obj);
			}
		});

		var timeSchedule = new Array();
		$(".scheduleTimeCard__edit").each(function () {
			var initTime = $(this).siblings(".scheduleTimeCard__time").text();
			if ($(this).val() != initTime) {
				timeSchedule.push([$(this).data("id"), $(this).val()])
			}
		});

		var changes = {};
		if (daySchedule.length > 0) {
			changes.group = $("#groupSchedule > .schedule__label").data("group");
			changes.schedule = daySchedule;
		}
		if (timeSchedule.length > 0) {
			changes.timeSchedule = timeSchedule;
		}

		if (!$.isEmptyObject(changes)) {
			$.post("scheduleUpdate", JSON.stringify(changes))
				.done(function (response) {
					if (response == "ok") {
						$('.schedule__editSubmit, .schedule__editCancel').hide()
							.siblings(".schedule__week, .schedule__editEdit").show();

						$(".scheduleTimeCard__edit").each(function () {
							$(this).hide()
								.siblings(".scheduleTimeCard__time").html($(this).val()).show();
						});
						$(".scheduleDiscipline__edit").each(function () {
							$(this).hide()
								.siblings(".scheduleCard__disciplineName").html($(this).val()).show();
						});

						$(".scheduleLocation__edit").each(function () {
							$(this).hide()
								.siblings(".scheduleCard__disciplineData").html($(this).val()).show();
						});
					};
				}).fail(function () {
					alert("Виникла помилка. Дані ще не введено.");
				});
		}
	});


});

function getGroupSchedule(){
	var form = $(".searchBar");

	checkInput(form.find(".searchBar__group"));
	checkSelect(form.find(".searchBar__institution"));

	if (form.find(".error").length < 1) {
		var data = {};
		data.group = form.find(".searchBar__group").val();
		data.institution = form.find(".searchBar__institution .select__item-first").data("id");
		data.week = $(".schedule__week .select__item-first").data("id");

		getSchedule(data);
	}
};

function getSchedule(data){
	$.post("getSchedule", JSON.stringify(data))
		.done(function (response) {
			if(response != 'err'){
				var notFinded = $("#groupSchedule").html(response)
					.find('.groupNotFound').length;
				
				if(notFinded){
					var code = $("#currentGroupCode");
					if(!code.is(".getSwitch")){
						code.html(data.group);
					}
				} else {
					var code = $("#currentGroupCode");
						if(code.is(".getSwitch")){
							var groupSwitcher = code.parent().siblings(".groupSwitcher");
							if(groupSwitcher.length){
								if(findInSelect(groupSwitcher, data.group)){
									code.html(data.group);
									groupSwitcher.find('.select__item-first').html(data.group).addClass('select__item-active');
								}
							}
						} else {
							code.html(data.group);
						}
					code.siblings(".institutionName").html($("#institutionName").val());
				}
			}













			// if (response != 'err') {
			// 	var exist = $("#groupSchedule").html(response)
			// 		.find('.groupNotFound').length;
			// 	if (!exist) {
			// 		$("#currentGroupCode").html(data.group)
			// 			.siblings(".institutionName").html($("#institutionName").val());
			// 	} else {
			// 		$("#currentGroupCode").html('')
			// 	}
			// }
		}).fail(function () {
			alert("Виникла помилка.");
		});
};

function groupSwitcherSchedule(e){
	var target = $(e.target);
	if (!target.is(".select__item") || target.is(".select__item-first")) return;
	var data = { 
		group: target.text(),
		institution: target.data("id"),
		week: $(".schedule__week .select__item-first").data('id')
	};
	getSchedule(data);
};