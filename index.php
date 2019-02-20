<?php

  namespace App;

  require './game/game.php';

  use App\Game\Game;

  $game = new Game();

  $game->play();