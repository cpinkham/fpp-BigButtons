<head>
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
<style>
html, body {
	height: 100%;
	min-height: 100%;
}

.tableWrapper {
	height: 100%;
	width: 100%;
	display: inline-block;
}

</style>
</head>
<body>
<center style='height: 100%'><b><font size='<?=$pluginSettings['buttonFontSize'];?>px'>
<?
if (isset($_GET['title']))
	echo $_GET['title'];
else if (isset($pluginSettings['buttonTitle']) && ($pluginSettings['buttonTitle'] != ''))
	echo $pluginSettings['buttonTitle'];
else
	echo $settings['HostName'] . " - Big Buttons Plugin";
?></b></font>
<table border=1 width='100%' height='90%' bgcolor='#000000' style='position: absolute;left: 0px;'>
<tr>
<?

$buttonCount = 0;
if (isset($pluginSettings['buttonCount']))
	$buttonCount = $pluginSettings['buttonCount'];

$start = 1;
$end = 9999;

if (isset($_GET['start']))
	$start = $_GET['start'];

if (isset($_GET['end']))
	$end = $_GET['end'];

$i = 1;
$width = 2;

if (isset($_GET['width']))
	$width = $_GET['width'];

for ($x = $start; isset($pluginSettings[sprintf("button%02ddesc", $x)]) && ($x <= $end); $x++, $i++)
{
	if (($i > 1) && (($i % $width) == 1))
		echo "</tr><tr>\n";

	printf( "<td width='25%%' bgcolor='%s' align='center' onClick='buttonClicked(this, \"%02d\");'><b><font size='%spx'>%s</font></b></td>\n",
		$pluginSettings[sprintf("button%02dcolor", $x)], $x,
		$pluginSettings["buttonFontSize"],
		$pluginSettings[sprintf("button%02ddesc", $x)]);
}

if (($x % 2) == 0)
	echo "<td bgcolor='black'>&nbsp;</td>\n";

?>
</tr>
</table>
</body>
</html>
