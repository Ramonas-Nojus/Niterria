$(document).ready(function(){

	$('#selectAllBoxes').click(function(event){

	if(this.checked) {

		$('.checkBoxes').each(function(){

	    this.checked = true;

	});

	} else {
		$('.checkBoxes').each(function(){
	    	this.checked = false;
			});
		}
	});

});


function loadUsersOnline() {

	$.get("/admin/functions.php?onlineusers=result", function(data){
		$(".usersonline").text(data);
	});
}


setInterval(function(){

	loadUsersOnline();


},30000);












