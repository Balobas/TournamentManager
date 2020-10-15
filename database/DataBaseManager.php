<?php

require_once $_SERVER['DOCUMENT_ROOT']."\datastructure\TournamentManager.php";

class DataBaseManager
{
	private $host = 'localhost';
	private $username = 'php';
	private $password = 'php';
	private $dbName = 'championships';
	private $connection;
	private $error;

	public function __construct()
	{
		$this->connection = mysqli_init();
		$connectionResult = $this->connection->real_connect($this->host, $this->username, $this->password, $this->dbName);

		if (!$connectionResult)
		{
			$error = $this->connection->connect_errno.":".$this->connection->connect_error;
			trigger_error($error, E_USER_ERROR);
		}
		$this->error = "";
	}

	public function getError()
	{
		return $this->error;
	}

	public function createTournament(string $name, array $teams)
	{
		$n = count($teams);

		if ($n === 0 || $n === 1)
		{
			$this->error = "Количество игр не является степенью двойки или равно 1";
			return false;
		}

		$lg = log($n, 2);
		$l = floor($lg);
		if ($lg - $l !== 0.)
		{
			$this->error = "Количество игр не является степенью двойки или равно 1";
			return false;
		}

		$query = "select ID from tournament where NAME = ?";
		$statement = $this->connection->prepare($query);
		$statement->bind_param("s", $name);
		$ok = $statement->execute();

		if (!$ok)
		{
			$this->error = "Ошибка обращения к БД 1";
			return false;
		}

		$result = $statement->get_result();
		$row = $result->fetch_assoc();
		if ($row !== null)
		{
			$this->error = "Турнир уже существует";
			return false;
		}

		$query = "insert into tournament (NAME) value (?)";
		$statement = $this->connection->prepare($query);
		$statement->bind_param("s", $name);
		$ok = $statement->execute();

		if (!$ok)
		{
			$this->error = "Ошибка вставки в БД 1";
			return false;
		}

		$tournamentID = $statement->insert_id;

		foreach ($teams as $teamName)
		{
			$query = "select ID from team where NAME = ?";
			$statement = $this->connection->prepare($query);
			$statement->bind_param("s", $teamName);
			$ok = $statement->execute();

			if (!$ok)
			{
				$query = "delete from tournament where ID = {$tournamentID}";
				$this->connection->query($query);
				$this->error = "Ошибка обращения к БД 2";
				return false;
			}

			$result = $statement->get_result();

			$row = $result->fetch_assoc();

			if ($row === null)
			{
				$query = "insert into team (NAME) value (?)";
				$statement = $this->connection->prepare($query);
				$statement->bind_param("s", $teamName);
				$ok = $statement->execute();

				if (!$ok)
				{
					$query = "delete from tournament where ID = {$tournamentID}";
					$this->connection->query($query);
					$this->error = "Ошибка вставки в БД 2";
					return false;
				}

				$teamID = $statement->insert_id;
			}
			else
			{
				$teamID = $row['ID'];
			}

			$query = "insert into tournamentteams (TOURNAMENT_ID, TEAM_ID) value ({$tournamentID}, {$teamID})";
			$this->connection->query($query);
		}

		return $tournamentID;

	}

	public function initTournament(int $tournamentID, TournamentManager $manager) : bool
	{
		$query = "select ID, NAME from team inner join tournamentteams tt on team.ID = tt.TEAM_ID where tt.TOURNAMENT_ID = {$tournamentID}";
		$result = $this->connection->query($query);

		$teams = [];
		$teamsBuff = [];
		$row = $result->fetch_assoc();

		while($row !== null)
		{
			$teamsBuff[$row['ID']] = new Team($row['NAME'], $row['ID']);
			$teams[] = $teamsBuff[$row['ID']];
			$row = $result->fetch_assoc();
		}

		$ok = $manager->createTournament($teams, $tournamentID);

		if (!$ok)
		{
			return false;
		}

		$query = "select ID, TEAM1ID, TEAM2ID, TEAM1SCORE, TEAM2SCORE, ROUND, TOURNAMENT_ID from game where TOURNAMENT_ID = {$tournamentID} order by ROUND";
		$result = $this->connection->query($query);

		$row = $result->fetch_assoc();

		while($row !== null)
		{
			$firstID = $row['TEAM1ID'];
			$secondID = $row['TEAM2ID'];
			$firstScore = $row['TEAM1SCORE'];
			$secondScore = $row['TEAM2SCORE'];
			$game = new Game($teamsBuff[$firstID], $teamsBuff[$secondID], $row['ID']);

			$ok = $game->setScore($firstID, $firstScore, $secondID, $secondScore);

			if (!$ok)
			{
				return false;
			}

			$ok = $manager->addGame($game);

			if (!$ok)
			{
				return false;
			}

			$row = $result->fetch_assoc();
		}

		return true;
	}

	public function addGame(Game $game, TournamentManager $manager)
	{
		$ok = $manager->addGame($game);

		if (!$ok)
		{
			return 'error-message:Не удалось добавить игру'.PHP_EOL.'Возможно команды находятся в разных группах';
		}

		//Добавляем игру в бд
		$firstID = $game->getFirstTeamID();
		$secondID = $game->getSecondTeamID();
		$firstScore = $game->getFirstTeamScore();
		$secondScore = $game->getSecondTeamScore();
		$round = $manager->getRound($game);

		$query = "insert into game (TEAM1ID, TEAM2ID, TEAM1SCORE, TEAM2SCORE, ROUND, TOURNAMENT_ID) 
					value ({$firstID}, {$secondID}, {$firstScore}, {$secondScore}, {$round}, {$manager->getTournamentID()})";

		return $this->connection->query($query);
	}

	public function deleteGame(int $gameID, TournamentManager $manager)
	{
		$ok = $manager->deleteGame($gameID);

		if ($ok === true)
		{
			$query = "delete from game where ID = {$gameID}";
			return $this->connection->query($query);
		}

		return 'error-message:Нельзя удалить игру';
	}

	public function updateGame(Game $game, TournamentManager $manager, int $team1ID, int $score1, int $team2ID, int $score2)
	{
		$ok = $manager->updateGame($game, $team1ID, $score1, $team2ID, $score2);

		if (!$ok)
		{
			return 'error-message: Нельзя изменить эту игру';
		}

		$query = "update game set TEAM1ID = {$team1ID}, TEAM2ID = {$team2ID}, TEAM1SCORE = {$score1}, TEAM2SCORE = {$score2} where ID = {$game->getID()}";
		$ok = $this->connection->query($query);

		if (!$ok)
		{
			return 'error-message: Ошибка записи';
		}

		return true;
	}

	public function getIDForNewGame() : int
	{
		$query = "select count(ID) as num from game";
		$result = $this->connection->query($query);
		$row = $result->fetch_assoc();
		return $row['num'] + 1;
	}

	public function getTournamentList()
	{
		$query = "select ID, NAME from tournament";
		$result = $this->connection->query($query);

		$res = [];
		$row = $result->fetch_assoc();

		while($row !== null)
		{
			$res[$row['ID']] = $row['NAME'];
			$row = $result->fetch_assoc();
		}

		return $res;
	}

	public function getTournamentTeams(TournamentManager $manager)
	{
		return $manager->getTeamsAssoc();
	}

}