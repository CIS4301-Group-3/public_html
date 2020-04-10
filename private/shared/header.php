<?php
    if(!isset($page_title)) { $page_title = 'Election Finance Insight'; }
?>

<!doctype html>

<html lang="en">
  <head>
    <title>EFI - <?php echo h($page_title); ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" media="all" href="<?php echo url_for('/stylesheets/site.css'); ?>" />
  </head>

  <body>

    <navigation>
      <ul>
        <li><a href="<?php echo url_for('/index.php'); ?>">Home</a></li>
        <li><a href="<?php echo url_for('/cand_comp.php'); ?>">Candidate Comparison</a></li>
        <li><a href="<?php echo url_for('/ind_cand_stats.php'); ?>">Individual Candidate Statistics</a></li>
      </ul>
    </navigation>