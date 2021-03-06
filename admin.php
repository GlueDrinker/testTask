<?php 
	$mysqli = mysqli_connect('mysql.zzz.com.ua', 'gluedrinker', 'TksDq2B6', 'gluedrinker');
	$column = $_GET['column'];
	$order = $_GET['order'];
	$submitButton = $_GET['submitButton'];
	$insertUserName = $_GET['insertUserName'];
	$email = $_GET['email'];
	$text = mysqli_real_escape_string($mysqli, $_GET['text']);
	function queryWithOrder($startFrom, $column, $order) {
		switch ($column) {
					case 'Электронная почта':
						$column = "email";
						break;
					case 'Статус':
						$column = "TaskComplete";
						break;
					
					default:
						$column = "userName";
						break;
				}		
		if ($order == "по убыванию") {
			return "SELECT * FROM `tasks` ORDER BY $column DESC LIMIT $startFrom, 3";
		}
		else {
			return "SELECT * FROM `tasks` ORDER BY $column LIMIT $startFrom, 3";
		}
	}
	function taskInsertion($insertUserName, $email, $text) {
		$ins = mysqli_query($GLOBALS['mysqli'], "INSERT INTO `tasks` (`userName`, `email`, `text`, `taskComplete`, `changedByAdmin`) VALUES ('$insertUserName', '$email', '$text', '0', '0')");
		echo "<strong>Запись добавлена</strong>";
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Задачи</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
</head>
<body>
	<nav class="navbar bg-light">
		<form action="index.php" method="get">
			<p><input type="submit" name="authButton" value="Выйти из профиля администратора"></p>
		</form>
	</nav>
	<div class="container-fluid">
		<form action="admin.php" method="get">
			<div class="row">
				<div class="col-4">
					<p>Введите имя пользователя: <input type="text" name="insertUserName" required></p>
				</div>
				<div class="col-4">
					<p>Введите электронную почту: <input type="email" name="email" required></p>
				</div>
			</div>
			<div class="row">
				<div class="col-8">
					<p>Введите текст задачи: <textarea name="text" required style="width: 100%"></textarea> <input type="submit" name="submitButton" value="Добавить"></p>
				</div>
			</div>
			<?php 
			if ($submitButton) {
			 	taskInsertion($insertUserName, $email, $text);

			} 
			?>
		</form>
		<div class="row">
			<div class="col-12">
				<form action="admin.php" method="get">
					<p>Отсортировать по полю <select name="column">
						<option>Имя пользователя</option>
						<option>Электронная почта</option>
						<option>Статус</option>
					</select>
					<select name="order">
						<option>по возрастанию</option>	
						<option>по убыванию</option>	
					</select>
					<input type="submit" name="orderButton" value="Отсортировать"></p>
					<?php 
						if ($orderButton) {
							$query = queryWithOrder($startFrom, $column, $order);
						} 
					?>
				</form>
			</div>
		</div>
		<div class="table-responsive">
			<?php
				$page = '';
				if (isset($_GET["page"])) {
					$page = $_GET["page"];
				}
				else {
					$page = 1;
				}
				$startFrom = ($page - 1) * 3;
				$query = queryWithOrder($startFrom, $column, $order);
				$result = mysqli_query($mysqli, $query);
				$status = '';
				
				echo "<form action = 'admin.php' method = 'get'>
				<table class = 'table table-bordered'>
					<tr>
						<th>Имя пользователя</th>
						<th>Электронная почта</th>
						<th>Текст задачи</th>
						<th>Выполнено</th>
						<th></th>
					</tr>";
				while ($row = mysqli_fetch_array($result)) {
					if ($row['taskComplete'] == 1) {
						$status = "<input name = 'changedStatus" . $row['id'] . "' type = 'checkbox' checked>";
					}
					else {
						$status = "<input name = 'changedStatus" . $row['id'] . "' type = 'checkbox'>";
					}
					
					echo "
					<tr>
						<td>" . $row['userName'] . "</td>
						<td>" . $row['email'] . "</td>
						<td><textarea name = 'changedText" . $row['id'] . "'>" . htmlspecialchars($row['text']) . "</textarea></td>
						<td>" . $status . "</td>
						<td><input type = 'submit' value = 'Сохранить' name = 'changeButton" . $row['id'] . "'></td>
					</tr>
					";
					if(isset($_GET['changeButton' . $row['id'] . ''])) {
						if (!empty($_GET['changedStatus' . $row['id'] . ''])) {
							$status = 1;
						}
						else {
							$status = 0;
						}
						if (($_GET['changedText' . $row['id'] . ''] == htmlspecialchars($row['text'])) && $row['changedByAdmin'] == '0') {
							$ins = mysqli_query($mysqli, "UPDATE `tasks` SET `text` = '" . $_GET['changedText' . $row['id'] . ''] . "', `taskComplete` = '$status', `changedByAdmin` = '0' WHERE `id` = '" . $row['id'] . "'");
						}
						else {
							$ins = mysqli_query($mysqli, "UPDATE `tasks` SET `text` = '" . $_GET['changedText' . $row['id'] . ''] . "', `taskComplete` = '$status', `changedByAdmin` = '1' WHERE `id` = '" . $row['id'] . "'");
						}
					}
					
				}
				echo "</table>";

				echo "</form>";

			 ?>
		</div>
		<div align="center">
			<?php  
				$pagesQuery = "SELECT * FROM `tasks`";
				$pagesResult = mysqli_query($mysqli, $pagesQuery);
				$amountOfRecords = mysqli_num_rows($pagesResult);
				$amountOfPages = ceil($amountOfRecords / 3);
				for ($i = 1; $i <= $amountOfPages; $i++) { 
					echo '<a href = "admin.php?page=' . $i . '&column=' . $column . '&order=' . $order . '">' . $i . '</a>';
				}
			?>
		</div>
	</div>
</body>
</html>