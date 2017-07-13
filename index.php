<?php


  ?>
<!DOCTYPE html>
<html>
<head>
	<?php
  header( 'Location: http://www.redirect-location.com' );
  exit();
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700|Nunito:200,300" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- WORDPRESS FUNCTION PULLING IN SCRIPTS -->
<!-- <?php wp_head(); ?> -->


</head>
<body>

<!-- <?php /* Template Name: Login Page */


	$login  = (isset($_GET['login']) ) ? $_GET['login'] : 0;

	$args = array(
	    'id_username' => 'user',
	    'id_password' => 'pass',
	   );
?>
 -->



<div class="row">
	<div class="wrapper">
			
		<?php create_opt_in_form(); ?>

		<div id="loginWrapper">
			<div id="innerBG">
				<p><button class="lightBox login-btn" id="lightBox-form"><a>Click HERE!</a></button></p>
			</div>

		</div>
	</div>

</div>
</body>
</html>