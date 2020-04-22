#!/usr/local/bin/php

<?php require_once('../private/initialize.php'); ?>
<script>
var candidates = [];
</script>

<?php

  $list_candidates = list_candidates();
  $i = 0;
  $cand_list = [];
  while($row = oci_fetch_array($list_candidates, OCI_ASSOC+OCI_RETURN_NULLS)) {
    $cand_list[$i] = $row['CANDIDATE'];
    $i++;
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
      $i = 0;
      $dataPointArray = array();
      $money = array();
      $num_donations = array();
      $donation_size = array();
      $donations_per_capita = array();
      $list_candidates = list_candidates();
      while($row = oci_fetch_array($list_candidates, OCI_ASSOC+OCI_RETURN_NULLS)) {
        $candidate = $row['CANDIDATE'];
        //	echo $candidate";
        $query = donations_over_time_usa($candidate, $format_start_date, $format_end_date);
        $nrows = oci_fetch_all($query, $dataPoints, null, null, OCI_FETCHSTATEMENT_BY_ROW);
	      $dataPointArray[$i] = $dataPoints;
       
	      $query2 = donation_data_us($format_start_date, $format_end_date);
        while($row = oci_fetch_array($query2, OCI_ASSOC+OCI_RETURN_NULLS)) {
          if ($row['CANDIDATE'] == $candidate) {
            $money[$i] = number_format($row['TOTAL_DONATIONS']);
            $num_donations[$i] = number_format($row['NUM_DONATIONS']);
            $donation_size[$i] = number_format($row['DONATION_SIZE'], 2);
            $donation_per_capita[$i] = number_format($row['DONATIONS_PER_CAPITA'], 4);
          }
        }
        $i++;
      }
//    var_dump($donation_per_capita);
    } else if ($locationOption == 'State') {
      $i = 0;
      $dataPointArray = array();
      $list_candidates = list_candidates();
      while($row = oci_fetch_array($list_candidates, OCI_ASSOC+OCI_RETURN_NULLS)) {
        $candidate = $row['CANDIDATE'];
        //	echo $candidate";
        $query = donations_over_time_state($candidate, $selected_state, $format_start_date, $format_end_date);
        $nrows = oci_fetch_all($query, $dataPoints, null, null, OCI_FETCHSTATEMENT_BY_ROW);
        $dataPointArray[$i] = $dataPoints;
        
	      $query2 = donation_data_state($format_start_date, $format_end_date, $selected_state);
        while($row = oci_fetch_array($query2, OCI_ASSOC+OCI_RETURN_NULLS)) {
          if ($row['CANDIDATE'] == $candidate) {
            $money[$i] = number_format($row['TOTAL_DONATIONS']);
            $num_donations[$i] = number_format($row['NUM_DONATIONS']);
            $donation_size[$i] = number_format($row['DONATION_SIZE'], 2);
            $donation_per_capita[$i] = number_format($row['DONATIONS_PER_CAPITA'], 4);
          }
        }
        $i++;
      }
    } else if ($locationOption == 'City') {
      $i = 0;
      $dataPointArray = array();
      $list_candidates = list_candidates();
      while($row = oci_fetch_array($list_candidates, OCI_ASSOC+OCI_RETURN_NULLS)) {
        $candidate = $row['CANDIDATE'];
        //	echo $candidate";
        $query = donations_over_time_city($candidate, $selected_state, $selected_city, $format_start_date, $format_end_date);
        $nrows = oci_fetch_all($query, $dataPoints, null, null, OCI_FETCHSTATEMENT_BY_ROW);
        $dataPointArray[$i] = $dataPoints;
        
	      $query2 = donation_data_city($format_start_date, $format_end_date, $selected_state, $selected_city);
        while($row = oci_fetch_array($query2, OCI_ASSOC+OCI_RETURN_NULLS)) {
          if ($row['CANDIDATE'] == $candidate) {
            $money[$i] = number_format($row['TOTAL_DONATIONS']);
            $num_donations[$i] = number_format($row['NUM_DONATIONS']);
            $donation_size[$i] = number_format($row['DONATION_SIZE'], 2);
            $donation_per_capita[$i] = number_format($row['DONATIONS_PER_CAPITA'], 4);
          }
        }
        $i++;
      }
    }
      
  } else {
  
    $start_date = '2019-01-01';
    $end_date = '2019-12-31';
    $selected_state = '';
    $display = 'none';

  }
  
?>
<?php $page_title = 'Candidate Comparison'; ?>
<?php include(SHARED_PATH . '/header.php'); ?>

<script type="text/javascript">
      var donationsArray;
      var newDonationsArray = [];
      var bulkDataArray = [];
      var moneyArray = [];
      var moneyDataPoints = [];
      var numOfDonationsArray = [];
      var numOfDonationsDataPoints = [];
      var donationSizeArray = [];
      var donationSizeDataPoints = [];
      var donationsPerCapArray = [];
      var donationsPerCapDataPoints = [];
//$donation_size = array();
//$donations_per_capita = array();
      var date;
      var year;
      var month;
      var day;
      var dataMagic = [];
      var candidate = [];
	<?php foreach($dataPointArray as $dataMagic => $val){
	?>
		dataMagic[<?php echo $dataMagic;?>] = <?php echo json_encode($val); ?>;
	<?php
	  }
	?>
	<?php foreach($cand_list as $can => $val){
	?>
		candidate[<?php echo $can;?>] = <?php echo json_encode($val); ?>;
	<?php
	  }
	?>
        <?php foreach($money as $money => $val){
        ?>
                moneyArray[<?php echo $money;?>] = <?php echo str_replace(',','',$val); ?>;
        <?php
          }
        ?>
        <?php foreach($num_donations as $num => $val){
        ?>
                numOfDonationsArray[<?php echo $num;?>] = <?php echo str_replace(',','',$val); ?>;
        <?php
          }
        ?>
        <?php foreach($donation_size as $num => $val){
        ?>
                donationSizeArray[<?php echo $num;?>] = <?php echo str_replace(',','',$val); ?>;
        <?php
          }
        ?>
        <?php foreach($donation_per_capita as $num => $val){
        ?>
                donationsPerCapArray[<?php echo $num;?>] = <?php echo str_replace(',','',$val); ?>;
        <?php
          }
        ?>
//      console.log(donationsPerCapArray);

      for (var i=0;i<dataMagic.length;i++){
        donationsArray = dataMagic[i];
        newDonationsArray = [];
        if (donationsArray != null) {
          for (var c=0;c<donationsArray.length;c++)
          {
            date = donationsArray[c]['DAY'];
            year = parseInt(date.substring(0, 4));
            month = parseInt(date.substring(4, 6)) - 1;
            day = parseInt(date.substring(6, 8));
            newDonationsArray.push({x: new Date(year, month, day),
                                    y: parseInt(donationsArray[c]['TOTAL_DONATIONS'])});
          }
        }
        bulkDataArray.push({type: "line", showInLegend: true,
                            name: candidate[i], dataPoints: newDonationsArray});
        moneyDataPoints.push({label: candidate[i], y: moneyArray[i]});
        numOfDonationsDataPoints.push({label: candidate[i], y: numOfDonationsArray[i]});
        donationSizeDataPoints.push({label: candidate[i], y: donationSizeArray[i]});
        donationsPerCapDataPoints.push({label: candidate[i], y: donationsPerCapArray[i]});
      }
        //	console.log(moneyDataPoints);
        window.onload = function () {
        
        var chart = new CanvasJS.Chart("chartContainer", {
          animationEnabled: true,
          title: {
            text: "<?php
                    if($locationOption == 'USA') {
                      echo "Donations Over Time From the US";
                    } else if ($locationOption == 'State') {
                      echo "Donations Over Time From " . $selected_state;
                    } else if ($locationOption == 'City') {
                      echo "Donations Over Time From " . $selected_city . ", " . $selected_state;
                    }
                  ?>"
          },
          axisY: {
            title: "Amount (USD)",
	          prefix: "$"
          },
          data: bulkDataArray
        });
        chart.render();

        var chart = new CanvasJS.Chart("chart2Container", {
          animationEnabled: true,
          title: {
            text: "<?php
                    if($locationOption == 'USA') {
                      echo "Total US Dollars Raised";
		                } else if ($locationOption == 'State') {
                      echo "Total Dollars Raised From " . $selected_state;
                    } else if ($locationOption == 'City') {
                      echo "Total Dollars Raised From " . $selected_city . ", " . $selected_state;
                    }
                  ?>"
          },
          axisY: {
            title: "Amount (USD)",
	    prefix: "$"
          },
          data: [{        
		type: "column",  
		dataPoints: moneyDataPoints
	  }]
        });
        chart.render();

        var chart = new CanvasJS.Chart("chart3Container", {
          animationEnabled: true,
          title: {
            text: "<?php
                    if($locationOption == 'USA') {
                      echo "Total Number of US Donations";
		                } else if ($locationOption == 'State') {
                      echo "Total Number of Donations From " . $selected_state;
                    } else if ($locationOption == 'City') {
                      echo "Total Number of Donations From " . $selected_city . ", " . $selected_state;
                    }
                  ?>"
          },
          axisY: {
            title: "Number of Donations"
          },
          data: [{        
		type: "column",  
		dataPoints: numOfDonationsDataPoints
	  }]
        });
        chart.render();

        var chart = new CanvasJS.Chart("chart4Container", {
          animationEnabled: true,
          title: {
            text: "<?php
                    if($locationOption == 'USA') {
                      echo "US Average Donation Amount";
		                } else if ($locationOption == 'State') {
                      echo "Average Donation Amount From " . $selected_state;
                    } else if ($locationOption == 'City') {
                      echo "Average Donation Amount From " . $selected_city . ", " . $selected_state;
                    }
                  ?>"
          },
          axisY: {
            title: "Amount (USD)",
	    prefix: "$"
          },
          data: [{        
		type: "column",  
		dataPoints: donationSizeDataPoints
	  }]
        });
        chart.render();

        var chart = new CanvasJS.Chart("chart5Container", {
          animationEnabled: true,
          title: {
            text: "<?php
                    if($locationOption == 'USA') {
                      echo "Average US Donations Per Capita";
		                } else if ($locationOption == 'State') {
                      echo "Average Donations Per Capita From " . $selected_state;
                    } else if ($locationOption == 'City') {
                      echo "Average Donations Per Capita From " . $selected_city . ", " . $selected_state;
                    }
                  ?>"
          },
          axisY: {
            title: "Amount (USD)",
	    prefix: "$"
          },
          data: [{        
		type: "column",  
		dataPoints: donationsPerCapDataPoints
	  }]
        });
        chart.render();

        }
   
</script>

<div class="row">
  <div class="col-2">
    
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
  <div class="col-8" id="content">
    <div>
      <h1 class="text-center">Candidate Comparison</h1>
    </div>

    <form action="<?php echo url_for('/cand_comp.php'); ?>" method="post">
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

    <div class="text-center" style="display: <?php echo $display ?>;">
      <?php if (count($dataPointArray[0]) < 1) {
        $display = 'none';
        if($locationOption == 'USA') {
          echo "<h3 style='margin-top: 20px; margin-bottom: 40px;'>There are no donations from the US during the specified time period</h3>";
        } else if ($locationOption == 'State') {
          echo "<h3 style='margin-top: 20px; margin-bottom: 40px;'>There are no donations from " . $selected_state . " during the specified time period</h3>";
        } else if ($locationOption == 'City') {
          echo "<h3 style='margin-top: 20px; margin-bottom: 40px;'>There are no donations from " . $selected_city . ", " . $selected_state . " during the specified time period</h3>";
        }
      }?>
    </div>

    <div id="chartContainer" style="height: 370px; width: 100%; display: <?php echo $display ?>"></div>
	<br>
    <div id="chart2Container" style="height: 370px; width: 100%; display: <?php echo $display ?>"></div>
	<br>
    <div id="chart3Container" style="height: 370px; width: 100%; display: <?php echo $display ?>"></div>
	<br>
    <div id="chart4Container" style="height: 370px; width: 100%; display: <?php echo $display ?>"></div>
	<br>
    <div id="chart5Container" style="height: 370px; width: 100%; display: <?php echo $display ?>"></div>
	<br>

  </div>
</div>
<?php
  oci_free_statement($query);
?>
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
