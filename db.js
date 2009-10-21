var loggedIn = "";
var favoriteStation = "";

function register(myUsername, stop) {
	$.ajax({
		type: "POST",
		url: "metro.php",
		data: {
			register : myUsername,
			favorite_stop : stop
		},
		success: function(data, textStatus) {
			
		},
		dataType: "json",
		error: function(XMLHttpRequest, textStatus, errorThrown){
			console.log("error.");
		}
	});
}

function login(myUsername) {
	$.ajax({
		type: "POST",
		url: "metro.php",
		data: {
			username : myUsername
		},
		success: function(data, textStatus) {
			if (data["num"] == "0") {
				if (myUsername != "") {
					$("p#message").html("The user " + myUsername + 
							" does not exist. Please try again.");
				}
			}
			else {
				$("legend#login_legend").text("Hey " + data["user"] + "!");
				$("div#login_version").hide();
				$("div#loggedin_version").show();
				$("span#favorite_station").text(data["name"]);
				loggedIn = myUsername;
				favoriteStation = data["num"];
				getNextTime(favoriteStation, Date.parse("now").toString("HH:mm"),
						Date.parse("now"), data["name"], $("div#favoriteTrain"));
			}
		},
		dataType: "json",
		error: function(XMLHttpRequest, textStatus, errorThrown){
			console.log("error.");
		}
	});
}

function logout() {
	$.ajax({
		type: "POST",
		url: "metro.php",
		data: {
			action : "logout"
		},
		success: function(data, textStatus) {
			$("div#login_version").show();
			$("div#loggedin_version").hide();
			$("p#message").text("");
			$("legend#login_legend").text("Login");
		},
		dataType: "json",
		error: function(XMLHttpRequest, textStatus, errorThrown){
			console.log("error.");
		}
	});
}

/* Get the time of the next server from the server and 
 * update the associated box
 */
function getNextTime(myStop, parsedTime, myTime, stopName, boxToUpdate) {
	$.ajax({
		type: "GET",
		url: "metro.php",
		data: {
			stop : myStop,
			time : parsedTime
		},
		success: function(data, textStatus) {
			if (Date.parse(data.next_time.time) != null) {
				var nextTime = Date.parse(data.next_time.time).toString("hh:mmtt");
				var nextTimeParsed = Date.parse(data.next_time.time).toString("HH:mm");
				
				boxToUpdate.html("Next train:<table><tr><td>After</td><td>" + 
						myTime.toString("h:mmtt") + "</td></tr><tr><td>Out of</td><td><b>" +
						stopName + "</b>" + "</td></tr><tr><td>Leaves at</td><td class = 'shown'><b>" + 
						nextTime + "</b>" +
						" (<a href = '#' class = 'show_all'>Show All Trains</a>)" +  
						"</td></tr><tr><td>Frequency</td><td class = 'frequency'></td>" +
						"<tr id = 'number'><td>Train Number</td><td>" + data.next_time.num + "</td></tr></table>");
				
				var old_diff = 0;
				for (var i = 1; i < data.all_trains.length; i++) {
					var date1 = Date.parse(data.all_trains[i]); 
					var date2 = Date.parse(data.all_trains[i - 1]);
					var diff = (date1 - date2) / 60000;
					if (Math.abs(diff - old_diff) > .95) {
						$(boxToUpdate).find(".frequency").append("Every " + diff + 
								" minutes starting at " + date1.toString("h:mmtt") +
								"<br />");
					}
					old_diff = diff;
				}
				boxToUpdate.find("a.show_all").unbind();
				boxToUpdate.find("a.show_all").click(function() {
					showAllTrains(myStop, boxToUpdate.find("td.shown"), 
							nextTimeParsed, nextTime, stopName);
				});
			}
			else {
				$("div#nextTrain").html("No trains today after that time!");
			}
		},
		dataType: "json",
		error: function(XMLHttpRequest, textStatus, errorThrown){
			console.log("error.");
		}
	});
}

/* TODO: Make this highlight the searched-for train */
function showAllTrains(myStop, boxToUpdate, theTime, myTime, stopName) {
	$.ajax({
		type: "GET",
		url: "metro.php",
		data: {
			all_trains : myStop
		},
		success: function(data, textStatus) {
			boxToUpdate.html("<table>");
			for (var i = 0; i < data.length; i++) {
				var date = Date.parse(data[i]); 
				if (i % 4 == 0) {
					boxToUpdate.children("table").append("<tr>");
				}
				var currentTime = date.toString("HH:mm");
				var classCurrent = "";
				console.log(currentTime + " " + theTime);
				if (currentTime == theTime) {
					classCurrent = "current_time";
				}
				boxToUpdate.find("tr:last").append("<td class = '" + classCurrent + 
						"'>" + date.toString("h:mm") + "</td>");
			}
			boxToUpdate.append("<tr><td>(<a href = '#' class = 'hide'>Hide</a>)</td></tr>");
			boxToUpdate.find("a:last").click(function() {
				getNextTime(myStop, theTime, Date.parse(myTime), 
						stopName, boxToUpdate.parent().parent().parent().parent());
			});
		},
		dataType: "json",
		error: function(XMLHttpRequest, textStatus, errorThrown){
			console.log("error.");
		}
	});
}

var oldTime = "";
function updateNextTime(force) {
	var stopStr = $("select#stop").children(":selected").text();
	var myStop = $("select#stop").val();
	var userTime = $("input#time").val();
	var myTime;
	
	if (userTime == 'Next Train Out!' || userTime == '') {
		myTime = Date.parse("now");
	}
	else {
		myTime = Date.parse(userTime);
		if (userTime.length <= 2) {
			myTime = Date.parse(userTime + ":00");
		}
		if (myTime != null && !userTime.match(/PM|AM/) && 
				myTime.compareTo(Date.parse("4:00AM")) == -1) {
			myTime = Date.parse(userTime + "PM");
		}
	}
	
	if (myTime != null) {
		
		var parsedTime = myTime.toString("HH:mm");

		/* Don't do an ajax request if the time/stop hasn't changed */
		if (parsedTime != oldTime || oldStop != myStop || force != undefined) {
			
			$("div#nextTrain").html("<img src = 'loading.gif' alt = 'Loading' />");
			
			oldTime = parsedTime;
			oldStop = myStop;
			getNextTime(myStop, parsedTime, myTime, stopStr, $("div#nextTrain"));
		}
	}
}