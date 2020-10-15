<?php

require_once $_SERVER['DOCUMENT_ROOT']."\database\DataBaseManager.php";
require_once $_SERVER['DOCUMENT_ROOT']."\datastructure\TournamentManager.php";
require_once $_SERVER['DOCUMENT_ROOT'].'\view\HTMLGenerator.php';

//Класс для обработки запросов пользователя

class InputHandler
{

	public static function handle(string $action, array $data)
	{

		switch ($action)
		{
			case 'open':
			{
				//результат помещается в grid_wrapper
				if (!isset($data['ID']))
				{
					return 'error-message:Ошибка';
				}

				setcookie('currentTournamentID', $data['ID'], time() + 60*60*24, '/');

				return self::openTournament($data['ID']);
			}
			case 'openChoseDialog':
			{
				//результат помещается в body
				$dbm = new DataBaseManager();
				$tournamentList = $dbm->getTournamentList();

				return self::openChoseDialog($tournamentList);
			}
			case 'addGame':
			{
				// data[] : ['firstID'], ['secondID'], ['firstName'], ['secondName'], ['score1'], ['score2']
				if(!isset($data['firstID']) || !isset($data['secondID'])
					|| !isset($data['firstName']) || !isset($data['secondName'])
					|| !isset($data['score1']) || !isset($data['score2']))
				{
					return 'error-message:Где то остались пустые поля :(';
				}

				$team1 = new Team($data['firstName'], $data['firstID']);
				$team2 = new Team($data['secondName'], $data['secondID']);

				if (!isset($_COOKIE['currentTournamentID']))
				{
					return 'error-message:Выберите турнир';
				}

				return self::addGame($_COOKIE['currentTournamentID'], $team1, $team2, $data['score1'], $data['score2']);
			}
			case 'updateGame':
			{
				// data[] : ['gameID'] , ['firstID'], ['secondID'], ['firstName'], ['secondName'], ['score1'], ['score2']
				if(!isset($data['gameID']) || !isset($data['firstID']) || !isset($data['secondID'])
					|| !isset($data['firstName']) || !isset($data['secondName'])
					|| !isset($data['score1']) || !isset($data['score2']))
				{
					return false;
				}

				$team1 = new Team($data['firstName'], $data['firstID']);
				$team2 = new Team($data['secondName'], $data['secondID']);
				$game = new Game($team1, $team2, $data['gameID']);

				if (!isset($_COOKIE['currentTournamentID']))
				{
					return 'error-message:Выберите турнир';
				}

				return self::updateGame($_COOKIE['currentTournamentID'], $game, $data['firstID'], $data['secondID'], $data['score1'], $data['score2']);
			}
			case 'deleteGame':
			{
				// data[] : ['gameID'] , ['firstID'], ['secondID'], ['firstName'], ['secondName'], ['score1'], ['score2']
				if(!isset($data['gameID']))
				{
					return false;
				}

				$gameID = $data['gameID'];

				if (!isset($_COOKIE['currentTournamentID']))
				{
					return 'error-message:Выберите турнир';
				}

				return self::deleteGame($gameID, $_COOKIE['currentTournamentID']);
			}
			case 'createTournament':
			{
				//результат помещается в grid_wrapper

				$dbm = new DataBaseManager();
				$tournamentID = $dbm->createTournament($data['name'], $data['teams']);

				if ($tournamentID === false)
				{
					return 'error-message:Ошибка при создании турнира'.PHP_EOL.$dbm->getError();
				}

				setcookie('currentTournamentID', $tournamentID, time() + 60*60*24, '/');
				return self::openTournament($tournamentID);
			}
			case 'addGameTeamsLists':
			{
				if (!isset($_COOKIE['currentTournamentID']))
				{
					return 'error-message:Выберите турнир';
				}

				$tournamentID = $_COOKIE['currentTournamentID'];

				$dbm = new DataBaseManager();
				$tournament = new TournamentManager();

				$dbm->initTournament($tournamentID, $tournament);

				$teams = $tournament->getTeamsAssoc();

				return HTMLGenerator::makeTeamsLists($teams);
			}
			case 'getStats':
			{
				if (!isset($data['tournamentID']))
				{
					return 'error-message:Ошибка';
				}

				$dbm = new DataBaseManager();
				$tournament = new TournamentManager();
				$dbm->initTournament($data['tournamentID'], $tournament);

				$stats = $tournament->getStats();

				uasort($stats, function ($a, $b)
				{
					if ($a['Place'] > $b['Place'])
					{
						return 1;
					}
					return -1;
				});

				return HTMLGenerator::makeStats($stats);
			}
			case 'getZeroRoundTeamsLists':
			{

				if (!isset($data['tournamentID']))
				{
					return 'error-message:Выберите турнир';
				}

				$id = $data['tournamentID'];
				$dbm = new DataBaseManager();
				$tournament = new TournamentManager();
				$dbm->initTournament($id, $tournament);

				$groupATeams = [];
				$groupBTeams = [];

				$teamsA = $tournament->getGroupATeams();
				$teamsB = $tournament->getGroupBTeams();

				foreach ($teamsA as $team)
				{
					$groupATeams[] = $team->getName();
				}

				foreach ($teamsB as $team)
				{
					$groupBTeams[] = $team->getName();
				}

				return HTMLGenerator::makeZeroRoundTeamsListGroupA($groupATeams).HTMLGenerator::makeZeroRoundTeamsListGroupB($groupBTeams);
			}
		}

		return 'error-message:Неверный запрос';
	}

	private static function addGame(int $tournamentID, Team $team1, Team $team2, int $score1, int $score2)
	{
		$dbm = new DataBaseManager();
		$tournament = new TournamentManager();
		$dbm->initTournament($tournamentID, $tournament);
		$gameID = $dbm->getIDForNewGame();
		$game = new Game($team1, $team2, $gameID);
		$game->setScore($team1->getID(), $score1, $team2->getID(), $score2);

		return $dbm->addGame($game, $tournament);
	}

	private static function deleteGame(int $gameID, int $tournamentID)
	{
		$tournament = new TournamentManager();
		$dbm = new DataBaseManager();
		$ok = $dbm->initTournament($tournamentID, $tournament);

		if (!$ok)
		{
			return false;
		}

		return $dbm->deleteGame($gameID, $tournament);
	}

	private static function updateGame(int $tournamentID, Game $game, int $firstTeamID, int $secondTeamID, int $score1, int $score2)
	{
		$tournament = new TournamentManager();
		$dbm = new DataBaseManager();
		$ok = $dbm->initTournament($tournamentID, $tournament);

		if (!$ok)
		{
			return false;
		}

		return $dbm->updateGame($game, $tournament, $firstTeamID, $score1, $secondTeamID, $score2);
	}

	private static function openTournament(int $id)
	{
		$tournament = new TournamentManager();
		$dbm = new DataBaseManager();
		$dbm->initTournament($id, $tournament);

		$result = "";

		if ($tournament->allGamesPlayed())
		{
			$final = $tournament->getFinal();
			$gameHtml = HTMLGenerator::makeGame($final);
			$result .= HTMLGenerator::makeFinalGame($gameHtml);
		}
		else
		{
			$emptyGame = HTMLGenerator::makeEmptyGame();
			$result .= HTMLGenerator::makeFinalGame($emptyGame);
		}

		$resA = [];
		$resB = [];
		$tournament->mapGroupA("HTMLGenerator::makeGameWithoutMenu", $resA);
		$tournament->mapGroupB("HTMLGenerator::makeGameWithoutMenu", $resB);

		$groupALastGames = $tournament->getLastGamesFromGroupA();
		$groupBLastGames = $tournament->getLastGamesFromGroupB();

		foreach ($groupALastGames as $lastGame)
		{
			if ($tournament->allGamesPlayed())
			{
				$resA[$lastGame->getRound()][] = HTMLGenerator::makeGameWithoutMenu($lastGame);
			}
			else
			{
				$resA[$lastGame->getRound()][] = HTMLGenerator::makeGame($lastGame);
			}
		}

		foreach ($groupBLastGames as $lastGame)
		{
			if ($tournament->allGamesPlayed())
			{
				$resB[$lastGame->getRound()][] = HTMLGenerator::makeGameWithoutMenu($lastGame);
			}
			else
			{
				$resB[$lastGame->getRound()][] = HTMLGenerator::makeGame($lastGame);
			}
		}

		$leftSideRoundLayers = [];
		$rightSideRoundLayers = [];

		$roundsNum = log($tournament->getTeamsNum()/2, 2);

		$teamsNum = $tournament->getTeamsNum()/2;
		for ($i = 1; $i <= $roundsNum; $i++)
		{

			if (isset($resA[$i]))
			{
				$leftSideRoundLayers[$i] = HTMLGenerator::makeRoundLayer($i, $resA[$i], $teamsNum);
			}
			else
			{
				$leftSideRoundLayers[$i] = HTMLGenerator::makeRoundLayer($i, [], $teamsNum);
			}

			if (isset($resB[$i]))
			{
				$rightSideRoundLayers[$i] = HTMLGenerator::makeRoundLayer($i, $resB[$i], $teamsNum);
			}
			else
			{
				$rightSideRoundLayers[$i] = HTMLGenerator::makeRoundLayer($i, [], $teamsNum);
			}

		}

		$leftSide = HTMLGenerator::makeLeftSide($leftSideRoundLayers);
		$rightSide = HTMLGenerator::makeRightSide($rightSideRoundLayers);

		$result .= $leftSide.$rightSide;

		return $result;
	}

	private static function openChoseDialog($tournamentsList)
	{
		$res = "<div class=\"chose-tournament-dialog\" id=\"t-dialog\">
					<p class=\"chose-title\">
						Доступные турниры
					</p>

					<div class=\"close-btn\" onclick=\"closeChoseDialog()\">
						<span class=\"close\"></span>
					</div>

					<div class='chose-list-wrapper'><ul>";

		foreach ($tournamentsList as $id => $name)
		{
			$res .= HTMLGenerator::makeTournamentListItem($id, $name);
		}

		$res .= "</ul></div></div>";

		return $res;
	}

}