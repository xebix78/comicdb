<?php 
	include '../views/head.php'; 
	$submitted = filter_input ( INPUT_POST, 'submitted' );
?>
	<script src="../material.min.js"></script>
	<title>Comicdb</title>
</head>

<body>
	<?php include '../views/header.php'; ?>

	<div class="mdl-layout">
		<div class="mdl-grid">
			<?php
			if ($login->isUserLoggedIn() == false) {
				include '../views/not_logged_in.php';
				die();
			}
			if (!$submitted) {
				$filename=$_SERVER["PHP_SELF"];
				?>
				<form method="post" action="<?php echo $filename; ?>">
					<label>Series Name</label>
					<input name="series_name" type="text" size="50" value=""/>
					<input type="hidden" name="submitted" value="yes" />
					<input type="submit" name="submit" value="Submit" />
				</form>
				<?php
			} else {
				$series_name = filter_input ( INPUT_POST, 'series_name' );
				$sql = "INSERT INTO series (series_name)
				VALUES ('$series_name')";

				if (mysqli_query ( $connection, $sql )) {
					echo $series_name . " series created in database.";
				} else {
					echo "Error: " . $sql . "<br>" . mysqli_error ( $connection );
				}
			}
			include '../views/footer.php';
			?>
		</div>
	</div>
</body>
</html>
