<?php


  if (!isset($_REQUEST["l"]))
    {
         if(!include("../includes/initiliaze_page.php"))
 {
 	include("includes/initiliaze_page.php");
 }
    }    


?><!DOCTYPE html>
<html><head>
<style>
body {background:white;margin:0;padding:0;text-align:center;font-family:arial;width:100%;height:100%}
.container { position: fixed;
    width: 100%;
    height: 100%;
}

</style>
</head>
<body>
    <?php
    if (!isset($_REQUEST["l"]))
    {
 
        
        
        
        
        echo "<h2>$phrase[758]</h2><ul>";
        $sql = "select  name, branchno from pc_branches ";

 $DB->query($sql,"bookings.php");
while ($row = $DB->get())
{
    $name = $row["name"];  
    $bid = $row["branchno"];
    echo "<li><a href=\"bookings.php?l=$bid\">$name</a></li>";
    
}    
     echo "</ul> ";  
        
    }
    else
    {
        
      $l = $_REQUEST["l"];  
        
echo "
    
    

 <script type=\"text/javascript\">
 
 window.slidecounter = 0;
  window.slidetimer = 30000;
  
  window.ts = setTimeout(refresh,window.slidetimer);
 
 function refresh()
 {
 try
  {
      var url = 'slide.php?l=$l' + '&count=' + window.slidecounter;
      //alert(url)
document.getElementById('frame').src = url;
}
catch(err)
  {
 }
  window. ts = setTimeout(refresh,window.slidetimer);
 }
 
 
 </script><div class='container'><iframe id='frame' src='slide.php?l=$l&count=0' style='width:100%;height:100%;border-style:none;'></iframe></div>

";
    }

?>

</body>
</html>
