<?php 
  $comic = new comicSearch ();
  $comic->issueLookup ( $comic_id );
  $comic->seriesInfo ( $comic->series_id );

  if (isset($comic->publisherName)) {
    $publisherName = $comic->publisherName;
    $publisherShort = $comic->publisherShort;
  } else {
    $messageNum = 60;
  }
  // Standardizes values for common variables for use in notifications
  if (isset($comic->series_name) || isset($comic->series_vol) || isset($comic->issue_number)) {
    $series_name = $comic->series_name;
    $series_vol = $comic->series_vol;
    $issue_num = $comic->issue_number;
  } else {
    $messageNum = 99;
  }

?>
<div class="row">
  <div class="col-sm-12 headline">
    <h2><?php echo $series_name . " #" . $issue_num; ?></h2>
    <div class="series-meta">
      <ul class="nolist">
        <?php if ($publisherName) { echo '<li class="logo-' . $publisherShort .'">' . $publisherName . '</li>'; } ?>
        <li>Volume <?php echo $series_vol; ?></li>
        <?php if ($comic->release_date) { ?>
          <li><?php echo DateTime::createFromFormat('Y-m-d', $comic->release_date)->format('M Y'); ?></li>
        <?php } ?>
      </ul>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-8">
    <div class="issue-story"><h4><?php echo $comic->story_name; ?></h4></div>
    <div class="issue-description">
      <?php if ($comic->plot != '') {
        echo $comic->plot; 
      } else {
        echo '<p>Plot details have not been entered.</p>';
      }
      ?>
    </div>
    <p>
      <?php
        if ($login->isUserLoggedIn () == true) { ?>
          <a href="/comic.php?comic_id=<?php echo $comic->comic_id; ?>&wiki_id=<?php echo $comic->wiki_id; ?>&type=edit" class="btn btn-default">Update Info</a>
        <?php } 
      ?>
    </p>
  </div>
  <div class="col-md-4 issue-image">
    <img src="<?php echo $comic->cover_image; ?>" alt="cover" />
  </div>
</div>