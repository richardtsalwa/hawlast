var hideCalendarTimer = new Array();

function calendarTimer(objname){
	this.objname = objname;
	this.timers = new Array();
}

function toggleCalendar(objname, auto_hide, hide_timer){
	var div_obj = document.getElementById('div_'+objname);
	if(div_obj != null){
		if (div_obj.style.visibility=="hidden") {
		  div_obj.style.visibility = 'visible';
		  document.getElementById(objname+'_frame').contentWindow.adjustContainer();
		  
		  //auto hide if inactivities with calendar after open
		  if(auto_hide){
			  if(hide_timer < 3000) hide_timer = 3000; //put default 3 secs
			  prepareHide(objname, hide_timer);
		  }
		}else{
		  div_obj.style.visibility = 'hidden';
		}
	}
}

function showCalendar(objname){
	var div_obj = document.getElementById('div_'+objname);
	if(div_obj != null){
		div_obj.style.visibility = 'visible';
		document.getElementById(objname+'_frame').contentWindow.adjustContainer();
	}
}

function hideCalendar(objname){
	var div_obj = document.getElementById('div_'+objname);
	if(div_obj != null){
		div_obj.style.visibility = 'hidden';	
	}
}

function prepareHide(objname, timeout){
	cancelHide(objname);
	
	var timer = setTimeout(function(){ hideCalendar(objname) }, timeout);
	
	var found = false;
	for(i=0; i<this.hideCalendarTimer.length; i++){
		if(this.hideCalendarTimer[i].objname == objname){
			found = true;
			this.hideCalendarTimer[i].timers[this.hideCalendarTimer[i].timers.length] = timer;
		}
	}
	
	if(!found){
		var obj = new calendarTimer(objname);
		obj.timers[obj.timers.length] = timer;
		
		this.hideCalendarTimer[this.hideCalendarTimer.length] = obj;
	}
}

function cancelHide(objname){
	for(i=0; i<this.hideCalendarTimer.length; i++){
		if(this.hideCalendarTimer[i].objname == objname){
			var timers = this.hideCalendarTimer[i].timers;
			for(n=0; n<timers.length; n++){
				clearTimeout(timers[n]);
			}
			this.hideCalendarTimer[i].timers = new Array();
			break;
		}
	}
}

function setValue(objname, d){
	//compare if value is changed
	var changed = (document.getElementById(objname).value != d) ? true : false;

	updateValue(objname, d);

	var dp = document.getElementById(objname+"_dp").value;
	if(dp) toggleCalendar(objname);

	checkPairValue(objname, d);

	//calling calendar_onchanged script
	if(document.getElementById(objname+"_och").value != "" && changed)
		calendar_onchange(objname);
		
	var date_array = document.getElementById(objname).value.split("-");	
	tc_submitDate(objname, date_array[2], date_array[1], date_array[0]);
}

function updateValue(objname, d){
	document.getElementById(objname).value = d;

	var dp = document.getElementById(objname+"_dp").value;
	if(dp == true){
		var date_arra