jQuery(function(){

	// CHECKS FOR LEGITIMATE EMAIL
			function isValidEmailAddress(emailAddress) {
			    var pattern = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()\.,;\s@\"]+\.{0,1})+[^<>()\.,;:\s@\"]{2,})$/;
			    return pattern.test(emailAddress);
			};

			// ON SUBMISSION OF THE FORM
			jQuery(".optin-form").on("submit", function(event){
				// DON'T SUBMIT YET
				event.preventDefault();

				//GET THE VALUES OF THE INPUTS IN THE FORM
      			var first_name = jQuery("#field_FirstName", this).val();
				var email = jQuery("#field_Email", this).val();
				var passedValidation = true;

				// VALIDATE THE FORM ON THE FRONTEND THAT IT IS FILLED OUT AND THAT THE EMAIL IS AN EMAIL ADDRESS
				// IF THEY ARE NOT, AN ERROR OCCURS
				// if more than one form on page with same id, add "this" to the function
				if(jQuery("#field_FirstName", this).length >= 1 && jQuery("#field_Email", this).length >= 1)   {
					
						if (first_name == '') {
							passedValidation = false;
							jQuery("div.error-div #name-error").addClass("valid-error");
						} else {
							jQuery("div.error-div #name-error").removeClass("valid-error");
						}


						if (email == '') {					
							passedValidation = false;
							jQuery("div.error-div #email-error").addClass("valid-error");
						} 
						if (!isValidEmailAddress(email)) {
							passedValidation = false;
							jQuery("div.error-div #email-error").addClass("valid-error");
						} else {
							jQuery("div.error-div #email-error").removeClass("valid-error");
						}
						

					}

					// ONCE FORM HAS PASSED VALIDATION, SEND AN AJAX CALL 
					if (passedValidation)	{
						submit_popup();
					} else {
						alert("Please fill out the required fields.");
					}

				// AJAX CALL
				function submit_popup(){
					// THE ACTION IS THE NAME OF THE WP_AJAX HANDLER (CUSTOM FUNCTION) IN FUNCTIONS.PHP
					var data = {'action': 'wme_create_contact', 'firstname': first_name, 'email': email};

					jQuery.ajax({
						method: "POST",
						// AJAXURL IS GLOBALLY DEFINED VARIABLE
						url: ajaxurl,
						data: data,
						dataType: "json",
						success: function(data) {

							if (data.status) {
								// REDIRECT TO FIRST LOGIN PAGE
								var path = site_url + "/lets-go/";

								var redirect_link = site_url + '/?memb_autologin=yes&Id=' + data.data.ID[0].Id + '&Email=' + data.data.Email + '&auth_key=BmzRNV0Z0z0i&redir=' + path;
								// ONCE DATA IS RETURNED FROM BACKEND, REDIRECT TO THIS LINK
								window.location.href = redirect_link;


							} else {
								// IF CONTACT ALREADY EXISTS, ALERT THE MESSAGE THAT COMES BACK
								alert(data);
							}
						},
						error: function() {
							// IF AJAX CALL IS NOT SUCCESSFUL, THIS ERROR APPEARS.
							alert("There is an error.");

						}
					});
				};	
			});			
		});


