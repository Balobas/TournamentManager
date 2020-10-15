<?php

require_once "Team.php";

class Game
{
	private $team1, $team2;
	private $score1, $score2;
	private $id;

	public function __construct(Team $team1, Team $team2, int $id)
	{
		$this->team1 = $team1;
		$this->team2 = $team2;
		$this->id = $id;
	}

	public function setScore(int $team1ID, int $score1, int $team2ID, int $score2): bool
	{
		if ($this->team1->getID() === $team1ID && $this->team2->getID() === $team2ID)
		{
			$this->score1 = $score1;
			$this->score2 = $score2;
			return true;
		}
		else if ($this->team1->getID() === $team2ID && $this->team2->getID() === $team1ID)
		{
			$this->score2 = $score1;
			$this->score1 = $score2;
			return true;
		}
		return false;
	}

	public function getWinner() : Team
	{
		if ($this->team1 !== null && $this->team2 !== null)
		{
			if ($this->score1 > $this->score2)
			{
				return $this->team1;
			}
			else{
				return $this->team2;
			}
		}
		return null;
	}

	public function getID()
	{
		return $this->id;
	}

	public function getFirstTeamID()
	{
		return $this->team1->getID();
	}

	public function getSecondTeamID()
	{
		return $this->team2->getID();
	}

	public function getFirstTeamName()
	{
		return $this->team1->getName();
	}

	public function getSecondTeamName()
	{
		return $this->team2->getName();
	}

	public function getFirstTeamScore()
	{
		return $this->score1;
	}

	public function getSecondTeamScore()
	{
		return $this->score2;
	}

	public function getStats(array &$stats) : void
	{
		$firstID = $this->team1->getID();
		$secondID = $this->team2->getID();

		$stats[$firstID]['Name'] = $this->team1->getName();
		$stats[$secondID]['Name'] = $this->team2->getName();

		$stats[$firstID]['SumScore'] += $this->score1;
		$stats[$secondID]['SumScore'] += $this->score2;

		$stats[$firstID]['GamesNum'] += 1;
		$stats[$secondID]['GamesNum'] += 1;

		if ($this->score1 > $stats[$firstID]['MaxScore'])
		{
			$stats[$firstID]['MaxScore'] = $this->score1;
		}

		if ($this->score2 > $stats[$secondID]['MaxScore'])
		{
			$stats[$secondID]['MaxScore'] = $this->score2;
		}
	}
}