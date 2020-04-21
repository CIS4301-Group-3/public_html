#!/usr/local/bin/php

<?php require_once('../../private/initialize.php'); ?>

<?php

  $state = $_GET['state'];
  echo $state;
  $query = get_cities($state);
  $nrows = oci_fetch_all($query, $city_list, null, null, OCI_FETCHSTATEMENT_BY_ROW+OCI_ASSOC);
  
?>

<script>
  var citiesJSON = <?php echo json_encode($city_list); ?>;
  document.write(citiesJSON);
</script>