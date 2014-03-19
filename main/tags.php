<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$page_title = "$phrase[337]";

include ("../includes/htmlheader.php");

echo "
<div style=\"padding:2em;\">
<a style=\"float:right\" href=\"javascript:window.close()\">$phrase[266]</a><br><br>";
?>

<h1>Formatting tags</h1>
[bold]this is bold[/bold]<br><br>

<b>this is bold</b>
<br><br><br>
[italic]this is italic[/italic]<br><br>

<i>this is italic</i>


<br><br><br>
[bulletlist]<br>

[listitem] bullet one[/listitem]<br>
[listitem] bullet two[/listitem]<br>
[listitem] bullet three[/listitem]<br>
[/bulletlist]<br><br>


<ul>
<li>bullet one</li>
<li>bullet two</li>
<li>bullet three</li>
</ul>

<br><br><br>
[numberlist]<br>
[listitem] number one[/listitem]<br>
[listitem] number two[/listitem]<br>
[listitem] number three[/listitem]<br>
[/numberlist]<br><br>


<ol>
<li>number one</li>
<li>number two</li>
<li>number three</li>
</ol><br><br>



[link=http://www.blah.com]this is a link[/link]
<br>
This is a link
<br><br>


[nwlink=http://www.blah.com]this is a link that opens in a new window[/nwlink]
<br>
This is a link that opens in a new window 
<br><br>


[indent]this is indented[/indent]<br><br>

<blockquote>this is indented</blockquote><br><br><br>

<p>RSS feeds can be embedded into page content using the following tag. Read documentation for full description.<br><br> <b>[rss_brief]feed url[/rss_brief]</b>

<br><br> <b>[rss_full]feed url[/rss_full]</b></p><br><br>


[indent]this is indented[/indent]<br><br><br>

[preformat]<br><br>

cat<br>
&nbsp;&nbsp;in<br>
&nbsp;&nbsp;&nbsp;a<br>
&nbsp;&nbsp;&nbsp;&nbsp;hat<br>
[/preformat]
<pre>
cat
  in
   a
    hat
</pre>


</div>
	
<?php
include ("../includes/footer.php");
	
?>