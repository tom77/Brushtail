
function checkboxes(value)
{
	
   var inputs = document.getElementsByTagName("input");
   for(var t = 0;t < inputs.length;t++){
     if(inputs[t].type == "checkbox")
       inputs[t].checked = value;
   }
}

	
	
	
	var cost = document.getElementById("cost");
	var hours = document.getElementById("hours");
	
	var costwarning = document.getElementById("costwarning");
	
	var replacement = document.getElementById("replacement");

	var replacewarning = document.getElementById("replacewarning");
	
	
	function testcost(element,warning){
	//alert(\"testing\")
	var td = element.parentNode
	
	testRegExp = /^[0-9]+\.[0-9][0-9]$/i;
		if (testRegExp.test(element.value)) {
			td.style.background = 'white';
			warning.innerHTML = '';
		}
		else {
			
		td.style.background = '#E05C5C';
		warning.innerHTML = '<br>Not a decimal value.';
		}
	}
	
	
		function testhours(){
	//alert(\"testing\")
	var td = hours.parentNode
	var status;
	
	if (hours.value.length == 1)
	{
	testRegExp = /[0-9]/i;
	}
	if (hours.value.length == 2)
	{
	testRegExp = /[0-9][0-9]/i;
	}
		if (hours.value.length == 3)
	{
	testRegExp = /^[0-9]+[\.0-9][0-9]$/i;
	}
	
	
		if (testRegExp.test(hours.value)) {
			td.style.background = 'white';
			document.getElementById("hourswarning").innerHTML = '';
		}
		else {
			
		td.style.background = '#E05C5C';
		document.getElementById("hourswarning").innerHTML = '<br> Not a numerical value. <br>eg 90 minutes would be 1.5 hours';
		}
	}
	
	
		function pop1(e)
	{
	var id;
	if (e) {id = e.target.id.substring(4);} 
	else
	 {id = window.event.srcElement.id.substring(4);}
	
	 id = 'p1_' + id; 
	 if (document.getElementById(id))
	 {
	document.getElementById(id).className = 'display';
	 }
	
	}
	
	
	function nopop1(e)
	{

	var id;
	if (e) {id = e.target.id.substring(4);} 
	else
	 {id = window.event.srcElement.id.substring(4);}
	
	 id = 'p1_' + id; 
	  if (document.getElementById(id))
	 {
	document.getElementById(id).className = 'nodisplay';
	 }
	}
	
	
	
			function pop2(e)
	{
	var id;
	if (e) {id = e.target.id.substring(4);} 
	else
	 {id = window.event.srcElement.id.substring(4);}
	
	 id = 'p2_' + id; 
	 if (document.getElementById(id))
	 {
	document.getElementById(id).className = 'display';
	 }
	
	}
	
	
	function nopop2(e)
	{

	var id;
	if (e) {id = e.target.id.substring(4);} 
	else
	 {id = window.event.srcElement.id.substring(4);}
	
	 id = 'p2_' + id; 
	  if (document.getElementById(id))
	 {
	document.getElementById(id).className = 'nodisplay';
	 }
	}
	
	
	
	function loadhover()
	{
	
	var tds = document.getElementsByTagName("td");
	for (var i=0;i<tds.length;i++)
	{
	if (tds[i].className == 'hoverdes')
		{
		tds[i].onmouseover = pop1
		tds[i].onmouseout = nopop1
			
		}
	
	if (tds[i].className == 'hoverdet')
		{
		tds[i].onmouseover = pop2
		tds[i].onmouseout = nopop2
			
		}
	
	}
	}
	
	
	function staff()
	{
	var selected = false;
	
	 var inputs = document.getElementsByTagName("input");
   for(var t = 0;t < inputs.length;t++){
     if(inputs[t].className == "staff")
     {
     //	alert(inputs[t].checked);
      if (inputs[t].checked == true) {selected = true}
     }
      
   }	
		
		if (selected == false) 
		{
		
		alert("No staff were selected!");
		return false;
		}
	}
	
	//cost.onchange=test
	
	if(cost) {cost.onkeyup= function() {testcost(cost,costwarning);}}
	
	if(replacement) {replacement.onkeyup= function() {testcost(replacement,replacewarning);}}
	if (hours) {hours.onkeyup=testhours}