$(document).ready(function() {
	var changeDisabled = function(bool) {
		$("#date").prop('disabled', bool);
		$("#timeBegin").prop('disabled', bool);
		$("#timeEnd").prop('disabled', bool);
		$("#appoint-survey-button").prop('disabled', bool);
	};
	
	changeDisabled(true);
	
	var checkOnFree = function(e, that) {
		var str = "";		
		var a = parseInt(480 + e.pageX - $(that).offset().left) + 1;
		jQuery.each($(that).data("options"), function() {
			var timeBegin = 480 + this.timeBegin;
			var timeEnd = 480 + this.timeEnd;
			if ((a >= timeBegin) && (a <= timeEnd)) {
				str = "занято";
				return false;
			} else 
				str = "свободно";
		});
		return str;
	};
	
	var toTime = function(e, that) {
		var a = parseInt(480 + e.pageX - $(that).offset().left) + 1;
		a = a > 1080 ? 1080 : a;
		var hour = parseInt(a / 60);
		hour = hour < 10 ? "0" + hour : hour;
		var minute = Math.round(60 * (a / 60 - hour));
		minute = minute < 10 ? "0" + minute : minute;
		$(event.currentTarget).val(hour + ":" + minute);
		
		return hour + ":" + minute;
	};
	
	var getTime = function() {
		$(".day-time").on("mousemove", function(e) {
			//var a = 480 + e.pageX - $(this).offset().left;
			var str = checkOnFree(e, this);
			$("#currentTime").text("Время " + toTime(e, this) + " " + str);
		});		
	};
	
	$('input[type="time"]').on("focus", function(event) {
		$(".day-time").css("cursor", "pointer");

		$(".day-time").on("mousemove", function(e) {
			$("#date").val($(this).prev().data("dayOfWeek"));
			//var a = 480 + e.pageX - $(this).offset().left;
			var str = checkOnFree(e, this);
			if (str == "свободно")
				$(event.currentTarget).val(toTime(e, this));
		});	
	});
	
	$('input[type="time"]').on("blur", function() {
		$(".day-time").off("mousemove");
		$(".day-time").on("mousemove", getTime);
		$(".day-time").css("cursor", "default");
	});
	
	$("#appoint-survey-button").on("click", function() {
		var bool = false;
		var date = $("#date").val();
		var timeBegin = $("#timeBegin").val();
		var timeEnd = $("#timeEnd").val();
		if (timeBegin == "" || timeEnd == "" || date == "")
			return;
			
		timeBegin = timeBegin.split(":");
		timeBegin = timeBegin[0] * 60 + parseInt(timeBegin[1]);
		
		timeEnd = timeEnd.split(":");
		timeEnd = timeEnd[0] * 60 + parseInt(timeEnd[1]);
		
		if (timeBegin < 480 || timeEnd < 480) {
			alert("Рабочий день начинается в 8:00");
			return;
		}
		if (timeBegin > 1080 || timeEnd > 1080) {
			alert("Рабочий день кончается в 18:00");
			return;
		}
		
		jQuery.each($(".day-title"), function() {
			if ($(this).data("dayOfWeek") == date) {
				jQuery.each($(this).next().data("options"), function() {
					if (((timeBegin >= (480 + this.timeBegin)) && (timeBegin <= (480 + this.timeEnd))) || ((timeEnd >= (480 + this.timeBegin)) && (timeEnd <= (480 + this.timeEnd)))) {
						alert("Выбранное время занято");
						bool = true;
					}
					if (timeBegin <= (480 + this.timeBegin) && timeEnd >= (480 + this.timeBegin)) {
						alert("Выбрано некорректное время");
						bool = true;;
					}
				});
			}
		});
		if (bool)
			return;
		var data = $("#appoint-survey-form").serialize();
		$.ajax({
			type: "POST",
			url: "php/setSurvey.php",
			data: data, 
			success: function(msg) {
				if (msg == "good") {
					alert("Вы успешно записаны на прием");
					location.reload();
				} else
					alert("Произошла ошибка. Попробуйте позднее");
			}
		});
	});

	$("#fio-doctor").autocomplete({
		source: "php/search.php",
		minLength: 3,
		close: function(event, ui) {
			if ($("#fio-doctor").val().length != 0) {
				var fio = $(this).val();
				
				$.ajax({
					type: "POST",
					url: "php/getSchedule.php",
					data: { fio: fio },
					success: function(msg) {
						if (msg.length == 0) {
							$("#user-schedule").fadeOut();
							$("#currentTime").fadeOut();
							$("#user-schedule").html("");
							changeDisabled(true);					
							alert("Введено некорректное ФИО врача")
						} else if (msg.indexOf("div") != -1) {
							$("#user-schedule").html(msg);
							$("#currentTime").fadeIn();
							$("#user-schedule").fadeIn();
							changeDisabled(false);
							
							$(".day-time").on("mousemove", getTime);
							$(".day-title").on("click", function() {
								$("#date").val($(this).data("dayOfWeek"));
							});
						} else
							alert(msg);
					}
				});
			}
		}		
	});
});