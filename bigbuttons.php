<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<?
require_once("config.php");
require_once("common.php");
require_once("bb-common.php");
require_once("fppversion.php");

$jquery = glob("/opt/fpp/www/js/jquery-*.min.js");
printf("<script type='text/javascript' src='js/%s'></script>\n", basename($jquery[0]));
?>

<script type="text/javascript">
var pluginJson;
var fppVersionTriplet;


function sendButtonCommand(x)
{    
    var url = "api/command/";
    url += pluginJson["buttons"][x]["command"];
    
    if (fppVersionTriplet != "3.5.0") { 
        var data = new Array();
        $.each( pluginJson["buttons"][x]["args"], function(i, v) {
           data.push(v);
        });
        console.log(data);
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            async: false,
            data: JSON.stringify(data),
            processData: false,
            contentType: 'application/json',
            success: function (data) {
            }
        });
     } else { 
        $.each( pluginJson["buttons"][x]["args"], function(i, v) {
           url += "/";
           url += encodeURIComponent(v);
        });
        $.get(url);
    }

}
$(function(){

    fppVersionTriplet=$('body').data('fpp-version-triplet');

    $.ajax({
        type: "GET",
        url: 'fppjson.php?command=getPluginJSON&plugin=fpp-BigButtons',
        dataType: 'json',
        contentType: 'application/json',
        success: function (data) {
            pluginJson = $.parseJSON(data);
          
            $(".bb_heading").html(pluginJson.title);
            $(document).prop('title', pluginJson.title);
            $.each(pluginJson.buttons,function(i,button){
               
                var $newButton= $($('#buttonTemplate').html());
                $newButton.find('.bb_buttonDescription').html(button.description);
                $newButton.css({
                    backgroundColor:button.color,
                    fontSize:pluginJson.fontSize
                })
                if(button["adjustable"] !== undefined ){
                    
                    var adjustmentKey = Object.keys(button["adjustable"])[0]-1;
                    var adjustmentType = button["adjustable"][adjustmentKey+1];
                    
                    if(adjustmentType==='number'){
                        var $adjustableNumber = $($('#adjustableNumberTemplate').html());
                        $newButton.append(
                            $adjustableNumber
                        )
                        var $slider = $adjustableNumber.find('input[type=range]');
                        var command = button.command;
                        $slider.on('change',function(){
                            pluginJson['buttons'][i]['args'][adjustmentKey] = $(this).val();
                            sendButtonCommand(i);
                        })
                        
                        $.ajax({
                            dataType: "json",
                            async: false,
                            url: "api/commands/" + command,
                            success: function(commandResponse) {
                                $slider.prop("min", commandResponse['args'][adjustmentKey].min);
                                $slider.prop("max", commandResponse['args'][adjustmentKey].max);
                        
                                $.ajax({
                                    dataType: "text",
                                    async: false,
                                    url: commandResponse['args'][adjustmentKey]['adjustableGetValueURL'],
                                    success: function(commandResponse) {
                                            
                                            pluginJson['buttons'][i]['args'][adjustmentKey] = commandResponse;
                                            $slider.val(commandResponse);
                                    }
                                });
                            }
                        });  
                    }
                    else if(adjustmentType==='text'){
                        var $adjustableText = $($('#adjustableTextTemplate').html());
                        $newButton.append(
                            $adjustableText
                        )
                        pluginJson['buttons'][i]['args'][adjustmentKey] = text.value;
                        var $input = $adjustableText.find('input[type=text]');
                        $input.on('input change',function(){
                            sendButtonCommand(i);
                        })
                    }else{
                        $newButton.on('click',function(){
                            sendButtonCommand(i);
                        })   
                    }

                }
                $('#bb_buttonList').append($newButton);
            })
        }
    });

})




</script>
<style>

</style>
</head>
<body data-fpp-version-triplet="<?=getFPPVersionTriplet();?>">
<template id="adjustableTextTemplate">
<div class="bb_buttonTextInput">
    <input type="text" />
</div>
</template>
<template id="adjustableNumberTemplate">
<div class="bb_buttonSlider">
    <input type="range" />
</div>
</template>
<template id="buttonTemplate">
    <div class="bb_button">
        <div class="bb_buttonDescription">
        </div>
        
    </div>
</template>
<h1 class="bb_heading"></h1>
<div id="bb_buttonList">
</div>


<style type="text/css">
    *, *:before, *:after {
    box-sizing: border-box;
    }
    body{
        margin:0;
        padding:0;
        font-family:Helvetica,Arial,sans-serif;
        font-weight:bold;
    }
    #bb_buttonList{
        display: flex;
margin:1%;
    }
    .bb_button{
        flex:1;
        flex-direction:column;
        cursor:pointer;
        margin:1%;
        height:100px;
        border-radius:12px;
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff;
    }
    .bb_heading{
        text-align:center;
        border-bottom:1px solid #d2d2d2;
        padding:1em;
        font-size:1.2em;
        color:#393939;
        background-color:#f4f4f4;
    }
</style>

</body>
</html>
