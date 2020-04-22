<?php

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

  function donations_over_time_state($candidate, $state, $start_date, $end_date) {
    global $db;

    $sql = "SELECT DG5.DONATION.DAY, ";
    $sql .= "SUM(DG5.DONATION.AMOUNT) AS Total_Donations ";
    $sql .= "FROM DG5.DONATION ";
    $sql .= "JOIN ELEHMANN.COMMITTEE ON ELEHMANN.COMMITTEE.COMMITTEE_ID = DG5.DONATION.COMMITTEEID ";
    $sql .= "WHERE ELEHMANN.COMMITTEE.CANDIDATE = :candidate_bv AND DG5.DONATION.STATE = :state_bv AND ";
    $sql .= "DG5.DONATION.DAY >= :start_date_bv AND DG5.DONATION.DAY <= :end_date_bv ";
    $sql .= "GROUP BY DG5.DONATION.DAY, ";
    $sql .= "ELEHMANN.COMMITTEE.CANDIDATE ";
    $sql .= "ORDER BY DG5.DONATION.DAY ASC";
    //echo $sql;
    $query = oci_parse($db, $sql);
    oci_bind_by_name($query, ":candidate_bv", $candidate);
    oci_bind_by_name($query, ":state_bv", $state);
    oci_bind_by_name($query, ":start_date_bv", $start_date);
    oci_bind_by_name($query, ":end_date_bv", $end_date);
    oci_execute($query);
    confirm_result_set($query);
    return $query;
  }

  function donations_over_time_city($candidate, $state, $city, $start_date, $end_date) {
    global $db;

    $sql = "SELECT DG5.DONATION.DAY, ";
    $sql .= "SUM(DG5.DONATION.AMOUNT) AS Total_Donations ";
    $sql .= "FROM DG5.DONATION ";
    $sql .= "JOIN ELEHMANN.COMMITTEE ON ELEHMANN.COMMITTEE.COMMITTEE_ID = DG5.DONATION.COMMITTEEID ";
    $sql .= "WHERE ELEHMANN.COMMITTEE.CANDIDATE = :candidate_bv AND DG5.DONATION.STATE = :state_bv AND DG5.DONATION.CITY = :city_bv AND ";
    $sql .= "DG5.DONATION.DAY >= :start_date_bv AND DG5.DONATION.DAY <= :end_date_bv ";
    $sql .= "GROUP BY DG5.DONATION.DAY, ";
    $sql .= "ELEHMANN.COMMITTEE.CANDIDATE ";
    $sql .= "ORDER BY DG5.DONATION.DAY ASC";
    //echo $sql;
    $query = oci_parse($db, $sql);
    oci_bind_by_name($query, ":candidate_bv", $candidate);
    oci_bind_by_name($query, ":state_bv", $state);
    oci_bind_by_name($query, ":city_bv", strtoupper($city));
    oci_bind_by_name($query, ":start_date_bv", $start_date);
    oci_bind_by_name($query, ":end_date_bv", $end_date);
    oci_execute($query);
    confirm_result_set($query);
    return $query;
  }

  function donation_data_us($start_date, $end_date) {
    global $db;
    
    $sql = "SELECT ELEHMANN.COMMITTEE.CANDIDATE, ";
    $sql .= "SUM(DG5.DONATION.AMOUNT) AS TOTAL_DONATIONS, ";
    $sql .= "COUNT(DG5.DONATION.AMOUNT) AS NUM_DONATIONS, ";
    $sql .= "ROUND(AVG(DG5.DONATION.AMOUNT), 2) AS DONATION_SIZE, ";
    $sql .= "ROUND((SUM(DG5.DONATION.AMOUNT) / (SELECT SUM(ELEHMANN.STATE.POPULATION) FROM ELEHMANN.STATE)), 3) AS DONATIONS_PER_CAPITA ";
    $sql .= "FROM DG5.DONATION ";
    $sql .= "JOIN ELEHMANN.COMMITTEE ON ELEHMANN.COMMITTEE.COMMITTEE_ID = DG5.DONATION.COMMITTEEID ";
    $sql .= "WHERE DG5.DONATION.DAY >= :start_date_bv AND DG5.DONATION.DAY <= :end_date_bv ";
    $sql .= "GROUP BY ELEHMANN.COMMITTEE.CANDIDATE ";
    $sql .= "ORDER BY ELEHMANN.COMMITTEE.CANDIDATE ASC";
    //echo $sql;
    $query = oci_parse($db, $sql);
    oci_bind_by_name($query, ":start_date_bv", $start_date);
    oci_bind_by_name($query, ":end_date_bv", $end_date);
    oci_execute($query);
    confirm_result_set($query);
    return $query;
  }

  function donations_by_state($candidate, $start_date, $end_date) {
    global $db;
    
    $sql = "SELECT ELEHMANN.STATE.DISPLAYNAME, ";
    $sql .= "SUM(DG5.DONATION.AMOUNT) AS TOTAL_DONATIONS ";
    $sql .= "FROM (DG5.DONATION ";
    $sql .= "JOIN ELEHMANN.COMMITTEE ON ELEHMANN.COMMITTEE.COMMITTEE_ID = DG5.DONATION.COMMITTEEID) ";
    $sql .= "JOIN ELEHMANN.STATE ON ELEHMANN.STATE.CODE = DG5.DONATION.STATE ";
    $sql .= "WHERE ELEHMANN.COMMITTEE.CANDIDATE = :candidate_bv AND ";
    $sql .= "DG5.DONATION.DAY >= :start_date_bv AND DG5.DONATION.DAY <= :end_date_bv ";
    $sql .= "GROUP BY ELEHMANN.STATE.DISPLAYNAME ";
    $sql .= "ORDER BY TOTAL_DONATIONS ASC";
    //echo $sql;
    $query = oci_parse($db, $sql);
    oci_bind_by_name($query, ":candidate_bv", $candidate);
    oci_bind_by_name($query, ":start_date_bv", $start_date);
    oci_bind_by_name($query, ":end_date_bv", $end_date);
    oci_execute($query);
    confirm_result_set($query);
    return $query;
  }

  function donations_by_state_per_capita($candidate, $start_date, $end_date) {
    global $db;
    
    $sql = "SELECT ELEHMANN.STATE.DISPLAYNAME, ";
    $sql .= "ROUND((SUM(DG5.DONATION.AMOUNT) / ELEHMANN.STATE.POPULATION), 3) AS DONATIONS_PER_CAPITA ";
    $sql .= "FROM (DG5.DONATION ";
    $sql .= "JOIN ELEHMANN.COMMITTEE ON ELEHMANN.COMMITTEE.COMMITTEE_ID = DG5.DONATION.COMMITTEEID) ";
    $sql .= "JOIN ELEHMANN.STATE ON ELEHMANN.STATE.CODE = DG5.DONATION.STATE ";
    $sql .= "WHERE ELEHMANN.COMMITTEE.CANDIDATE = :candidate_bv AND ";
    $sql .= "DG5.DONATION.DAY >= :start_date_bv AND DG5.DONATION.DAY <= :end_date_bv ";
    $sql .= "GROUP BY ELEHMANN.STATE.DISPLAYNAME ";
    $sql .= "ORDER BY AS DONATIONS_PER_CAPITA ASC";
    //echo $sql;
    $query = oci_parse($db, $sql);
    oci_bind_by_name($query, ":candidate_bv", $candidate);
    oci_bind_by_name($query, ":start_date_bv", $start_date);
    oci_bind_by_name($query, ":end_date_bv", $end_date);
    oci_execute($query);
    confirm_result_set($query);
    return $query;
  }

  /*function donor_data($start_date, $end_date) {
    global $db;
    
    $sql = "SELECT ELEHMANN.COMMITTEE.CANDIDATE, ";
    $sql .= "SUM(DG5.DONATION.AMOUNT) AS TOTAL_DONATIONS, ";
    $sql .= "COUNT(DG5.DONATION.AMOUNT) AS NUM_DONATIONS, ";
    $sql .= "ROUND(AVG(DG5.DONATION.AMOUNT), 2) AS DONATION_SIZE ";
    $sql .= "FROM DG5.DONATION ";
    $sql .= "JOIN ELEHMANN.COMMITTEE ON ELEHMANN.COMMITTEE.COMMITTEE_ID = DG5.DONATION.COMMITTEEID ";
    $sql .= "WHERE DG5.DONATION.DAY >= :start_date_bv AND DG5.DONATION.DAY <= :end_date_bv ";
    $sql .= "GROUP BY ELEHMANN.COMMITTEE.CANDIDATE ";
    $sql .= "ORDER BY ELEHMANN.COMMITTEE.CANDIDATE ASC";
    //echo $sql;
    $query = oci_parse($db, $sql);
    oci_bind_by_name($query, ":start_date_bv", $start_date);
    oci_bind_by_name($query, ":end_date_bv", $end_date);
    oci_execute($query);
    confirm_result_set($query);
    return $query;
  }*/

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
      echo "Picture not found!";
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

    $sql = "SELECT REGEXP_REPLACE(REGEXP_REPLACE(REGEXP_REPLACE(REGEXP_REPLACE(REGEXP_REPLACE(REGEXP_REPLACE(REGEXP_REPLACE(CITY, ' city and burrow$', ''),' city$',''),' borough$',''),' town$',''),' municipality$',''),' village$',''),' unified government$','') as CITY FROM ELEHMANN.CITY ";
    $sql .= "WHERE ELEHMANN.CITY.STATE = :state_bv";
    //echo $sql;
    $query = oci_parse($db, $sql);
    oci_bind_by_name($query, ":state_bv", $state);
    oci_execute($query);
    confirm_result_set($query);
    return $query;
  }
  
?>
