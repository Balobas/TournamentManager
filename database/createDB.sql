create table Tournament
(
    ID int not null auto_increment,
    NAME varchar(50),
    Primary Key (ID)
);

create table Team
(
    ID int not null auto_increment,
    NAME varchar(50),
    PRIMARY KEY (ID)
);

create table Game
(
    ID int not null auto_increment,
    TEAM1ID int not null,
    TEAM2ID int not null,
    TEAM1SCORE int not null,
    TEAM2SCORE int not null,
    ROUND int not null,
    TOURNAMENT_ID int not null,
    PRIMARY KEY (ID)
);

create table TournamentTeams
(
    TOURNAMENT_ID int not null,
    TEAM_ID int not null
);
