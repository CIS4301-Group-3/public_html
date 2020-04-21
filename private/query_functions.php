<?php

  //*********These are example queries from the php/mysql course I took.*****//
  //*********I like the idea of putting all queries in one file**************//

  function donations_over_time_usa($candidate, $start_date, $end_date) {
    global $db;
    
    $sql = "SELECT DG5.DONATION.DAY, ";
    $sql .= "SUM(DG5.DONATION.AMOUNT) AS Total_Donations ";
    $sql .= "FROM DG5.DONATION ";
    $sql .= "JOIN ELEHMANN.COMMITTEE ON ELEHMANN.COMMITTEE.COMMITTEE_ID = DG5.DONATION.COMMITTEEID ";
    $sql .= "WHERE ELEHMANN.COMMITTEE.CANDIDATE = :candidate_bv AND ";
    $sql .= "DG5.DONATION.DAY >= :start_date_bv AND DG5.DONATION.DAY <= :end_date_bv ";
    $sql .= "GROUP BY DG5.DONATION.DAY, ";
    $sql .= "ELEHMANN.COMMITTEE.CANDIDATE ";
    $sql .= "ORDER BY DG5.DONATION.DAY ASC";
    //echo $sql;
    $query = oci_parse($db, $sql);
    oci_bind_by_name($query, ":candidate_bv", $candidate);
    oci_bind_by_name($query, ":start_date_bv", $start_date);
    oci_bind_by_name($query, ":end_date_bv", $end_date);
    oci_execute($query);
    confirm_result_set($query);
    return $query;
  }

  function candidate_photo($candidate) {
    global $db;
    
    $sql = "SELECT IMAGE ";
    $sql .= "FROM ELEHMANN.CAMPAIGN ";
    $sql .= "WHERE ELEHMANN.CAMPAIGN.CANDIDATE = :candidate_bv";
    //echo $sql;
    $query = oci_parse($db, $sql);
    oci_bind_by_name($query, ":candidate_bv", $candidate);
    oci_execute($query);
    confirm_result_set($query);
    $row = oci_fetch_array($query, OCI_ASSOC);
    if(!$row){
      echo "Error!";
    }
    else{
      $imageData = $row['IMAGE']->load();
      //header("Content-type: image/jpeg");
      //print $imageData;
      //print('<img src="data:image/jpeg;base64,'.base64_encode($imageData).'" />');
      return $imageData;
    }
    //return $query;
  }

  function list_candidates() {
    global $db;
    
    //$sql = "SELECT * FROM aukee.employer ORDER BY FORTUNE_RANK ASC";
    $sql = "SELECT ELEHMANN.CAMPAIGN.CANDIDATE ";
    $sql .= "FROM ELEHMANN.CAMPAIGN ";
    $sql .= "ORDER BY ELEHMANN.CAMPAIGN.CANDIDATE ASC";
    //echo $sql;
    $query = oci_parse($db, $sql);
    oci_execute($query);
    confirm_result_set($query);
    return $query;
  }

  function get_cities($state) {
    global $db;

    $sql = "SELECT ELEHMANN.CITY.CITY FROM ELEHMANN.CITY ";
    $sql .= "WHERE ELEHMANN.CITY.STATE = :state_bv";
    //echo $sql;
    $query = oci_parse($db, $sql);
    oci_bind_by_name($query, ":state_bv", $state);
    oci_execute($query);
    confirm_result_set($query);
    return $query;
  }

  function validate_subject($subject) {

    $errors = [];
    
    // menu_name
    if(is_blank($subject['menu_name'])) {
      $errors[] = "Name cannot be blank.";
    } else if(!has_length($subject['menu_name'], ['min' => 2, 'max' => 255])) {
      $errors[] = "Name must be between 2 and 255 characters.";
    }
  
    // position
    // Make sure we are working with an integer
    $postion_int = (int) $subject['position'];
    if($postion_int <= 0) {
      $errors[] = "Position must be greater than zero.";
    }
    if($postion_int > 999) {
      $errors[] = "Position must be less than 999.";
    }
  
    // visible
    // Make sure we are working with a string
    $visible_str = (string) $subject['visible'];
    if(!has_inclusion_of($visible_str, ["0","1"])) {
      $errors[] = "Visible must be true or false.";
    }
  
    return $errors;
  }
  
  function insert_subject($subject) {
    global $db;

    $errors = validate_subject($subject);
    if(!empty($errors)) {
      return $errors;
    }

    $sql = "INSERT INTO subjects ";
    $sql .= "(menu_name, position, visible) ";
    $sql .= "VALUES (";
    $sql .= "'" . db_escape($db, $subject['menu_name']) . "',";
    $sql .= "'" . db_escape($db, $subject['position']) . "',";
    $sql .= "'" . db_escape($db, $subject['visible']) . "'";
    $sql .= ")";
    $result = mysqli_query($db, $sql);
    // For INSERT statements, $result is true/false
    if($result) {
      return true;
    } else {
      // INSERT failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }

  }
  
?>
