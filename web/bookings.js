
function updatePage(url,parameters,method,id,format,append){
	
                if(typeof(format)==='undefined') {format = 'html';}
                if(typeof(append)==='undefined') {append = 'overwrite';}
//format usually html, but if inserting conten into a textarea use 'text'
	
		var req = null;
		var id;
		if (id)
		{
		var element = document.getElementById(id)
		}
		
		
		if (window.XMLHttpRequest) {
			req = new XMLHttpRequest();
			
		}
		else 
			if (window.ActiveXObject) {
				try {
					req = new ActiveXObject("Msxml2.XMLHTTP");
				} 
				catch (e) {
					try {
						req = new ActiveXObject("Microsoft.XMLHTTP");
					} 
					catch (e) {
					}
				}
			}
		
		
		
		req.onreadystatechange = function(){
		
		
			if (req.readyState == 4) {
			
			if (req.status == 200)
			{
                            
                          //  alert('banana')
                        
			//alert(format  + " " + append)
			if(element)
			{
                      //alert(req.responseText);
                        if (format == 'html' && append == 'overwrite'){
                            
                            element.innerHTML = ''; 
                            //alert(req.responseText);
                            element.innerHTML = req.responseText;
                        }
                        else if (format == 'html' && append == 'append')
                            {
                             element.innerHTML = element.innerHTML + req.responseText;   
                                
                            }
			if (format == 'text' && append == 'overwrite'){
                            
                           element.innerText = req.responseText;
                        element.textContent = req.responseText; 
                        }
                        else if (format == 'text' && append == 'append')
                            {
                                
                            // element.innerText = element.innerHTML + req.responseText;
                      //  element.textContent = element.innerHTML + req.responseText;    
                      
                        element.innerText = element.innerText + req.responseText;
                      //  alert(element.value)
                      //  alert(element.textContent)
                        element.value +=   req.responseText;
                            }
			}
				//alert(fullurl);
			}
			
			if (req.status == 401)
			{
			element.innerHTML = "Unauthorized";
			}
			
			if (req.status == 403)
			{
			element.innerHTML = "Forbidden";
			}
			
			if (req.status == 404)
			{
			element.innerHTML = "Not found";
			//alert(fullurl);
			}
			
			}
			
		}
		
		if (method == 'get')
                    {
		var timestamp = new Date();
		//alert(url)
		//var fullurl = encodeURI(url) + "&t=" + timestamp.getTime()
		var fullurl = url + "?" + parameters + "&rt=" + timestamp.getTime()
		//alert(fullurl)
		//var fullurl = "http://192.168.10.233/brushtail_intranet/main/rss.php";
		//var fullurl = "http://192.168.10.233/brushtail_intranet/main/rss.php?link=http://www.maxdesign.com.au/feed/&mode=full&t=123412341234";
		//alert(fullurl)
		req.open("GET", fullurl, true);
		req.send(null);
                }
                
                if (method == 'post')
                    {
                      req.open("POST", url, true)
                        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
                        req.send(parameters)  
                 // alert(parameters)
                    }
			
	}





function setfocus() 
                                 {
                              if (document.getElementById('focus'))
                              {
                                 document.getElementById('focus').focus(); 
                              }
                                 }


 function getTypes(bid)
{
 var url = 'ajax_pc.php';
 var parameters = 'get=types&bid=' + bid;
  document.getElementById('panel').innerHTML = '';
 document.getElementById('panel').innerHTML = '<p style="text-align:center"><img src="ajax-loader.gif" style="margin-top:5em"></p>'; 
//alert(url);
updatePage(url,parameters,'get','panel','html','overwrite');       
    
 //  
    
} 
   
function getDays(bid,useno,pcno)
{
	

var url = 'ajax_pc.php';
var parameters = 'get=days&bid=' + bid + '&bookingtype=' + useno + '&pcno=' + pcno;
 document.getElementById('panel').innerHTML = '';
 document.getElementById('panel').innerHTML = '<p style="text-align:center"><img src="ajax-loader.gif" style="margin-top:5em"></p>'; 
 

//alert(url);
updatePage(url,parameters,'get','panel','html','overwrite'); 
	
}
                                
function getTimes(bid,useno,timestamp,pcno)
{
	

var url = 'ajax_pc.php';
var parameters = 'get=times&bid=' + bid + '&bookingtype=' + useno + '&day=' + timestamp+ '&pcno=' + pcno;
 document.getElementById('panel').innerHTML = '';
 document.getElementById('panel').innerHTML = '<p style="text-align:center"><img src="ajax-loader.gif" style="margin-top:5em"></p>'; 
 

//alert(url);
updatePage(url,parameters,'get','panel','html','overwrite'); 
	
}

function getPCs(bid)
{
	

var url = 'ajax_pc.php';
var parameters = 'get=PCs&bid=' + bid ;
 document.getElementById('panel').innerHTML = '';
 document.getElementById('panel').innerHTML = '<p style="text-align:center"><img src="ajax-loader.gif" style="margin-top:5em"></p>'; 
 

//alert(url);
updatePage(url,parameters,'get','panel','html','overwrite'); 
	
} 
  
 
 function bookingForm(bookingtype,t,duration,pcno,bid)
{
	

var url = 'ajax_pc.php';
var parameters = 'get=bookingForm&bid=' + bid + '&bookingtype=' + bookingtype + '&pcno=' + pcno + '&duration=' + duration + '&start=' + t;
 document.getElementById('panel').innerHTML = '';
 document.getElementById('panel').innerHTML = '<p style="text-align:center"><img src="ajax-loader.gif" style="margin-top:5em"></p>'; 
 

//alert(url);
updatePage(url,parameters,'get','panel','html','overwrite'); 
	
} 


 
 function booking()
{
//alert('hello')

var bid = document.getElementById('bid').value 	;
var start = document.getElementById('start').value 	;
var name = document.getElementById('name').value;
name = encodeURIComponent(name);

var barcode = document.getElementById('barcode').value 	;
barcode = encodeURIComponent(barcode);
var password = document.getElementById('password').value ;
password = encodeURIComponent(password);
var pcno = document.getElementById('pcno').value ;
var bookingtype = document.getElementById('bookingtype').value ;
var duration = document.getElementById('duration').options[document.getElementById('duration').selectedIndex].value;

var url = 'ajax_pc.php';
var parameters = 'get=booking&bid=' + bid + '&bookingtype=' + bookingtype + '&pcno=' + pcno + '&duration=' + duration + '&start=' + start + '&barcode=' + barcode + '&password=' + password + '&name=' + name;
//alert(parameters)

document.getElementById('panel').innerHTML = '';
 document.getElementById('panel').innerHTML = '<p style="text-align:center"><img src="ajax-loader.gif" style="margin-top:5em"></p>'; 
 

//alert(url);
updatePage(url,parameters,'post','panel','html','overwrite'); 
	
}