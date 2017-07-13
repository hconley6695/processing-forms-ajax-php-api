jQuery(function(){


			function isValidEmailAddress(emailAddress) {
			    var pattern = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()\.,;\s@\"]+\.{0,1})+[^<>()\.,;:\s@\"]{2,})$/;
			    return pattern.test(emailAddress);
			};


			jQuery(".optin-infusion-form").on("submit", function(event){
				event.preventDefault();

				//CALL AJAX
      			var first_name = jQuery("#inf_field_FirstName", this).val();
				var email = jQuery("#inf_field_Email", this).val();
				var passedValidation = true;

				if(jQuery("#inf_field_FirstName", this).length >= 1 && jQuery("#inf_field_Email", this).length >= 1)   {
					
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

					if (passedValidation)	{
						submit_popup();
					} else {
						alert("Please fill out the required fields.");
					}

				function submit_popup(){

					var data = {'action': 'wme_create_contact', 'firstname': first_name, 'email': email};

					jQuery.ajax({
						method: "POST",
						url: ajaxurl,
						data: data,
						dataType: "json",
						success: function(data) {

							if (data.status) {
								// REDIRECT TO FIRST LOGIN PAGE
								var path = site_url + "/lets-go/";

								var redirect_link = site_url + '/?memb_autologin=yes&Id=' + data.data.ID[0].Id + '&Email=' + data.data.Email + '&auth_key=BmzRNV0Z0z0i&redir=' + path;

								window.location.href = redirect_link;


							} else {
								// IF CONTACT ALREADY EXISTS
								alert(data);
							}
						},
						error: function() {
							alert("There is an error.");

						}
					});
				};	
			});			
		});


