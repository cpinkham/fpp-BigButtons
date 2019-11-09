
<?
require 'bb-common.php';
$pluginJson = convertAndGetSettings();
?>


<div id="global" class="settings">
<fieldset>
<legend>Big Buttons Config</legend>

<script>
function ButtonColorChanged(id)
{
	var selectID = "button_" + id + "_color";
	var color = $('#' + selectID).val();
	$('#row' + id).css("background-color", color);
}



var bigButtonsConfig = <? echo json_encode($pluginJson, JSON_PRETTY_PRINT); ?>;
function SaveBigButtonConfig() {
    $.ajax({
        type: "POST",
        url: 'fppjson.php?command=setPluginJSON&plugin=fpp-BigButtons',
        dataType: 'json',
        async: false,
        data: JSON.stringify(bigButtonsConfig),
        processData: false,
        contentType: 'application/json',
        success: function (data) {
           //bigButtonsConfig = data;
        }
    });
}

function buttonFontSizeChanged() {
    bigButtonsConfig['fontSize'] = parseInt($('#buttonFontSize').val());
    SaveBigButtonConfig();
}

function SaveButton(i) {
    if (typeof bigButtonsConfig["buttons"][i] == "undefined") {
        bigButtonsConfig["buttons"][i] = {};
    }
    bigButtonsConfig["buttons"][i]["description"] = $('#button_' + i + '_Title').val();
    bigButtonsConfig["buttons"][i]["color"] = $('#button_' + i + '_color').val();
    CommandToJSON('button_' + i + '_Command', 'tableButton' + i, bigButtonsConfig["buttons"][i]);
    
    if (bigButtonsConfig["buttons"][i]["description"] == ""
        && bigButtonsConfig["buttons"][i]["command"] == "") {
        delete bigButtonsConfig["buttons"][i];
    }
}
function SaveButtons() {
    for (var x = 1; x <= 20; x++) {
        SaveButton(x);
    }
    SaveBigButtonConfig();
}

</script>
<?

$colorList = array();
array_push($colorList, "aqua");
array_push($colorList, "blue");
array_push($colorList, "chocolate");
array_push($colorList, "coral");
array_push($colorList, "cyan");
array_push($colorList, "darkcyan");
array_push($colorList, "green");
array_push($colorList, "grey");
array_push($colorList, "ivory");
array_push($colorList, "lightblue");
array_push($colorList, "lightcoral");
array_push($colorList, "lightcyan");
array_push($colorList, "lightgrey");
array_push($colorList, "lightgreen");
array_push($colorList, "lightpink");
array_push($colorList, "lightyellow");
array_push($colorList, "olive");
array_push($colorList, "orange");
array_push($colorList, "pink");
array_push($colorList, "plum");
array_push($colorList, "purple");
array_push($colorList, "red");
array_push($colorList, "slategrey");
array_push($colorList, "tan");
array_push($colorList, "white");
array_push($colorList, "whitesmoke");
array_push($colorList, "yellow");

function PrintFontSizes($cur2) {
    $cur = (int)$cur2;
    
    for ($i = 10; $i <= 64; $i += 2) {
        echo "<option value='" . $i . "'";
        if ($i == $cur) {
            echo " selected";
        }
        echo ">" . $i . "</option>";
    }
}
function PrintColors($cur) {
    global $colorList;
    foreach ($colorList as $color) {
        echo "<option value='$color'";
        if ($color == $cur) {
            echo " selected";
        }
        echo ">$color</option>\n";
    }
}

?>

<table border=0>
<tr><td>Button Page Title:</td><td><input type='text' id='buttonTitle' maxlength='80' size='60' value='<? echo $pluginJson["title"] ?>'></input></td></tr>
<tr><td>Text Font Size:</td><td><select id='buttonFontSize' onChange='buttonFontSizeChanged();'><? PrintFontSizes($pluginJson["fontSize"]) ?></select></td></tr>
<tr><td><input type="button" value="Save" class="buttons" onclick="SaveButtons();"></td></tr>
</table>
<script>
        $('#buttonTitle').on('change keydown paste input', function() {
            bigButtonsConfig['title'] = $('#buttonTitle').val();
            SaveBigButtonConfig();
        });
</script>

<table border=1>
<?
for ($x = 1; $x <= 20; $x++) {
    
    if ($x % 2) {
        echo "<tr>";
    }
    
    $description = "";
    $color = "aqua";
    $command = "";
    $buttonJson = array();
    $buttonJson["command"] = "";
    if (array_key_exists("buttons", $pluginJson)
        && array_key_exists($x, $pluginJson["buttons"])) {
        $buttonJson = $pluginJson["buttons"][$x];
        $description = returnIfExists($pluginJson["buttons"][$x], "description");
        $color = returnIfExists($pluginJson["buttons"][$x], "color");;
        $command = returnIfExists($pluginJson["buttons"][$x], "command");;
    }

?>
    <td id='row<?=$x;?>'>Button #<?=$x;?></td>
	<td><table border=0 id='tableButton<?=$x; ?>'>
    
	<tr><td>Description:</td><td><input type='text' id='button_<?=$x;?>_Title' maxlength='80' size='60' value='<?=$description;?>'></input></td></tr>
	<tr><td>Color:</td>
        <td><select id='button_<?=$x;?>_color' onChange='ButtonColorChanged(<?=$x;?>);'><? PrintColors($color); ?></select></td></tr>
    <tr><td>Command:</td>
        <td><select id='button_<?=$x;?>_Command' onChange='CommandSelectChanged("button_<?=$x; ?>_Command", "tableButton<?=$x;?>", true);'><option value=""></option></select></td></tr>
	</table>
	</td>
	<script>
        ButtonColorChanged(<?=$x;?>);
        LoadCommandList('button_<?=$x;?>_Command');
	</script>
<?
    if (!($x % 2)) {
        echo "</tr>";
    }
}
?>
</table>


<script>
<?
for ($x = 1; $x <= 20; $x++) {
    echo "PopulateExistingCommand(bigButtonsConfig['buttons'][" . $x . "], 'button_" . $x . "_Command', 'tableButton" . $x . "', true);\n";
}
?>
</script>
