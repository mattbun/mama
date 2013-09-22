var sparkle_id;

function startSparkling($message){
	clearInterval(sparkle_id);
	$("#sparkleMessage").html($message);
	sparkle_id = setInterval(function(){sparkle()},250);	
	$("#sparkle").show();
}

function stopSparkling($message){
	if ($("#sparkleMessage").html() == $message){ 
		clearInterval(sparkle_id);
		$("#sparkle").hide();
	}
}

function sparkle(){
	current = $("#sparkler").html();

	if (current == "-"){
		$("#sparkler").html("\\");
	}
	else if (current == '\\'){
		$("#sparkler").html("|");
	}
	else if (current == '|'){
		$("#sparkler").html("/");
	}
	else if (current == '/'){
		$("#sparkler").html("-");
	}
}
