#!/usr/local/bin/php

<?php require_once('../../private/initialize.php'); ?>
<html>
<body>
<?php

if(isset($_GET['id']))
{
  $id = urldecode($_GET['id']);
  //echo $id;
  $query = candidate_photo($id);
  $row = oci_fetch_array($query, OCI_ASSOC);
  if(!$row){
  echo "Error!";
  }
  else{
    $imageData = $row['IMAGE']->load();
//    header("Content-type: image/jpeg");
//    print $imageData;
    print('<img src="data:image/jpeg;base64,'.base64_encode($imageData).'" />');
    return $imageData;
  }
}

?>
</body>
</html>
