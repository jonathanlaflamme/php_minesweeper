<?php

  namespace App\Game;

  include 'table.php';

  /**
   * Game class handle interaction with user and game in general,
   */
  class Game
  {
    /** @var Table the table of the game */
    protected $table;

    function __construct() {
      $this->table = new Table();
    }

    /**
     * The play method to be called to start the game. The
     * game is CLI based;
     */
    public function play() {
      $end = false;

      while(!$end) {
        $this->table->printTable();

        try {
          list($x, $y) = $this->askCoordinates();
        } catch(Exception $e) {
          print($e);
        } finally {

          if($this->table->cellContainMine($x, $y)) {
            print("Oups! It's a mine, game over !");
            $this->table->printSolution();
            $end = true;
          }

          if(!$this->table->validateCoordsToReveal($x, $y)) {
            print("This coordinate is already reveal");
          } else {
            $this->table->revealCell($x, $y);
          }

          if(!$this->table->containMineToReveal()) {
            print("\nYou WON!!");
            $this->table->printSolution();
            $end = true;
          }
        }
      }
    }

    /**
     * Method handle the interaction to the user, and validate thems
     *
     * @return int[] coordinates
     */
    private function askCoordinates() {
      $x = intval(readline("Row (Valid between 1 and 5):"));
      $y = intval(readline("Column (Valid between 1 and 5):"));

      while(!$this->table->validateCoords($x, $y)) {
        print("Invalid value!\n");
        $x = intval(readline("Row (Valid between 1 and 5):"));
        $y = intval(readline("Column (Valid between 1 and 5):"));
      }

      return [$x, $y];
    }
  }