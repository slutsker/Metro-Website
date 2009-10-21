<?php require_once 'metro.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <title>Metro Stop Finder</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />	
    <link rel="stylesheet" href="jquery-tooltip/jquery.tooltip.css" type="text/css" />
    <link rel="stylesheet" href="personal.css" type="text/css" />
	<script type="text/javascript" src="jquery.min.js"></script>
	<script type="text/javascript" src="date.js"></script>
    <script type="text/javascript" src="jquery-tooltip/lib/jquery.bgiframe.js"></script>
    <script type="text/javascript" src="jquery-tooltip/lib/jquery.dimensions.js"></script>
    <script type="text/javascript" src="jquery-tooltip/jquery.tooltip.min.js"></script>
    <script type="text/javascript" src="db.js"></script>
    <script type="text/javascript" src="personal.js"></script>
</head>
<body>
<div id = "body">
	
	<form action = "" method = "get">
	<fieldset id = "next_stop">
	<legend>Find the Train's Next Stop</legend>
	Where are you? 
	<select id = "stop">
	<?php
		$stops = $metro->get_stops();
		foreach ($stops as $stop_num => $stop) {
	?>
		<option value = "<?php echo $stop_num; ?>"><?php echo $stop; ?></option>
	<?php } ?>
	</select>
	<br />
	When do you want to leave? 
		<input title = "Type in the time in any format. For example: 2pm, 3:10, 11:12, 9, etc."
			id = "time" name = "time" class = "nontyping" autocomplete = "off" value = "Next Train Out!" />
	<div id = "nextTrain">
	</div>
	</fieldset>
	</form>
	
	<fieldset id = "login_fieldset">
	<legend id = "login_legend">Login</legend>
	<div id = "login_version">
	<form action = "" method = "get" id = "loginForm">
		<div id = "login">
			Username <input type = "text" id = "username" /> <input type = "submit" value = "Login" />
			<p id = "message"></p>
		</div>
	</form>
	<form action = "" method = "get" id = "registerForm">
		<div id = "register">
			<b>Create an Account</b><br />
			<table id = "create_account">
				<tr><td>Username</td><td><input type = "text" id = "username_register" /></td></tr>
				<tr><td>Favorite Stop</td><td><select id = "favorite_stop">
			<?php
				$stops = $metro->get_stops();
				foreach ($stops as $stop_num => $stop) {
			?>
				<option value = "<?php echo $stop_num; ?>"><?php echo $stop; ?></option>
			<?php } ?>
			</select></td></tr>
			<tr><td colspan = "2"><input class = "submit" type = "submit" value = "Register" /></td></tr>
			</table>
		</div>
	</form>
	</div>
	<div id = "loggedin_version">
		Favorite Station: <span id = "favorite_station"></span>
		<div id = "favoriteTrain"></div>
		<a href = "#" id = "logout">Logout</a>
	</div>
	</fieldset>
	</form>
</div>
</body>
</html>
        

