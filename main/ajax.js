	function updatePage(url,id){
	

	
		var req = null;
		var id;
		var element = document.getElementById(id)
		//alert(id)
		
		
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
			//alert("response is" + req.responseText)
				element.innerHTML = req.responseText;
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
		
		
		var timestamp = new Date();
		//alert(url)
		//var fullurl = encodeURI(url) + "&t=" + timestamp.getTime()
		var fullurl = url + "&rt=" + timestamp.getTime()
		//alert(fullurl)
		//var fullurl = "http://192.168.10.233/brushtail_intranet/main/rss.php";
		//var fullurl = "http://192.168.10.233/brushtail_intranet/main/rss.php?link=http://www.maxdesign.com.au/feed/&mode=full&t=123412341234";
		//alert(fullurl)
		req.open("GET", fullurl, true);
		req.send(null);
		
	
			
	}
	
	
	     	
	     	
    
function updatecal(t,m,name)
    {
   
   var id = "cal" + m;
    document.getElementById(id).innerHTML = '<img src="../images/loading.gif">';
    var url = "ajax.php?event=cal&m=" + m + "&name=" + name + "&t=" + t; 
    updatePage(url,id);
    }
   
    
    
    
	
function update_rss(link,labelid,id)
	{
	//alert("link= " + link + " labelid= " + labelid + " id=" + id)
		
	for (var key in panelfeeds) {
    	
    		if (key == id)
    		{
    		var value = panelfeeds[key];
    		for (var i in value) {
    			if (i == "labels")
    			{
    				var list= value[i];
    				for (var ind in list ) {
    					//alert(list[ind])
    					document.getElementById(list[ind]).className="accent";
    				}
    				
    			}
    		}
    		//for (var i in key) 
    			//{
    				
    			//alert(i + " " + key[i])	
    			//document.getElementById(key[i]).className="accent";
    			//}
    		}
    	
    	}

	
	document.getElementById(labelid).className="white";

	document.getElementById(id).innerHTML = "<img src='../images/loading.gif'>";
	updatePage(link,id);
	
	}

    

   
addEvent(window,'onload',loadfeeds)    
window.onload=loadfeeds