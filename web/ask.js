function addEvent( obj, type, fn )
{
	if (obj.addEventListener)
		obj.addEventListener( type, fn, false );
	else if (obj.attachEvent)
	{
		obj["e"+type+fn] = fn;
		obj[type+fn] = function() { obj["e"+type+fn]( window.event ); }
		obj.attachEvent( "on"+type, obj[type+fn] );
	}
}




function gblvars()
{
noselect = false
emptytextfields = false	
emptytextarea = false	
}

gvars = new gblvars()
	
	
	function check()
	{
	
		//alert("checking")
		
			 //check for menus not selected
		 var formInputs = document.getElementsByTagName('select');
		 gvars.noselect = false
    for (var i = 0; i < formInputs.length; i++) {
		var theInput = formInputs[i];
		if (theInput.className == 'required') {
			
			var value = theInput.options[theInput.selectedIndex].value
			//alert(value)
		if (value == '0')
				{
				gvars.noselect = true
				//alert("display menu field " + theInput.id)
				warning_id = "warning_" + theInput.id.substring(6)
				document.getElementById(warning_id).style.display = "inline"
				}
			else
				{
				gvars.noselect = false;
				//alert("none")
				warning_id = "warning_" + theInput.id.substring(6)
				document.getElementById(warning_id).style.display = "none"
			
				}
				
			}
			
			
		}
	
		//alert(gvars.noselect)
		 //check for required textfields are not empty
		 var formInputs = document.getElementsByTagName('input');
		 gvars.emptytextfields = false
    for (var i = 0; i < formInputs.length; i++) {
		var theInput = formInputs[i];
		//var id = theInput.id.substring(6);
		//alert(theInput.className);
		if (theInput.type == 'text' && theInput.className == 'required') {
			if (theInput.value == "") {
				gvars.emptytextfields = true
				warning_id = "warning_" + theInput.id.substring(6)
				//alert("display menu field " + warning_id)
				document.getElementById(warning_id).style.display = "inline"
				
			}
			else
			{
				warning_id = "warning_" + theInput.id.substring(6)
				//alert("none " + warning_id)
				document.getElementById(warning_id).style.display = "none"
			}
			
			
		}
	}
			
			
				//check required not empty textarea
			 var formInputs = document.getElementsByTagName('textarea');
			 gvars.emptytextarea = false
    for (var i = 0; i < formInputs.length; i++) {
		var theInput = formInputs[i];
		 if ( theInput.className ==  'required' )
		 {
		 	if (theInput.value == "")
				{
				gvars.emptytextarea = true	
				//alert("empty " + theInput.id)
						warning_id = "warning_" + theInput.id.substring(6)
				document.getElementById(warning_id).style.display = "inline"
				
			}
			else
			{
				warning_id = "warning_" + theInput.id.substring(6)
				document.getElementById(warning_id).style.display = "none"
			
				}
			 
			}

			}
		
		//alert ("noselect " + gvars.noselect + " empty " + gvars.emptyfields)
		if (gvars.noselect == false && gvars.emptytextfields == false && gvars.emptytextarea == false) {
			document.getElementById('submit').disabled = false;
			//if (document.getElementById('require_warning')) 
			//{
			//document.getElementById('require_warning').style.display = "none";
			//}
		}
		else{
			document.getElementById('submit').disabled= true;
			//if (document.getElementById('require_warning')) 
			//{
				//document.getElementById('require_warning').style.display = "block";
			//}
		}
		
	}
	
	
	

			
					//add events to menus
			 var formInputs = document.getElementsByTagName('select');
    for (var i = 0; i < formInputs.length; i++) {
		var theInput = formInputs[i];
		 if ( theInput.className ==  'required' )
		 {
		 	theInput.onchange=check
			 
			}

			}
	
	document.onkeydown=check
	addEvent(window, 'load', check);
	
	
	