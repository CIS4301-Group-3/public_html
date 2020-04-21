#!/usr/local/bin/php

<?php require_once('../private/initialize.php');

  $list_candidates = list_candidates();
  $count = 0;

?>

<?php $page_title = 'Main Menu'; ?>
<?php include(SHARED_PATH . '/header.php'); ?>

<div id="container" style="margin-left: 10%">
  <div id="main-menu">
    <p>Our intent is to design and deploy a web application that allows users to gain insight into the
        finances of the 2020 Presidential Primary. In an election season with so many candidates,
        having a tool able to assist in analyzing campaign nance trends would be an invaluable
        asset. Each candidate is required to report information about individual donations to the
        Federal Election Commission. This report includes detailed information about each individ-
        ual donor, including their name, the donation amount, city, and state as well as employer
        and occupation. We are interested in seeing comparisons to individual campaign events as
        well as coverage of candidates and their competitors in the news media. Lastly, we believe we
        will be able to gain insight into the donors by comparing their employers and occupations.
    </p>
    <h2><a href="<?php echo url_for('/cand_comp.php'); ?>">Candidate Comparison</a></h2>
    <h2>Individual Candidate Page</h2>
    <ul>
    <?php while($cand = oci_fetch_array($list_candidates, OCI_ASSOC+OCI_RETURN_NULLS)) { ?>
        <li>
          <a class="action"
            href="<?php echo url_for('/ind_cand_stats.php?id=' . $count); ?>">
            <?php echo $cand['CANDIDATE']; ?></a>
        </li>
      <?php $count++;
            }
            oci_free_statement($list_candidates); ?>
    </ul>
  </div>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
