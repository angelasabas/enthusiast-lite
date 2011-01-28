<?php error_reporting( E_ALL ); ?>
<html>
<head>
<title> Sample Pages </title>
</head>

<body>
<center>

<p>
<a href="join.php">Join</a> |
<a href="list.php">List</a> |
<a href="update.php">Update</a> |
<a href="lostpass.php">Lost Password</a> |
<a href="affiliates.php">Affiliates</a>
</p>

<?php
include 'backend/show_category.php';
include 'backend/show_fans.php';
?>
</p>

</center>
</body></html>