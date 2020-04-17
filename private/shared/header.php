<?php
    if(!isset($page_title)) { $page_title = 'Election Finance Insight'; }
?>

<!doctype html>

<html lang="en">
  <head>
    <title>EFI - <?php echo h($page_title); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script type="text/javascript">
      var jsonArray = <?php echo json_encode($dataPoints); ?>
      document.write(jsonArray);
      /*var obj;
      for (int i=0;i<jsonArray.length;i++)
      {
        obj = JSON.parse(jsonArray)[i];
      }*/
      window.onload = function () {
      
      var chart = new CanvasJS.Chart("chartContainer", {
        title: {
          text: "<?php echo $candidate?> Donations Over Time"
        },
        axisY: {
          title: "Amount (USD)"
        },
        data: [{
          type: "line",
          dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
        }]
      });
      chart.render();
      
      }
</script>
  </head>

  <body>
    <div class="container">
    <header>
      <h1>Election Finance Insights</h1>
    </header>
    <navigation class="navbar navbar-expand-lg">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active"><a class="nav-link" href="<?php echo url_for('/index.php'); ?>">Home</a></li>
        <li class="nav-item active"><a class="nav-link" href="<?php echo url_for('/cand_comp.php'); ?>">Candidate Comparison</a></li>
        <li class="nav-item active"><a class="nav-link" href="<?php echo url_for('/ind_cand_stats.php'); ?>">Individual Candidate Statistics</a></li>
      </ul>
    </navigation>