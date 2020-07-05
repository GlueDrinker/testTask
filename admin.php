<?php 
	$mysqli = mysqli_connect('localhost', 'root', '', 'TestTask');
	$column = $_GET['column'];
	$order = $_GET['order'];
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
	function taskChange($id, $text, $status) {
		if ($status == "<input type = 'checkbox' checked>") {
			$status = 1;
		}
		else {
			$status = 0;
		}
		$ins = mysqli_query($GLOBALS['mysqli'], "UPDATE `tasks` SET `text` = '$text', `taskComplete` = '$status', `changedByAdmin` = '1' WHERE `tasks`.`id` = '$id'");
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
		<form action="action.php" method="get">
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
				$status = [];
				$buttonsArray = [];
				$changedTexts = [];
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
						$status[$row['id']] = "<input type = 'checkbox' checked>";
					}
					else {
						$status[$row['id']] = "<input type = 'checkbox'>";
					}
					
					echo "
					<tr>
						<td>" . $row['userName'] . "</td>
						<td>" . $row['email'] . "</td>
						<td><textarea name = 'changedText" . $row['id'] . "'>" . htmlspecialchars($row['text']) . "</textarea></td>
						<td>" . $status[$row['id']] . "</td>
						<td><input type = 'submit' value = 'Сохранить' name = 'changeButton" . $row['id'] . "'></td>
					</tr>
					";
					$buttonsArray[$row['id']] =  $_GET['changeButton' . $row['id'] . ''];
					$changedTexts[$row['id']] =  $_GET['changedText' . $row['id'] . ''];
					if ($buttonsArray[3]) {
						echo "<script>alert('test')<.script>";
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