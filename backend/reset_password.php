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
require_once( 'config.inc.php' );

// clean function
function clean( $data ) {
   $data = trim( htmlentities( strip_tags( $data ), ENT_QUOTES ) );

   if( get_magic_quotes_gpc() )
      $data = stripslashes( $data );

   $data = addslashes( $data );

   return $data;
}

$show_form = true;
$message = '';

if( isset( $_POST["email"] ) && $_POST["email"] != '' ) {

   // do some spam/bot checking first
   $goahead = false;
   $badStrings = array( 'Content-Type:',
      'MIME-Version:',
      'Content-Transfer-Encoding:',
      'bcc:',
      'cc:',
      'content-type',
      'onload',
      'onclick',
      'javascript' );
   // 1. check that user is submitting from browser
   // 2. check the POST was indeed used
   // 3. no bad strings in any of the form fields
   if( isset( $_SERVER['HTTP_USER_AGENT'] ) &&
      $_SERVER['REQUEST_METHOD'] == 'POST' ) {
      foreach( $_POST as $k => $v ) {
         foreach( $badStrings as $v2 ){
            if( strpos( $v, $v2 ) !== false ) {
               die( "<p$errorstyle>Bad strings found in form.</p>" );
               exit;
            }
         }
      }
      $goahead = true;
   }
   unset( $k, $v, $v2, $badStrings );
   if( !$goahead ) {
      echo "<p$errorstyle>ERROR: Attempted circumventing of the form detected.</p>";
      return;
   }

	$email = clean( $_POST["email"] );
	$show_form = false;

	// create password
	$password = '';
	$k = 0;
	while( $k <= 10 ) {
		$password .= chr( rand( 97, 122 ) );
		$k++;
		}

	// create sql query
	$query = 'UPDATE `' . $db_table . '` SET `password` = PASSWORD( "' .
		$password . '" ) WHERE email = "' . $email . '"';

	$db_link = mysql_connect( $db_server, $db_user, $db_password )
		or die( 'Cannot connect to the database. Try again.' );
	mysql_select_db( $db_database )
		or die( 'Cannot connect to the database. Try again.' );

	mysql_query( $query );
	$affected_rows = mysql_affected_rows();

	if( $affected_rows > 0 ) {
		$show_form = false;

		$to = $email;
		$subject = $fanlisting_title .
			' FL: Password reset';
		$headers = "From: " . $fanlisting_title . " <" .
			$fanlisting_email . ">\r\n";

		$lostpass_file = $backend_dir . 'emails/lostpass.txt';
		$lostpass_email = fopen( $lostpass_file, 'r' );
		$body = fread( $lostpass_email, filesize(
			$lostpass_file ) );
		fclose( $lostpass_email );

		require_once( $backend_dir . 'parse_email.php' );
		$body = parse_email( $body, $to, $password );

		$success_mail = mail( $to, $subject, $body,
			$headers );

		if( $success_mail ) {
?>
			<p>
			Your password has been successfully reset
			and an email has been sent to you containing the
			generated password.
			</p>
<?php
			}
		else {
?>
			<p>
			Your password has been successfully reset,
			however, there was a problem sending you an
			email regarding this. Please contact us instead by
			<a href="mailto:<?= str_replace( '@', '{ at }',
			$fanlisting_email ) ?>">email</a>.
			</p>
<?php
			}
		}
	else {
?>
		<p>
		There was an error in your request to reset your
		password. This may be because there is no member
		recorded in the <?= $listing_type ?> with that email address.
		Please check your spelling and try again.
		</p>
<?php
		$show_form = true;
		} // end of if email and password is present

	mysql_close( $db_link );

	} // end of if update = yes

if( $show_form ) {
?>
	<p>
	If you have lost or forgotten your password, you can reset your
	password using this form. The new generated password will be sent to
	you, and we advise you to immediately change/update your password
	once you receive this.
	</p>

	<p>
	Enter your email address on the field below to generate
	a password.
	</p>

	<p>
	<b><?= $message ?></b>
	</p>

	<form method="post" action="<?= $lostpass_url ?>">

	<p class="noindent">
	Email address:<br />
	<input type="text" name="email" />
	<input type="submit" value="Reset my password" />
	</p>

	</form>

<?php
	}
?>