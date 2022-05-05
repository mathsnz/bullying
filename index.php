<?php
// Settings
// These are the settings you need to adjust

$schoolname = "Kāpiti College";		// This is your school name
$host = '172.30.0.91'; 				// This is your SMTP host
$port = '25';						// This is the port for your SMTP host
$from = 'bullying@kc.school.nz';	// This is the email the notifications will come from
$to = 'jake.wills@kc.school.nz';	// This is the email the notifications will go to

// reCAPTCHA Details
// You can register for reCAPTCHA here: https://g.co/recaptcha/v3 ... it's free

$sitekey = "YOUR-SITE-KEY";
$secretkey = "YOUR-SECRET-KEY";

// You shouldn't need to change anything after this point.

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

?>
<html>
<head>
<title>Make A Notification</title>
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0">
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $sitekey; ?>"></script>
    <script>
        grecaptcha.ready(function () {
            grecaptcha.execute('<?php echo $sitekey; ?>', { action: 'contact' }).then(function (token) {
                var recaptchaResponse = document.getElementById('recaptchaResponse');
                recaptchaResponse.value = token;
            });
        });
    </script>
<style>
	body, input, select,textarea{
		font-family: 'Open Sans', sans-serif;
	}
	input, select, textarea{
		width:100%;
		font-size:16px;
		border-radius:3px;
		border:1px solid #ccc;
	}
	body {
		background-color:#fff8f9;
	}
	#content {
		max-width:600px;
		margin: 0 auto;
		background-color:#fff;
		padding:20px;
		box-shadow:0px 0px 5px rgba(0,0,0,0.3);
		border-radius:3px;
	}
	h1{
		margin:0px;
	}
</style>
</head>
<body>
<div id=content>
<?php
if(isset($_POST['name']) && isset($_POST['recaptcha_response'])){
	// reCAPTCHA authentication
	// Build POST request:
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = $secretkey;
    $recaptcha_response = $_POST['recaptcha_response'];

    // Make and decode POST request:
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptcha);

    // Take action based on the score returned:
    if ($recaptcha->score >= 0.5) {
        // Verified - send email
		$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 
					"https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  
					$_SERVER['REQUEST_URI']; 
		$email = "
		<img src='$link/Logo.png' style='width:100px;'>
		<br>
		A form has been submitted on ".$link."<br>
		<br>
		<div style='border:1px solid #ccc;padding:10px;max-width:600px;'>
		<b>Name of the person being bullied or harmed *</b>
		<br>
		".$_POST['name']."
		<br>
		<br>
		<b>Do they go to $schoolname *</b>
		<br>
		".$_POST['goestoschool']."
		<br>
		<br>
		<b>If not, which school do they go to?</b>
		<br>
		".$_POST['school']."
		<br>
		<br>
		<b>What year level are they?</b>
		<br>
		".$_POST['yearlevel']."
		<br>
		<br>
		<b>What happened? </b>
		<br>
		".nl2br($_POST['happened'])."
		<br>
		<br>
		<b>Where did this happen?</b>
		<br>
		".$_POST['where']."
		<br>
		<br>
		<b>How many others were involved? </b>
		<br>
		".$_POST['others']."
		<br>
		<br>
		<b>How often does this happen?</b>
		<br>
		".$_POST['often']."
		<br>
		</div>
		";
		
		require './vendor/autoload.php';

		$mail = new PHPMailer;

		$mail->isSMTP();      		// Set mailer to use SMTP
		$mail->Host = $host;  		// Specify main and backup SMTP servers
		$mail->Port = $port;  		// TCP port to connect to

		$mail->setFrom($from, 'Bullying Notification');
		$mail->addAddress($to);     // Add a recipient

		$mail->isHTML(true);        // Set email format to HTML

		$mail->Subject = 'Bullying Notification - '.stripslashes(strip_tags($_POST['name']));
		$mail->Body    = $email;
		$mail->AltBody = 'Oh dear... you cant read this email!';

		if(!$mail->send()) {
			echo '<center><img src="Logo.png" style="width:100px;"><h1>Error</h1></center><br>Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
?>
			<center><img src='Logo.png' style='width:100px;'><h1>Thanks</h1></center>
			<br>
			Thanks for submitting your notification. This helps keep <?php echo $schoolname; ?> a great place for all students.<br>
			<br>
			If you need to you can <a href="./">submit another notification</a>.
<?php
		}
    } else {
?>
			<center><img src='Logo.png' style='width:100px;'><h1>Oh dear</h1></center>
			<br>
			Seems like you might be a bot... not ideal.. if you're not a bot you can <a href="./">try again</a>.
<?php
    }
} else {
?>
<center><img src='Logo.png' style='width:100px;'><h1>Make A Notification</h1></center>
<br>
The information you are entering is about someone you believe is being bullied or harmed. You are making an anonymous notification however; you must be truthful and responsible.<br>
<br>
<form method=post onsubmit="document.getElementById('button').value='Sending...';document.getElementById('button').disabled = true;">
Name of the person being bullied or harmed * 
<br>
<input type="text" name="name" required>
<br>
<br>
Do they go to <?php echo $schoolname; ?> *
<br>
<select name="goestoschool" required>
  	<option></option>
    <option>Yes</option>
    <option>No</option>
</select>
<br>
<br>
If not, which school do they go to?
<br>
<input type="text" name="school">
<br>
<br>
What year level are they?
<br>
<input type="text" name="yearlevel">
<br>
<br>
What happened? 
<br>
<textarea name="happened"></textarea>
<br>
<br>
Where did this happen?
<br>
<select name="where">
	<option></option>
	<option>In class</option>
	<option>On school grounds</option>
	<option>At home</option>
	<option>Outside school</option>
	<option>Instagram</option>
	<option>Facebook</option>
	<option>Snapchat</option>
	<option>Text messages</option>
	<option>Email</option>
	<option>On the bus / train</option>
	<option>At a party</option>
	<option>Other</option>
</select>
<br>
<br>
How many others were involved? 
<br>
<input type=number name="others">
<br>
<br>
How often does this happen?
<br>
<select name="often">
	<option></option>
	<option>Every hour</option>
	<option>Every day</option>
	<option>Every other day</option>
	<option>Once per week</option>
	<option>Once per month</option>
	<option>I don't know</option>
</select>
<br>
<br>
<input type=checkbox name=checked required style='width:auto;'> I understand that this notification may not be read immediately. If it is an urgent you should contact someone directly. You must check this box before sending a notification.
<br>
<br>
<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
<input id=button type=submit value='Send Notification' style='background-color:#000;color:#fff;border:none;border-radius:20px;padding:10px;font-weight:bold;' >
</form>
<?php
}
?>
</div>
</body>
</html>

// You shouldn't need to change anything after this point.

?>
<html>
<head>
<title>Make A Notification</title>
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0">
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $sitekey; ?>"></script>
    <script>
        grecaptcha.ready(function () {
            grecaptcha.execute('<?php echo $sitekey; ?>', { action: 'contact' }).then(function (token) {
                var recaptchaResponse = document.getElementById('recaptchaResponse');
                recaptchaResponse.value = token;
            });
        });
    </script>
<style>
	body, input, select,textarea{
		font-family: 'Open Sans', sans-serif;
	}
	input, select, textarea{
		width:100%;
		font-size:16px;
		border-radius:3px;
		border:1px solid #ccc;
	}
	body {
		background-color:#fff8f9;
	}
	#content {
		max-width:600px;
		margin: 0 auto;
		background-color:#fff;
		padding:20px;
		box-shadow:0px 0px 5px rgba(0,0,0,0.3);
		border-radius:3px;
	}
	h1{
		margin:0px;
	}
</style>
</head>
<body>
<div id=content>
<?php
if(isset($_POST['name']) && isset($_POST['recaptcha_response'])){
	// reCAPTCHA authentication
	// Build POST request:
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = $secretkey;
    $recaptcha_response = $_POST['recaptcha_response'];

    // Make and decode POST request:
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptcha);

    // Take action based on the score returned:
    if ($recaptcha->score >= 0.5) {
        // Verified - send email
		$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 
					"https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  
					$_SERVER['REQUEST_URI']; 
		$email = "
		<img src='$link/Logo.png' style='width:100px;'>
		<br>
		A form has been submitted on ".$link."<br>
		<br>
		<div style='border:1px solid #ccc;padding:10px;max-width:600px;'>
		<b>Name of the person being bullied or harmed *</b>
		<br>
		".$_POST['name']."
		<br>
		<br>
		<b>Do they go to $schoolname *</b>
		<br>
		".$_POST['goestoschool']."
		<br>
		<br>
		<b>If not, which school do they go to?</b>
		<br>
		".$_POST['school']."
		<br>
		<br>
		<b>What year level are they?</b>
		<br>
		".$_POST['yearlevel']."
		<br>
		<br>
		<b>What happened? </b>
		<br>
		".nl2br($_POST['happened'])."
		<br>
		<br>
		<b>Where did this happen?</b>
		<br>
		".$_POST['where']."
		<br>
		<br>
		<b>How many others were involved? </b>
		<br>
		".$_POST['others']."
		<br>
		<br>
		<b>How often does this happen?</b>
		<br>
		".$_POST['often']."
		<br>
		</div>
		";
		
		require './vendor/autoload.php';

		$mail = new PHPMailer;

		$mail->isSMTP();      		// Set mailer to use SMTP
		$mail->Host = $host;  		// Specify main and backup SMTP servers
		$mail->Port = $port;  		// TCP port to connect to

		$mail->setFrom($from, 'Bullying Notification');
		$mail->addAddress($to);     // Add a recipient

		$mail->isHTML(true);        // Set email format to HTML

		$mail->Subject = 'Bullying Notification - '.stripslashes(strip_tags($_POST['name']));
		$mail->Body    = $email;
		$mail->AltBody = 'Oh dear... you cant read this email!';

		if(!$mail->send()) {
			echo '<center><img src="Logo.png" style="width:100px;"><h1>Error</h1></center><br>Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
?>
			<center><img src='Logo.png' style='width:100px;'><h1>Thanks</h1></center>
			<br>
			Thanks for submitting your notification. This helps keep <?php echo $schoolname; ?> a great place for all students.<br>
			<br>
			If you need to you can <a href="./">submit another notification</a>.
<?php
		}
    } else {
?>
			<center><img src='Logo.png' style='width:100px;'><h1>Oh dear</h1></center>
			<br>
			Seems like you might be a bot... not ideal.. if you're not a bot you can <a href="./">try again</a>.
<?php
    }
} else {
?>
<center><img src='Logo.png' style='width:100px;'><h1>Make A Notification</h1></center>
<br>
The information you are entering is about someone you believe is being bullied or harmed. You are making an anonymous notification however; you must be truthful and responsible.<br>
<br>
<form method=post onsubmit="document.getElementById('button').value='Sending...';document.getElementById('button').disabled = true;">
Name of the person being bullied or harmed * 
<br>
<input type="text" name="name" required>
<br>
<br>
Do they go to <?php echo $schoolname; ?> *
<br>
<select name="goestoschool" required>
  	<option></option>
    <option>Yes</option>
    <option>No</option>
</select>
<br>
<br>
If not, which school do they go to?
<br>
<input type="text" name="school">
<br>
<br>
What year level are they?
<br>
<input type="text" name="yearlevel">
<br>
<br>
What happened? 
<br>
<textarea name="happened"></textarea>
<br>
<br>
Where did this happen?
<br>
<select name="where">
	<option></option>
	<option>In class</option>
	<option>On school grounds</option>
	<option>At home</option>
	<option>Outside school</option>
	<option>Instagram</option>
	<option>Facebook</option>
	<option>Snapchat</option>
	<option>Text messages</option>
	<option>Email</option>
	<option>On the bus / train</option>
	<option>At a party</option>
	<option>Other</option>
</select>
<br>
<br>
How many others were involved? 
<br>
<input type=number name="others">
<br>
<br>
How often does this happen?
<br>
<select name="often">
	<option></option>
	<option>Every hour</option>
	<option>Every day</option>
	<option>Every other day</option>
	<option>Once per week</option>
	<option>Once per month</option>
	<option>I don't know</option>
</select>
<br>
<br>
<input type=checkbox name=checked required style='width:auto;'> I understand that this notification may not be read immediately. If it is an urgent you should contact someone directly. You must check this box before sending a notification.
<br>
<br>
<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
<input id=button type=submit value='Send Notification' style='background-color:#000;color:#fff;border:none;border-radius:20px;padding:10px;font-weight:bold;' >
</form>
<?php
}
?>
</div>
</body>
</html>