<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>Title</title>
<link rel="stylesheet" href="form.css" type="text/css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.0/jquery.min.js"></script>
<script type="text/javascript">
<!--
	function sendForm(){	
		jQuery("#imessageOK, #imessageERROR").hide();
		jQuery(".required").removeClass("required");
		if(jQuery("#query").val()==""){
		  jQuery("#query").addClass("required");
		  window.scroll(0,0);
		  return false;
		}
		jQuery("#Whatdoyouknowaboutme").val("Please Wait...");	
		return true;	
	}
-->
</script>
<form method="post" action="index.php" onsubmit="return sendForm()" class="iform"> 
<?php if(!empty($error)) { echo '<div id="imessageERROR" style="display:block;">'.$error.'</div> '; } ?>
<div id="imessageOK">Thank you! Message Sent!</div> 
<div id="imessageERROR">ERROR: Message Not Sent!</div> 
<ul> 
<li class="iheader">About You</li> 
<li><label for="query">*Name/ID</label><input class="itext" type="text" name="query" id="query" /></li> 
<li class="iseparator">&nbsp;</li> 
<li><label>&nbsp;</label><input type="submit" onsubmit="return sendForm()" class="ibutton" name="Whatdoyouknowaboutme" id="Whatdoyouknowaboutme" value="What do you know about me?" /></li> 
</ul></form> 
</body>
</html>