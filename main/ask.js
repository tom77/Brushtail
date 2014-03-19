




function gblvars()
{
noselect = false
emptytextfields = false	
emptytextarea = false	

}

gvars = new gblvars()
	
	
	function check(e)
	{
	
		
	
		
		
		
		//alert("checking")
		
			 //check for menus not selected
		 var formInputs = document.getElementsByTagName('select');
		 gvars.noselect = false
    for (var i = 0; i < formInputs.length; i++) {
		var theInput = formInputs[i];
		if (theInput.className == 'required') {
			
			var value = theInput.options[theInput.selectedIndex].value
			
		if (value == '0')
				{
				gvars.noselect = true
				//alert(theInput.id)
				warning_id = "warning_" + theInput.id.substring(6)
				document.getElementById(warning_id).style.display = "inline";
				}
			else
				{
				//gvars.noselect = false;
				warning_id = "warning_" + theInput.id.substring(6)
				document.getElementById(warning_id).style.display = "none";
				
				
			
				}
				
			}
			
			
		}
	
		
		 //check for required textfields are not empty
		 var formInputs = document.getElementsByTagName('input');
		 gvars.emptytextfields = false
    for (var i = 0; i < formInputs.length; i++) {
		var theInput = formInputs[i];
		if (theInput.type == 'text' && theInput.className == 'required') {
			if (theInput.value == "") {
				gvars.emptytextfields = true
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
				//alert(theInput.id)
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
		if (gvars.noselect == false && gvars.emptytextfields == false && gvars.emptytextarea == false ) {
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
	
	
function select_other(fieldno)
	{
		var theInput = document.getElementById("field_" + fieldno)
		
		var value = theInput.options[theInput.selectedIndex].value
		
		
		var other_id = "other_" + fieldno;
				
				if (value.toUpperCase() == "OTHER" )
					{
					
					if (document.getElementById(other_id))
					{	
 					document.getElementById(other_id).style.display = "inline";
 				
					}
					}
					else
					{
					
					if (document.getElementById(other_id))
					{
 					document.getElementById(other_id).style.display = "none";
					}
					}
		
		
		
	}

	
	
	
function radio_other(fieldno,value)
	{
		
		var other_id = "other[" + fieldno + "]";
		
		if (value.toUpperCase() == "OTHER")
		{
		
		document.getElementById(other_id).style.display = "inline";
 		document.getElementById(other_id).focus();	
			
		}
		else
		{
		document.getElementById(other_id).style.display = "none";
 	
			
		}
		
		
	}
	

	function loadevents()
	{
		
		
		//document.onmousemove=check
		window.onmouseclick=check
		
				//add events to menus
			 var formInputs = document.getElementsByTagName('select');
    for (var i = 0; i < formInputs.length; i++) {
		var theInput = formInputs[i];
		 if ( theInput.className ==  'required' )
		 {
		 	theInput.onchange=check
			// alert(theInput.id)
			}

			}
	
		
	
			//add events to textfields
			 var formInputs = document.getElementsByTagName('input');
    for (var i = 0; i < formInputs.length; i++) {
		var theInput = formInputs[i];
		 if ( theInput.className ==  'required' )
		 {
		 	theInput.onblur=check
		 	theInput.onkeyup=check
	
		 	
			 
			}

			}
			
				//add events to textareas
			 var formInputs = document.getElementsByTagName('textarea');
    for (var i = 0; i < formInputs.length; i++) {
		var theInput = formInputs[i];
		 if ( theInput.className ==  'required' )
		 {
		 	theInput.onblur=check
			 
			}

			}
			
					//add events to radios
			 var formInputs = document.getElementsByTagName('radio');
    for (var i = 0; i < formInputs.length; i++) {
		var theInput = formInputs[i];
		 if ( theInput.className ==  'required' )
		 {
		 	theInput.onblur=check
			 
			}

			}

	}		
	
	//document.onmouseup=check		
	//document.onkeyup=check
	//window.onload=loadevents
	 addEvent(window, 'load', loadevents);
	 addEvent(window, 'load', check);
	
	