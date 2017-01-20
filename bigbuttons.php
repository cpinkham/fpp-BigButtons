<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
<?
require_once("config.php");
require_once("common.php");
$pluginSettings = array();
$pluginConfigFile = $settings['configDirectory'] . "/plugin." . $_GET['plugin'];
if (file_exists($pluginConfigFile))
	$pluginSettings = parse_ini_file($pluginConfigFile);
?>
<script type="text/javascript">
$(document).ready(function(){GetVolume();});
var pluginSettings = new Array();
<?
foreach ($pluginSettings as $key => $value) {
	printf("    pluginSettings['%s'] = \"%s\";\n", $key, $value);
}
?>
function buttonClicked(cell, x)
{
	var txt;
	var r = confirm("You are about run: " + pluginSettings["button" + x + "desc"] + "\nArguments: " + pluginSettings["button" + x + "args"]);
	if (r == true) {
	$(cell).animate({'opacity':'0.5'}, 100);
	$.get("runEventScript.php?scriptName=" + pluginSettings["button" + x + "script"] + "&args=" + pluginSettings["button" + x + "args"]);
	$(cell).animate({'opacity':'1.0'}, 100);
	} else {
    alert("CANCELED")
	}
}
function volumeSet()
{
var x = document.getElementById("slider").value
$.get("runEventScript.php?scriptName=ChangeVolume.sh&args=SET "+ x);
$('#volume').html(x);
}

function GetVolume()
{
    var xmlhttp=new XMLHttpRequest();
		var url = "fppxml.php?command=getVolume";
		xmlhttp.open("GET",url,true);
		xmlhttp.setRequestHeader('Content-Type', 'text/xml');
		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4 && xmlhttp.status==200) 
			{
					var xmlDoc=xmlhttp.responseXML; 
					var Volume = parseInt(xmlDoc.getElementsByTagName('Volume')[0].childNodes[0].textContent);
					if ((Volume < 0) || (Volume == "NaN"))
					{
						Volume = 75;
						SetVolume(Volume);
					}
					$('#volume').html(Volume);
					$('#slider').val(Volume);
			}
		};
		xmlhttp.send();

}
</script>
<style>
.button-page {
  width: 95%;
  padding: 0.5% 0 0;
  margin: 0 auto;
}
.title {
text-align: center;
font-size:50px;
}
.form {
  position: relative;
  z-index: 1;
  background: #FFFFFF;
  max-width: inherit;
  padding: 45px;
  text-align: center;
  box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
}

.container {
  position: 90%;
  z-index: 1;
  max-width: 300px;
  margin: 0 auto;
}
.container:before, .container:after {
  content: "";
  display: block;
  clear: both;
}
.container .info {
  margin: auto;
  text-align: center;
}
.container .info h1 {
  margin: 0 0 15px;
  padding: 0;
  font-size: 36px;
  font-weight: 300;
  color: #1a1a1a;
}
.container .info span {
  color: #4d4d4d;
  font-size: 12px;
}
.container .info span a {
  color: #000000;
  text-decoration: none;
}
.container .info span .fa {
  color: #EF3B3A;
}
.volume {
font-size: 60px;
width:100%;
text-align: center;
padding: 0 0 10px;
}
body {
  background: #76b852; /* fallback for old browsers */
  background: -webkit-linear-gradient(right, #76b852, #8DC26F);
  background: -moz-linear-gradient(right, #76b852, #8DC26F);
  background: -o-linear-gradient(right, #76b852, #8DC26F);
  background: linear-gradient(to left, #76b852, #8DC26F);
  font-family: "Roboto", sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;  
  overflow-x: hidden;    
}
</style>


<body>
<div class="button-page">
<div class="title"><h2>
<?
if (isset($pluginSettings['buttonTitle']) && ($pluginSettings['buttonTitle'] != ''))
	echo $pluginSettings['buttonTitle'];
else
	echo $settings['HostName'] . " - Big Buttons Plugin";
?>
</h2></div>
  <div class="form">
<select name="volume" id="slider" class="volume" onchange="volumeSet()">
<?php
    for ($i=1; $i<=100; $i++)
    {
        ?>
            <option value="<?php echo $i;?>">Volume: <?php echo $i;?>%</option>
        <?php
    }
?>
  </select>
  <br />
  <br />

<table border=0 width='100%' height='65%' bgcolor=''>
<tr>
<?
$buttonCount = 0;
if (isset($pluginSettings["buttonTotal"]))
	$buttonCount = $pluginSettings["buttonTotal"];



for ($x = 1; $pluginSettings["buttonTotal"] >= $x; $x++)
{
	if (($x > 1) && (($x % 2) == 1)){
		echo "</tr><tr>\n";
		}
	printf( "<td " );
	
		if ($x == ($pluginSettings["buttonTotal"]) && ($x % 2)==1){
			echo(" colspan=2 ");
			

		}else{
			echo(" width='50%' ");
		}
	
printf(" bgcolor='%s' align='center' onClick='buttonClicked(this, \"%02d\");'><b><font size='%spx'>%s</font></b></td>\n",
$pluginSettings[sprintf("button%02dcolor", $x)], $x,
$pluginSettings["buttonFontSize"],
$pluginSettings[sprintf("button%02ddesc", $x)]);
		
}

?>
</tr>
</table>
  </div>
  <h1><center><p class="message"><a href="plugin.php?plugin=fpp-BigButtons&page=config.php">Configuration Page</a></p></center></h1>


</body>