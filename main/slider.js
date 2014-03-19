var url = "http://www.erl.vic.gov.au/main/feed.php?m=151&page_id=1006";
var url = "http://192.168.10.233/brushtail_5/main/feed.php?m=82&page_id=695";

var slideshowStatus = 'play';


var ts = '';
var tf = '';

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


function ajax(url) {

var req = null;

//alert('feed is ' +  feed)


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
//alert(fullurl)
//var fullurl = "http://192.168.10.233/brushtail_intranet/main/rss.php";
//var fullurl = "http://192.168.10.233/brushtail_intranet/main/rss.php?link=http://www.maxdesign.com.au/feed/&mode=full&t=123412341234";
//alert(fullurl)
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



function feeddata(xmldata)
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
   //  alert('length is ' + length)
     
    //for (var i = 0; i < html.length; i++) 
    // {
    // alert(html[i])
    
    // }
     
    fade();
	
}


function Opacityto(v){
	elm =  document.getElementById("slider");
	//alert(Slideshow.slider.innerHTML)
    elm.style.opacity = v/100; 
    elm.style.MozOpacity =  v/100; 
    elm.style.KhtmlOpacity =  v/100; 
    //if (elm.style.filter)
   // {
    elm.style.filter=" alpha(opacity ="+v+")";
   // }
}



function fade(){
	//var slider = document.getElementById("slider");
	if (Slideshow.opacity == 0)
		{slide()}
	else
		{

Slideshow.opacity = Slideshow.opacity - 5;
//alert(Slideshow.opacity);
//alert(slider.innerHTML)
Opacityto(Slideshow.opacity);
 tf=setTimeout("fade()",50);

		}

}

function show(){
	
	if (Slideshow.opacity == 100)
		{
			
		if (slideshowStatus == 'play')
		{	
			tf=setTimeout("fade()",6000);
		}
		
		}
	else
		{
		Slideshow.opacity = Slideshow.opacity + 5;
		//alert("checking IE" + Slideshow.opacity)
Opacityto(Slideshow.opacity);
//alert(opacity)
ts=setTimeout("show()",50);

		}

}

function status()
{
	
if (slideshowStatus == 'play')
{
slideshowStatus = 'pause'	
clearTimeout(tf) 
var label = document.getElementById("statusLabel")
label.innerHTML = '<img src=\"../images/play.png\" alt=\"Play\">';
}
	else
	{
	slideshowStatus = 'play'	
	var label = document.getElementById("statusLabel")
	label.innerHTML = '<img src=\"../images/pause.png\" alt=\"Pause\">';	
	//tf=setTimeout("fade()",1000);
	fade();
	}
	
	
	
	
}



function slide()
{
	clearTimeout(tf) 
	clearTimeout(ts)  
	count++;
	//alert(count)
	if (count == length) {count = 0}
	
	var slider = document.getElementById("slider");
	slider.innerHTML = '<h4>' + itemtitles[count] + '</h4>' + itemhtml[count] + '';
	//slider.innerHTML = '<div onclick="go(\'' + itemlinks[count] + '\')"><h4>' + itemtitles[count] + '</h4>' + itemhtml[count] + '</div>';
	//alert(html[count])
	
		show();
}




function backslide()
{

	clearTimeout(tf) 
	clearTimeout(ts)  
	
	count--;

	
	
	//alert(count)
	if (count == -1) {count = length - 1}
	//alert(count)
	var slider = document.getElementById("slider");
	slider.innerHTML = '<h4>' + itemtitles[count] + '</h4>' + itemhtml[count] + '';
	//slider.innerHTML = '<div onclick="go(\'' + itemlinks[count] + '\')"><h4>' + itemtitles[count] + '</h4>' + itemhtml[count] + '</div>';
	//alert(html[count])
	
		show();
}


function go(url)
{
window.location = url;	
}






var itemhtml  = new Array();
var itemtitles  = new Array();
var itemlinks  = new Array();
var length;
var count = -1;
var opacity = 100;
Slideshow = {};
Slideshow.opacity = 100;



function start()
{
	
	//alert(url)
	ajax(url);

	
}

addEvent(window, 'load', start);
