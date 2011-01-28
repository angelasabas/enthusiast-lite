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

<p>
This is simply a sample page for you to see how the script works, its
features, etc. Feel free to use these scripts as templates.
</p>

<blockquote>
<b>Fanlisting statistics</b><br />
Last updated:
<?php
include 'backend/get_last_updated.php';
?><br />
Member count:
<?php
include 'backend/get_member_count.php';
?><br />
Pending members:
<?php
include 'backend/get_pending_count.php';
?><br />
Newest members:
<?php
include 'backend/get_newest_members.php';
?>
</blockquote>

</center>
</body></html>