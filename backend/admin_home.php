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
require_once( 'config.inc.php' );
if( !isset( $_COOKIE["login_password"] ) ||
	$_COOKIE['login_password'] != md5( $set_password . 'ENTH2' ) ) {
	$_SESSION["login_message"] = 'Please log in first before viewing ' .
		'anything.';
	header( 'location: index.php' );
	die( 'Redirecting you...' );
	}
require_once( 'header.inc.php' );
?>

<p class="location">Enthusiast > Home</p>

<?php
$today = date( 'F j, Y (l)' );
if( date( 'a' ) == 'am' )
	$greeting = 'Good morning';
else {
	if( date( 'G' ) <= 18 )
		$greeting = 'Good afternoon';
	else
		$greeting = 'Good evening';
	}
?>
<p><?= $greeting ?>! Today is <?= $today ?>.</p>

<p>
You are managing:<br />
<?= $fanlisting_title ?>: the <?= $fanlisting_subject ?> <?= $listing_type ?>.
</p>


<?php
require_once( 'footer.inc.php' );
?>