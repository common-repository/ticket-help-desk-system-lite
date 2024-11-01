function v_a(msg,e){
	if(confirm(msg)){}
	else {
		e.stopPropogation();
	}
}