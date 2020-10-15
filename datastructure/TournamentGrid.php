<?php

require_once "TournamentGridNode.php";

class TournamentGrid
{
	private $lastGames;
	private $teams;
	private $usedTeams;
	private $tournamentID;

	public function __construct(int $id, array $teams)
	{
		$this->lastGames = array();
		$this->usedTeams = array();
		$this->tournamentID = $id;
		$this->teams = $teams;
	}

	private function addFirstGame(Game $game) : bool
	{
		// Находим в teams нужные команды, создаем объект TournamentGridNode и помещаем его в lastGames, помечаем команды как использованные
		// Если команды не найдены, или они помечены как использованные, то добавить игру нельзя, выдаем ошибку

		$firstID = $game->getFirstTeamID();
		$secondID = $game->getSecondTeamID();

		if ($firstID === $secondID)
		{
			return false;
		}

		if (isset($this->usedTeams[$firstID]) || isset($this->usedTeams[$secondID]))
		{
			return false;
		}

		$firstTeamFounded = false;
		$secondTeamFounded = false;

		foreach ($this->teams as $team)
		{
			if ($team->getID() === $firstID)
			{
				$firstTeamFounded = true;
			}
			if ($team->getID() === $secondID)
			{
				$secondTeamFounded = true;
			}
		}

		if ($firstTeamFounded && $secondTeamFounded)
		{
			$this->usedTeams[$firstID] = 1;
			$this->usedTeams[$secondID] = 1;

			$node = new TournamentGridNode($game);
			$this->lastGames[] = $node;

			return true;
		}
		return false;
	}

	public function addGame(Game $game) : bool
	{
		// Находим в lastGames две игры ( Точнее их обертки в виде TournamentGridNode ),
		// предшествующие добавляемой
		// У искомых узлов номер раунда должен совпадать
		// Заменяем эти два найденных узла на новый, содержащий добавляемую игру
		// Если предшествующих игр не найдено, то выдаем ошибку, что игра не может быть добавлена

		$firstID = $game->getFirstTeamID();
		$secondID = $game->getSecondTeamID();

		if ($firstID === $secondID)
		{
			return false;
		}

		$rightNode = null;
		$leftNode = null;

		$key1 = -1;
		$key2 = -1;


		foreach ($this->lastGames as $key => $lastGameNode)
		{
			$winnerID = $lastGameNode->getWinnerID();

			if ( $firstID === $winnerID)
			{
				$rightNode = $lastGameNode;
				$key1 = $key;
			}
			if ($secondID === $winnerID)
			{
				$leftNode = $lastGameNode;
				$key2 = $key;
			}
		}

		if ($rightNode !== null && $leftNode !== null)
		{

			$rightNodeRound = $rightNode->getRound();
			$leftNodeRound = $leftNode->getRound();

			if ($rightNodeRound === $leftNodeRound)
			{
				$node = new TournamentGridNode($game, $leftNode, $rightNode, $rightNodeRound + 1);

				unset($this->lastGames[$key1]);
				unset($this->lastGames[$key2]);

				$this->lastGames[] = $node;
				return true;
			}
		}

		return $this->addFirstGame($game);
	}

	// Для методов delete и update
	// Ищем в lastGames игру
	// Если ничего не найдено, то либо после игры прошли еще игры, либо такой игры нет вообще, возвращаем ошибку

	public function deleteGame(int $gameID): bool
	{
		$node = null;
		$nodeKey = -1;

		foreach ($this->lastGames as $key => $gameNode)
		{
			if ($gameID === $gameNode->getGameID())
			{
				$node = $gameNode;
				$nodeKey = $key;
				break;
			}
		}

		if ($node === null)
		{
			return false;
		}

		$leftNode = $node->getLeftParent();
		$rightNode = $node->getRightParent();
		$round = $node->getRound();

		if ($round === 1)
		{
			$team1ID = $node->getFirstTeamID();
			$team2ID = $node->getSecondTeamID();

			unset($this->usedTeams[$team1ID]);
			unset($this->usedTeams[$team2ID]);
		}
		else
		{
			$this->lastGames[] = $leftNode;
			$this->lastGames[] = $rightNode;
		}

		unset($this->lastGames[$nodeKey]);
		return true;
	}

	public function updateGame(Game $game, int $team1ID, int $score1, int $team2ID, int $score2): bool
	{

		$gameID = $game->getID();

		foreach ($this->lastGames as $node)
		{
			if ($node->getGameID() === $gameID)
			{
				return $node->updateGame($team1ID, $score1, $team2ID, $score2);
			}
		}

		return false;
	}

	public function getWinner()
	{
		if ($this->allGamesPlayed())
		{
			if (count($this->teams) === 1)
			{
				reset($this->teams);
				return current($this->teams);
			}
			else{
				reset($this->lastGames);
				return (current($this->lastGames))->getWinner();
			}
		}
		return null;
	}

	public function allGamesPlayed() : bool
	{
		if (count($this->lastGames) === 1)
		{
			reset($this->lastGames);
			$lastNode = current($this->lastGames);

			if ($lastNode->getRound() === (int)log(count($this->teams), 2))
			{
				return true;
			}
		}
		elseif (count($this->teams) === 1)
		{
			return true;
		}

		return false;
	}

	public function getFinalNode()
	{
		if ($this->allGamesPlayed() && count($this->teams) > 1)
		{
			reset($this->lastGames);
			return current($this->lastGames);
		}
		return null;
	}

	public function getStats(array &$stats)
	{
		if (count($this->lastGames) > 0)
		{
			foreach ($this->lastGames as  $node)
			{
				$node->getStats($stats);
			}
		}

		foreach ($this->teams as $team)
		{
			$stats[$team->getID()]['Name'] = $team->getName();
		}
	}

	public function getRound(Game $game)
	{
		$res = [];

		foreach ($this->lastGames as $gameNode) {

			if ($gameNode->getGameID() === $game->getID())
			{
				return $gameNode->getRound();
			}

			$right = $gameNode->getRightParent();
			$left = $gameNode->getLeftParent();

			if ($right !== null)
			{
				$res[] = $right->getRoundRec($game);
			}

			if ($left !== null)
			{
				$res[] = $left->getRoundRec($game);
			}
		}

		foreach ($res as $round)
		{
			if ($round !== 0)
			{
				return $round;
			}
		}

		return 0;
	}

	public function map(callable $func, &$res)
	{
		foreach ($this->lastGames as $node)
		{
			$node->map($func, $res);
		}
	}

	public function maxRound()
	{
		$maxRound = 0;
		foreach ($this->lastGames as $node)
		{
			$nodeRound = $node->getRound();
			if ($nodeRound > $maxRound)
			{
				$maxRound = $nodeRound;
			}
		}

		return $maxRound;
	}

	public function getNotPlayedTeamsAssoc()
	{
		$res = [];

		foreach ($this->lastGames as $node)
		{
			$team = $node->getWinner();

			$res[$team->getID()] = $team->getName();
		}

		foreach ($this->teams as $team)
		{
			if (!isset($this->usedTeams[$team->getID()]))
			{
				$res[$team->getID()] = $team->getName();
			}
		}

		return $res;
	}

	public function getFinalRound()
	{
		$finalNode = $this->getFinalNode();

		if ($finalNode === null)
		{
			return 0;
		}

		return $finalNode->getRound();
	}

	public function getTeams() : array
	{
		return $this->teams;
	}

	public function getLastGames()
	{
		return $this->lastGames;
	}

}