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
require( 'config.inc.php' );

// clean function
function clean( $data ) {
   $data = trim( htmlentities( strip_tags( $data ), ENT_QUOTES ) );

   if( get_magic_quotes_gpc() )
      $data = stripslashes( $data );

   $data = addslashes( $data );

   return $data;
}

// automatically clean inputs
foreach( $_GET as $index => $value ) {
   $_GET[$index] = clean( $value );
}
foreach( $_POST as $index => $value ) {
   if( is_array( $value ) ) {
      foreach( $value as $i => $v ) {
         $value[$i] = clean( $v );
      }
      $_POST[$index] = $value;
   } else
      $_POST[$index] = clean( $value );
}
foreach( $_COOKIE as $index => $value ) {
   $_COOKIE[$index] = clean( $value );
}

?>
<html>
<head>
<title> &amp;enthus!ast; » managing the <?= $fanlisting_subject ?> <?=
	$listing_type ?> </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div class="contents">

<img src="enthusiast_logo.gif" width="135" height="125: alt="" />

<div class="menu">

<a href="admin_home.php"><img src="menu_home.gif" width="105" height="20" alt=" home/index" border="0" /></a>

<a href="admin_pending.php"><img src="menu_pending.gif" width="105" height="20" alt=" pending" border="0" /></a>

<a href="admin_members.php"><img src="menu_members.gif" width="105" height="20" alt=" members" border="0" /></a>

<a href="admin_email.php"><img src="menu_email.gif" width="105" height="20" alt=" email" border="0" /></a>

<?php
if( isset( $enable_affiliates ) && $enable_affiliates ) {
?>
<a href="admin_affiliates.php"><img src="menu_affiliates.gif" width="105" height="20" alt=" affiliates" border="0" /></a>
<?php
	}
?>

<a href="admin_logout.php"><img src="menu_logout.gif" width="105" height="20" alt=" logout" border="0" /></a>

</div>