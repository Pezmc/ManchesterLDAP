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
<li class="iheader">About You</li>
<?php

//var_dump($peopleFound);

  createRow("Chosen Name", $peopleFound[0]['cn'][0]);
  createRow("School", $peopleFound[0]['umanprimaryou'][0]);
  createRow("Faculty", $peopleFound[0]['ou'][1]);
  createRow("Level", $peopleFound[0]['title'][0]);
  createRow("Email", $peopleFound[0]['mail'][0]);
  createRow("ID Number", $peopleFound[0]['umanpersonid'][0]);
  if(is_numeric($peopleFound[0]['umanstudentyearofstudy'][0]))
    createRow("Year of Study", ordinal($peopleFound[0]['umanstudentyearofstudy'][0]));
  if(isset($peopleFound[0]['umantelephonenumberallow'][0])&&$peopleFound[0]['umantelephonenumberallow'][0]>=1)
    createRow("Telephone Number", $peopleFound[0]['telephonenumber'][0]);


?>
</ul>
</div> 
</body>
</html>
