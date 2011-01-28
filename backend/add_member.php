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

$show_form = true;
$message = '';

$name = '';
$email = '';
$country = '';
$password = '';
$vpassword = '';
$url = '';
$comments = '';
if( $additional_field ) {
	$additional_fields = array();
	foreach( $additional_field_names as $field )
		$additional_fields["$field"] = ' ';
	}

if( isset( $_POST["join"] ) && $_POST["join"] == "yes" ) {

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


	if( $_POST["name"] )
		$name = clean( $_POST["name"] );
	else
		$message = 'You must enter your name.<br />';

   $matchstring = "^([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z]+" .
      "@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}$";
   if( $_POST['email'] && ereg( $matchstring, $_POST['email'] ) )
      $email = clean( $_POST['email'] );
   else
      $message .= 'You must enter a valid email address.<br />';

	if( isset( $_POST["country"] ) && $_POST["country"] )
		$country = clean( $_POST["country"] );
	else if( !isset( $disable_country) || !$disable_country )
		$message .= 'You must enter your country.<br />';

	if( $_POST["password"] )
		$password = clean( $_POST["password"] );
	else
		$message .= 'You must enter a password to change your ' .
			'information.<br />';

	if( $_POST["password"] && $_POST["vpassword"] &&
		$_POST["password"] == $_POST["vpassword"] )
		$vpassword = clean( $_POST["vpassword"] );
	else if( $_POST['password'] == '' && $_POST['vpassword'] == '' ) {
		// no password, must generate
		$password = '';
		$k = 0;
		while( $k <= 10 ) {
			$password .= chr( rand( 97, 122 ) );
			$k++;
			}
		$vpassword = $password;
		}
	else
		$message .= 'Your password verification does not match.';

	if( $_POST["url"] ) {
		$url = clean( $_POST["url"] );
		if( substr_count( $url, 'http://' ) == 0 )
			$url = 'http://' . $url;
		}

	if( $additional_field ) {
		foreach( $additional_field_names as $field ) {
			$additional_fields["$field"] = clean( $_POST["$field"] );
			}
		}

	if( isset( $_POST["comments"] ) ) {
		$comments = clean( $_POST["comments"] );
		}

	if( $name && $email && $password && $vpassword &&
		$password == $vpassword && substr_count( $email, '@' ) > 0 &&
		( ( ( !isset( $disable_country ) || !$disable_country ) &&
		$country ) || ( isset( $disable_country ) &&
		$disable_country ) ) ) {

		$show_form = false;
		$show_email = clean( $_POST["show_email"] );

		$query = 'INSERT INTO ' . $db_table . ' VALUES( ';
		$query .= '"' . $email . '", ';
		$query .= '"' . $name . '", ';
		if( $country )
			$query .= '"' . $country . '", ';
		$query .= '"' . $url . '", ';

		if( $additional_field ) {
			foreach( $additional_field_names as $field ) {
				$query .= '"' . $additional_fields["$field"] .
				'", ';
				}
			}

		$query .= '1, PASSWORD( "' . $password . '" ), ' .
			$show_email . ', 1, NULL )';

		$db_link = mysql_connect( $db_server, $db_user, $db_password )
			or die( 'Cannot connect to the database. Try again.' );
		mysql_select_db( $db_database )
			or die( 'Cannot connect to the database. Try again.' );
		$result = mysql_query( $query );

		// if addition is successful
		if( $result ) {

			// notify owner
			$notify_subject = $fanlisting_subject . ": New" .
				" member!";
			$notify_message = "Someone has joined your " .
				$fanlisting_subject . ' ' . $listing_type .
				" (\"" .
				$fanlisting_title . "\"). Relevant " .
				"information is below:\r\n\r\n" .
				"Name: " . $name . "\r\n" . 
				"Email: " . $email . "\r\n" .
				"Country: " . $country . "\r\n" .
				"URL: " . $url . "\r\n";

			if( $additional_field ) {
				foreach( $additional_field_names as $field ) {
					$notify_message .= ucfirst( $field ) .
						": " .
						$additional_fields["$field"] .
						"\r\n";
					}
				}

			$notify_message .= "Comments: " . $comments . "\r\n";

			$notify_message .= "\r\nTo add this member, go to " .
				$fanlisting_url . "/" . $backend_dir .
				"admin_pending.php\r\n";
			$notify_message = stripslashes( $notify_message );

			@mail( $fanlisting_email, $notify_subject,
				stripslashes( $notify_message ) );

			// create email
			$to = $email;
			$subject = $fanlisting_title . ' ' .
				ucfirst( $listing_type ) . ' Information';
			$headers = 'From: ' . $fanlisting_title . ' <' .
				$fanlisting_email . '>';

			$signup_file = $backend_dir . 'emails/signup.txt';
			$signup_email = fopen( $signup_file, 'r' );
			$message = fread( $signup_email, filesize(
				$signup_file ) );
			fclose( $signup_email );

			require_once( $backend_dir . 'parse_email.php' );
			$message = parse_email( $message, $email, $password );
			$message = stripslashes( $message );

			$success_mail = mail( $to, $subject, $message,
				$headers );

			if( !$success_mail ) {
?>
				<p>
				Your form has been processed correctly, but
				unfortunately there was an error sending your
				application information to you. If you wish
				to receive information about your application,
				please feel free to 
				<a href="mailto:<?= str_replace( '@', '{ at }',
				$fanlisting_email ) ?>">email me</a> and
				I will personally look into it.
				</p>

				<p>
				If two weeks have passed and you have not yet
				been added, please feel free to check up on
				your application.
				</p>
<?php
				}
			else {
?>
				<p>
				The application form for the <?=
				$fanlisting_subject ?> <?= $listing_type ?> has been
				sent. You will be notified when you have been
				added into the official members list. If two
				weeks have passed and you have received no
				email, please email me if you wish to check
				up on your form.
				</p>

				<p>
				An email has also been sent to you with your
				information. Please do not lose this
				information.
				</p>
<?php
				}
			}
		else {
			if( mysql_errno() != 1062 ) {
?>
				<p>
				An error occured while attempting to add you
				to the pending members queue. Unfortunately,
				this was caused by a database error on this
				<?= $listing_type ?>. Please notify the fanlisting
				owner immediately.</p>
<?php
				}
			else {
				$message = 'An error occured while ' .
					'attempting to add you to the ' .
					'pending members queue. This is ' .
					'because you are already a ' .
					'member (approved or unapproved) or ' .
					'someone used your email address to ' .
					'join this ' . $listing_type . ' ' .
					'before. If you wish to update your ' .
					'information, please go <a href="' .
					$update_url . '">here</a>.';
				$show_form = true;
				}
			}

		mysql_close( $db_link );

		}
	else {
		$message = 'ERROR: ' . $message;
		}
	}

if( $show_form ) {
?>

	<p>
	Please use the form below for joining the <?= $listing_type ?>.
	<b>Please hit the submit button only once.</b> Your entry is fed
	instantly into the database, and your email address is checked for
	duplicates. Passwords are encrypted into the database and will not be
	seen by anyone else other than you.
	</p>

	<p>
	If you encounter problems, please feel free to <a href="mailto:<?=
	str_replace( '@', ' {at} ', $fanlisting_email ) ?>">email me</a>.
	</p>

	<p>
	The fields with asterisks (*) are required fields.
	</p>

	<p>
	<b><?= $message ?></b>
	</p>

	<form method="post" action="<?= $join_url ?>">
	<input type="hidden" name="join" value="yes" />

	<p class="noindent">
	* Name: <br />
	<input type="text" name="name" value="<?= $name ?>" />
	</p>

	<p class="noindent">
	* Email address: <br />
	<input type="text" name="email" value="<?= $email ?>" />
	</p>

	<p class="noindent">
	Show email address on the list?<br />
	<input type="radio" name="show_email" value="1" class="noborder"
		checked="checked" /> Yes (SPAM-protected on the site)<br />
	<input type="radio" name="show_email" value="0" class="noborder" /> No
	</p>

<?php
	if( !isset( $disable_country ) || !$disable_country ) {
?>

		<p class="noindent">
		* Country<br />
		<select name="country">
<?php
		if( $country != '' )
			echo '<option selected="selected">' . $country .
				'</option>';
?>
		<option value=""></option>
<?php
		include $backend_dir . 'countries.inc.php';
?>
		</select>
		</p>

<?php
		}
?>

	<p class="noindent">
	* Password (to change your details; type twice): <br />
	<input type="password" name="password" />
	<input type="password" name="vpassword" />
	</p>

	<p class="noindent">
	Website URL: <br />
	<input type="text" name="url" value="<?= $url ?>" />
	</p>

<?php
	if( $additional_field && !( file_exists( 'addform.inc.php' ) ) ) {
		reset( $additional_fields );
		do {
?>
			<p class="noindent">
			<?= ucfirst( key( $additional_fields ) ) ?>:<br />
			<input type="text" name="<?=
				key( $additional_fields ) ?>" value="<?=
				current( $additional_fields ) ?>" />
			</p>
<?php
			} while( next( $additional_fields ) );
		}
	elseif( $additional_field && file_exists( 'addform.inc.php' ) ) {
		require( 'addform.inc.php' );
		}
?>

	<p class="noindent">
	Comments:<br />
	<textarea name="comments" rows="3" cols="40"><?= $comments
        ?></textarea>
	</p>

	<p class="noindent">
	<input type="submit" value="Join the <?= $listing_type ?>" />
	<input type="reset" value="Clear form" />
	</p>

	</form>

	<p align="center">
	<a href="http://scripts.indisguise.org"><img
	src="<?= $backend_dir ?>elink.gif" width="85" height="15" border="0"
	alt=" enthusiast" /></a>
	</p>
<?php
	}
?>