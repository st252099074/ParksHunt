$(document).ready(function() {
	var form = $('#signup');
	var response = $('.response');
	form.on('submit', function(e) {
		e.preventDefault();
		// reload the image
		var id = Math.random();
		$('#captcha').attr('src', 'captcha.php?id=' + id);
		$.ajax({
			url: 'register.php',
			type: 'POST',
			dataType: 'html',
			data: form.serialize(),
		
	       success: function(serverResponse) {
            // handle output from server here ('Success!' or 'Error' from PHP script)
       console.log("Successful");
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            // handle any network/server errors here
            console.log("Status: " + textStatus); 
            console.log("Error: " + errorThrown); 
        }
		});
	});
});