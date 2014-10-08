<?php
class DB extends mysqli {
  private $inTrans = false;
  
  function __construct($db) {
    parent::__construct('localhost', 'root', 'mysql5', $db);
    if (mysqli_connect_error()) {
      die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }
    
    $this->autocommit(true);
  }
  
  function __destruct() {
    if ($this->inTrans) $this->rollback();
    $this->close();
  }
  
  function startTransaction() {
    if (!$this->inTrans) {
      $this->autocommit(false);
      $this->inTrans = true;
    }
  }
  
  function commitTransaction() {
    if ($this->inTrans) {
      $this->commit();
      $this->autocommit(true);
      $this->inTrans = false;
    }
  }
  
  function rollbackTransaction() {
    if ($this->inTrans) {
      $this->rollback();
      $this->autocommit(true);
      $this->inTrans = false;
    }
  }
  
  function doSQL($query) {
    $return = FALSE;
    $result = $this->query($query);
    if (gettype($result) == 'object') {
      $return = array();
      while ($row = $result->fetch_assoc()) {
        $return[] = $row;
      }
      if (sizeof($return) == 0) $return = FALSE;
    } elseif ($result === TRUE) {
      if ($this->insert_id !== 0) {
        $return = $this->insert_id;
      } elseif ($this->affected_rows > -1) {
        $return = $this->affected_rows;
      }
    }
    return $return;
  }
  
  function select($table, $where='', $order='', $to='', $from='', $cols=array('*')) {
    $return = FALSE;
    $cols = implode(', ', $cols);
    $sql = "SELECT $cols FROM $table";
    if ($where != '') $sql .= " WHERE $where";
    if ($order != '') $sql .= " ORDER BY $order";
    if ($to != '') {
      if ($from != '') {
        $sql .= " LIMIT $from, $to";
      }
      else {
        $sql .= " LIMIT $to";
      }
    }
    $return = $this->doSQL($sql);
    return $return;
  }
  
  function insert($table, $data=array()) {
    $cols = array();
    $values = array();
    foreach ($data as $col=>$value) {
      if ($value !== '' && $value !== NULL) {
        $cols[] = $col;
        $values[] = $this->dbClean($value);
      }
    }
    $cols = implode('`, `', $cols);
    $values = implode("', '", $values);
    $sql = "INSERT INTO $table (`$cols`) VALUES ('$values')";
    $return = $this->doSQL($sql);
    return $return;
  }
  
  function update($table, $data=array(), $where='') {
    $cols = array_keys($data);
    $values = array_values($data);
    for ($i=0; $i<sizeof($data); $i++) {
      if ($values[$i] === '') {
        $values[$i] = 'NULL';
      } else {
        $values[$i] = '`' . $cols[$i] . "` = '" . $this->real_escape_string($values[$i]) . "'";
      }
    }
    $values = implode(', ', $values);
    $sql = "UPDATE $table SET $values";
    if ($where != '') $sql .= " WHERE $where";
    $return = $this->doSQL($sql);
    return $return;
  }
  
  function delete($table, $where='', $limit='', $order='', $deltabs='') {
    $sql = 'DELETE ';
    if ($deltabs != '') $sql .= $deltabs . ' ';
    $sql .= "FROM $table";
    if ($where != '') $sql .= " WHERE $where";
    if ($order != '') $sql .= " ORDER BY $order";
    if ($limit != '') {
      $sql .= " LIMIT $limit";
    }
    $return = $this->doSQL($sql);
    return $return;
  }
  
  public function dbClean($value) {
    return $this->real_escape_string($value);
  }
}
?>