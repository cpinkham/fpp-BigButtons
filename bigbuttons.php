<head>
<?
require_once("config.php");
require_once("common.php");
require_once("bb-common.php");

$jquery = glob("/opt/fpp/www/js/jquery-*.min.js");
printf("<script type='text/javascript' src='js/%s'></script>\n", basename($jquery[0]));

$pluginJson = convertAndGetSettings();

?>
<script type="text/javascript">
var pluginJson = <? echo json_encode($pluginJson, JSON_PRETTY_PRINT); ?>;

function buttonClicked(cell, x)
{
	$(cell).animate({'opacity':'0.5'}, 100);
    
    var url = "api/command/";
    url += pluginJson["buttons"][x]["command"];
    $.each( pluginJson["buttons"][x]["args"], function(key, v) {
           url += "/";
           url += encodeURIComponent(v);
       });
	$.get(url);
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

.adjustableText {
    width: 90%;
}

.adjustableSlider {
  -webkit-appearance: none;
  width: 90%;
  height: 15px;
  border-radius: 5px;
  outline: none;
  opacity: 0.7;
  background: #3F3F3F;
  -webkit-transition: .2s;
  transition: opacity .2s;
}

.adjustableSlider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 25px;
  height: 25px;
  border-radius: 50%;
  background: black;
  cursor: pointer;
}

.adjustableSlider::-moz-range-thumb {
  width: 25px;
  height: 25px;
  border-radius: 50%;
  cursor: pointer;
}

</style>
</head>
<body>
<center style='height: 100%'><b><font style='font-size: <?=$pluginJson["fontSize"];?>px;'>
<?
if (isset($_GET['title']))
	echo $_GET['title'];
else if (isset($pluginJson['title']) && ($pluginJson['title'] != ''))
	echo $pluginJson['title'];
else
	echo $settings['HostName'] . " - Big Buttons Plugin";
?></b></font>
<table border=1 width='100%' height='90%' bgcolor='#000000' style='position: absolute;left: 0px;'>
<tr>
<?

$buttonCount = 0;
if (array_key_exists("buttons", $pluginJson)) {
    for ($x = 1; $x <= 20; $x++) {
        if (array_key_exists($x, $pluginJson["buttons"])) {
            $buttonCount = $x;
        }
    }
}

$start = 1;
$end = $buttonCount;

$buttonFontSize = returnIfExists($pluginJson, "fontSize");
if ($buttonFontSize == "") {
    $buttonFontSize = 12;
}

if (isset($_GET['start']))
	$start = $_GET['start'];

if (isset($_GET['end']))
	$end = $_GET['end'];

$i = 1;
$width = 2;

if (isset($_GET['width']))
	$width = $_GET['width'];

for ($x = $start; $x <= $end; $x++) {
    if (array_key_exists($x, $pluginJson["buttons"])) {
        if (($i > 1) && (($i % $width) == 1))
            echo "</tr><tr>\n";
        $i++;

        $color = returnIfExists($pluginJson["buttons"][$x], "color");
        if ($color == "") {
            $color = "aqua";
        }
        printf( "<td width='25%%' bgcolor='%s' align='center' onClick='buttonClicked(this, \"%d\");'><b><font style='font-size: %spx;'>%s</font></b>",
               $color,
               $x,
               $buttonFontSize,
            returnIfExists($pluginJson["buttons"][$x], "description"));
        
        if (returnIfExists($pluginJson["buttons"][$x], "adjustable") != "") {
            $adj = array_keys($pluginJson["buttons"][$x]["adjustable"])[0];
            $type = $pluginJson["buttons"][$x]["adjustable"][$adj];
            if ($type == "number") {
            
                printf("\n<script>\nfunction OnSlider%dChanged(slider) { \n", $x);
                printf("    var command = \"%s\";\n", returnIfExists($pluginJson["buttons"][$x], "command"));
                printf("    var arg = %d;\n", ((int)$adj - 1));
                printf("    pluginJson['buttons'][%d]['args'][arg] = slider.value;\n", $x);
                printf("}\n</script>\n");
                printf("<br><input type='range' class='adjustableSlider' id='slider%d' onchange='OnSlider%dChanged(this);' min='0' max='10' value='1'></input>\n", $x, $x);
                printf("<script>\n");
                printf("    var command = \"%s\";\n", returnIfExists($pluginJson["buttons"][$x], "command"));
                printf("    var arg = %d;\n", ((int)$adj - 1));
            ?>
                $.ajax({
                         dataType: "json",
                         async: false,
                         url: "api/commands/" + command,
                         success: function(data) {
                            $('#slider<?=$x;?>').prop("min", data['args'][arg].min);
                            $('#slider<?=$x;?>').prop("max", data['args'][arg].max);
                       
                            $.ajax({
                                   dataType: "text",
                                   async: false,
                                   url: data['args'][arg]['adjustableGetValueURL'],
                                   success: function(data) {
                                        pluginJson['buttons'][<?=$x;?>]['args'][arg] = data;
                                        $('#slider<?=$x;?>').prop("value", data);
                                   }
                            });
                         }
                       });
            <?
                printf("</script>\n");

            } else if ($type == "text") {
                printf("<br><input type='text' class='adjustableText' id='text%d' onchange='OnText%dChanged(this);' ></input>\n", $x, $x);
                ?>
                <script>
                $("#text<?=$x;?>").click(function(e){
                    e.stopPropagation();
                });
                function OnText<?=$x;?>Changed(text) {
                    var arg = <?=$adj;?> - 1;
                    pluginJson['buttons'][<?=$x;?>]['args'][arg] = text.value;
                    buttonClicked(text.parentElement, '<?=$x;?>');
                }
                </script>
                <?
            }

        }

        
        printf("</td>\n");
    }
}

if (($x % 2) == 0)
	echo "<td bgcolor='black'>&nbsp;</td>\n";

?>
</tr>
</table>
</body>
</html>
