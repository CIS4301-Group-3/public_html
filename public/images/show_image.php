<?php require_once('../../private/initialize.php'); ?>

<?php

if(isset($_GET['id']))
{
  $id = urldecode($_GET['id']);
  $query = candidate_photo($id);
  while($row = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS)) {
    $imageData = $row['IMAGE'];
  }
  header("content-type: image/jpeg");
} else {
  echo "Error!";
}

?>