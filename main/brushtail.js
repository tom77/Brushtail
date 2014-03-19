/**
 * @author david.funnell
 */

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

function removeEvent( obj, type, fn )
{
	if (obj.removeEventListener)
		obj.removeEventListener( type, fn, false );
	else if (obj.detachEvent)
	{
		obj.detachEvent( "on"+type, obj[type+fn] );
		obj[type+fn] = null;
		obj["e"+type+fn] = null;
	}
}


function ajax(url,callback){
	

	
		var req = null;
		
		
		
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
		
		var response;
                
			if (req.readyState == 4) {
			
			if (req.status == 200)
			{
		//	alert("response is" + req.responseText)
					
			response = req.responseText;
			
				//alert(fullurl);
			}
			
			if (req.status == 401)
			{
			response = "Unauthorized";
			}
			
			if (req.status == 403)
			{
			response = "Forbidden";
			}
			
			if (req.status == 404)
			{
			response = "Not found";
			//alert(fullurl);
			}
                        callback(response);

			}
			
		}
		
		
		var timestamp = new Date();
		//alert(url)
		//var fullurl = encodeURI(url) + "&t=" + timestamp.getTime()
		var fullurl = url + "&rt=" + timestamp.getTime()
	
		req.open("GET", fullurl, true);
		req.send(null);
		
	
			
	}


function updatePage(url,id,format,append){
	
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
			//alert(format  + " " + append)
			if(element)
			{
                        
                        if (format == 'html' && append == 'overwrite'){
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
	
	function loadfeeds()
    {
    	
    
    	if (typeof feeds != 'undefined')
    	{
    	for (var key in feeds) {
    		 updatePage(feeds[key],key);
    	}
    	}
    	if (typeof panelfeeds != 'undefined')
    	{
    		
    	for (var key in panelfeeds) {
    		
    		
    		var value = panelfeeds[key];
    		var link = "";
    		var labelid = "";
    		
    		for (var i in value) {
    		//alert( i + " " + value[i])
    		if (i == "labelid") {labelid = value[i]}	
    		if (i == "link") {link= value[i]}	
    		}
    		
    		if (labelid != "") {
    			//alert(key)
    			update_rss(link,labelid,key);
    		}
    		//alert(value.label + " " + value.link)
    		
    		}
    		
    		
    		//update_rss(key.link,key.labelid,panelfeeds[key]);
    		//alert("_" + key.link + "_" + key.labelid + "_" + panelfeeds[key] + "_")
    	
    	
    	}
    	

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
	
	
	function flyout(e)
{
	//alert("flying out");
	var targ;
	if (!e) var e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	if (targ.nodeType == 3) // defeat Safari bug
		targ = targ.parentNode;

 
	var sub = targ.childNodes
	for (var i = 0; i < sub.length; i++) 
	{
		
	if (sub[i].setAttribute)
	{
	sub[i].setAttribute("class", "showsub");	
	}	
	else{
	sub[i].className="showsub";		
	}
		
	
	}
}

	function flyin(e)
{
	//alert("flying in");
	var targ;
	if (!e) var e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	if (targ.nodeType == 3) // defeat Safari bug
		targ = targ.parentNode;
		

	var sub = targ.childNodes
	for (var i = 0; i < sub.length; i++) 
	{
	if (sub[i].setAttribute)
	{
	sub[i].setAttribute("class", "hidesub");	
	}	
	else{
	sub[i].className="hidesub";		
	}
	}
}
	



 function addEventsToMenu()
{
	

	//alert("hello page")
	//if (document.getElementById("navli")) {alert("found navli")} else {alert("navli not found")}
	var navli = document.getElementsByTagName("li");
	
	
	for (var i = 0; i < navli.length; i++) 
	{
	if (navli[i].className=="mainmenu")
	{
	addEvent(navli[i],"mouseover",flyout);
	addEvent(navli[i],"mouseout",flyin);
	
	}
	
	}
	
}

//window.onload=function()
//{
	
	//addEventsToMenu();
	//loadfeeds();
//}
//addEvent(window,"onload",addEventsToMenu) 



                
                
                
                
function scroll(scrollpoint){

if (document.getElementById(scrollpoint))
{

        var curtop = 0;
        var obj =  document.getElementById(scrollpoint)
    if (obj.offsetParent) {
       
        curtop = obj.offsetTop
        while (obj = obj.offsetParent) {
       
            curtop += obj.offsetTop
        }
    }
   
    window.scrollBy(0,curtop);

       
}
        } 
                
                


		
	//pcadmin.php	
	function checkDefault(e)
	{

	var id;
	var source;
	
	if (e) {source = e.target} 
	else
	 {source = window.event.srcElement}
	
	 id = source.id.substring(4);
	 
	 
	 id = 'all_' + id; 
	  if (document.getElementById(id) && source.checked == true)
	 {
	document.getElementById(id).checked = true;
	 }
	}
	
	
	
	//pcadmin.php
		function checkAllocated(e)
	{

	var id;
	var source;
	
	if (e) {source = e.target} 
	else
	 {source = window.event.srcElement}
	
	 id = source.id.substring(4);
	
	 
	 defid = 'def_' + id; 
	  if (document.getElementById(defid) && document.getElementById(defid).checked == true)
	 {
	 	 

	 allid = 'all_' + id; 
	
	document.getElementById(allid).checked = true;
	 }
	}
	
	
	//pcadmin.php
	
	function addDefault()
	{
	
	var inputs = document.getElementsByTagName("input");
	for (var i=0;i<inputs.length;i++)
	{
	if (inputs[i].className == 'default_usage')
		{
		inputs[i].onclick = checkDefault
		
			
		}
	
	
	
	}
	}
	
	
	//pcadmin.php

	function addCheckAllocated()
	{
	
	var inputs = document.getElementsByTagName("input");
	for (var i=0;i<inputs.length;i++)
	{
	if (inputs[i].className == 'allocated_usage')
		{
		inputs[i].onclick = checkAllocated
		
			
		}
	
	
	
	}
	}
	
	function toggle(div)
				{
				var div = document.getElementById(div);
				if (div.style.display=="block")
				{div.style.display="none";}
				else {div.style.display="block";}
				}
				
				
	function showelement(element,width)
			{
			
			var sel = document.getElementById(element);
			sel.style.width = width + 'em';
			sel.style.display = 'block';
			sel.style.zIndex = '500';
			
			
			
			}
			
	function hideelement(element)
			{
			
			var sel = document.getElementById(element);
			sel.style.display = 'none';
			sel.style.zIndex = '1';
			}
			
			
//begin slideshow code			
			
	function slider(id,height)
	{
		
		
		var height = height;
		this.element = id;
		var elementid = this.element;
		this.itemhtml  = new Array();
		var itemhtml = this.itemhtml;
		this.itemtitles  = new Array();
		var itemtitles = this.itemtitles;
		this.itemlinks  = new Array();
		var itemlinks = this.itemlinks;
	 	this.length;
	 	var length = this.length;
		var count = -1;
		this.opacity = 100;
		Slideshow = {};
		Slideshow.opacity = 100;
		var slideshowStatus = 'play';
		var ts = '';
		var tf = '';
		
		
		
function Opacityto(v){

	elm =  document.getElementById(elementid);
	//alert(Slideshow.slider.innerHTML)
	elm.style.zoom= 1;
    elm.style.opacity = v/100; 
    elm.style.MozOpacity =  v/100; 
    elm.style.KhtmlOpacity =  v/100; 
    elm.style.filter= "alpha(opacity=" + v + ")";
    elm.style.background = "#ffffff"


}

this.hello = function ()
{
	alert('saying hello')
}

this.fade = function (){

	//alert('hello fade')
	
	if (Slideshow.opacity == 0)
		{this.slide()}
	else
		{

Slideshow.opacity = Slideshow.opacity - 5;
//alert(Slideshow.opacity);
//alert(slider.innerHTML)
Opacityto(Slideshow.opacity);
var string = "slider" + elementid + ".fade()";
//alert(string)
tf =setTimeout(string,50);

		}

}
var fade = this.fade;



this.show  = function (){
	
	//alert('hello showing')
	if (Slideshow.opacity == 100)
		{
			
			var string = "slider" + elementid + ".fade()";
			tf =setTimeout(string,6000);}
	else
		{
		Slideshow.opacity = Slideshow.opacity + 5;
		
Opacityto(Slideshow.opacity);
//alert(opacity)
var string = "slider" + elementid + ".show()";
ts = setTimeout(string,50);

		}

}

var show = this.show;

this.slide = function ()
{
	clearTimeout(tf) 
	clearTimeout(ts) 
	
	count++
	if (count == length) {count = 0}
	
	var slider = document.getElementById(elementid);
	
	slider.innerHTML = '<div style=\"height:' + height + 'em;overflow:hidden\"><span style=\"font-size:1.8em;color:black\">' + itemtitles[count] + '</span><br>' + itemhtml[count] + '</div>';
		show();
	
}

this.backslide = function ()
{

	clearTimeout(tf) 
	clearTimeout(ts)  
	
	count--;

	//alert(count)
	if (count == -1) {count = length - 1}
	//alert(count)
	var slider = document.getElementById(elementid);
	slider.innerHTML = '<div style=\"height:' + height + 'em;overflow:hidden\"><span style=\"font-size:1.8em;color:black\">' + itemtitles[count] + '</span><br>' + itemhtml[count] + '</div>';
	
		show();
}

		

this.status = function (labelid)
{
	//alert(labelid)
if (slideshowStatus == 'play')
{
slideshowStatus = 'pause'	
clearTimeout(tf) 
var string = this.element + "label";
var label = document.getElementById(labelid)
label.innerHTML = '<img src=\"../images/play.png\" alt=\"Play\"  title=\"Play\">';
}
	else
	{
	slideshowStatus = 'play'	
	var label = document.getElementById(labelid)
	label.innerHTML = '<img src=\"../images/pause.png\" alt=\"Pause\"  title=\"Pause\">';	
	//tf=setTimeout("fade()",1000);
	fade();
	}
	
	
	
	
}




this.ajax = function (url) {

var req = null;

//alert(url)


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
	
		//alert(req.status)
	if (req.status == 200)
	{
	//alert("response is" + req.responseText)
	//feed =  'feed is ' + req.responseText;
	//alert('xml is ' + req.responseText)
	
//	if (req.responseXML.getElementsByTagName("item").length==0) {alert('xml error')}
	//alert(req.responseText)
	feeddata(req.responseText);
	//alert(feed)
		//alert(fullurl);
	}
	
	if (req.status == 401)
	{
		//feed =  req.responseText;
	}
	
	if (req.status == 403)
	{
		//feed = req.responseText;
	}
	
	if (req.status == 404)
	{
		//feed =  req.responseText;
	//alert(fullurl);
	}
	
	}
	
}


var timestamp = new Date();
//alert(url)
//var fullurl = encodeURI(url) + "&t=" + timestamp.getTime()
var fullurl = url + "&rt=" + timestamp.getTime()

req.open("GET", fullurl, true);
req.send(null);


	
}


//var xmlDoc=new ActiveXObject("Microsoft.XMLDOM");


function StringtoXML(text){

    if (window.ActiveXObject){
      var doc=new ActiveXObject('Microsoft.XMLDOM');
      doc.async='false';
      doc.loadXML(text);
    } else {
      var parser=new DOMParser();
      var doc=parser.parseFromString(text,'text/xml');
    }
    return doc;
}



this.feeddata  = function (xmldata)
{

	var test = StringtoXML(xmldata)
	
	var counter = 0

	 var items = test.getElementsByTagName('item');
     for (var i = 0; i < items.length; i++) 
     {
    	 //alert("another item")
    	 var descriptions = items[i].getElementsByTagName("description")
    	var description = descriptions[0].childNodes[0].nodeValue;
    	  	  itemhtml[counter] = description
    	 
    	 var titles = items[i].getElementsByTagName("title")
     	var itemtitle = titles[0].childNodes[0].nodeValue;
     	 
     	// alert(description)
     	 
     	 itemtitles[counter] = itemtitle
    	 
    	 
     	 var links = items[i].getElementsByTagName("link")
      	var itemlink = links[0].childNodes[0].nodeValue;
      	 
      	// alert(description)
      	 
      	 itemlinks[counter] = itemlink
    	 
    	// alert(description)
    	 
    	
    	 counter++;
     }

     length = itemhtml.length
    // alert('length is ' + length)
     
    //for (var i = 0; i < html.length; i++) 
    // {
    // alert(html[i])
    
    // }
     
    fade();
	
}
		
var feeddata = this.feeddata	
		
	}
	
function hello(test)
{
    
    alert(test);
}
        
        
	//end slideshow code
function displayinline(id)
        {
        document.getElementById(id).style.display = 'inline';                          
       }
function displaynone(id)
        {
        document.getElementById(id).style.display = 'none';      
        }
function displayblock(id)
        {
        document.getElementById(id).style.display = 'block';      
        }

function addtag(text,id)
  {
  var oldText = document.getElementById(id).innerHTML;      
  var newText = oldText + ' ' + text;  
  document.getElementById(id).innerHTML = newText;
 
  }