<div id="global" class="settings">
<fieldset>
<legend>Big Buttons Config</legend>

<script>
$(document).ready(function(){
	$("#buttonTotal").on("change", function (event){
		
		location.reload(true);
		});
});
function colorChanged(id)
{
	var selectID = "button" + id + "color";
	var color = $('#' + selectID).val();
	$('#row' + id).css("background-color", color);
}
function SaveBigButtonConfig()
{
	var data = $('#bigButtonsForm').serialize();
	$.get('');
}
</script>
<?
$scripts = array();
if (file_exists($settings['scriptDirectory']))
{
	if ($handle = opendir($settings['scriptDirectory']))
	{
		while (($file = readdir($handle)) !== false)
		{
			if (!in_array($file, array('.', '..')))
			{
				$scripts[$file] = $file;
			}
		}
		$scripts['-- Choose a Script --'] = '';
		ksort($scripts);
	}
}
$colorList = array();
array_push($colorList, "aqua");
array_push($colorList, "blue");
array_push($colorList, "chocolate");
array_push($colorList, "coral");
array_push($colorList, "darkcyan");
array_push($colorList, "green");
array_push($colorList, "grey");
array_push($colorList, "ivory");
array_push($colorList, "olive");
array_push($colorList, "orange");
array_push($colorList, "pink");
array_push($colorList, "plum");
array_push($colorList, "purple");
array_push($colorList, "red");
array_push($colorList, "slategrey");
array_push($colorList, "tan");
array_push($colorList, "yellow");
$colors = array();
$colors['-- Choose a Color --'] = '';
foreach ($colorList as $color)
{
	$colors[$color] = $color;
}
$fontSizes = array();
for ($i = 10; $i <= 64; $i += 2)
{
	$fontSizes["$i"] = "$i";
}
$totalButtons = array();
for ($i = 1; $i <= 64; $i += 1)
{
	$totalButtons["$i"] = "$i";
}
function colorSelect($id)
{
	global $colors;
	echo "<select id='button" . $id . "color' onChange='colorChanged(\"" . $id . "\");'>\n";
	echo "<option value=''>-- Choose a Color --</option>\n";
	foreach ($colors as $color)
	{
		echo "<option>$color</option>\n";
	}
	echo "</select>\n";
}
?>
<table border=0>
<tr><td>Button Page Title:</td><td><? PrintSettingText("buttonTitle", 0, 0, 80, 60, "fpp-BigButtons"); ?></td></tr>
<tr><td>Text Font Size:</td><td><? PrintSettingSelect("Font Size", "buttonFontSize", 0, 0, '', $fontSizes, "fpp-BigButtons"); ?></td></tr>
<tr><td>Total Buttons:</td><td><? PrintSettingSelect("Button Total", "buttonTotal", 0, 0, '', $totalButtons, "fpp-BigButtons"); ?></td></tr>

</table>
<script>
		$('#buttonTitle').on('change keydown paste input', function()
			{
				var key = 'buttonTitle';
				var title = $('#' + key).val();
				if (pluginSettings[key] != title)
				{
					$.get('fppjson.php?command=setPluginSetting&plugin=fpp-BigButtons&key=' + key + '&value=' + title);
					pluginSettings[key] = title;
				}
			});
</script>

<form id='bigButtonsForm'>
<table border=1>
<?
for ($x = 1; $x <= $pluginSettings["buttonTotal"]; $x++)
{
	$id = sprintf( '%02d', $x);
	$color = $pluginSettings[sprintf("button%02dcolor", $x)];
?>
<tr><td id='row<?=$id; ?>'><center>Button <br / >#<?=$id; ?></center></td>
	<td><table border=0>
	<tr><td>Description:</td>
<!--		<td><input type='text' maxlength=60 size=60 id='button<?=$id; ?>' value='the desc'></td></tr> -->
		<td><? PrintSettingText("button" . $id . "desc", 0, 0, 80, 60, "fpp-BigButtons"); ?></td></tr>
	<tr><td>Script:</td>
		<td><? PrintSettingSelect("Script", "button" . $id . "script", 0, 0, '', $scripts, "fpp-BigButtons"); ?></td></tr>
	<tr><td>Color:</td>
		<td bgcolor='<?php echo $color; ?>';><? PrintSettingSelect("Color", "button" . $id . "color", 0, 0, '', $colors, "fpp-BigButtons"); ?></td></tr>
	<tr><td>Script Arguments:</td>
		<td><? PrintSettingText("button" . $id . "args", 0, 0, 80, 60, "fpp-BigButtons"); ?></td></tr>
		
	</table>
	</td></tr>
	<script>
		$('#button<?=$id;?>desc').on('change keydown paste input', function()
			{
				var key = 'button<?=$id;?>desc';
				var desc = $('#' + key).val();
				if (pluginSettings[key] != desc)
				{
					$.get('fppjson.php?command=setPluginSetting&plugin=fpp-BigButtons&key=' + key + '&value=' + desc);
					pluginSettings[key] = desc;
				}
			});
	</script>
	<script>
		$('#button<?=$id;?>args').on('change keydown paste input', function()
			{
				var key = 'button<?=$id;?>args';
				var args = $('#' + key).val();
				if (pluginSettings[key] != args)
				{
					$.get('fppjson.php?command=setPluginSetting&plugin=fpp-BigButtons&key=' + key + '&value=' + args);
					pluginSettings[key] = args;
				}
			});
	</script>
<?
}
?>
</table>
</form>