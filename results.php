<?php

function createRow($title, $variable) {
  if(!empty($variable))
    echo "<li><label>".$title."</label>".$variable."</li>\n";
}

function ordinal($input_number)
  {
    $number            = (string) $input_number;
    $last_digit        = substr($number, -1);
    $second_last_digit = substr($number, -2, 1);
    $suffix            = 'th';
    if ($second_last_digit != '1')
    {
      switch ($last_digit)
      {
	case '1':
	  $suffix = 'st';
	  break;
	case '2':
	  $suffix = 'nd';
	  break;
	case '3':
	  $suffix = 'rd';
	  break;
	default:
	  break;
      }
    }
    return $number.$suffix;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>Title</title>
<link rel="stylesheet" href="form.css" type="text/css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.0/jquery.min.js"></script>
<div class="iform"> 
<ul> 
<li class="iheader">Which one of these is you?</li>
<table style="width:750px">
<?php for($i=0; $i<$peopleFound['count']; $i++) {?>
<tr>
  <td><a href="?q=<?php echo $peopleFound[$i]['umanpersonid'][0]; ?>"><?php echo $peopleFound[$i]['cn'][0]; ?></a></td>
  <td><?php if(isset($peopleFound[$i]['umanstudentyearofstudy'])) echo ordinal($peopleFound[$i]['umanstudentyearofstudy'][0]).' year ';
            if(isset($peopleFound[$i]['title'])) echo $peopleFound[$i]['title'][0]; 
       ?></td>
  <td><?php echo $peopleFound[$i]['umanprimaryou'][0]; ?></td>
</tr>
<?php } ?>
</table>
</ul>
</div> 
</body>
</html>