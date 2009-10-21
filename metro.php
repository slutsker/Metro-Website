<?php
	session_start();
	
	/* servervars.php contains the server variables */
	require_once 'servervars.php';
	
	$metro = new Metro($db_location, $db_username, $db_password, $db_name);
	$user = new User($db_location, $db_username, $db_password, $db_name);
	
	class User {
		var $mysqli;
		var $getUserSql = "SELECT favorite_station, stop_name FROM 
			users JOIN stop_names ON stop_num = favorite_station 
			WHERE username = ?";
		var $addUserSql = "INSERT INTO users (username, favorite_station)
			VALUES (?, ?)";
		
		function User($db_location, $db_username, $db_password, $db_name) {
			$this->mysqli = new mysqli($db_location, $db_username, $db_password, $db_name);
			if ($this->mysqli->connect_error) {
			    die('Connect Error');
			}
		}
		
		function register($username, $favorite_station) {
			$stmt = $this->mysqli->prepare($this->addUserSql);
			$stmt->bind_param('si', $username, $favorite_station);
			$stmt->execute();
			$stmt->close();
			if ($this->mysqli->affected_rows() > 0) {
				return 'Success';
			}
			return 'Failure';
		}
		
		function login($username) {
			if ($username == "" && isset($_SESSION['user'])) {
				$username = $_SESSION['user'];
			}
			$stmt = $this->mysqli->prepare($this->getUserSql);
			$stmt->bind_param('s', $username);
			$stmt->execute();
			$stmt->bind_result($favorite_station, $stop_name);
			$stmt->fetch();
			$stmt->close();
			if ($favorite_station > 0) {
				$_SESSION['user'] = $username;
			}
			return array('num' => $favorite_station, 'name' => $stop_name, 'user' => $username);
		}
		
		function logout() {
			unset($_SESSION['user']);
		}
	}
	
	class Metro {
		var $getStopsSql = "SELECT stop_num, stop_name FROM stop_names ORDER BY stop_num ASC";
		var $getStopSql = "SELECT train_num, timestamp FROM stops WHERE stops.stop_num = ? 
			AND timestamp > ? ORDER BY time ASC LIMIT 1";
		var $getFirstStopSql = "SELECT train_num, timestamp FROM stops WHERE stops.stop_num = ? 
			ORDER BY timestamp ASC LIMIT 1";
		var $getAllTrainsSql = "SELECT timestamp FROM stops WHERE stops.stop_num = ? 
			ORDER BY timestamp ASC";
		var $mysqli; 
		
		function Metro($db_location, $db_username, $db_password, $db_name) {
			$this->mysqli = new mysqli($db_location, $db_username, $db_password, $db_name);
			if ($this->mysqli->connect_error) {
			    die('Connect Error');
			}
		}
		
		function get_stops() {
			$stmt = $this->mysqli->prepare($this->getStopsSql);
			$stmt->execute();
			$stmt->bind_result($stop_num, $stop_name);
			$stop_names = array();
			while ($stmt->fetch()) {
				$stop_names[$stop_num] = $stop_name;
			}
			$stmt->close();
			return $stop_names;
		}
		
		/* Gets the next train for 
		 * 	stop -> exact name of stop
		 *  time -> the JS formats it so that it will always be:
		 *  		HH:MM on the 24 hour clock
		 */
		function get_next_train($stop, $time) {
			$stmt = $this->mysqli->prepare($this->getStopSql);
			$stmt->bind_param('ss', $stop, $time);
			$stmt->execute();
			$stmt->bind_result($train_num, $next_time);
			$stmt->fetch();
			$stmt->close();
			if ($next_time == null) {
				$stmt = $this->mysqli->prepare($this->getFirstStopSql);
				$stmt->bind_param('s', $stop);
				$stmt->execute();
				$stmt->bind_result($train_num, $next_time);
				$stmt->fetch();
				$stmt->close();
			}
			return array("num" => $train_num, "time" => $next_time);
		}
		
		function get_all_trains_at_stop($stop) {
			$stmt = $this->mysqli->prepare($this->getAllTrainsSql);
			$stmt->bind_param('s', $stop);
			$stmt->execute();
			$stmt->bind_result($next_time);
			$trains = array();
			while($stmt->fetch()) {
				$trains[] = $next_time;
			}
			$stmt->close();
			return $trains;
		}
	}
	
	if (isset($_GET['stop'])) {
		echo json_encode(array("next_time" => 
									$metro->get_next_train($_GET['stop'], $_GET['time']),
								"all_trains" =>
									$metro->get_all_trains_at_stop($_GET['stop'])));
	}
	if (isset($_POST['username'])) {
		echo json_encode($user->login($_POST['username']));
	}
	if (isset($_POST['register'])) {
		echo json_encode($user->register($_POST['register'], $_POST['favorite_stop']));
	}
	if (isset($_POST['action']) && $_POST['action'] == 'logout') {
		echo json_encode($user->logout());
	}
	if (isset($_GET['all_trains'])) {
		echo json_encode($metro->get_all_trains_at_stop($_GET['all_trains']));
	}
		
?>