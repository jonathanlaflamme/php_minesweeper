<?php

  namespace App\Game;

  include 'cell.php';

  /**
   * This Class handle the logical part of a game board, here a Table. It
   * handle the reveal of cells and can give information about the table State
   */
  class Table
  {
    protected $nb_rows;
    protected $nb_columns;
    protected $nb_mines;
    protected $nb_case_without_mine_to_reveal;
    protected $cells;

    const NEIGHBOORS = [
      [-1, -1], [-1, 0], [-1, 1],
      [0, -1],           [0, 1],
      [1, -1], [1, 0], [1, 1]
    ];

    /**
     * contructor
     *
     * It's for now an hardcoded 5x5 w/ 5 mines, but certainly it could be dynamic;
     */
    function __construct() {
      $this->nb_rows = 5;
      $this->nb_columns = 5;
      $this->nb_mines = 5;

      $this->nb_case_without_mine_to_reveal = ($this->nb_rows * $this->nb_columns) - $this->nb_mines;
      $this->cells = [];

      $this->initTable();
    }

    /**
     * Method responsable of initating a game Table. It randomize
     * mines creaction and placement based on tables properties.
     */
    protected function initTable() {
      // Let's fill the $cells table;
      for($row = 1; $row <= $this->nb_rows; $row++) {
        for($col = 1; $col <= $this->nb_columns; $col++) {
          $this->cells[$row][$col] = new Cell();
        }
      }

      // Lets randomize some mines;
      $nb_mines = 0;
      $position_mine = [];
      while ($nb_mines < $this->nb_mines) {
        $temp_x = rand(1, $this->nb_rows);
        $temp_y = rand(1, $this->nb_columns);

        if(!isset($position_mine[$temp_x][$temp_y])) {
          $position_mine[$temp_x][$temp_y] = true;
          $nb_mines += 1;
        }
      }

      // Assing thoses random mines to actual cells
      foreach($position_mine as $row => $columns) {
        foreach($columns as $col => $cell) {
          $this->cells[$row][$col]->addMine();
        }
      }


      // Put the number of mines nearby mines
      foreach ($this->cells as $x => $columns) {
        foreach($columns as $y => $cell) {
          $list_neighboors = $this->getNeighboors($x, $y);

          foreach ($list_neighboors as $neighboor) {
            if($this->cells[$neighboor[0]][$neighboor[1]]->containMine()) {
              $cell->addAsideMine();
            }
          }
        }
      }

    }

    /**
     * Method responsible of getting the coords of all neighboors of the
     * cell specified by coords in param
     *
     * @param int $row_x  the x coordinate
     * @param int $col_y  the y coordinate
     *
     * @return array An array of coords
     */
    protected function getNeighboors($row_x, $col_y) {

      $list_of_neighboor_cooridnates = [];

      foreach(Table::NEIGHBOORS as $neighboor) {
        $coordinates = [];
        $coordinates[0] = $neighboor[0] + $row_x;
        $coordinates[1] = $neighboor[1] + $col_y;

        if($this->validateCoords($coordinates[0], $coordinates[1])) {
          array_push($list_of_neighboor_cooridnates, $coordinates);
        }
      }

      return $list_of_neighboor_cooridnates;
    }

    /**
     * Method that validate if the coord is valid
     *
     * @param int $row_x  the x coordinate
     * @param int $col_y  the y coordinate
     *
     * @return bool It is valid
     */
    public function validateCoords($coord_x, $coord_y) {
      if(!isset($this->cells[$coord_x][$coord_y])) {
        return false;
      }

      return true;
    }

    /**
     * Method that validate if the coord is valid and is to reveal
     *
     * @param int $row_x  the x coordinate
     * @param int $col_y  the y coordinate
     *
     * @return bool It is valid
     */
    public function validateCoordsToReveal($coord_x, $coord_y) {
      if(isset($this->cells[$coord_x][$coord_y]) && $this->cells[$coord_x][$coord_y]->isToReveal()) {
        return true;
      }

      return false;
    }

    /**
     * Method that print the solution
     *
     * @return string the solution
     */
    public function printSolution() {
      $string = "\n";

      foreach(range(1, $this->nb_rows) as $row_x) {
        foreach(range(1, $this->nb_columns) as $col_y) {
          $cell = $this->cells[$row_x][$col_y];

          if($cell->containMine()) {
            $string .= "M\t";
          } else {
            $string .= (strval($cell->getNbMineAside()) . "\t");
          }
        }

        $string .= "\n";
      }

      print $string;
    }

    /**
     * Method that return the current state of the table
     *
     * @return string the state
     */
    public function printTable() {
      $string = "\n";

      foreach(range(1, $this->nb_rows) as $row_x) {
        foreach(range(1, $this->nb_columns) as $col_y) {
          $cell = $this->cells[$row_x][$col_y];

          if ($cell->isToReveal()) {
            $string .= "_\t";
          } elseif ($cell->containMine()) {
            $string .= "M\t";
          } else {
            $string .= (strval($cell->getNbMineAside()) . "\t");
          }
        }

        $string .= "\n";
      }

      print $string;
    }

    /**
     * Method responsible to say if there is still mine to reveal
     *
     * @return bool true if it still contain unrevealed mines
     */
    public function containMineToReveal() {
      if($this->nb_case_without_mine_to_reveal > 0) {
        return true;
      }

      return false;
    }

    /**
     * This method reveal a case after user decide to. As Minesweeper
     * if the cas doesn't contain mine and isn't next to a mine it spread the
     * reveal in every direction until it reach a cell next to one.
     *
     * In code it translate by a recursive method!
     */
    public function revealCell($row_x, $col_y) {
      $list_of_neighboor_cooridnates = $this->getNeighboors($row_x, $col_y);

      if($this->validateCoords($row_x, $col_y) && $this->validateCoordsToReveal($row_x, $col_y)) {
        $this->cells[$row_x][$col_y]->reveal();
        $this->nb_case_without_mine_to_reveal -= 1;

        if(!$this->cells[$row_x][$col_y]->isNextToAMine()) {
          foreach ($list_of_neighboor_cooridnates as $neighboor) {
            $this->revealCell($neighboor[0], $neighboor[1]);
          }
        }
      }
    }

    /**
     * Method responsible to say if the cell to coordinate contain a mine
     *
     * @param int $row_x  x coordinate
     * @param int $col_y  y coordinate
     *
     * @return bool true if contain a mine
     */
    public function cellContainMine($row_x, $col_y) {
      if($this->cells[$row_x][$col_y]->containMine()) {
        return true;
      }

      return false;
    }
  }