#!/usr/local/bin/php

<?php require_once('../private/initialize.php'); ?>

<?php $page_title = 'Main Menu'; ?>
<?php include(SHARED_PATH . '/header.php'); ?>

<div id="content">
  <div id="main-menu">
    <h2>Test Page</h2>
    <ul>
      <li><a href="<?php echo url_for('/test.php'); ?>">Candidate Comparison</a>
      </li>
      <li><a href="">Individual Candidate Page</a>
      </li>
    </ul>
  </div>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
