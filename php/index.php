<!DOCTYPE html>
<html lang="en">
<head>
	<title>Shift Tips</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
	<div id="header">
		<div class="name"><a href=".">Shift Tips</a></div>
		<ul class="menu">
			<li><a class="link-button" href="shifts.php">Shifts</a></li>
			<li><a class="link-button" href="summary.php">Summary</a></li>
			<li><a class="link-button" href="add.php">Add</a></li>
		</ul>
	</div>
	<div id="content">
		<div id="wrapper">
			<h1>PHP Home Page</h1>
			<table class="old-links">
				<tr><td><a class="link-button button-inverse" href="../">Angular Home Page</a></td></tr>
				<tr><th><h2>Incomplete Pages</h2></th></tr>
				<tr><td><a class="link-button" href="column-nonscrollable-summary.php">Column Summary</a></td></tr>
				<tr><td><a class="link-button" href="one-row-summary.php">One Row Summary</a></td></tr>
				<tr><td><a class="link-button" href="weekly.php">Weekly Summary</a></td></tr>
				<tr><td><a class="link-button" href="monthly.php">Monthly Summary</a></td></tr>
			</table>
		</div>
	</div>
	<div id="footer">
		<a class="debug" href="#" target="popup" onClick="wopen('#', 'popup', 320, 480); return false;">debug mobile popup</a>
	</div>
	<script>
		function wopen(url, name, w, h)
		{
			//Fudge factors for window decoration space.
			w += 32;
			h += 96;
			var win = window.open(url, name, 'width=' + w + ', height=' + h + ', ' +
				'location=no, menubar=no, ' + 'status=no, toolbar=no, scrollbars=yes, resizable=yes');
			win.resizeTo(w, h);
			win.focus();
		}
	</script>
</body>
</html>