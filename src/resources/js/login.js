
(function($) {
    $.getScript('/js/alertValidatForme.js', function()
    {

    });
	// Initialize events
	$("#login_form").validate({
		rules: {
			"email":{
				"email": true,
				"required": true
			},
			"passwd": {
				"required": true
			}
		},
		submitHandler: function(form) {
			doAjaxLogin(form);

		},
		// override jquery validate plugin defaults for bootstrap 3
		highlight: function(element) {
			$(element).closest('.form-group').addClass('has-error');
		},
		unhighlight: function(element) {
			$(element).closest('.form-group').removeClass('has-error');
		},
		errorElement: 'span',
		errorClass: 'help-block',
		errorPlacement: function(error, element) {
			if(element.parent('.input-group').length) {
				error.insertAfter(element.parent());
			} else {
				error.insertAfter(element);
			}
		}
	});

	$("#forgot_password_form").validate({
		rules: {
			"email_forgot": {
				"email": true,
				"required": true
			}
		},
		submitHandler: function(form) {

		  doAjaxForgot(form);
		},
		// override jquery validate plugin defaults for bootstrap 3
		highlight: function(element) {
			$(element).closest('.form-group').addClass('has-error');
		},
		unhighlight: function(element) {
			$(element).closest('.form-group').removeClass('has-error');
		},
		errorElement: 'span',
		errorClass: 'help-block',
		errorPlacement: function(error, element) {
			if(element.parent('.input-group').length) {
				error.insertAfter(element.parent());
			} else {
				error.insertAfter(element);
			}
		}
	});

	$('#reset_password_form').validate({
		rules: {
			"reset_passwd": {
				"required": true
			},
			"reset_confirm": {
				"required": true
			}
		},
		submitHandler: function(form) {

			doAjaxReset(form);
		},
		// override jquery validate plugin defaults for bootstrap 3
		highlight: function(element) {
			$(element).closest('.form-group').addClass('has-error');
		},
		unhighlight: function(element) {
			$(element).closest('.form-group').removeClass('has-error');
		},
		errorElement: 'span',
		errorClass: 'help-block',
		errorPlacement: function(error, element) {
			if(element.parent('.input-group').length) {
				error.insertAfter(element.parent());
			} else {
				error.insertAfter(element);
			}
		}
	});

	$('.show-forgot-password').on('click',function(e) {
		e.preventDefault();
		displayForgotPassword();
	});

	$('.show-login-form').on('click',function(e) {
		e.preventDefault();
		displayLogin();
	});



	$('#email').focus();

	//Tab-index loop
	$('form').each(function(){
		var list  = $(this).find('*[tabindex]').sort(function(a,b){ return a.tabIndex < b.tabIndex ? -1 : 1; }),
			first = list.first();
		list.last().on('keydown', function(e){
			if( e.keyCode === 9 ) {
				first.focus();
				return false;
			}
		});
	});


   //matricule verification
    $("#envoyeMatricul").click(function(){
        var matricul = $("#MatriculeQueryEtudian").val();
        if(matricul != ""){
            $.ajax({
                type: 'POST',
                headers: {'cache-control': 'no-cache'},
                url: 'postUrl-matriculeSearch',
                dataType: 'json',
                data: {
                    matricule : matricul
                },success: function(data){
                    if(data.Success){
                        window.location="detailEtudiant-"+data.id+"-"+data.matricule;
                    }else{
                        notify('error',' Matricule Incorrect !');
                    }
                },error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $('#error').html(XMLHttpRequest.responseText).removeClass('hide').fadeIn('slow');
                }
        });
        }else{
            notify('error', ' Le champ Matricule est vide .');
        }

    });


})(jQuery);



//todo: ladda init
var l = new Object();
function feedbackSubmit() {
	l = Ladda.create( document.querySelector( 'button[type=submit]' ) );
}

function displayForgotPassword() {
	$('#error').hide();
	$("#login").find('.flip-container').toggleClass("flip");
	setTimeout(function(){$('.front').hide()},200);
	setTimeout(function(){$('.back').show()},200);
	$('#email_forgot').select();
}

function displayLogin() {
	$('#error').hide();
	$("#login").find('.flip-container').toggleClass("flip");
	setTimeout(function(){$('.back').hide()},200);
	setTimeout(function(){$('.front').show()},200);
	$('#email').select();
	return false;
}

/**
 * Check user credentials
 *
 * @param string redirect name of the controller to redirect to after login (or null)
 */
function doAjaxLogin(formData) {
	$('#error').hide();
	$('#boutonlogin').html(' ').prop('disabled', true);
	$('#ajax_running_login').show();

	serialiseform(formData).then((res) => {
		$("#error").html("<div class=\"alert alert-success\">Success</div>").removeClass('hide').fadeIn('slow');
		window.location="/dashboard";
	}).catch(error => {
		console.log(error)
		notify('error', displayErrors(error.responseJSON))

		$('#boutonlogin').html('Se Connecter').attr('disabled', false);
		$('#ajax_running_login').hide();

		$("#error").html("<div class=\"alert alert-danger\">Erreur <de>connection</de></div>").removeClass('hide').fadeIn('slow');
	})
	/*
		$.ajax({
			url: "/login",
			data: formData,
			processData: false,
			contentType: false,
			type: 'POST',
			success (data) {
					notify('success !',' Vous etes connecté');
					$("#error").html("<div class=\"alert alert-success\">Success</div>").removeClass('hide').fadeIn('slow');
					window.location="/dashboard";
			},
			error(jqxhr){

			}
		});

	 */
}


function doAjaxForgot(form) {
	serialiseform(form).then(res => {
		console.log(res)
	}).catch(error => {
		console.log(error)
		notify('error', displayErrors(error.responseJSON))
	})
}

function doAjaxReset(form) {
	serialiseform(form).then(res => {
		console.log(res)
	}).catch(error => {
		console.log(error)
		notify('error', displayErrors(error.responseJSON))
	})
}
