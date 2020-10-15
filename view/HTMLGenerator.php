<?php

class HTMLGenerator
{
	public static function makeTournamentListItem(int $id, string $name)
	{
		return "<li onclick='openTournament(this)'>".$name."<p class=\"t-id\">".$id."</p></li>";
	}

	public static function makeTeamsLists($teams)
	{
		$list1 = '<ul class="list1" >';
		$list2 = '<ul class="list2">';

		foreach ($teams as $id => $name)
		{
			$list1 .= "<li onclick='takeGameFromFirstList(this)' class='add-team-list-item'>".$name."<p class='hidden'>".$id."</p></li>";
			$list2 .= "<li onclick='takeGameFromSecondList(this)' class='add-team-list-item'>".$name."<p class='hidden'>".$id."</p></li>";
		}

		$list1 .= '</ul>';
		$list2 .= '</ul>';

		return $list1.$list2;
	}

	public static function makeGame(TournamentGridNode $node)
	{
		$gameID = $node->getGameID();

		$firstID = $node->getFirstTeamID();
		$firstName = $node->getFirstTeamName();
		$firstScore = $node->getFirstTeamScore();

		$secondID = $node->getSecondTeamID();
		$secondName = $node->getSecondTeamName();
		$secondScore = $node->getSecondTeamScore();

		return '<div class="game">
					<input type="checkbox" id="game-toggle">'.

					self::makeGameMenu().

					self::makeGameInfo($gameID, $firstID, $secondID).

					self::makeCommandWrapper($firstName, $firstScore, $secondName, $secondScore).
				'</div>';
	}

	public static function makeGameWithoutMenu(TournamentGridNode $node)
	{
		$firstName = $node->getFirstTeamName();
		$firstScore = $node->getFirstTeamScore();

		$secondName = $node->getSecondTeamName();
		$secondScore = $node->getSecondTeamScore();

		return '<div class="game">
					<input type="checkbox" id="game-toggle">'.

			self::makeCommandWrapper($firstName, $firstScore, $secondName, $secondScore).
			'</div>';
	}

	public static function makeFinalGame(string $gameHtml)
	{
		return "<div class=\"final-game\">

					<span class=\"final-label\">
						Final
					</span>".

			$gameHtml.

			"</div>";
	}

	public static function makeGameMenu()
	{
		return "<div class=\"game-menu\">
					<ul>

						<li onclick='openUpdateDialog(this)'>
							Изменить результат
						</li>

						<li onclick='openDeleteDialog(this)'>
							Удалить игру
						</li>

					</ul>
				</div>";
	}

	public static function makeCommandWrapper($team1Name, $team1Score, $team2Name, $team2Score)
	{
		return '<div class="command-wrapper">

					<div class="command first-team-name">'.$team1Name.": ".$team1Score.'</div>
					<hr>
					<div class="command second-team-name">'.$team2Name.": ".$team2Score.'</div>
				</div>';
	}

	public static function makeGameInfo($gameID, $firstID, $secondID)
	{
		return '<div class="game-info">

					<p class="gameID">'.$gameID.'</p>

					<p class="team1ID">'.$firstID.'</p>

					<p class="team2ID">'.$secondID.'</p>

				</div>';
	}

	public static function makeRoundLayer(int $round, array $gamesHtml, int $teamsNum)
	{
		$roundLayerHTML = '<div class="round-layer">';

		foreach ($gamesHtml as $gameHtml)
		{
			$roundLayerHTML .= $gameHtml;
		}

		$num = $teamsNum / (pow(2, $round));

		if (count($gamesHtml) < $num)
		{
			$start = count($gamesHtml);
			for ($i = $start; $i < $num; $i++)
			{
				$roundLayerHTML .= self::makeEmptyGame();
			}
		}

		$roundLayerHTML .= '</div>';

		return $roundLayerHTML;
	}

	public static function makeEmptyGame()
	{
		return '<div class="game">

				<input type="checkbox" id="game-toggle">

				<div class="game-menu">
					<ul>

						<li onclick="openAddGameDialog()">
							Добавить игру
						</li>

					</ul>
				</div>

				<div class="command-wrapper">

					<div class="command">
						Команда1 счет
					</div>
					<hr>
					<div class="command">
						Команда2 счет
					</div>

				</div>

			</div>';
	}

	public static function makeLeftSide(array $layers)
	{
		$res = '<div class="left-side">';

		foreach ($layers as $layer)
		{
			$res .= $layer;
		}

		$res .= '</div>';

		return $res;
	}

	public static function makeRightSide(array $layers)
	{
		if (count($layers) === 1)
		{
			$res = '<div class="right-side" style="display: unset">';
		}
		else
		{
			$res = '<div class="right-side">';
		}

		krsort($layers);

		foreach ($layers as $layer)
		{
			$res .= $layer;
		}

		$res .= '</div>';

		return $res;
	}

	public static function makeStats(array $stats)
	{
		$res = '<div class="stats-wrapper" id="stats-wrapper"><p class="stats-title">Статистика</p><table class="stats-table" id="stats-table">
					<tr>
						<td>Команда</td>
						<td>Сыгранные игры</td>
						<td>Общее количество очков</td>
						<td>Среднее количество очков</td>
						<td>Максимальное количество очков</td>
						<td>Место в турнире</td>
					</tr>';

		foreach ($stats as $row)
		{
			$res .= '<tr>
						<td>'.$row['Name'].'</td>
						<td>'.$row['GamesNum'].'</td>
						<td>'.$row['SumScore'].'</td>
						<td>'.$row['Average'].'</td>
						<td>'.$row['MaxScore'].'</td>
						<td>'.$row['Place'].'</td>
					</tr>';
		}

		$res .= '</table></div>';

		return $res;
	}

	public static function makeZeroRoundTeamsList($teamsNames)
	{
		$res = '<ul class="teams">';

		foreach ($teamsNames as $teamName)
		{
			$res .= '<li>'.$teamName.'</li>';
		}

		$res .= '</ul>';
		return $res;
	}

	public static function makeZeroRoundTeamsListGroupA($teamsNames)
	{
		return '<div class="group-a-teams" id="group-a-teams">'.self::makeZeroRoundTeamsList($teamsNames).'</div>';
	}

	public static function makeZeroRoundTeamsListGroupB($teamsNames)
	{
		return '<div class="group-b-teams" id="group-b-teams">'.self::makeZeroRoundTeamsList($teamsNames).'</div>';
	}

}