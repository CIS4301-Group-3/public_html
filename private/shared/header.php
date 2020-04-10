<?php
    if(!isset($page_title)) { $page_title = 'Election Finance Insight'; }
?>

<!doctype html>

<html lang="en">
  <head>
    <title>GBI - <?php echo h($page_title); ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" media="all" href="<?php echo url_for('/stylesheets/site.css'); ?>" />
  </head>

  <body>
    <header>
      <h1>GBI Staff Area</h1>
    </header>

    <navigation>
      <ul>
        <li><a href="<?php echo url_for('/index.php'); ?>">Main Page</a></li>
      </ul>
    </navigation>