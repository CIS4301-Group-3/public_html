#!/usr/local/bin/php

<?php require_once('../private/initialize.php'); ?>
<?php

  if(!isset($_GET['id'])) {
    redirect_to(url_for('/index.php'));
  }
  $id = $_GET['id'];
  $list_candidates = list_candidates();
  $i = 0;
  $cand_exists = false;
  while($row = oci_fetch_array($list_candidates, OCI_ASSOC+OCI_RETURN_NULLS)) {
    if ($i == $id) {
      $candidate = $row['CANDIDATE'];
      $cand_exists = true;
    }
    $i++;
  }
  if (!$cand_exists)
  {
    redirect_to(url_for('/index.php'));
  }
  oci_free_statement($list_candidates);

  if($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle form values sent by form below
  
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $locationOption = $_POST['locationOption'];
    $selected_state = $_POST['selected_state'];
    $selected_city = $_POST['selected_city'];

    $format_start_date = format_date($start_date);
    $format_end_date = format_date($end_date);
    $display = 'block';

    if ($locationOption == 'USA') {
      $query = donations_over_time_usa($candidate, $format_start_date, $format_end_date);
      $nrows = oci_fetch_all($query, $dataPoints, null, null, OCI_FETCHSTATEMENT_BY_ROW);

      $query2 = donation_data_us($format_start_date, $format_end_date);
      while($row = oci_fetch_array($query2, OCI_ASSOC+OCI_RETURN_NULLS)) {
        if ($row['CANDIDATE'] == $candidate) {
          $money = number_format($row['TOTAL_DONATIONS']);
          $num_donations = number_format($row['NUM_DONATIONS']);
          $donation_size = number_format($row['DONATION_SIZE'], 2);
          $donation_per_capita = number_format($row['DONATIONS_PER_CAPITA'], 3);
        }
      }

    } else if ($locationOption == 'State') {
      $query = donations_over_time_state($candidate, $selected_state, $format_start_date, $format_end_date);
      $nrows = oci_fetch_all($query, $dataPoints, null, null, OCI_FETCHSTATEMENT_BY_ROW);
      //$query2 = total_donations_received($start_date, $end_date, $state);
    } else if ($locationOption == 'City') {
      $query = donations_over_time_city($candidate, $selected_state, $selected_city, $format_start_date, $format_end_date);
      $nrows = oci_fetch_all($query, $dataPoints, null, null, OCI_FETCHSTATEMENT_BY_ROW);
      //$query2 = total_donations_received($start_date, $end_date, $state, $city);
    }
    $query3 = donations_by_state($candidate, $format_start_date, $format_end_date);
    $nrows = oci_fetch_all($query3, $dataPoints2, null, null, OCI_FETCHSTATEMENT_BY_ROW);

    oci_free_statement($query);
    oci_free_statement($query2);
    oci_free_statement($query3);
    //oci_free_statement($query4);
    //oci_free_statement($query5);
    //oci_free_statement($query6);  

  } else {
  
    $start_date = '2019-01-01';
    $end_date = '2019-12-31';
    $format_start_date = format_date($start_date);
    $format_end_date = format_date($end_date);
    $selected_state = '';
    $display = 'none';

  }
  
?>
<?php $page_title = 'Individual Stats'; ?>
<?php include(SHARED_PATH . '/header.php'); ?>

<script type="text/javascript">
      var donationsArray = <?php echo json_encode($dataPoints); ?>;
      var newDonationsArray = [];
      var stateDonationArray = <?php echo json_encode($dataPoints2); ?>;
      var newStateDonationArray = [];
      var date;
      var year;
      var month;
      var day;

      if (donationsArray != null) {

        for (var i=0;i<donationsArray.length;i++)
        {
          date = donationsArray[i]['DAY'];
          year = parseInt(date.substring(0, 4));
          month = parseInt(date.substring(4, 6)) - 1;
          day = parseInt(date.substring(6, 8));
          newDonationsArray.push({x: new Date(year, month, day), y: parseInt(donationsArray[i]['TOTAL_DONATIONS'])});
        }

        for (var i=0;i<stateDonationArray.length;i++)
        {
          newStateDonationArray.push({label: stateDonationArray[i]['STATE'], y: parseInt(stateDonationArray[i]['TOTAL_DONATIONS'])});
        }

        window.onload = function () {
        var chart = new CanvasJS.Chart("chartContainer", {
          animationEnabled: true,
          exportEnabled: true,
          title: {
            text: "<?php echo $candidate?> Donations Over Time"
          },
          axisY: {
            title: "Amount (USD)",
            prefix: "$",
          },
          data: [{
            type: "line",
            dataPoints: newDonationsArray
          }]
        });
        chart.render();

        var chart2 = new CanvasJS.Chart("chart2Container", {
          animationEnabled: true,
          title:{
            text: "Total Donations By State Over Time Period"
          },
          axisY: {
            title: "Donations (in USD)",
            prefix: "$",
          },
          data: [{
            type: "bar",
            dataPoints: newStateDonationArray
          }]
        });
        chart2.render();
        }

      }
</script>

<div class="row">
  <div class="col-2">
    <h4 class="text-center">Select a</h4>
    <h2 class="text-center">Candidate</h2>
    
    <?php $candidate_array = list_candidates();
      $count = 0;
      while($cand = oci_fetch_array($candidate_array, OCI_ASSOC+OCI_RETURN_NULLS)) { ?>
      <a class="action"
        href="<?php echo url_for('/ind_cand_stats.php?id=' . $count); ?>">
        <?php
          $imageData = candidate_photo($cand['CANDIDATE']);
          print('<img src="data:image/png;base64,'.base64_encode($imageData).'" class="img-thumbnail"/>');
        ?>
        <h6 class="text-center"><?php echo $cand['CANDIDATE']; ?></h6>
      </a>
    <?php $count++;
          }
          oci_free_statement($candidate_array); ?>
  </div>
  <div class="col-2">
	<?php
	  $imageData = candidate_photo($candidate);
    print('<img src="data:image/png;base64,'.base64_encode($imageData).'" class="img-fluid" />');
	?>
  </div>
  <div class="col-8" id="content">
    <div>
      <h1 class="text-center"><?php echo $candidate; ?> Statistics</h1>
    </div>

    <form action="<?php echo url_for('/ind_cand_stats.php?id=' . h(u($id))); ?>" method="post">
      <div class="form-row">
        <div class="col"></div>
        <div class="col">
          <label>From</label>
          <input type="date" name="start_date" class="form-control" value="<?php echo h($start_date); ?>">
        </div>
        <div class="col">
          <label>To</label>
          <input type="date" name="end_date" class="form-control" value="<?php echo h($end_date); ?>">
        </div>
        <div class="col"></div>
      </div>
      <div class="text-center">
        <div class="text-center" style="margin-top: 5px">
          <label>Filter Location By:</label>
        </div>
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
          <label class="btn btn-secondary active">
            <input type="radio" name="locationOption" value="USA" id="USAOption" autocomplete="off" checked>USA
          </label>
          <label class="btn btn-secondary">
            <input type="radio" name="locationOption" value="State" id="stateOption" autocomplete="off" >State
          </label>
          <label class="btn btn-secondary">
            <input type="radio" name="locationOption" value="City" id="cityOption" autocomplete="off" >City
          </label>
        </div>
      </div>
      <div id="usmap" class="centerItem">
        <div id="map" class="mapSize"></div>
        <input type="text" name="selected_state" style="display: none"
               value="<?php echo h($selected_state); ?>" id="clicked-state">
      </div>
      <div id="state_selected" class="centerItem">
          <h6 id="state_text" style="display: none">Select a State</h6>
      </div>
      <div id="city_selector" style="display: none" class="text-center">
        <select id="city_dropdown" name="selected_city" class="selectpicker" data-live-search="true"
                title="Select A City"></select>
      </div>
      <div class="text-center" style="padding: 10px">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </form>

    <div id="chartContainer" style="height: 370px; width: 100%;"></div>

    <div class="text-center" style="margin-top: 20px; display: <?php echo $display ?>;">
      <?php if($locationOption == 'USA') {
        echo "<h3>List of Stats from the US</h3>";
      } else if ($locationOption == 'State') {
        echo "<h3>List of Stats from " . $selected_state . "</h3>";
      } else if ($locationOption == 'City') {
        echo "<h3>List of Stats from " . $selected_city . ", " . $selected_state . "</h3";
      }
      ?>

      <div class="row">
        <div class ="col-6" style="text-align: right">Total Amount of Money Raised</div>
        <div class ="col-6" style="text-align: left">$<?php echo $money?></div>
      </div>

      <div class="row">
        <div class ="col-6" style="text-align: right">Total Number of Donations</div>
        <div class ="col-6" style="text-align: left"><?php echo $num_donations?></div>
      </div>

      <div class="row">
        <div class ="col-6" style="text-align: right">Average Donation Size</div>
        <div class ="col-6" style="text-align: left">$<?php echo $donation_size?></div>
      </div>

      <div class="row">
        <div class ="col-6" style="text-align: right">Amount Donated Per Capita</div>
        <div class ="col-6" style="text-align: left">$<?php echo $donation_per_capita?></div>
      </div>

      <div id="chart2Container" style="height: 700px; width: 100%;"></div>

    </div>
  </div>
</div>
<script>
var map = document.getElementById('map');
var stateSelected = document.getElementById('clicked-state');
var stateText = document.getElementById('state_text');
var cityOptions = document.getElementById('city_selector');
var state = document.getElementById('stateOption');
var usa = document.getElementById('USAOption');
var city = document.getElementById("cityOption");

state.onchange = handleState;
usa.onchange = handleUSA;
city.onchange = handleCity;

function handleState(e) {
  map.style.display = "block";
  cityOptions.style.display = "none";
  stateText.style.display = "block";
}

function handleUSA(e) {
  map.style.display = "none";
  cityOptions.style.display = "none";
  stateText.style.display = "none";
  stateSelected.value = "";
  stateText.textContent = "Select A State";
}

function handleCity(e) {
  map.style.display = "block";
  cityOptions.style.display = "block";
  stateText.style.display = "block";

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
      .val(data.name);
    $('#state_text')
      .text('State Selected: '+data.name);
    var state = data.name;
    var txt = '';
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          myObj = JSON.parse(this.responseText);
          for (x in myObj) {
            txt += "<option value='" + myObj[x]['CITY'] + "'>" + myObj[x]['CITY'] + "</option>";
          }
          document.getElementById("city_dropdown").innerHTML = txt;
          $('#city_dropdown').selectpicker('refresh');
        }
    };
    xmlhttp.open("GET", "php/cities.php?state=" + state, true);
    xmlhttp.send();
  }
});

</script>
<?php include(SHARED_PATH . '/footer.php'); ?>
