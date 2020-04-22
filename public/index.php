#!/usr/local/bin/php

<?php require_once('../private/initialize.php');

  $list_candidates = list_candidates();
  $count = 0;
  $tuple_query = num_tuples();
  while($row = oci_fetch_array($tuple_query, OCI_ASSOC+OCI_RETURN_NULLS)) {
    $num_tuples = $row['TOTAL_TUPLES'];
  }
?>

<?php $page_title = 'Main Menu'; ?>
<?php include(SHARED_PATH . '/header.php'); ?>

<div id="container" style="margin-left: 15%; margin-right: 15%">
  <div id="main-menu">
    <p>Our intent is to design and deploy a web application that allows users to gain insight into the
        finances of the 2020 Presidential Primary. In an election season with so many candidates,
        having a tool able to assist in analyzing campaign nance trends would be an invaluable
        asset. Each candidate is required to report information about individual donations to the
        Federal Election Commission. This report includes detailed information about each individ-
        ual donor, including their name, the donation amount, city, and state as well as employer
        and occupation. We are interested in seeing comparisons to individual campaign events as
        well as coverage of candidates and their competitors in the news media. Lastly, we believe we
        will be able to gain insight into the donors by comparing their employers and occupations.
    </p>
    <h2 class="text-center"><a href="<?php echo url_for('/cand_comp.php'); ?>">Candidate Comparison</a></h2>
    <h2 class="text-center">Individual Candidate Page</h2>
    <div class="row">
      <?php while($cand = oci_fetch_array($list_candidates, OCI_ASSOC+OCI_RETURN_NULLS)) { ?>
        <div class="col-4">
          <a class="action"
             href="<?php echo url_for('/ind_cand_stats.php?id=' . $count); ?>">
            <?php
              $imageData = candidate_photo($cand['CANDIDATE']);
              print('<img src="data:image/png;base64,'.base64_encode($imageData).'" class="img-thumbnail"/>');
            ?>
            <h4 class="text-center"><?php echo $cand['CANDIDATE']; ?></h4>
          </a>
        </div>
      <?php $count++;
            }
            oci_free_statement($list_candidates); ?>
    </div>
    <div class="row">
      <div class="text-center" style="padding: 10px" id="tuple-button">
        <button class="btn btn-primary" onclick="showTuple();">Number of Tuples</button>
      </div>
    </div>
    <div class="row">
      <div class="text-center" style="padding: 10px" id="tuple-text" style="display: none">
        <p class="btn btn-primary"><?php echo $num_tuples ?></p>
      </div>
    </div>
  </div>
</div>
<script>
  function showTuple() {
    //var tupleButton = document.getElementById('tuple-button');
    var tupleText = document.getElementById('tuple-text');

    tuplleText.style.display = "block";
  }
</script>
<?php include(SHARED_PATH . '/footer.php'); ?>
