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
    <p>Our intent is to design and deploy a web application that allows users to gain insight
      into the finances of the 2020 Democratic Presidential Primary. Both time and money are
      limited resources, especially in the field of political elections. Often campaigns spend
      their time and effort with the intention of raising additional funds to fuel the campaign.
      Due to the limited time available, it is important to optimize the time to ensure that
      it is effectively spent raising revenue. Using publicly available information from the
      Federal Election Commission, we have created a tool to examine the connections between
      a campaign's events and their fundraising. In addition to the amount and date of a
      donation, we have access to each donor's employer and state of residence, we feel that
      useful details can be found in this data as well.
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
    
    <div class="text-center" style="margin-top: 20px" id="tuple-button">
      <button class="btn btn-primary" onclick="showTuple()">No. of Tuples</button>
    </div>
    
    
    <div class="text-center" style="padding: 10px">
      <p id="tuple-text"></p>
    </div>
    
  </div>
</div>
<script>
  function showTuple() {
    document.getElementById("tuple-text").innerHTML = "<?php echo $num_tuples ?>";
  }
</script>
<?php include(SHARED_PATH . '/footer.php'); ?>
