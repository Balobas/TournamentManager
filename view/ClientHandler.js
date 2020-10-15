const infoDelay = 3000;

function addTeamToList()
{
	let input = document.getElementById("team-input");
	let teamName = input.value;

	if (teamName !== '')
	{
		let list = document.getElementById("team-list");
		let item = document.createElement('li');
		item.innerText = teamName;
		list.prepend(item);
		input.value = "";
	}
}

function createTournament()
{

	let tournamentName = document.getElementById('tournament-name').value;

	if (tournamentName === '')
	{
		return;
	}

	let teamList = document.getElementById('team-list').childNodes;

	let list = [];

	for (i = 0; i < teamList.length; i++)
	{
		list[i] = teamList[i].textContent;
	}

	let request = new XMLHttpRequest();

	request.onreadystatechange = function()
	{
		if (request.readyState === 4 && request.status === 200)
		{
			let result = request.responseText;
			let cl = result.split(':');

			if (cl[0] === 'error-message')
			{
				errorInfo(cl[1]);
			}
			else
			{
				successInfo();
				let gridWrapper = document.getElementById('grid_wrapper');

				if (gridWrapper !== null)
				{
					gridWrapper.innerHTML = result;
					updateTournamentChoseDialog();
					let id = getCookie('currentTournamentID');

					if (id !== undefined)
					{
						addStats(id);
						addTeamsLists(id);
					}
					closeCreateDialog();
				}
			}
			setTimeout(closeStatusBox, infoDelay);
		}

	};

	request.open('POST', 'controller/RequestHandler.php', true);

	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	let data = 'data=' + JSON.stringify({
		"action" : 'createTournament',
		"name" : tournamentName,
		"teams" : list
	});

	request.send(data);

}

function updateTournamentChoseDialog()
{
	let request = new XMLHttpRequest();

	request.onreadystatechange = function()
	{
		if (request.readyState === 4 && request.status === 200)
		{
			let result = request.responseText;

			let dialog = document.getElementById('t-dialog');

			if (dialog !== null)
			{
				dialog.remove();
				document.body.innerHTML += result;
			}
		}
	};

	request.open('POST', 'controller/RequestHandler.php', true);

	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	let data = 'data=' + JSON.stringify({
		"action" : 'openChoseDialog',
	});

	request.send(data);
}

function openTournament(el)
{
	let p = el.querySelector('p');
	let id = p.innerText;
	openAndPostTournament(id);
	addTeamsLists(id);
	addStats(id);
}

function openAndPostTournament(id)
{
	let request = new XMLHttpRequest();

	request.onreadystatechange = function()
	{
		if (request.readyState === 4 && request.status === 200)
		{
			let result = request.responseText;
			let cl = result.split(':');

			if (cl[0] === 'error-message')
			{
				errorInfo(cl[1]);
			}
			else
			{
				successInfo();
				let gridWrapper = document.getElementById('grid_wrapper');

				if (gridWrapper !== null)
				{
					gridWrapper.innerHTML = result;
				}
			}

			setTimeout(closeStatusBox, infoDelay);
		}
	};

	request.open('POST', 'controller/RequestHandler.php', true);

	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	let data = 'data=' + JSON.stringify({
		"action" : 'open',
		"ID" : id
	});

	request.send(data);
}

function openCreateDialog()
{
	let dialog = document.getElementById('create-dialog');
	if (dialog !== null)
	{
		dialog.style.display = "block";
	}
	let menuToggle = document.getElementById('toggle');
	menuToggle.checked = !menuToggle.checked;
}

function closeCreateDialog()
{
	let dialog = document.getElementById('create-dialog');
	if (dialog !== null)
	{
		dialog.style.display = "none";
	}
}

function openChoseDialog()
{
	let choseDialog = document.getElementById('t-dialog');

	if (choseDialog !== null)
	{
		choseDialog.style.display = "block";
	}

	let menuToggle = document.getElementById('toggle');
	menuToggle.checked = !menuToggle.checked;
}

function closeChoseDialog()
{
	let choseDialog = document.getElementById('t-dialog');

	if (choseDialog !== null)
	{
		choseDialog.style.display = "none";
	}

}

function clearTeamList()
{
	let teamList = document.getElementById('team-list');

	if (teamList !== null)
	{
		teamList.innerHTML = "";
	}
}

function addGame()
{
	let firstID = document.getElementById('first-team-id').innerText;
	let secondID = document.getElementById('second-team-id').innerText;
	let firstName = document.getElementById('first-team-input-name').value;
	let secondName = document.getElementById('second-team-input-name').value;
	let firstScore = document.getElementById('first-team-input-score').value;
	let secondScore = document.getElementById('second-team-input-score').value;

	if (firstID === '' || secondID === '' || firstName === '' || secondName === '' || firstScore === '' || secondScore === '')
	{
		errorInfo('Ошибка');
		setTimeout(closeStatusBox, infoDelay);
		return;
	}

	let request = new XMLHttpRequest();

	request.onreadystatechange = function()
	{
		if (request.readyState === 4 && request.status === 200)
		{
			let result = request.responseText;
			let cl = result.split(':');

			if (cl[0] === 'error-message')
			{
				errorInfo(cl[1]);
			}
			else
			{
				if (result === 'false')
				{
					errorInfo('Нельзя добавить такую игру');
					setTimeout(closeStatusBox, infoDelay);
					return;
				}

				let id = getCookie('currentTournamentID');

				if (id !== undefined)
				{
					openAndPostTournament(id);
					openAddGameDialog();
					addStats(id);
				}
				else
				{
					errorInfo('Выберите турнир');
				}
			}

			setTimeout(closeStatusBox, infoDelay);
		}
	};

	request.open('POST', 'controller/RequestHandler.php', true);

	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	let data = 'data=' + JSON.stringify({
		"action" : 'addGame',
		"firstID" : firstID,
		"secondID" : secondID,
		"firstName" : firstName,
		"secondName" : secondName,
		"score1" : firstScore,
		"score2" : secondScore
	});

	request.send(data);
}

function openAddGameDialog()
{
	let dialog = document.getElementById('add-game-dialog');

	if (dialog)
	{
		dialog.style.display = "inline-block";
	}

	let request = new XMLHttpRequest();

	request.onreadystatechange = function()
	{
		if (request.readyState === 4 && request.status === 200)
		{
			let result = request.responseText;
			let cl = result.split(':');

			if (cl[0] === 'error-message')
			{
				errorInfo(cl[1]);
				setTimeout(closeStatusBox, infoDelay);
			}
			else
			{
				let teamList = document.getElementById('add-team-list');

				if (teamList !== null)
				{
					teamList.innerHTML = result;
				}
			}
		}
	};

	request.open('POST', 'controller/RequestHandler.php', true);

	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	let data = 'data=' + JSON.stringify({
		"action" : 'addGameTeamsLists',
	});

	request.send(data);

}

function closeAddGameDialog()
{
	let dialog = document.getElementById('add-game-dialog');

	if (dialog !== null)
	{
		dialog.style.display = "none";
	}

}

function takeGameFromFirstList(el)
{
	let name = el.innerText;
	let nameInput = document.getElementById('first-team-input-name');
	let p = el.querySelector('p');
	let idInput = document.getElementById('first-team-id');

	if (nameInput !== null && p !== null && idInput !== null)
	{
		nameInput.value = name;
		idInput.innerText = p.innerText;
	}

}

function takeGameFromSecondList(el)
{
	let name = el.innerText;
	let nameInput = document.getElementById('second-team-input-name');
	let p = el.querySelector('p');
	let idInput = document.getElementById('second-team-id');

	if (nameInput !== null && p !== null && idInput !== null)
	{
		nameInput.value = name;
		idInput.innerText = p.innerText;
	}

}

function getCookie(name)
{
	let matches = document.cookie.match(new RegExp(
		"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	));
	return matches ? decodeURIComponent(matches[1]) : undefined;
}

function updateGame()
{
	let gameIDP = document.getElementById('update-game-id');
	let firstTeamNameSpan = document.getElementById('update-dialog-first-name');
	let secondTeamNameSpan = document.getElementById('update-dialog-second-name');

	if (gameIDP === null || firstTeamNameSpan ===  null || secondTeamNameSpan === null)
	{
		errorInfo('Ошибка');
		setTimeout(closeStatusBox, infoDelay);
		return;
	}

	let firstTeamName = firstTeamNameSpan.innerText;
	let secondTeamName = secondTeamNameSpan.innerText;
	let gameID = gameIDP.innerText;

	let firstIDP = firstTeamNameSpan.querySelector('p');
	if (firstIDP !== null)
	{
		var firstTeamID = firstIDP.innerText;
	}

	let secondIDP = secondTeamNameSpan.querySelector('p');
	if (secondIDP !== null)
	{
		var secondTeamID = secondIDP.innerText;
	}

	let firstScore = document.getElementById('update-first-score').value;
	let secondScore = document.getElementById('update-second-score').value;

	if (firstTeamName === '' || secondTeamName === '' || gameID === '' || firstTeamID === '' || secondTeamID === '' || firstScore === '' || secondScore === '') {
		errorInfo('Ошибка');
		setTimeout(closeStatusBox, infoDelay);
		return;
	}

	let request = new XMLHttpRequest();

	request.onreadystatechange = function()
	{
		if (request.readyState === 4 && request.status === 200)
		{
			let result = request.responseText;
			let cl = result.split(':');

			if (cl[0] === 'error-message')
			{
				errorInfo(cl[1]);
				setTimeout(closeStatusBox, infoDelay);
			}
			else
			{
				let id = getCookie('currentTournamentID');

				if (id !== undefined)
				{
					openAndPostTournament(id);
					addStats(id);
				}
				else{
					errorInfo('Выберите турнир');
					setTimeout(closeStatusBox, infoDelay);
				}

				closeUpdateDialog();
			}
		}
	};

	request.open('POST', 'controller/RequestHandler.php', true);

	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	let data = 'data=' + JSON.stringify({
		"action" : 'updateGame',
		"gameID" : gameID,
		"firstID" : firstTeamID,
		"secondID" : secondTeamID,
		"firstName" : firstTeamName,
		"secondName" : secondTeamName,
		"score1" : firstScore,
		"score2" : secondScore
	});

	request.send(data);

}

function openUpdateDialog(el)
{
	let game = el.closest('.game');

	if (game !== null)
	{

		let info = game.querySelector('.game-info');
		let commandWrapper = game.querySelector('.command-wrapper');

		if (info !== null && commandWrapper !== null)
		{
			let gameID = info.querySelector('.gameID').innerText;
			let firstTeamID = info.querySelector('.team1ID').innerText;
			let secondTeamID = info.querySelector('.team2ID').innerText;

			let firstTeam = commandWrapper.querySelector('.first-team-name').innerText;
			let secondTeam = commandWrapper.querySelector('.second-team-name').innerText
			let firstScore = firstTeam.split(':')[1];
			let secondScore = secondTeam.split(':')[1];
			let firstTeamName = firstTeam.split(':')[0];
			let secondTeamName = secondTeam.split(':')[0];

			let dialog = document.getElementById('update-game-dialog');

			if (dialog !== null)
			{
				let firstTeamNameSpan = document.getElementById('update-dialog-first-name');

				if (firstTeamNameSpan !== null)
				{
					firstTeamNameSpan.innerHTML = firstTeamName + '<p class="hidden"></p>';
				}

				let firstIDP = firstTeamNameSpan.querySelector('p');

				if (firstIDP !== null)
				{
					firstIDP.innerText = firstTeamID;
				}

				let secondTeamNameSpan = document.getElementById('update-dialog-second-name');

				if (secondTeamNameSpan !== null)
				{
					secondTeamNameSpan.innerHTML = secondTeamName + '<p class="hidden"></p>';
				}

				let secondIDP = secondTeamNameSpan.querySelector('p');

				if (secondIDP !== null)
				{
					secondIDP.innerText = secondTeamID;
				}

				let gameIDP = document.getElementById('update-game-id');

				if (gameIDP !== null)
				{
					gameIDP.innerText = gameID;
				}

				let firstInput = document.getElementById('update-first-score');

				if (firstInput !== null)
				{
					firstInput.value = firstScore;
				}

				let secondInput = document.getElementById('update-second-score')

				if (secondInput !== null)
				{
					secondInput.value = secondScore;
				}

				dialog.style.display = "block";
				return;
			}
		}

		errorInfo('Ошибка');
		setTimeout(closeStatusBox, infoDelay);
	}
}

function closeUpdateDialog()
{
	let dialog = document.getElementById('update-game-dialog');

	if (dialog !== null)
	{
		dialog.style.display = "none";
		let firstScoreInput = document.getElementById('update-first-score');
		firstScoreInput.value = '';

		let secondScoreInput = document.getElementById('update-second-score');
		secondScoreInput.value = '';
	}
}

function closeStatusBox()
{
	let box = document.getElementById('status');

	if (box !== null)
	{
		box.style.display = '';
		box.innerText = '';
	}
}

function errorInfo(error)
{
	let box = document.getElementById('status');

	if (box !== null)
	{
		box.style.borderColor = 'red';
		box.style.textEmphasisColor = 'red';
		box.innerText = error;
		box.style.display = 'block';
	}
}

function successInfo()
{
	let box = document.getElementById('status');

	if (box !== null)
	{
		box.style.borderColor = 'green';
		box.style.textEmphasisColor = 'red';
		box.innerText = 'Операция успешно выполнена';
		box.style.display = 'block';
	}
}

function openDeleteDialog(el)
{

	let game = el.closest('.game');

	if (game !== null)
	{
		let info = game.querySelector('.game-info');
		let commandWrapper = game.querySelector('.command-wrapper');

		if (info !== null && commandWrapper !== null)
		{
			let gameID = info.querySelector('.gameID').innerText;

			let dialog = document.getElementById('delete-game-dialog');

			if (dialog !== null)
			{
				let gameIDP = document.getElementById('delete-game-id');

				if (gameIDP !== null)
				{
					gameIDP.innerText = gameID;
				}

				dialog.style.display = "block";
			}
		}
	}
}

function closeDeleteDialog()
{
	let dialog = document.getElementById('delete-game-dialog');

	if (dialog !== null)
	{
		dialog.style.display = "none";
	}
}

function deleteGame()
{
	let gameID = document.getElementById('delete-game-id').innerText;

	if (gameID !== '')
	{
		let request = new XMLHttpRequest();

		request.onreadystatechange = function()
		{
			if (request.readyState === 4 && request.status === 200)
			{
				let result = request.responseText;
				let cl = result.split(':');

				if (cl[0] === 'error-message')
				{
					errorInfo(cl[1]);
				}
				else
				{
					if (result === 'false')
					{
						errorInfo('Нельзя удалить эту игру');
						setTimeout(closeStatusBox, infoDelay);
						return;
					}

					successInfo();
					let id = getCookie('currentTournamentID');

					if (id !== undefined)
					{
						openAndPostTournament(id);
						addStats(id);
						closeDeleteDialog();
					}
					else
					{
						errorInfo('Выберите турнир');
					}
				}

				setTimeout(closeStatusBox, infoDelay);
				closeDeleteDialog();
			}
		};

		request.open('POST', 'controller/RequestHandler.php', true);

		request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		let data = 'data=' + JSON.stringify({
			"action" : 'deleteGame',
			"gameID" : gameID
		});

		request.send(data);
	}
}

function addStats(id)
{
	let request = new XMLHttpRequest();

	request.onreadystatechange = function()
	{
		if (request.readyState === 4 && request.status === 200)
		{
			let result = request.responseText;

			let stats = document.getElementById('stats-wrapper');

			if (stats !== null)
			{
				stats.remove();
			}

			document.body.innerHTML += result;
		}
	};

	request.open('POST', 'controller/RequestHandler.php', true);

	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	let data = 'data=' + JSON.stringify({
		"action" : 'getStats',
		"tournamentID" : id
	});

	request.send(data);
}

function addTeamsLists(id)
{
	let request = new XMLHttpRequest();

	request.onreadystatechange = function()
	{
		if (request.readyState === 4 && request.status === 200)
		{
			let result = request.responseText;
			let cl = result.split(':');

			if (cl[0] === 'error-message')
			{
				errorInfo(cl[1]);
				setTimeout(closeStatusBox, infoDelay);
			}
			else
			{
				let leftTeams = document.getElementById('group-a-teams');
				let rightTeams = document.getElementById('group-b-teams');

				if (leftTeams !== null)
				{
					leftTeams.remove();
				}

				if (rightTeams !== null)
				{
					rightTeams.remove();
				}

				document.body.innerHTML += result;
			}
		}
	};

	request.open('POST', 'controller/RequestHandler.php', true);

	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	let data = 'data=' + JSON.stringify({
		"action" : 'getZeroRoundTeamsLists',
		"tournamentID" : id,
	});

	request.send(data);
}

function clickOnLeftTeamList()
{
	let list = document.getElementById('group-a-teams');

	if (list !== null)
	{
		if (list.style.display === "block")
		{
			list.style.display = "none";
		}
		else
		{
			list.style.display = "block";
		}
	}
}

function clickOnRightTeamList()
{
	let list = document.getElementById('group-b-teams');

	if (list !== null)
	{
		if (list.style.display === "block")
		{
			list.style.display = "none";
		}
		else
		{
			list.style.display = "block";
		}
	}
}