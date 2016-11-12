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
var pluginSettings = new Array();
<?
foreach ($pluginSettings as $key => $value) {
	printf("    pluginSettings['%s'] = \"%s\";\n", $key, $value);
}
?>
function buttonClicked(cell, x)
{
	$(cell).animate({'opacity':'0.5'}, 100);
	$.get("runEventScript.php?scriptName=" + pluginSettings["button" + x + "script"] + "&args=" + pluginSettings["button" + x + "args"]);
	$(cell).animate({'opacity':'1.0'}, 100);
}
</script>
<center><b><font size='<?=$pluginSettings['buttonFontSize'];?>px'><?
if (isset($pluginSettings['buttonTitle']) && ($pluginSettings['buttonTitle'] != ''))
	echo $pluginSettings['buttonTitle'];
else
	echo $settings['HostName'] . " - Big Buttons Plugin";
?></b></font>
<table border=0 width='100%' height='90%' bgcolor=''>
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


