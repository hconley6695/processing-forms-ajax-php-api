
<?php


function create_opt_in_form() {

	// CREATING FORM THAT APPEARS IN A LIGHTBOX PRIOR TO SIGNING UP FOR A MEMBERSHIP
	$outputHTML = '';

	$outputHTML .=	'<div id="System-free-div">
						<div class="System-container">
							<h2>Form appears here!</h2>
							<div class="error-div">
								<span class="valid" id="name-error">Please fill out the required field.</span>
								<span class="valid" id="email-error">Please fill out a valid email address.</span>
							</div>
							<form accept-charset="UTF-8" action="wme_create_contact" class="opt-in-submit optin-form" method="POST">
								<div class="field wtc-popup valid">
									<span>
										<span class="icon-profile-icon"></span>
										<input class="field-input-container" id="field_FirstName" name="inf_field_FirstName" type="text" placeholder="First Name" />
									</span>
								</div>
								<div class="field wtc-popup valid">
									<span>
										<span class="icon-envelop"></span>

										<input class="field-input-container" id="field_Email" name="inf_field_Email" type="text" placeholder="Email" />
									</span>
								</div>
								<div class="wtc-popup">
									<a href=redirect_link><input type="submit" id="wtc-popup-submit" class="wtc-popup-submit" value="TRY IT" /></a>
								</div>
							</form>
						</div>
					</div>

					<script>
						jQuery(document).ready(function() {

							jQuery("body").on("click", ".lightBox", function(event){
								event.preventDefault();
								jQuery("#System-free-div").fadeIn(500);
								jQuery("body").addClass("pointer");
							});
							jQuery("body").on("click", function (e) {
								if (jQuery(e.target).has(".WTC-System-container").length) {
									jQuery("#System-free-div").fadeOut(500);
									jQuery("body").removeClass("pointer");
								}
							})

						});

					</script>';
	echo $outputHTML;

}


add_action('wp_ajax_wme_create_contact','guw_ajax_submit_opt_in' );
add_action('wp_ajax_nopriv_wme_create_contact','guw_ajax_submit_opt_in' );

function guw_ajax_submit_opt_in() {
	// CONNECT TO ISDK API
	global $i2sdk;
	$app = $i2sdk->isdk;

	// PULLING DATA FROM AJAX REQUEST ON FRONTED
	$first_name = trim($_POST['firstname']);
	$email = trim($_POST['email']);

	// DEFINING THE FIELDS IN INFUSIONSOFT THAT WE WANT TO SEE
	$returnFields = array('Id', 'FirstName', 'LastName');
	// FIND A CONTACT BY EMAIL ADDRESS
	$data = $app->findByEmail($email, $returnFields);

	// IF THERE IS NO MATCHING EMAIL ADDRESS, CREATE A NEW CONTACT
	if (empty($data)) {
		// CREATE A PASSWORD
		function give_new_contact_pw($length = 12, $add_dashes = false, $available_sets = 'luds') {
			$sets = array();
			if(strpos($available_sets, 'l') !== false)
				$sets[] = 'abcdefghjkmnpqrstuvwxyz';
			if(strpos($available_sets, 'u') !== false)
				$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
			if(strpos($available_sets, 'd') !== false)
				$sets[] = '23456789';
			if(strpos($available_sets, 's') !== false)
				$sets[] = '!@#$%&*?';

			$all = '';
			$password = '';
			foreach($sets as $set) {
				$password .= $set[array_rand(str_split($set))];
				$all .= $set;
			}

			$all = str_split($all);
			for($i = 0; $i < $length - count($sets); $i++)
				$password .= $all[array_rand($all)];

			$password = str_shuffle($password);

			if(!$add_dashes)
				return $password;

			$dash_len = floor(sqrt($length));
			$dash_str = '';
			while(strlen($password) > $dash_len) {
				$dash_str .= substr($password, 0, $dash_len) . '-';
				$password = substr($password, $dash_len);
			}
			$dash_str .= $password;
			return $dash_str;
		}

		// CREATE A PASSWORD USING ABOVE FUNCTION
		$random = give_new_contact_pw();
		// PUT FIRST NAME AND EMAIL FROM THE FORM AND THE NEW PASSWORD INTO ONE ARRAY
		$contactData = array('FirstName' => $first_name,
		                'Email'     => $email, 'Password' => $random);
		// INFUSIONSOFT API--ADD THE CONTACT DATA AND CREATE A NEW CONTACT
		$conID = $app->addCon($contactData);
		// THE ID FROM THE NEW CONTACT
		$returnNewContactID = array('Id');
		// USING THE EMAIL, FIND THE NEW CONTACT'S ID AND PUT IT IN A VARIABLE
		$newContactID = $app->findByEmail($email, $returnNewContactID);
		// ADD A NEW TAG TO THE CONTACT
		$tagId = 366;
		$newTaggedContact = $app->grpAssign($conID, $tagId);

		// DATA TO BE RETURNED TO THE FRONTEND
		$return_data = ['status'=>'success','msg'=>'success', 'data'=>array('ID'=>$newContactID, 'Email'=>$email)];

	} else {
		// IF THERE IS A DUPLICATE EMAIL ADDRESS, THIS IS THE DATA TO BE RETURNED TO THE FRONTEND
		$return_data = "You are already a member! Please login with your current login and password.";
	}
		// SEND DATA IN JSON FORMAT TO THE FRONTEND
		echo json_encode($return_data);
// EXIT
	exit;
}


?>