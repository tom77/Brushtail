	
	
	

	
	
	function getbookings(phrase)
{
	
 var text =  prompt("enter barcode")	;
    if(text == "" || text == null) 
	{ 
	
	return ;
	}
    else
    	{
		 var link = document.getElementById("getbookings") ;
		 link.href = link.href + "&cardnumber=" + escape(text);	
		}


 
}

function patron(phrase)
{
 var text =  prompt(phrase)
    if(text == "" || text == null) {  return ;}	
    else {
	 var link = document.getElementById("patron") ;
	 var string = link.href + "&barcode=" + escape(text);
 link.href = string;	

		
	}


}

	
