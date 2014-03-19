




var timer = 0
var offset = 0

function time_update()
{

 now = gettime();
 
//alert(offset)
  //diff = "hello" + counter;
var diff1 = Math.floor((now - start) / 1000) + offset

var hours = Math.floor(diff1 /3600);
var diff2 = diff1 % 3600;

var minutes = Math.floor(diff2 /60);

var seconds = diff2 % 60; 
//alert("diff2 is " + diff2 + "hours is " + hours + " minutes " + minutes + "sec is " + seconds)
//var seconds = diff1 % 60
//var minutes = (diff1 - seconds) / 60

if (seconds < 10) { seconds = "0" + seconds ;}
if (minutes < 10) { minutes = "0" + minutes ;}
if (hours < 1) { hours = "0"  ;}

output = hours + "." + minutes + ":" + seconds;
var span = document.getElementById("timer").innerHTML = output;
   
//alert("")
   
  } 
 
function gettime()
{
	var newdate   = new Date();
	var timestamp = newdate.getTime()
	return timestamp;
}  
 
function runclock(startoff){

   start =  gettime();
   offset = startoff

 	timer = window.setInterval("this.time_update(offset)", 1000);
 }

function stopclock(){
	//alert("stopping")
	window.clearInterval(timer);
	timer = 0;
}

function stop()
{
	//alert("stop function")
	
	if (timer != 0) {
		
		//alert("stopping timer")
		if (document.getElementById("clockbutton")) {
			document.getElementById("clockbutton").click()
		}
		
		if (timer != 0) {
			stopclock()
		}
	}
}


  addEvent( window, "unload", stop ) 

