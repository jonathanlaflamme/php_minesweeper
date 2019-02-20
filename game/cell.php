<?php
  namespace App\Game;

  /**
   * Cell Class
   */
  class Cell
  {
    /** @var bool is the cell is a mine */
    protected $is_mine;

    /** @var bool is the cell revealed */
    protected $is_seen;

    /** @var int number of mines aside this cell */
    protected $nb_mine_aside;

    function __construct() {
      $this->is_mine = false;
      $this->is_seen = false;
      $this->nb_mine_aside = 0;
    }

    /**
     * Method responsible to reveal the cell
     */
    public function reveal() {
      $this->is_seen = true;
    }

    /**
     * Method responsible to turn the cell into a mine
     */
    public function addMine() {
      $this->is_mine = true;
    }

    /**
     * Method responsible to say if this cell contains a mine
     *
     * @return bool true if mine
     */
    public function containMine() {
      if($this->is_mine) {
        return true;
      }

      return false;
    }

    /**
     * Method responsible to say if the cell is to reveal or not
     *
     * @return bool true if revealed
     */
    public function isToReveal() {
      if($this->is_seen) {
        return false;
      }

      return true;
    }

    /**
     * Method responsible to append the number of mine aside
     */
    public function addAsideMine() {
      $this->nb_mine_aside += 1;
    }

    /**
     * Method responsible to return the number of mine aside
     *
     * @return int number of mine aside
     */
    public function getNbMineAside() {
      return $this->nb_mine_aside;
    }

    /**
     * Method responsible to say if the cell is next to a mine
     *
     * @return bool true if next to a mine;
     */
    public function isNextToAMine() {
      if($this->nb_mine_aside == 0) {
        return false;
      }

      return true;
    }
  }
?>