<?php
/*****************************************************************************
 Enthusiast Lite: Fanlisting Management System
 Copyright (c) by Angela Sabas
 http://scripts.indisguise.org

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.

 For more information please view the readme.txt file.
******************************************************************************/
session_start();
if( !isset( $_SESSION["login_message"] ) )
	$_SESSION["login_message"] = '';

require_once( 'config.inc.php' );
if( isset( $_COOKIE["login_password"] ) &&
	$_COOKIE['login_password'] == md5( $set_password . 'ENTH2' ) ) {
	header( 'location: admin_home.php');
	die();
}

require_once( 'header.inc.php' );
?>

<p class="title">Welcome to Enthusiast!<br />
<?= $fanlisting_title ?>: the <?= $fanlisting_subject ?> <?= $listing_type
	?></p>

<p><span class="important"><?= htmlentities( $_SESSION["login_message"] ) ?></span></p>

<form action="admin_login.php" method="post">

<p>Please log in:</p>

<p>
<table border="0">

<tr><td>Password</td></tr>

<tr><td><input type="password" name="login_password" /></td></tr>

<tr><td colspan="2">
<input type="submit" value="Log in" /><br />
<input type="checkbox" name="rememberme" value="yes" /> Remember me?
</td></tr>

</table>
</p>

</form>

<?php
require_once( 'footer.inc.php' );
?>