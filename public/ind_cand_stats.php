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
    <h4 class="text-center">Select a</h4>
    <h2 class="text-center">Candidate</h2>
    <!--<img src="images/show_images.php?id=Bernie+Sanders" alt="Bernie Sanders" class="img-thumbnail">-->
    <?php // Trying to make code to dynamically add the candidates as they are added to the
          // database ****I can't get the images to load!!*****
    ?>
    <?php $candidate_array = list_candidates();
      $count = 0;
      while($cand = oci_fetch_array($candidate_array, OCI_ASSOC+OCI_RETURN_NULLS)) { ?>
      <a class="action"
        href="<?php echo url_for('/ind_cand_stats.php?id=' . $count); ?>">
        <img class="text-center" src="<?php echo url_for('/images/show_image.php?id=' . $count);?>"
              alt="<?php echo $cand['CANDIDATE']; ?>" class="img-thumbnail">
        <h6 class="text-center"><?php echo $cand['CANDIDATE']; ?></h6>
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
        <img algin="center" src="<?php echo url_for('/images/show_image.php?id=' . $count);?>"
              alt="<?php echo $cand['CANDIDATE']; ?>" class="img-fluid">
    <?php } $count++;
        }
        oci_free_statement($candidate_array); ?>
  </div>
  <div class="col-8" id="content">
    <div class="text-center">
      <h2>Individual Candidate Statistics</h2>
    </div>

    <form action="<?php echo url_for('/ind_cand_stats.php?id=' . h(u($id))); ?>" method="post">
      <div class="form-row">
        <div class="col"></div>
        <div class="col">
          <label>From</label>
          <input type="date" class="form-control" value="2019-01-01">
        </div>
        <div class="col">
          <label>To</label>
          <input type="date" class="form-control" value="2019-12-31">
        </div>
        <div class="col"></div>
      </div>
      <div class="text-center">
        <div class="text-center">
          <label>Filter Location By:</label>
        </div>
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
          <label class="btn btn-secondary active">
            <input type="radio" name="options" id="USAOption" autocomplete="off" checked>USA
          </label>
          <label class="btn btn-secondary">
            <input type="radio" name="options" id="stateOption" autocomplete="off">State
          </label>
          <label class="btn btn-secondary">
            <input type="radio" name="options" id="cityOption" autocomplete="off">City
          </label>
        </div>
      </div>
      <div id="usmap" class="centerItem">
        <div id="map" class="mapSize"></div>
        <div id="clicked-state"></div>
        <div id="city_selector"></div>
      </div>
      <div class="text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </form>

    <div id="chartContainer" style="height: 370px; width: 100%;"></div>

  </div>
</div>
<?php
  oci_free_statement($query);
?>
<script>
var map = document.getElementById('map');
var cityOptions = document.getElementById('city_selector');
var state = document.getElementById('stateOption');
var usa = document.getElementById('USAOption');
var city = document.getElementById("cityOption");

state.onchange = handleState;
usa.onchange = handleUSA;
city.onchange = handleCity;

function handleState(e) {
  map.style.display = "block";
}

function handleUSA(e) {
  map.style.display = "none";
}

function handleCity(e) {
  map.style.display = "block";
}

$('#map').usmap({
  stateStyles: {
	      fill: 'beige'
      },
  showLabels: true,
  labelBackingStyles: {
    fill: 'beige'
  },
  labelTestStyles: {
    fill: 'black',
    'font-size': '10px',
    'font-weight': 300
  },
  // The click action
  click: function(event, data) {
    $('#clicked-state')
      .text('You clicked: '+data.name)
      .parent().effect('highlight', {color: '#C7F464'}, 2000);
  }
});
</script>
<?php include(SHARED_PATH . '/footer.php'); ?>
