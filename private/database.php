<?php

  require_once('db_credentials.php');

  function db_connect() {
    $oracle = gethostbyname('oracle.cise.ufl.edu');
    $dbstr = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$oracle.")(PORT = 1521)))(CONNECT_DATA=(SID=orcl)))";
    $connection = oci_connect(DB_USER, DB_PASS, $dbstr);
    confirm_db_connect($connection);
    return $connection;
  }

  function db_disconnect($connection) {
    if(isset($connection)) {
      oci_close($connection);
    }
  }

  function db_escape($connection, $string) {
    return mysqli_real_escape_string($connection, $string);
  }
  
  function confirm_db_connect($conn) {
    if (!$conn) {
      $e = oci_error();   // For oci_connect errors pass no handle
      echo htmlentities($e['message']);
      exit($msg);
    }
  }

  function confirm_result_set($result_set) {
    if (!$result_set) {
      exit("Database query failed.");
    }
  }
?>