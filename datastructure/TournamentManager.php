<?php

require_once "TournamentGrid.php";

class TournamentManager
{
	private $groupA;
	private $groupB;
	private $final;
	private $teams;
	private $tournamentID;

	public function __construct()
	{
		$this->groupA = null;
		$this->groupB = null;
		$this->final = null;
		$this->teams = [];
	}

	public function createTournament(array $teams, $tournamentID) : bool
	{
		//Проверяем что количество команд равно степени двойки (необходимо для реализации турнира на выбывание, где всего две группы)
		$n = count($teams);
		if ($n === 0 || $n === 1)
		{
			return false;
		}
		$lg = log($n, 2);
		$l = floor($lg);
		if ($lg - $l !== 0.)
		{
			return false;
		}

		$a = array();
		$b = array();

		$half = $n / 2;

		for ($i = 0; $i < $half; $i++)
		{
			$a[] = $teams[$i];
		}

		for ($i = $half; $i < $n; $i++)
		{
			$b[] = $teams[$i];
		}

		$this->groupA = new TournamentGrid(1, $a);
		$this->groupB = new TournamentGrid(2, $b);

		$this->teams = $teams;

		$this->final = null;
		$this->tournamentID = $tournamentID;
		return true;
	}

	public function addGame(Game $game) : bool
	{
		if ($this->groupA === null || $this->groupB === null)
		{
			return false;
		}

		if ($this->groupA->addGame($game))
		{
			return true;
		}

		if ($this->groupB->addGame($game))
		{
			return true;
		}

		return $this->addFinalGame($game);
	}

	private function addFinalGame(Game $game) : bool
	{
		if ($this->final !== null)
		{
			return false;
		}
		if ($this->groupA->allGamesPlayed() && $this->groupB->allGamesPlayed())
		{
			$firstID = $game->getFirstTeamID();
			$secondID = $game->getSecondTeamID();

			$groupAWinner = $this->groupA->getWinner();
			$groupBWinner = $this->groupB->getWinner();

			$groupAWinnerID = $groupAWinner->getID();
			$groupBWinnerID = $groupBWinner->getID();

			if ($firstID === $groupAWinnerID && $secondID === $groupBWinnerID  ||  $secondID === $groupAWinnerID && $firstID === $groupBWinnerID)
			{
				$round = $this->groupA->getFinalRound() + 1;
				$this->final = new TournamentGridNode($game, $this->groupA->getFinalNode(), $this->groupB->getFinalNode(), $round);
				return true;
			}
		}
		return false;
	}

	public function deleteGame(int $gameID) : bool
	{
		if ($this->groupA === null || $this->groupB === null)
		{
			return false;
		}

		if ($this->allGamesPlayed())
		{
			return $this->deleteFinalGame($gameID);
		}

		if ($this->groupA->deleteGame($gameID))
		{
			return true;
		}

		return $this->groupB->deleteGame($gameID);

	}

	private function deleteFinalGame(int $gameID) : bool
	{
		if ($this->final !== null)
		{
			$finalGameID = $this->final->getGameID();

			if ($finalGameID === $gameID)
			{
				$this->final = null;
				return true;
			}
		}
		return false;
	}

	public function updateGame(Game $game, int $team1ID, int $score1, int $team2ID, int $score2) : bool
	{
		if ($this->groupA === null || $this->groupB === null)
		{
			return false;
		}

		if ($this->allGamesPlayed())
		{
			return $this->updateFinalGame($game, $team1ID, $score1, $team2ID, $score2);
		}

		if ($this->groupA->updateGame($game, $team1ID, $score1, $team2ID, $score2))
		{
			return true;
		}

		return $this->groupB->updateGame($game, $team1ID, $score1, $team2ID, $score2);
	}

	private function updateFinalGame(Game $game, int $team1ID, int $score1, int $team2ID, int $score2) : bool
	{
		if ($this->final === null)
		{
			return false;
		}

		$gameID = $game->getID();

		if ($gameID === $this->final->getGameID())
		{
			return $this->final->updateGame($team1ID, $score1, $team2ID, $score2);
		}
		return false;
	}

	public function allGamesPlayed() : bool
	{
		return $this->final !== null;
	}

	public function getWinner()
	{
		if ($this->allGamesPlayed())
		{
			return $this->final->getWinner();
		}
		return null;
	}

	public function getFinal()
	{
		return $this->final;
	}

	public function getStats()
	{
		$res = [];
		foreach ($this->teams as $team) {
			$teamID = $team->getID();
			$res[$teamID]['MaxRound'] = 0;
			$res[$teamID]['SumScore'] = 0;
			$res[$teamID]['GamesNum'] = 0;
			$res[$teamID]['MaxScore'] = 0;
		}

		$lastPlace = 0;

		if ($this->final !== null)
		{
			$this->final->getStats($res);

			$firstID = $this->final->getFirstTeamID();
			$secondID = $this->final->getSecondTeamID();

			if ($this->final->getWinnerID() === $firstID)
			{
				$res[$firstID]['Place'] = 1;
				$res[$secondID]['Place'] = 2;
			}
			else
			{
				$res[$firstID]['Place'] = 2;
				$res[$secondID]['Place'] = 1;
			}
			$lastPlace = 2;
		}
		else
		{
			if ($this->groupA !== null && $this->groupB !== null)
			{
				$this->groupA->getStats($res);
				$this->groupB->getStats($res);
			}
		}

		foreach ($res as $key => $elem)
		{
			if ($res[$key]['GamesNum'] !== 0)
			{
				$res[$key]['Average'] = ($elem['SumScore'] / $elem['GamesNum']);
				$res[$key]['ID'] = $key;
			}
		}

		if ($this->final === null)
		{
			return $res;
		}

		$maxRound = $this->final->getRound();

		for ($i = 1; $i < $maxRound; $i++)
		{
			$tmp = [];
			foreach ($res as $elem)
			{
				if ($elem['MaxRound'] === $i)
				{
					$tmp[] = $elem;
				}
			}

			usort($tmp, function ($a, $b)
			{
				if ($a['MaxScore'] < $b['MaxScore'])
				{
					return 1;
				}
				else
				{
					return -1;
				}
			});

			foreach ($tmp as $key => $elem)
			{
				$res[$elem['ID']]['Place'] = $lastPlace + $key + 1;
			}
			$lastPlace += count($tmp);
		}

		return $res;
	}

	public function getRound($game)
	{

		if ($this->final !== null)
		{
			return $this->final->getRoundRec($game);
		}

		$res1 = $this->groupA->getRound($game);
		$res2 = $this->groupB->getRound($game);

		if ($res1 !== 0)
		{
			return $res1;
		}

		return $res2;
	}

	public function getTournamentID()
	{
		return $this->tournamentID;
	}

	public function mapGroupA(callable $func, &$res)
	{
		if ($this->groupA !== null)
		{
			$this->groupA->map($func, $res);
		}
	}

	public function mapGroupB(callable $func, &$res)
	{
		if ($this->groupB !== null)
		{
			$this->groupB->map($func, $res);
		}
	}

	public function getTeamsNum()
	{
		return count($this->teams);
	}

	public function getTeamsAssoc()
	{
		if ($this->final !== null)
		{
			return [];
		}

		if ($this->groupA === null || $this->groupB === null)
		{
			return [];
		}

		$a = $this->groupA->getNotPlayedTeamsAssoc();
		$b = $this->groupB->getNotPlayedTeamsAssoc();

		$res = $a + $b;
		arsort($res);
		return $res;
	}

	public function getGroupATeams()
	{
		if ($this->groupA !== null)
		{
			return $this->groupA->getTeams();
		}

		return [];
	}

	public function getGroupBTeams()
	{
		if ($this->groupB !== null)
		{
			return $this->groupB->getTeams();
		}

		return [];
	}

	public function getLastGames()
	{
		if ($this->final !== null)
		{
			return [];
		}

		return $this->getLastGamesFromGroupA() + $this->getLastGamesFromGroupB();
	}

	public function getLastGamesFromGroupA()
	{
		if ($this->groupA !== null)
		{
			return $this->groupA->getLastGames();
		}
		return [];
	}

	public function getLastGamesFromGroupB()
	{
		if ($this->groupB !== null)
		{
			return $this->groupB->getLastGames();
		}
		return [];
	}

}