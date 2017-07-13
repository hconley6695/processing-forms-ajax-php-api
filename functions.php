
<?php


function create_opt_in_form() {

	// GUW CUSTOM CODE --CREATING OPT IN FORM THAT APPEARS IN A LIGHTBOX PRIOR TO SIGNING UP FOR A MEMBERSHIP
	$outputHTML = '';

	$outputHTML .=	'<div id="WTC-System-free-div">
						<div class="WTC-System-container">
							<h2>Try the WTC System for Free!</h2>
							<div class="error-div">
								<span class="valid" id="name-error">Please fill out the required field.</span>
								<span class="valid" id="email-error">Please fill out a valid email address.</span>
							</div>
							<form accept-charset="UTF-8" action="wme_create_contact" class="infusion-form opt-in-submit optin-infusion-form" method="POST">
								<input name="sub_nonce" type="hidden" value="1" />
								<input name="inf_form_xid" type="hidden" value="5f69307793941614162d62b0dfa52be8" />
								<input name="inf_form_name" type="hidden" value="Subscriber Opt In Form Submitted" />
								<input name="infusionsoft_version" type="hidden" value="1.59.0.51" />
								<div class="infusion-field wtc-popup valid">
									<span>
										<span class="icon-profile-icon"></span>
										<input class="infusion-field-input-container" id="inf_field_FirstName" name="inf_field_FirstName" type="text" placeholder="First Name" />
									</span>
								</div>
								<div class="infusion-field wtc-popup valid">
									<span>
										<span class="icon-envelop"></span>

										<input class="infusion-field-input-container" id="inf_field_Email" name="inf_field_Email" type="text" placeholder="Email" />
									</span>
								</div>
								<div class="infusion-submit wtc-popup">
									<a href=redirect_link><input type="submit" id="wtc-popup-submit" class="wtc-popup-submit" value="TRY IT" /></a>
								</div>
							</form>
							<script type="text/javascript" src="https://df152.infusionsoft.com/app/webTracking/getTrackingCode?trackingId=2e75877995f3556f7c02c4b01cb6df5d"></script>
						</div>
					</div>

					<script>
						jQuery(document).ready(function() {

							jQuery("body").on("click", ".lightBox", function(event){
								event.preventDefault();
								jQuery("#WTC-System-free-div").fadeIn(500);
								jQuery("body").addClass("pointer");
							});
							jQuery("body").on("click", function (e) {
								if (jQuery(e.target).has(".WTC-System-container").length) {
									jQuery("#WTC-System-free-div").fadeOut(500);
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
	global $i2sdk;
	$app = $i2sdk->isdk;

	$first_name = trim($_POST['firstname']);
	$email = trim($_POST['email']);

	$returnFields = array('Id', 'FirstName', 'LastName');
	$data = $app->findByEmail($email, $returnFields);

	if (empty($data)) {

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

		$random = give_new_contact_pw();
		$contactData = array('FirstName' => $first_name,
		                'Email'     => $email, 'Password' => $random);
		$conID = $app->addCon($contactData);

		$returnNewContactID = array('Id');
		$newContactID = $app->findByEmail($email, $returnNewContactID);

		$tagId = 366;
		$newTaggedContact = $app->grpAssign($conID, $tagId);

		$return_data = ['status'=>'success','msg'=>'success', 'data'=>array('ID'=>$newContactID, 'Email'=>$email)];

	} else {
		$return_data = "You are already a member! Please login with your current login and password.";
	}

		echo json_encode($return_data);

	exit;
}


?>