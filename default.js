$(document).ready(function() {
	$.ajax({
		url : "Controller.php",
		data : {
			"action" : $("#action").val(),
			"context" : "load",
			"user" : $("#loggedInUserName").val()
		},
		type : "POST",
		dataType : "html"
	}).done(function(resp) {
		$("#contentDiv").html(resp);
	});
});

function loginForm_Load() {
	$("#userNameTextBox").focus();
}

function loginForm_Submit() {
	if ($("#userNameTextBox").val() == "" || $("#passwordTextBox").val() == "") {
		alert("User name and password are required.");
		return false;
	}

	var theData = $("#loginForm").serialize();

	$.ajax({
		url : "Controller.php",
		type : "POST",
		async : false,
		cache : false,
		data : theData,
		dataType : "html"
	}).success(function(resp) {
		if (resp != "") {
			$("#loggedInUserName").val($("#userNameTextBox").val());
			$("#contentDiv").html(resp);
		} else {
			alert("Invalid password for this account.");
		}
	});
}

function profileForm_Load() {
	$.ajax({
		url : "Controller.php",
		async : false,
		cache : false,
		data : {
			"action" : "loadProfile",
			"user" : $("#loggedInUserName").val()
		},
		type : "POST",
		dataType : "json"
	}).done(function(resp) {
		$("#userNameTextBox").val(resp.data.username);
		$("#nameTextBox").val(resp.data.name);
		$("#majorTextBox").val(resp.data.major);
	});
}

function profileForm_Submit() {
	if ($("#userNameTextBox").val() == "" || $("#nameTextBox").val() == "" || $("#majorTextBox").val() == "") {
		alert("All fields on this page are required.");
		return false;
	}

	var theData = $("#profileForm").serialize();

	$.ajax({
		url : "Controller.php",
		type : "POST",
		async : false,
		cache : false,
		data : theData,
		dataType : "html"
	}).success(function(resp) {
		if (resp == "") {
			alert("Error updating user profile.");
		} else {
			$("#contentDiv").html(resp);
		}
	});
}

function classRegForm_Load() {
	$.ajax({
		url : "Controller.php",
		async : false,
		cache : false,
		data : {
			"action" : "loadClassReg",
			"user" : $("#loggedInUserName").val()
		},
		type : "POST",
		dataType : "html"
	}).done(function(resp) {
		$("#classesFieldSet").html(resp);
	});

}

function classRegForm_Submit() {
	checkedCount = 0;
	checkedClasses = "";
	theBoxes = document.getElementsByName("classCheckBox");
	for ( i = 0; i < theBoxes.length; i++) {
		if (theBoxes[i].checked) {
			checkedCount++;
			checkedClasses = checkedClasses + theBoxes[i].value + "|";
		}
	}

	if (checkedCount > 3) {
		alert("You may select three or fewer classes only.");
		return false;
	}

	$.ajax({
		url : "Controller.php",
		async : false,
		cache : false,
		data : {
			"action" : "classReg",
			"user" : $("#loggedInUserName").val(),
			"classes" : checkedClasses
		},
		type : "POST",
		dataType : "html"
	}).done(function(resp) {
		if (resp == "") {
			alert("Unable to update registrations.");
		} else {
			$("#contentDiv").html(resp);
		}
	});
}

function summaryForm_Load() {
	$.ajax({
		url : "Controller.php",
		async : false,
		cache : false,
		data : {
			"action" : "loadSummary",
			"user" : $("#loggedInUserName").val()
		},
		type : "POST",
		dataType : "json"
	}).done(function(resp) {
		$("#userNameSpan").html(resp.Student.username);
		$("#nameSpan").html(resp.Student.studentName);
		$("#majorSpan").html(resp.Student.major);
		$("#class1Span").html(resp.Student.class1 + " " + resp.Class1Name);
		$("#class2Span").html(resp.Student.class2 + " " + resp.Class2Name);
		$("#class3Span").html(resp.Student.class3 + " " + resp.Class3Name);
	});
}

function summaryForm_Submit() {
	if (confirm("Are you sure?")) {
		$.ajax({
			url : "Controller.php",
			async : false,
			cache : false,
			data : {
				"action" : "logout",
				"user" : $("#loggedInUserName").val()
			},
			type : "POST",
			dataType : "html"
		}).done(function(resp) {
			$("#loggedInUserName").val();
			$("#contentDiv").html(resp);
		});
	}
}
