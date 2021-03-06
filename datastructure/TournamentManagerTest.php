<?php

require_once "TournamentManager.php";

function tournamentManagerTest()
{
	$team1 = new Team("LA Lakers", 1);
	$team2 = new Team("Miami Heat", 2);
	$team3 = new Team("Brooklyn Nets", 3);
	$team4 = new Team("Cleveland Cavaliers", 4);

	$manager = new TournamentManager();
	echo $manager->createTournament([$team1, $team2, $team3, $team4], 1) === true ? "passed" : "failed";

	$game1 = new Game($team1, $team2, 1);
	echo "INFO|Game 1 :  ".$game1->getFirstTeamName()." vs ".$game1->getSecondTeamName().PHP_EOL;
	echo "TEST|Set game 1 score 127:113 :               ".($game1->setScore($team1->getID(), 127, $team2->getID(), 113) === true ? "passed" : "failed").PHP_EOL;
	echo PHP_EOL;

	$game2 = new Game($team3, $team4, 2);
	echo "INFO|Game 2 :  ".$game2->getFirstTeamName()." vs ".$game2->getSecondTeamName().PHP_EOL;
	echo "TEST|Set game 2 score 98:103 :                ".($game2->setScore($team3->getID(), 98, $team4->getID(), 103) === true? "passed" : "failed").PHP_EOL;
	echo PHP_EOL;

	echo "TEST|Game 1 added to tournament grid :        ".($manager->addGame($game1) === true ? "passed" : "failed").PHP_EOL;
	echo "TEST|Game 2 added to tournament grid :        ".($manager->addGame($game2) === true ? "passed" : "failed").PHP_EOL;
	echo PHP_EOL;

	$team5 = new Team("Ficklyn Keks", 5);
	$team6 = new Team("Duckland Tatarians", 6);

	echo "INFO|Team 5 :   Name: ".$team5->getName()."  ID: ".$team5->getID().PHP_EOL;
	echo "INFO|Team 6 :   Name: ".$team6->getName()."  ID: ".$team6->getID().PHP_EOL;
	echo PHP_EOL;

	$fakeGame = new Game($team5, $team6, 5);
	echo "INFO|Fake game :  ".$fakeGame->getFirstTeamName()." vs ".$fakeGame->getSecondTeamName().PHP_EOL;
	echo PHP_EOL;

	echo "TEST|Set fake game score 110:80 :             ".($fakeGame->setScore($team5->getID(), 110, $team6->getID(), 80) === true? "passed" : "failed").PHP_EOL;
	echo PHP_EOL;

	echo "TEST|Fake game not added to tournament grid : ".($manager->addGame($fakeGame) === true ? "failed" : "passed").PHP_EOL;
	echo PHP_EOL;

	echo "TEST|Delete game 2 :                          ".($manager->deleteGame($game2->getID()) === true ? "passed" : "failed").PHP_EOL;
	echo "TEST|Add game 2 again :                       ".($manager->addGame($game2) === true ? "passed" : "failed").PHP_EOL;
	echo PHP_EOL;

	$finalGame = new Game($game1->getWinner(), $game2->getWinner(), 6);
	echo "INFO|Final Game :  ".$finalGame->getFirstTeamName()." vs ".$finalGame->getSecondTeamName().PHP_EOL;
	echo "TEST|Set final game score 110:113 :           ".($finalGame->setScore($finalGame->getFirstTeamID(), 110, $finalGame->getSecondTeamID(), 113) === true ? "passed" : "failed").PHP_EOL;
	echo PHP_EOL;

	echo "TEST|Add final game :                         ".($manager->addGame($finalGame) === true ? "passed" : "failed").PHP_EOL;
	echo PHP_EOL;

	$allGamesPlayed = $manager->allGamesPlayed();
	echo "TEST|All games played :                       ".($allGamesPlayed === true ? "passed" : "failed").PHP_EOL;

	if ($allGamesPlayed)
	{
		$winner = $manager->getWinner();
		$isNull = $winner === null ? true : false;
		echo "TEST|Winner is not a null :                   ".($isNull ? "failed" : "passed").PHP_EOL;
		if (!$isNull)
		{
			echo "INFO|Winner team :  Name : ".$winner->getName()."  ID: ".$winner->getID().PHP_EOL;
			echo PHP_EOL;

			$stats = $manager->getStats();
			echo "INFO|Statistics:".PHP_EOL;
			print_r($stats);
			echo PHP_EOL;

			echo "TEST|Try to delete game 2 :                   ".($manager->deleteGame($game2->getID()) === true ? "failed" : "passed").PHP_EOL;
			echo PHP_EOL;

			echo "TEST|Delete final game :                      ".($manager->deleteGame($finalGame->getID()) === true ? "passed" : "failed").PHP_EOL;
			echo PHP_EOL;

			echo "TEST|All games played (false):                ".($manager->allGamesPlayed() === false ? "passed" : "failed").PHP_EOL;
			echo PHP_EOL;

			echo "INFO|Game 2 :  ".$game2->getFirstTeamName()." ".$game2->getFirstTeamScore()." : ".$game2->getSecondTeamScore()." ".$game2->getSecondTeamName().PHP_EOL;
			echo "TEST|Update game 2 :                          ".($manager->updateGame($game2, $game2->getFirstTeamID(), 59, $game2->getSecondTeamID(), 65) === true ? "passed" : "failed").PHP_EOL;
			echo "INFO|Game 2 :  ".$game2->getFirstTeamName()." ".$game2->getFirstTeamScore()." : ".$game2->getSecondTeamScore()." ".$game2->getSecondTeamName().PHP_EOL;

			$stats = $manager->getStats();
			echo "INFO|Statistics:".PHP_EOL;
			print_r($stats);
			echo PHP_EOL;

		}
	}
}

tournamentManagerTest();