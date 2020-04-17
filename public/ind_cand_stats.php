#!/usr/local/bin/php

<?php require_once('../private/initialize.php'); ?>
<?php

  $candidate = 'Bernie Sanders';
  $start_date = '20190204';
  $end_date = '20191225';
  $query = donations_over_time_usa($candidate, $start_date, $end_date);
  $nrows = oci_fetch_all($query, $dataPoints, null, null, OCI_FETCHSTATEMENT_BY_ROW);
  $images = candidate_photos();
  
?>
<?php $page_title = 'Individual Stats'; ?>
<?php include(SHARED_PATH . '/header.php'); ?>

<script type="text/javascript">
      var jsonArray = <?php echo json_encode($dataPoints); ?>;
      var newJSONArray = [];
      var date;
      var year;
      var month;
      var day;
      for (var i=0;i<jsonArray.length;i++)
      {
        date = jsonArray[i]['DAY'];
        year = parseInt(date.substring(0, 4));
        month = parseInt(date.substring(4, 6)) - 1;
        day = parseInt(date.substring(6, 8));
        newJSONArray.push({x: new Date(year, month, day), y: parseInt(jsonArray[i]['TOTAL_DONATIONS'])});
      }
      console.log(newJSONArray);
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
          dataPoints: newJSONArray
        }]
      });
      chart.render();
      
      }
</script>

<div class="row">
  <div class="col-2">
    <h4>Choose a Candidate</h4>
    <?php
      while($row = oci_fetch_array($images, OCI_ASSOC+OCI_RETURN_NULLS)) {
        echo "<img src=\"";
        row['IMAGE'];
        echo "\" alt=\""  ;
        row['CANDIDATE'];
        echo "\" class=\"img-thumbnail\">\n";
      }
    ?>
    
    <img src="<?php echo candidate_photo($candidate)?>" alt="<?php echo $candidate?>" class="img-thumbnail">
  </div>
  <div class="col-2">
  
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
  oci_free_statement($images);
?>
<?php include(SHARED_PATH . '/footer.php'); ?>
