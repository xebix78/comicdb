<?php
  require_once('views/head.php');
  $filename = $_SERVER["PHP_SELF"];
  $comic_id = filter_input ( INPUT_GET, 'comic_id' );
  $comic = new comicSearch ();
  $comic->issueLookup ( $comic_id );

  $editMode = filter_input ( INPUT_GET, 'type' );
  if ($editMode == 'edit') { include('admin/formprocess.php'); }
?>
  <title><?php echo $comic->series_name . " #" . $comic->issue_number; ?> :: POW! Comic Book Manager</title>
</head>

<body>
  <?php include 'views/header.php';?>
  <div class="container content">
    <?php if ($editMode == 'edit') { ?>
      <div class="col-sm-12 headline">
        <h2>Add Issue: <?php echo $series_name; ?> (Vol <?php echo $series_vol; ?>) #<?php echo $issue_number; ?></h2>
      </div>
      <div class="col-md-8 col-sm-12">
        <form method="post" action="<?php echo $filename; ?>?comic_id=<?php echo $comic->comic_id; ?>&type=edit-save">
          <div class="form-group">
            <label for="story_name">Story Name: </label>
            <input class="form-control" name="story_name" type="text" maxlength="255" value="<?php echo $storyName; ?>" />
          </div>
          <div class="form-group">
            <label for="released_date">Release Date:</label>
            <input class="form-control" name="released_date" size="10" maxlength="10" value="<?php if ($release_date) { echo $release_date; } ?>" type="date" placeholder="YYYY-MM-DD" />
          </div>
          <div class="form-group form-radio">
            <label for="original_purchase">Purchased When Released:</label>
            <fieldset>
              <input name="original_purchase" id="original-yes" value="1" type="radio" <?php if ($original_purchase == 1) { echo 'selected'; } ?> /> <label for="original-yes">Yes</label>
              <input name="original_purchase" id="original-no" value="0" type="radio" <?php if ($original_purchase == 0) { echo 'selected'; } ?> /> <label for="original-no">No</label>
            </fieldset>
          </div>
          <div class="plot form-group">
            <label for="plot">Plot:</label>
            <small><a href="#">[edit]</a></small>
            <?php echo $wiki->synopsis; ?>
          </div>
          <input type="hidden" name="series_name" value="<?php echo $series_name; ?>" />
          <input type="hidden" name="series_vol" value="<?php echo $series_vol; ?>" />
          <input type="hidden" name="issue_number" value="<?php echo $issue_number; ?>" />
          <input type="hidden" name="cover_image" value="<?php echo $wiki->coverURL; ?>" />
          <input type="hidden" name="cover_image_file" value="<?php echo $wiki->coverFile; ?>" />
          <input type="hidden" name="plot" value="<?php echo htmlspecialchars($wiki->synopsis); ?>" />
          <input type="hidden" name="series_id" value="<?php echo $series_id; ?>" />
          <input type="hidden" name="wiki_id" value="<?php echo $wiki_id; ?>" />
          <input type="hidden" name="submitted" value="yes" />
          <div class="text-center center-block">
            <a href="#" class="btn btn-default form-back">&lt; Back</a>
            <input type="submit" name="submit" value="Submit" class="btn btn-primary form-submit" />
          </div>
        </form>
      </div>
      <div class="col-md-4 issue-image">
        <img src="<?php echo $wiki->coverURL; ?>" alt="Cover" />
      </div>
    <?php } else { include 'views/single_comic.php'; } ?>
  </div>
  <?php include 'views/footer.php';?>
</body>
</html>