<?php

class Team
{
	private $name;
	private $id;

	public function __construct(string $teamName, int $id)
	{
		$this->name = $teamName;
		$this->id = $id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getID(): int
	{
		return $this->id;
	}

}
