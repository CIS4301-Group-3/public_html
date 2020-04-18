#!/usr/local/bin/php

<?php require_once('../private/initialize.php'); ?>
<?php

  if(!isset($_GET['id'])) {
    redirect_to(url_for('/index.php'));
  }
  $id = $_GET['id'];
  $list_candidates = list_candidates();
  $i = 0;
  while($row = oci_fetch_array($list_candidates, OCI_ASSOC+OCI_RETURN_NULLS)) {
    if ($i == $id) {
      $candidate = $row['CANDIDATE'];
    }
    $i++;
  }
  oci_free_statement($list_candidates);
  $start_date = '20190101';
  $end_date = '20191231';

  $query = donations_over_time_usa($candidate, $start_date, $end_date);
  $nrows = oci_fetch_all($query, $dataPoints, null, null, OCI_FETCHSTATEMENT_BY_ROW);
  
?>
<?php $page_title = 'Individual Stats'; ?>
<?php include(SHARED_PATH . '/header.php'); ?>

<script type="text/javascript">
      var donationsArray = <?php echo json_encode($dataPoints); ?>;
      var newDonationsArray = [];
      var date;
      var year;
      var month;
      var day;
      for (var i=0;i<donationsArray.length;i++)
      {
        date = donationsArray[i]['DAY'];
        year = parseInt(date.substring(0, 4));
        month = parseInt(date.substring(4, 6)) - 1;
        day = parseInt(date.substring(6, 8));
        newDonationsArray.push({x: new Date(year, month, day), y: parseInt(donationsArray[i]['TOTAL_DONATIONS'])});
      }
      console.log(newDonationsArray);
      window.onload = function () {
      
      var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        exportEnabled: true,
        title: {
          text: "<?php echo $candidate?> Donations Over Time"
        },
        axisY: {
          title: "Amount (USD)"
        },
        data: [{
          type: "line",
          dataPoints: newDonationsArray
        }]
      });
      chart.render();
      
      }
</script>

<div class="row">
  <div class="col-2">
    <h4>Choose a Candidate</h4>
    <!--<img src="images/show_images.php?id=Bernie+Sanders" alt="Bernie Sanders" class="img-thumbnail">-->
    <?php // Trying to make code to dynamically add the candidates as they are added to the
          // database ****I can't get the images to load!!*****
    ?>
    <?php $candidate_array = list_candidates();
      $count = 0;
      while($cand = oci_fetch_array($candidate_array, OCI_ASSOC+OCI_RETURN_NULLS)) { ?>
      <a class="action"
        href="<?php echo url_for('/ind_cand_stats.php?id=' . $count); ?>">
        <img src="<?php echo url_for('/images/show_image.php?id=' . $count);?>"
              alt="<?php echo $cand['CANDIDATE']; ?>" class="img-thumbnail">
        <h6><?php echo $cand['CANDIDATE']; ?></h6>
      </a>
    <?php $count++;
          }
          oci_free_statement($candidate_array); ?>
  </div>
  <div class="col-2">
    <?php $candidate_array = list_candidates();
      $count = 0;
      while($cand = oci_fetch_array($candidate_array, OCI_ASSOC+OCI_RETURN_NULLS)) {
        if ($id == $count) { ?>
      <a class="action"
        href="<?php echo url_for('/ind_cand_stats.php?id=' . $count); ?>">
        <img src="<?php echo url_for('/images/show_image.php?id=' . $count);?>"
              alt="<?php echo $cand['CANDIDATE']; ?>" class="img-fluid">
        <h6><?php echo $cand['CANDIDATE']; ?></h6>
      </a>
    <?php } $count++;
        }
        oci_free_statement($candidate_array); ?>
  </div>
  <div class="col-8" id="content">
    <div id="main-menu">
      <h2 align="center">Individual Candidate Statistics</h2>
    </div>
    <div class="col" align="center">
      <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-secondary active">
          <input type="radio" name="options" id="option1" autocomplete="off" checked>USA
        </label>
        <label class="btn btn-secondary">
          <input type="radio" name="options" id="option2" autocomplete="off">State
        </label>
        <label class="btn btn-secondary">
          <input type="radio" name="options" id="option3" autocomplete="off">City
        </label>
      </div>
    </div>
    <div id="chartContainer" style="height: 370px; width: 100%;"></div>
  </div>
</div>
<?php
  
  /*echo "<div><br>
          <hr><table>\n";
  while ($row = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS)){
          echo "<tr>\n";
          foreach ($row as $item){
                  echo "<td>" . ($item !== NULL ? htmlentities($item, ENT_QUOTES) : "&nbsp") . "</td>\n";
          }
          echo "</tr>\n";
  }
  echo "</table>\n";*/
  oci_free_statement($query);
?>
<?php include(SHARED_PATH . '/footer.php'); ?>
