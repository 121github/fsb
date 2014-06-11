function checkSubmit(submit_id) {
	document.getElementById(submit_id).value = "Submitting...";
	document.getElementById(submit_id).disabled = true;
	return true;
}

$(document).ready(function() {    
	$(".close-message").click(function () {
        $("#message").fadeOut("slow");
    });
    
    
    setTimeout(function(){
        $("#message").fadeOut("slow");
    },5000)

});