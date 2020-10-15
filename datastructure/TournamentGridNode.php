<?php

require_once "Team.php";
require_once "Game.php";

class TournamentGridNode
{
	private $leftParent;
	private $rightParent;
	private $game;
	private $round;

	public function __construct(Game $game, TournamentGridNode $tournamentGrid1 = null, TournamentGridNode $tournamentGrid2 = null, $round = 1)
	{
		$this->leftParent = $tournamentGrid1;
		$this->rightParent = $tournamentGrid2;
		$this->game = $game;
		$this->round = $round;
	}

	public function getRound()
	{
		return $this->round;
	}

	public function getRoundRec(Game $game)
	{
		if ($this->game->getID() === $game->getID())
		{
			return $this->round;
		}

		$res1 = 0;
		$res2 = 0;

		if ($this->rightParent !== null)
		{
			$res1 = $this->rightParent->getRoundRec($game);
		}

		if ($this->leftParent !== null)
		{
			$res2 = $this->leftParent->getRoundRec($game);
		}

		if ($res1 !== 0)
		{
			return $res1;
		}

		return $res2;
	}

	public function getGameID()
	{
		return $this->game->getID();
	}

	public function updateGame(int $team1ID, int $score1, int $team2ID, int $score2) : bool
	{
		return $this->game->setScore($team1ID, $score1, $team2ID, $score2);
	}

	public function getWinner()
	{
		return $this->game->getWinner();
	}

	public function getWinnerID()
	{
		$winner = $this->game->getWinner();
		return $winner->getID();
	}

	public function getFirstTeamID()
	{
		return $this->game->getFirstTeamID();
	}

	public function getFirstTeamName()
	{
		return $this->game->getFirstTeamName();
	}

	public function getSecondTeamID()
	{
		return $this->game->getSecondTeamID();
	}

	public function getSecondTeamName()
	{
		return $this->game->getSecondTeamName();
	}

	public function getLeftParent()
	{
		return $this->leftParent;
	}

	public function getRightParent()
	{
		return $this->rightParent;
	}

	public function getStats(array &$stats)
	{

		$this->game->getStats($stats);

		if ($this->round > $stats[$this->getFirstTeamID()]['MaxRound'])
		{
			$stats[$this->getFirstTeamID()]['MaxRound'] = $this->round;
		}

		if ($this->round > $stats[$this->getSecondTeamID()]['MaxRound'])
		{
			$stats[$this->getSecondTeamID()]['MaxRound'] = $this->round;
		}

		if ($this->leftParent !== null)
		{
			$this->leftParent->getStats($stats);
		}

		if ($this->rightParent !== null)
		{
			$this->rightParent->getStats($stats);
		}
	}

	public function getFirstTeamScore()
	{
		return $this->game->getFirstTeamScore();
	}

	public function getSecondTeamScore()
	{
		return $this->game->getSecondTeamScore();
	}

	public function map(callable $func, &$res)
	{
		//Функция $func применяется к дочерним элементам узла, и результат добавляется в переданный массив $res, где ключ
		//это номер раунда
		//Это позволяет пройтись по всем играм функцией $func, кроме последних
		if ($this->leftParent !== null)
		{
			$res[$this->leftParent->getRound()][] = $func($this->leftParent);
			$this->leftParent->map($func, $res);
		}

		if ($this->rightParent !== null)
		{
			$res[$this->rightParent->getRound()][] = $func($this->rightParent);
			$this->rightParent->map($func, $res);
		}

		return;
	}

}