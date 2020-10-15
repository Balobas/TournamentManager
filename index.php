<?php
require_once "controller\InputHandler.php";
?>

<!doctype html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="style/style.css">
	<title>Турнирная сетка</title>
</head>
<body>

	<script src="view/ClientHandler.js"></script>

	<div class="header">

		<div class="logo">
		Tournament manager
		</div>

		<nav class="nav-menu-wrapper">

			<input id="toggle" type="checkbox" />

			<label class="menu-btn" for="toggle">
				<span>

				</span>
			</label>

			<div class="menu-box">

				<div class="nav-menu">
					<ul>

						<li class="nav-menu-item">
							<p onclick="openChoseDialog()">Выбрать турнир</p>
						</li>

						<li class="nav-menu-item">
							<p onclick="openCreateDialog()">Создать новый турнир</p>
						</li>

					</ul>
				</div>

			</div>

		</nav>
	</div>

	<div class="status-box" id="status">

	</div>

	<div class="left-teams" id="left-teams" onclick="clickOnLeftTeamList()">Команды группы</div>

	<div class="right-teams" id="right-teams" onclick="clickOnRightTeamList()">Команды группы</div>

	<div class="delete-game-dialog" id="delete-game-dialog">

		<p>Вы действительно хотите удалить эту игру?</p>

		<p class="hidden" id="delete-game-id"></p>

		<button id="delete-yes" onclick="deleteGame()">Да</button>

		<button id="delete-no" onclick="closeDeleteDialog()">Нет</button>

	</div>

	<?=InputHandler::handle('openChoseDialog', [])?>

	<div class="update-game-dialog" id="update-game-dialog">

		<p>Изменить счет</p>

		<div class="close-btn" onclick="closeUpdateDialog()">
			<span class="close"></span>
		</div>

		<p class="hidden" id="update-game-id"></p>

		<span class="first-team" id="update-dialog-first-name"><p class="hidden"></p></span>

		<input id="update-first-score" type="text" value="">
		<input id="update-second-score" type="text" value="">

		<span class="second-team" id="update-dialog-second-name"><p class="hidden"></p></span>

		<button id="update" onclick="updateGame()">Изменить</button>

	</div>

	<div class="add-game-dialog" id="add-game-dialog">

		<p>Добавить игру</p>

		<div class="close-btn" onclick="closeAddGameDialog()">
			<span class="close"></span>
		</div>

		<label for="first-team-input-name">Команда</label>
		<input type="text" value="" id="first-team-input-name">
		<p class="hidden" id="first-team-id"></p>

		<label>Счет</label>
		<input type="number" id="first-team-input-score" value="">
		<input type="number" id="second-team-input-score" value="">


		<input type="text" value="" id="second-team-input-name">
		<label for="second-team-input-name">Команда</label>
		<p class="hidden" id="second-team-id"></p>

		<div class="add-team-list" id="add-team-list">

		</div>

		<button class="add-game-btn" onclick="addGame()">Добавить</button>

	</div>

	<div class="create-dialog" id="create-dialog">

		<p class="title" id="create-title">Создать турнир</p>

		<div class="close-btn" onclick="closeCreateDialog()">
			<span class="close"></span>
		</div>

		<label for="tournament-name">Название Турнира</label>
		<input type="text" id="tournament-name" value="">

		<label for="team-input">Название команды</label>
		<input type="text" id="team-input" value="">

		<button id="add-team-btn" class="add-team-btn" onclick="addTeamToList()">Добавить</button>

		<ul class="team-list" id="team-list"></ul>

		<button id="create-tournament" class="create-tournament" onclick="createTournament()">Создать</button>

		<button id="clear-team-list" class="create-tournament" onclick="clearTeamList()">Отчистить список</button>

	</div>

	<div class="grid_wrapper" id="grid_wrapper">

		<div class="final-game">

			<span class="final-label">
				Final
			</span>

			<div class="game">

				<input type="checkbox" id="game-toggle">

				<div class="game-menu">
					<ul>

						<li>
							Изменить результат
						</li>

						<li>
							Удалить игру
						</li>

					</ul>
				</div>

				<div class="game-info">
					<p id="gameID">
					</p>
					<p id="team1ID">

					</p>

					<p id="team2ID">

					</p>
				</div>

				<div class="command-wrapper">

					<div class="command">
						Команда1
					</div>
					<hr>
					<div class="command">
						Команда2
					</div>

				</div>

			</div>

		</div>

		<div class="left-side">

			<div class="round-layer">

				<div class="game">

					<input type="checkbox" id="game-toggle">

					<div class="game-menu">
						<ul>

							<li>
								Изменить результат
							</li>

							<li>
								Удалить игру
							</li>

						</ul>
					</div>

					<div class="command-wrapper">

						<div class="command">
							Команда1
						</div>
						<hr>
						<div class="command">
							Команда2
						</div>

					</div>

				</div>

				<div class="game">

					<input type="checkbox" id="game-toggle">

					<div class="game-menu">
						<ul>

							<li>
								Изменить результат
							</li>

							<li>
								Удалить игру
							</li>

						</ul>
					</div>

					<div class="command-wrapper">

						<div class="command">
							Команда1
						</div>
						<hr>
						<div class="command">
							Команда2
						</div>

					</div>

				</div>

				<div class="game">

					<input type="checkbox" id="game-toggle">

					<div class="game-menu">
						<ul>

							<li>
								Изменить результат
							</li>

							<li>
								Удалить игру
							</li>

						</ul>
					</div>

					<div class="command-wrapper">

						<div class="command">
							Команда1
						</div>
						<hr>
						<div class="command">
							Команда2
						</div>

					</div>

				</div>

				<div class="game">

					<input type="checkbox" id="game-toggle">

					<div class="game-menu">
						<ul>

							<li>
								Изменить результат
							</li>

							<li>
								Удалить игру
							</li>

						</ul>
					</div>

					<div class="command-wrapper">

						<div class="command">
							Команда1
						</div>
						<hr>
						<div class="command">
							Команда2
						</div>

					</div>

				</div>

			</div>

			<div class="round-layer">

					<div class="game">

						<input type="checkbox" id="game-toggle">

						<div class="game-menu">
							<ul>

								<li>
									Изменить результат
								</li>

								<li>
									Удалить игру
								</li>

							</ul>
						</div>

						<div class="command-wrapper">

							<div class="command">
								Команда1
							</div>
							<hr>
							<div class="command">
								Команда2
							</div>

						</div>

					</div>

					<div class="game">

						<input type="checkbox" id="game-toggle">

						<div class="game-menu">
							<ul>

								<li>
									Изменить результат
								</li>

								<li>
									Удалить игру
								</li>

							</ul>
						</div>

						<div class="command-wrapper">

							<div class="command">
								Команда1
							</div>
							<hr>
							<div class="command">
								Команда2
							</div>

						</div>

					</div>


			</div>

		</div>

		<div class="right-side">



			<div class="round-layer">

					<div class="game">

						<input type="checkbox" id="game-toggle">

						<div class="game-menu">
							<ul>

								<li>
									Изменить результат
								</li>

								<li>
									Удалить игру
								</li>

							</ul>
						</div>

						<div class="command-wrapper">

							<div class="command">
								Команда1
							</div>
							<hr>
							<div class="command">
								Команда2
							</div>

						</div>

					</div>

					<div class="game">

						<input type="checkbox" id="game-toggle">

						<div class="game-menu">
							<ul>

								<li>
									Изменить результат
								</li>

								<li>
									Удалить игру
								</li>

							</ul>
						</div>

						<div class="command-wrapper">

							<div class="command">
								Команда1
							</div>
							<hr>
							<div class="command">
								Команда2
							</div>

						</div>

					</div>

			</div>

			<div class="round-layer">

				<div class="game">

					<input type="checkbox" id="game-toggle">

					<div class="game-menu">
						<ul>

							<li>
								Изменить результат
							</li>

							<li>
								Удалить игру
							</li>

						</ul>
					</div>

					<div class="command-wrapper">

						<div class="command">
							Команда1
						</div>
						<hr>
						<div class="command">
							Команда2
						</div>

					</div>

				</div>

				<div class="game">

					<input type="checkbox" id="game-toggle">

					<div class="game-menu">
						<ul>

							<li>
								Изменить результат
							</li>

							<li>
								Удалить игру
							</li>

						</ul>
					</div>

					<div class="command-wrapper">

						<div class="command">
							Команда1
						</div>
						<hr>
						<div class="command">
							Команда2
						</div>

					</div>

				</div>

				<div class="game">

					<input type="checkbox" id="game-toggle">

					<div class="game-menu">
						<ul>

							<li>
								Изменить результат
							</li>

							<li>
								Удалить игру
							</li>

						</ul>
					</div>

					<div class="command-wrapper">

						<div class="command">
							Команда1
						</div>
						<hr>
						<div class="command">
							Команда2
						</div>

					</div>

				</div>

				<div class="game">

					<input type="checkbox" id="game-toggle">

					<div class="game-menu">
						<ul>

							<li>
								Изменить результат
							</li>

							<li>
								Удалить игру
							</li>

						</ul>
					</div>

					<div class="command-wrapper">

						<div class="command">
							Команда1
						</div>
						<hr>
						<div class="command">
							Команда2
						</div>

					</div>

				</div>

			</div>

		</div>


	</div>

	<div class="stats-wrapper" id="stats-wrapper">
		<p class="stats-title">Статистика</p>
	</div>



</body>
</html>