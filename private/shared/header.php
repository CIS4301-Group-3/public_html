<?php
    if(!isset($page_title)) { $page_title = 'Election Finance Insight'; }

    $navbar_candidate_list = list_candidates();
    $count2 = 0;

?>

<!doctype html>

<html lang="en">
  <head>
    <title>EFI - <?php echo h($page_title); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="js/jquery-3.5.0.min.js"></script>
    <script src="js/raphael.js"></script>
    <script src="js/color.jquery.js"></script>
    <script src="js/jquery.usmap.js"></script>
  </head>

  <body>
    <div class="container-fluid">
    <header>
      <h1>Election Finance Insights</h1>
    </header>
    <nav class="navbar navbar-expand-lg">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active"><a class="nav-link" href="<?php echo url_for('/index.php'); ?>">Home</a></li>
        <li class="nav-item active"><a class="nav-link" href="<?php echo url_for('/cand_comp.php'); ?>">Candidate Comparison</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
             data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Individual Candidate Statistics
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <?php while($cand = oci_fetch_array($navbar_candidate_list, OCI_ASSOC+OCI_RETURN_NULLS)) { ?>
              <a class="dropdown-item"
                href="<?php echo url_for('/ind_cand_stats.php?id=' . $count2); ?>">
                <?php echo $cand['CANDIDATE']; ?></a>
            <?php $count2++;
                  }
                  oci_free_statement($navbar_candidate_list);?>
          </div>
        </li>
      </ul>
    </nav>