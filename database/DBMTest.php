<?php

require_once "DataBaseManager.php";
require_once $_SERVER['DOCUMENT_ROOT']."\datastructure\TournamentManager.php";


$dbm = new DataBaseManager();

$tm = new TournamentManager();


$dbm->initTournament(1, $tm);

$game = new Game(new Team('asd', 2), new Team('asd', 3), 5);
$dbm->addGame($game->getID());


