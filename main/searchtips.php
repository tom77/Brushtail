<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


include("../includes/leftsidebar.php");

echo "<div id=\"content\"><div>
<h1>Search tips</h1>
			An intranet search will search the content of intranet pages. It will also seach the metadata(if any) of uploaded documents .<br><br>";

if ($DATABASE_FORMAT == "mysql")
{
echo "<ul id=\"searchtips\">
						<li>Search words must be minimum of 4 characters.</li>
						<li>Searching is not case sensitive.</li>
						<li>By default an <b>OR</b> search is performed.<br>
						<b>information manual</b> would return pages containing either of these words.</li>
						
						<li>Boolean searching is possible with plus and minus signs.
                         A leading plus sign indicates that this word must be present.<br>
                         A leading minus sign indicates that this word must NOT be present.
                        <b>+leave</b> will return matches containing leave.<br>
						<b>-leave</b> will return matches that must not contain word leave.<br>
						 <b>+leave +form</b> will return matches containing both the words leave and form.
                         <b>+leave -form</b> will return matches containing the word leave but  NOT the word form.</li>
						<li>Using quotes will conduct a phrase search.<br>
                        <b>\"leave form\"</b> will return records containing this exact phrase.</li>
						<li>The asterisk (*) character can be used for truncation.<br>
                        <b>hol*</b> will return matches containing words that start with \"hol\" such as hold, hole, holiday etc.<br></li>
						<li><b>( )</b> Parentheses are used to group words into subexpressions. Parenthesized groups can be nested. </li>
                        </ul>";
}

else
{
echo "If multiple search terms are used, a \"AND\" search is performed.";

}


echo "</div></div>";

include("../includes/rightsidebar.php");


include ("../includes/footer.php");

?>

