<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
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
	$.get("runEventScript.php?scriptName=" + pluginSettings["button" + x + "script"]);
	$(cell).animate({'opacity':'1.0'}, 100);
}
</script>
<center><b><font size='<?=$pluginSettings['buttonFontSize'];?>px'><?
if (isset($pluginSettings['buttonTitle']) && ($pluginSettings['buttonTitle'] != ''))
	echo $pluginSettings['buttonTitle'];
else
	echo $settings['HostName'] . " - Big Buttons Plugin";
?></b></font>
<table border=1 width='100%' height='90%' bgcolor='#000000' style='position: absolute'>
<tr>
<?

$buttonCount = 0;
if (isset($pluginSettings['buttonCount']))
	$buttonCount = $pluginSettings['buttonCount'];

for ($x = 1; isset($pluginSettings[sprintf("button%02ddesc", $x)]); $x++)
{
	if (($x > 1) && (($x % 2) == 1))
		echo "</tr><tr>\n";

	printf( "<td width='50%%' bgcolor='%s' align='center' onClick='buttonClicked(this, \"%02d\");'><b><font size='%spx'>%s</font></b></td>\n",
		$pluginSettings[sprintf("button%02dcolor", $x)], $x,
		$pluginSettings["buttonFontSize"],
		$pluginSettings[sprintf("button%02ddesc", $x)]);
}

if (($x % 2) == 0)
	echo "<td bgcolor='black'>&nbsp;</td>\n";

?>
</tr>
</table>
