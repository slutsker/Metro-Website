$(document).ready(function() {
	$("form").submit(function() {
		return false;
	});
	
	$("form#loginForm").submit(function() {
		var username = $("input#username").val();
		login(username);
	});
	
	$("form#registerForm").submit(function() {
		var username = $("input#username_register").val();
		var favorite_stop = $("select#favorite_stop").val();
		$("table#create_account").hide();
		$("div#register").append("<img class = 'loading' src = 'loading.gif' alt = 'Loading' />");
		register(username, favorite_stop);
		login(username);
	});
	
	$("form#loginForm").submit(function() {
		var username = $("input#username").val();
		login(username);
	});
	
	$("input#time").tooltip();
	
	$("input#time").click(function() {
		if ($(this).val() == "Next Train Out!") {
			$(this).val("");
			$(this).addClass("typing");
			$(this).removeClass("nontyping");
		}
		updateNextTime();
	});

	$("input#time").blur(function() {
		if ($(this).val() == "") {
			$(this).val("Next Train Out!");
			$(this).removeClass("typing");
			$(this).addClass("nontyping");
		}
		updateNextTime();
	});
	
	$("select#stop").keyup(function() {
		updateNextTime();
	});
	
	$("select#stop").change(function() {
		updateNextTime();
	});
	
	$("input#time").change(function() {
		$(this).click();
	});
	
	$("input#time").keyup(function() {
		$(this).click();
	});
	
	$("a#logout").click(function() {
		logout();
		$("table#create_account").show();
		$("img.loading").hide();
		return false;
	});
	
	var oldStop = "";
	
	updateNextTime();
	login("");
});

console.log('blah');
