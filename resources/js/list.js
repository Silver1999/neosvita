var cellEdit = $("#cellEdit"); //контекстное меню редактирования ячейки
var currentCell = null; //последняя кликнутая ячейка
var editedSkips = [];
var editing = false;
var errorText = "Виникла помилка.";
var isFilteredByDay = false;

$(document).ready(function () {
	resizeCellMid();

	$( window ).resize(function() {
		resizeCellMid();
	});

	//-------------------------- Filter -----------------------------
	$(".list__filterMode").click(function (e) {
		var target = $(e.target);
		var value = 0;
		if (target.is(".select__item") && !target.is(".select__item-first")  && !target.is(".clearSelect")) {
			value = target.data("id");
		}

		if (value) {
			$(this).data("type", value)
				.siblings(".list__filterBlock").hide();
			var blockClass = '';

			$("#sheet .filtered").removeClass("filtered");
			$(".sheet__sortRow .cell-mid").css("width", $(".sheet__days").outerWidth());

			//Восстановление пропусков после фильтра по дню
			if(isFilteredByDay && value != 3){
				$(".sheet__itemRow").find(".ppCount, .bpCount, .sumCount").each(function(){
					$(this).html($(this).data("sum"));
				});
				isFilteredByDay = false;
			}

			switch (value) {
				case 1:
					blockClass = '.list__filterBlockFIO';
					break;
				case 2:
					blockClass = '.list__filterBlockSkips';
					break;
				case 3:
					blockClass = '.list__filterBlockDay';
					break;
			}
			
			var filterData = $(this).siblings(blockClass).show()
				.children().hide()
				.filter(".list__filterData").show();
			resetSelect(filterData);
		}

	});

	$(".clearSelect").click(function (e) {
		e.stopImmediatePropagation();
		var select = $(this).parent().parent();
		var block = select.siblings(".list__filterBlock:visible").hide();

		if(block.length){
			block.children().hide();
			resetSelect(block.find(".list__filterData").hide());
			block.find(".list__filterVal").val("");
			resetSign(block.find(".list__filterSign"));
		}

		select.siblings(".select-active").removeClass("select-active");
		select.removeClass("select-active");
		resetSelect(select);
	});

	$(".list__filterData").click(function (e) {
		var target = $(e.target);
		var isMob = window.innerWidth < 993;

		if (!target.is('.select__item-first')) {
			switch ($(".list__filterMode").data("type")) {
				case 1:
					$(this).siblings(".list__filterVal").val('');
					break;
				case 2:
					$(this)
						.siblings(".list__filterSign").removeClass("active")
						.find(".select__item-first").removeClass("select__item-active")
						.data("id", 0).html("0");
					$(this)
						.siblings(".list__filterVal").val('')
						.mask("9999", {
							autoclear: false,
							placeholder: " "
						});
					break;
				case 3:
					$(this).siblings(".list__filterVal").val('')
						.mask("99.99.9999", {
							autoclear: false,
							placeholder: " "
						});
					break;
			}
			if (isMob) {
				$(this).hide()
					.siblings().show()
					.parent().addClass("mob")
					.siblings(" .list__filterMode").hide();
			} else {
				$(this).siblings().not(".list__filterCancel").show();
			}

		}
	});

	$(".list__filterSign").click(function (e) {
		var target = $(e.target);

		if (!target.is('.select__item-first')) {
			$(this).addClass("active");
		}
	});

	$(".list__filterSubmit").click(function (e) {
		var typeSelect = $(this).siblings(".list__filterMode");
		var type = typeSelect.find(".select__item-first").data("id");
		if (type) {
			typeSelect.removeClass("error");
			switch (type) {
				case 1: // фильтр по имени студента
					var block = $(this).siblings(".list__filterBlockFIO");
					block.find(".error").removeClass("error");

					var selectData = block.find(".list__filterData");
					if (!selectData.find(".select__item-first").data("id")) {
						selectData.addClass("error");
						return;
					}

					var input = block.find(".list__filterVal");
					var substr = input.val().trim();

					if (substr.length) {
						input.removeClass("error");
						$(".sheet__content .sheet__itemRow").each(function () {
							var name = $(this).find(".cell-name").text().trim();
							if (name.indexOf(substr) == 0) {
								$(this).removeClass("filtered");
							} else {
								$(this).addClass("filtered");
							}
						});
					} else {
						input.addClass("error");
						$(".sheet__content .sheet__itemRow").each(function () {
							$(this).removeClass("filtered");
						});
					}
					break;

				case 2: // фильтр по пропускам
					var block = $(this).siblings(".list__filterBlockSkips");
					block.find(".error").removeClass("error");

					var selectData = block.find(".list__filterData");
					if (!selectData.find(".select__item-first").data("id")) {
						selectData.addClass("error");
						return;
					}

					var sign = block.find(".list__filterSign .select__item-first").data('id');
					if (sign == '0') {
						block.find(".list__filterSign").addClass("error");
					}

					var value = parseInt(block.find(".list__filterVal").val());

					if (block.find(".error").length < 1) {
						$(".sheet__content .sheet__itemRow").each(function () {
							var skips = parseInt($(this).find(".sumCount").text());

							switch (sign) {
								case 1:
									if (skips <= value) {
										$(this).addClass("filtered");
									} else {
										$(this).removeClass("filtered");
									}
									break;
								case 2:
									if (skips >= value) {
										$(this).addClass("filtered");
									} else {
										$(this).removeClass("filtered");
									}
									break;
								case 3:
									if (skips != value) {
										$(this).addClass("filtered");
									} else {
										$(this).removeClass("filtered");
									}
									break;
								case 4:
									if (skips < value) {
										$(this).addClass("filtered");
									} else {
										$(this).removeClass("filtered");
									}
									break;
								case 5:
									if (skips > value) {
										$(this).addClass("filtered");
									} else {
										$(this).removeClass("filtered");
									}
									break;
							}
						});
					} else {
						$(".sheet__content .sheet__itemRow").each(function () {
							$(this).removeClass("filtered");
						});
					}
					break;

				case 3: // фильтр по дню
					var block = $(this).siblings(".list__filterBlockDay");
					var selectData = block.find(".list__filterData");
					if (!selectData.find(".select__item-first").data("id")) {
						selectData.addClass("error");
						return;
					}

					var input = block.find(".list__filterVal");
					block.find(".error").removeClass("error");

					var value = input.val().trim();
					if (value.match(/^\d\d.\d\d\.\d\d\d\d$/g)) {
						input.removeClass("error");

						$(".sheet__days .cell-day").each(function () {
							var currentDate = $(this).find(".dayDate").text().trim();
							var dayID = $(this).data("id");

							if (value == currentDate) {
								//верхняя часть столбца
								$(this).removeClass("filtered").addClass("visible");
								//нижняя часть с пропусками
								var userDaySkeeps = $('.sheet__itemDaySkips[data-day="' + dayID + '"]');
								userDaySkeeps.removeClass("filtered").addClass("visible");

								//изменение сумм пропусков по фильтру
								isFilteredByDay = true;
								skipsCountWithDayFilter(userDaySkeeps);
							} else {
								$(this).addClass("filtered").removeClass("visible");
								$('.sheet__itemDaySkips[data-day="' + dayID + '"]')
									.addClass("filtered").removeClass("visible");
							}
						});
						$(".sheet__sortRow .cell-mid").css("width", $(".sheet__days").outerWidth());

					} else {
						input.addClass("error");
					}

					break;
			}
		} else {
			typeSelect.addClass("error");
		}
	});

	$(".list__filterCancel").click(function () {
		//очистка инпута и знака
		$(this)
			.siblings().removeClass("error")
			.filter(".list__filterVal").val('');

		resetSign($(this).siblings(".list__filterSign"));

		var filterData = $(this).hide()
			.siblings().hide().filter(".list__filterData");

		resetSelect(filterData);

		var filterMode = $(this)
			.parent().removeClass("mob")
			.siblings(".list__filterMode").show();

		resetSelect(filterMode);
		
	});
	//---------------------------------------------------------------
	$(".sheet__sortRow .sheet__cell").click(function () {
		$(this).toggleClass("desc")
			.siblings(".desc").removeClass("desc");
		var classes = ['sheetSortN', 'sheetSortName', 'sheetSortPP', 'sheetSortBP', 'sheetSortSUM'];

		// нажатая кнопка сортировки
		var thisClass = '';
		for (var i = 0; i < classes.length; i++) {
			if ($(this).is('.' + classes[i])) {
				thisClass = classes[i];
				break;
			}
		}

		// сортировочный столбец
		var arItems = $.makeArray($('.sheet__content .sheet__itemRow')),
			sortUsers = null,
			valueClass = '',
			desc = $(this).is('.desc');
		switch (thisClass) {
			case 'sheetSortN':
				valueClass = '.cell-n';
				break;
			case 'sheetSortName':
				valueClass = '.cell-name';
				break;
			case 'sheetSortPP':
				valueClass = '.ppCount';
				break;
			case 'sheetSortBP':
				valueClass = '.bpCount';
				break;
			case 'sheetSortSUM':
				valueClass = '.sumCount';
				break;
		}

		// сортировка
		if (valueClass) {
			if (valueClass == '.cell-name') {
				sortUsers = function (a, b) {
					var contentA = $(a).find(valueClass).text().toUpperCase();
					var contentB = $(b).find(valueClass).text().toUpperCase();
					if (desc) {
						return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
					} else {
						return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
					}
				};
			} else {
				sortUsers = function (a, b) {
					var contentA = parseInt($(a).find(valueClass).text());
					var contentB = parseInt($(b).find(valueClass).text());
					if (desc) {
						return contentB - contentA;
					} else {
						return contentA - contentB;
					}
				};
			}
		}

		if (sortUsers) {
			arItems.sort(sortUsers);
			$(arItems).appendTo(".sheet__content");
		}



	});

	$(".cell-skip").click(function () {
		if (!editing) return;
		$(this).append(cellEdit);
		currentCell = $(this);

		if ($(this).data("idx") < 4) {
			cellEdit.addClass("cellEdit-left").removeClass("cellEdit-right");
		} else {
			cellEdit.addClass("cellEdit-right").removeClass("cellEdit-left");
		}
		cellEdit.show();
	});

	$(".cellEditCell").click(function (e) {
		e.stopPropagation();

		if (!$.isEmptyObject(currentCell)) {
			editedSkips.push({
				cell: currentCell,
				value: currentCell.data("val")
			});
			switch ($(this).data("val")) {
				case -1:
					cellEdit.hide();
					return;
				case 0:
					currentCell.removeClass("pp bp sp").data("val", 0);
					break;
				case 1:
					currentCell.removeClass("pp bp").addClass('sp').data("val", 1);
					break;
				case 2:
					currentCell.removeClass("bp sp").addClass('pp').data("val", 2);
					break;
				case 3:
					currentCell.removeClass("pp sp").addClass('bp').data("val", 3);
					break;
			}
		}
		currentCell.parent().addClass("edited");
		cellEdit.hide();
	});
	//---------------------------------------------------------------
	$(".list__switch").click(function (e) {
		var sender = $(e.target);

		if (sender.is(".list__switchCase")) {
			sender
				.siblings().removeClass("active")
				.end().addClass("active");

			var table = $("#sheet");
			table.find(".visible").removeClass("visible");
			table.find("." + sender.data("col")).addClass("visible");

			$(".sheet__sortRow .cell-mid").css("width", table.find(".sheet__days").outerWidth());
		}
	});
	//------------------------- BOTTOM EDIT BUTTONS ------------------------
	$(".list__editEdit").click(function () {
		$(this).hide()
			.siblings(".list__sendSubmit, .list__exelExport").hide()
			.siblings(".list__editCancel, .list__editSubmit").show();

		$(".btnEditDiscipleName").show();
		editing = true;
	});

	$(".list__editCancel").click(function () {
		if (editedSkips.length) undoCells(editedSkips);
		saveButtonClick();
	});

	$(".list__editSubmit").click(function () {
		var editedSkips = getEditedSkips();
		var data = {};

		if (editedSkips.length) data.skips = editedSkips;

		if (!$.isEmptyObject(data)) {
			$.post("tableUpdate", JSON.stringify(data))
				.done(function (response) {
					if (response == 'ok') {
						saveButtonClick();
						skipsCount();

						if(isFilteredByDay){
							skipsCountWithDayFilter($(".sheet__itemDaySkips").not(".filtered"));
						}
					} else alert(errorText);
				}).fail(function () {
					alert(errorText);
				});
		} else {
			saveButtonClick();
		}
	});

	$(".list__period").click(function (e) {
		var target = $(e.target);
		if (!target.is(".select__item") || target.is(".select__item-first")) return;

		var period = target.data("id");
		var form = $(".list__search");

		checkInput(form.find(".list__group"));
		checkSelect(form.find(".list__institution"));

		if(!form.find(".error").length) {
			var data = {
				institution: form.find(".list__institution .select__item-first").data("id"),
				group: form.find(".list__group").val().trim(),
				period: period
			};
			var cntrl = '';
			switch ($(this).data("type")) {
				case 'week':
					cntrl = "getWeekTable";
					break;
				case 'month':
					cntrl = "getMonthTable";
					break;
				case 'semester':
					cntrl = "getYearTable";
					break;
			}

			if (!$.isEmptyObject(data) && cntrl.length) {
				$.post(cntrl, JSON.stringify(data))
					.done(function (response) {
						var parent = $("#listContent");
						parent.html(response)
							.find(".cell-mid").css("width", parent.find(".sheet__days").outerWidth());
					}).fail(function () {
						alert(errorText);
					});
			}
		}

		scrollTo(".list__search", 1000);
	});
	//----------------------------------------------------------

	$('.sheetSwitch__button').click(function (e) {
		$(this).addClass("sheetSwitch__button-active")
			.siblings().removeClass("sheetSwitch__button-active");

		var form = $(".list__search");

		var data = {
			institution: form.find(".list__institution .select__item-first").data("id"),
			group: form.find(".list__group").val(),
			period: 0
		};
		var cntrl = '';
		switch ($(this).data("val")) {
			case 'week':
				cntrl = "getWeekTable";
				break;
			case 'month':
				cntrl = "getMonthTable";
				break;
			case 'semester':
				cntrl = "getYearTable";
				break;
		}

		if (!$.isEmptyObject(data) && cntrl.length) {
			$.post(cntrl, JSON.stringify(data))
				.done(function (response) {
					var parent = $("#listContent");
					parent.html(response)
						.find(".cell-mid").css("width", parent.find(".sheet__days").outerWidth());
				}).fail(function () {
					alert(errorText);
				});
		}
	});

	$(".list__exelExport").click(function () {
		$.ajax({
			url: 'getExcel/export',
			method: 'GET',
			xhrFields: {
				responseType: 'blob'
			},
			success: function (response) {
				var a = document.createElement('a');
				var url = window.URL.createObjectURL(response);
				a.href = url;
				a.download = 'ВІДОМІСТЬ.xlsx';
				document.body.append(a);
				a.click();
				a.remove();
				window.URL.revokeObjectURL(url);
			}
		});
	});

	$(".list__sendSubmit").click(function () {
		$.post('getExcel/send', '')
			.done(function () {
				$("#overlay").show();
			}).fail(function () {
				alert(errorText);
			});
	});

	$(".modalSended .button-ok").click(function () {
		$("#overlay").hide();
	});
});

function getEditedSkips() {
	var editedSkips = [];
	$(".sheet__itemDaySkips.edited").each(function () {
		var daySkips = [];

		$(this).find(".cell-skip").each(function () {
			daySkips[$(this).data("idx")] = $(this).data("val");
		});
		editedSkips.push({
			user: $(this).data("user"),
			day: $(this).data("day"),
			week: $(this).data("w"),
			status: daySkips,
		});
	});
	return editedSkips;
}

function undoCells(editedSkips) {
	while (editedSkips.length) {
		var cell = editedSkips.pop();
		switch (cell.value) {
			case 0:
				cell.cell.removeClass("pp bp sp").data("val", 0);
				break;
			case 1:
				cell.cell.removeClass("pp bp").addClass('sp').data("val", 1);
				break;
			case 2:
				cell.cell.removeClass("bp sp").addClass('pp').data("val", 2);
				break;
			case 3:
				cell.cell.removeClass("pp sp").addClass('bp').data("val", 3);
				break;
		}
	}
}

function saveButtonClick() {
	//удаления флагов редактирования
	$(".sheet__itemRow .sheet__itemDaySkips.edited").each(function () {
		$(this).removeClass("edited");
	});

	//обнуление переменных
	editing = false;
	editedSkips.length = 0;

	//скрытие кнопок
	$(".list__edit")
		.find(".list__editSubmit, .list__editCancel").hide()
		.siblings(".list__editEdit, .list__sendSubmit, .list__exelExport").show();

	cellEdit.hide();
}

function skipsCount() {
	$(".sheet__itemRow").each(function () {
		var pp = $(this).find(".pp").length;
		var bp = $(this).find(".bp").length;
		var sum = pp + bp;

		$(this).find(".ppCount").html(pp);
		$(this).find(".bpCount").html(bp);

		var sumCountCell = $(this).find(".sumCount");
		sumCountCell.html(sum);

		if(sum < 20) {
			sumCountCell.removeClass("text-warning text-danger");
		} else if (sum < 30){
			sumCountCell.addClass("text-warning").removeClass("text-danger");
		} else {
			sumCountCell.addClass("text-danger").removeClass("text-warning");
		}
	});
}

function skipsCountWithDayFilter(userDaySkeeps){
	userDaySkeeps.each(function(){
		var pp = $(this).find(".pp").length;
		var bp = $(this).find(".bp").length;
		var sum = pp + bp;

		var sumCell = $(this).parent().siblings(".ppCount");
		sumCell.data("sum", sumCell.text()).html(pp);

		sumCell = sumCell.siblings(".bpCount");
		sumCell.data("sum", sumCell.text()).html(bp);

		sumCell = sumCell.siblings(".sumCount");
		sumCell.data("sum", sumCell.text()).html(sum);

		if(sum < 20) {
			sumCell.removeClass("text-warning text-danger");
		} else if (sum < 30){
			sumCell.addClass("text-warning").removeClass("text-danger");
		} else {
			sumCell.addClass("text-danger").removeClass("text-warning");
		}
	});
}

function resizeCellMid(){
	$(".sheet__sortRow .cell-mid").css("width", $(".sheet__content .sheet__days").outerWidth());
}

function resetSign(select){
	select.removeClass("active")
		.find(".select__item-first").data("id", 0)
		.removeClass("select__item-active")
		.html("0");
}
