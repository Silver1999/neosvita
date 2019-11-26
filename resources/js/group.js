$(document).ready(function () {

	$(".group__switch").click(function (e) {
		var sender = $(e.target);

		if (sender.is(".group__switchCase")) {
			sender
				.siblings().removeClass("active")
				.end().addClass("active");

			var table = $("." + $(this).data("table"));
			table.find(".visible").removeClass("visible");
			table.find("." + sender.data("col")).addClass("visible");
		}
	});

	$(".group__editEdit").click(function () {
		$(this).hide()
			.siblings(".group__removeGroup, .group__changeGrName").hide()
			.end()
			.siblings(".group__editCancel, .group__editSubmit").show();

		$("#groupContent .editable .group__text").each(function () {

				$(this).hide()
					.siblings(".group__input").val($(this).html()).show();
			})
			.siblings(".group__grant").each(function () {
				var text = $(this).siblings(".group__text").text();
				if (text == 'ТАК') {
					$(this).find(".radioYES").prop('checked', true);
				} else {
					$(this).find(".radioNO").prop('checked', true);
				}
				$(this).show();
			});
	});

	$(".group__editCancel").click(function () {
		$(this).hide()
			.siblings(".group__editSubmit").hide()
			.siblings(".group__removeGroup, .group__changeGrName, .group__editEdit").show();

		$("#groupContent .group__text").show()
			.siblings(".group__input, .group__grant").hide();
	});

	$(".group__editSubmit").click(function () {
		var data = getChanges();
		if (data) {
			$.post("groupUpdate", JSON.stringify(data))
				.done(function (response) {
					if (response == 'ok') {
						afterUpdate();
						$(".group__editCancel, .group__editSubmit").hide()
							.siblings(".group__removeGroup, .group__changeGrName, .group__editEdit").show();
					}
				}).fail(function () {
					alert("Виникла помилка. Дані ще не введено.");
				});
		}
	});

	$(".group__delete").click(function () {
		var uname = $(this).siblings(".userName").text();

		$("#overlay").show()
			.find(".modalRemoveUser").show()
			.find(".userName").html(uname).end()
			.find(".btnRemoveUser").data("id", $(this).data("id"));
	});

	$(".group__removeGroup").click(function () {
		$("#overlay").show()
			.find(".modalRemoveGroup").show();
	});

	$(".group__changeGrName").click(function () {
		$("#overlay").show()
			.find(".modalChangeGrName").show()
			.find(".error").removeClass("error");
	});

	$(".modal__button.button-cancel").click(function () {
		$("#overlay").hide()
			.find(".modal").hide();
	});

	$(".userDofB").focus(function () {
		$(this).addClass("edited");
		var cleave = new Cleave('.userDofB.edited', {
			date: true,
			delimiter: '-',
			datePattern: ['d', 'm', 'Y']
		});
	}).blur(function () {
		$(this).removeClass("edited");
	});
	//----------------------- Модальные ОК -----------------------
	$(".btnRemoveUser").click(function () {
		var data = {
			id: $(this).data("id")
		};

		$.post("userRemove", JSON.stringify(data))
			.done(function (response) {
				if (response == 'ok') {
					$("#overlay").hide().find(".modalRemoveUser").hide();
					$(".userRow[data-id=" + data.id + "]").remove();
				}
			}).fail(function () {
				alert("Виникла помилка.");
			});
	});

	$(".btnRemoveGroup").click(function () {
		var data = {
			id: $(this).data("id")
		};
		$.post("groupRemove", JSON.stringify(data))
			.done(function (response) {
				if (response == 'ok') {
					$("#overlay").hide().find(".modalRemoveGroup").hide();
					$("#groupContent").html('');
				} else if (response == 'reload'){
					window.location.reload(true);
				}
			}).fail(function () {
				alert("Виникла помилка.");
			});
	});

	$(".btnChangeGrName").click(function () {
		var inp = $(this).parent().siblings(".modal__newGrName");
		var newGrName = inp.val().trim();

		if (newGrName.length < 4) {
			inp.addClass("error");
			return;
		} else {
			inp.removeClass("error");
			var data = {
				id: $(this).data("id"),
				newName: newGrName,
			};

			$.post("chnggrname", JSON.stringify(data))
				.done(function (response) {
					if (response == 'ok') {
						$("#overlay").hide().find(".modalChangeGrName").hide();
					}
				}).fail(function () {
					alert("Виникла помилка.");
				});
		}
	});

	//----------------------- FUNCTIONS -----------------------
	function getChanges() {
		var users = new Array();
		$(".userRow.editable").each(function () {
			var isChanged = false;
			var user = {};
			user.id = $(this).data("id");

			var inp = $(this).find(".userDofB");
			user.dofb = inp.val().trim();
			if (!isChanged && user.dofb != inp.siblings(".group__text").html()) {
				isChanged = true;
			}

			inp = $(this).find(".userPhone");
			user.phone = inp.val().trim();
			if (!isChanged && user.phone != inp.siblings(".group__text").html()) {
				isChanged = true;
			}

			inp = $(this).find(".userEmail");
			user.email = inp.val().trim();
			if (!isChanged && user.email != inp.siblings(".group__text").html()) {
				isChanged = true;
			}

			inp = $(this).find(".userAddressOfResidence");
			user.addressOfResidence = inp.val().trim();
			if (!isChanged && user.addressOfResidence != inp.siblings(".group__text").html()) {
				isChanged = true;
			}

			inp = $(this).find(".userAddressOfRegistration");
			user.addressOfRegistration = inp.val().trim();
			if (!isChanged && user.addressOfRegistration != inp.siblings(".group__text").html()) {
				isChanged = true;
			}

			inp = $(this).find(".userParentName1");
			user.parentName1 = inp.val().trim();
			if (!isChanged && user.parentName1 != inp.siblings(".group__text").html()) {
				isChanged = true;
			}

			inp = $(this).find(".userParentName2");
			user.parentName2 = inp.val().trim();
			if (!isChanged && user.parentName2 != inp.siblings(".group__text").html()) {
				isChanged = true;
			}

			inp = $(this).find(".userParentPhone");
			user.parentPhone = inp.val().trim();
			if (!isChanged && user.parentPhone != inp.siblings(".group__text").html()) {
				isChanged = true;
			}

			$(this).find(".group__grant").each(function () {
				user.grant = $(this).find("input:checked").val();
				if (!isChanged &&
					$(this).siblings(".group__text").text() != $(this).find("input:checked").data("val")) {
					isChanged = true;
				}
			});

			if (isChanged) users.push(user);
		});

		return $.isEmptyObject(users) ? '' : users;
	}

	function afterUpdate() {
		$("#groupContent .group__input").each(function () {
			$(this).hide()
				.siblings(".group__text").html($(this).val()).show();
		});
		$("#groupContent .group__grant").each(function () {
			$(this).hide()
				.siblings(".group__text")
				.html($(this).find("input:checked").data("val")).show();
		});
	}

});