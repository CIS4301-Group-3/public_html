#!/usr/local/bin/php

<?php require_once('../private/initialize.php'); ?>

<?php $page_title = 'Individual Stats'; ?>
<?php include(SHARED_PATH . '/header.php'); ?>

<div id="content">
  <div id="main-menu">
    <h2>Individual Candidate Statistics</h2>
  </div>
</div>
<?php
  $query = oci_parse($db, 'SELECT * FROM aukee.employer');
  oci_execute($query);
  echo "<div><br>
          <hr><table>\n";
  while ($row = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS)){
          echo "<tr>\n";
          foreach ($row as $item){
                  echo "<td>" . ($item !== NULL ? htmlentities($item, ENT_QUOTES) : "&nbsp") . "</td>\n";
          }
          echo "</tr>\n";
  }
  echo "</table>\n";
  oci_free_statement($query);
?>
<?php include(SHARED_PATH . '/footer.php'); ?>
