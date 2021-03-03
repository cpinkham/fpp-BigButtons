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

function sendButtonCommand(tab_i,j)
{    
    var url = "api/command/";
    url += pluginJson[tab_i]["buttons"][j]["command"];
    
    if (fppVersionTriplet != "3.5.0") { 
        var data = new Array();
        $.each( pluginJson[tab_i]["buttons"][j]["args"], function(i, v) {
           data.push(v);
        });
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
        $.each( pluginJson[tab_i]["buttons"][j]["args"], function(i, v) {
           url += "/";
           url += encodeURIComponent(v);
        });
        $.get(url);
    }

}
function lerp(x, y, a) {
 return x * (1 - a) + y * a;
}
var startScaling=360;
var endScaling=900;
function getScale(number){
  var at = number-startScaling;
  return at / (endScaling-startScaling);
}
function getFontScale(){
    return lerp(0.6, 1, 
        getScale(
            Math.max(
                Math.min(
                    window.innerWidth
                ,endScaling)
            ,startScaling)
        )
    );
}

function slugify(text)
{
  return text.toString().toLowerCase()
    .replace(/\s+/g, '-')           // Replace spaces with -
    .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
    .replace(/^-+/, '')             // Trim - from start of text
    .replace(/-+$/, '');            // Trim - from end of text
}
function getParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}
function SetCurrentTab(i){
    $('.bb-nav-item[data-tab-index='+i+']').addClass('bb-active').siblings().removeClass('bb-active');
    $('.bb-tab-panel[data-tab-index='+i+']').addClass('bb-active').siblings().removeClass('bb-active');
    document.title = $('.bb-nav-item[data-tab-index='+i+']').html();
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
            
            $.each(pluginJson,function(i,tab){
               $navButton = $('<button class="bb-nav-item">'+tab.title+'</button>').attr('data-tab-index',i);
               $tabPanel = $('<div class="bb-tab-panel"></div>').attr('data-tab-index',i);
               $navButton.click(function(){
                    SetCurrentTab(i);
               })
               $('#bb-nav').append($navButton)
               $('#bb-tabs').append($tabPanel)
               if(getParameterByName('tab')){
                    if(getParameterByName('tab')==slugify(tab.title)){
                        SetCurrentTab(i);
                    }
               }

                $.each(tab.buttons,function(j,button){
                
                    var $newButton= $($('#buttonTemplate').html());
                    $newButton.find('.bb-buttonDescription').html(button.description);
                    $newButton.css({
                        backgroundColor:button.color,
                        fontSize: tab.fontSize * getFontScale(),
                        color:'#fff'
                    })
                   
                    if(button["adjustable"] !== undefined ){
                        
                        var adjustmentKey = Object.keys(button["adjustable"])[0]-1;
                        var adjustmentType = button["adjustable"][adjustmentKey+1];
                        console.log(adjustmentKey)
                        if(adjustmentType==='number'){
                            var $adjustableNumber = $($('#adjustableNumberTemplate').html());
                            $newButton.append(
                                $adjustableNumber
                            )
                            var $slider = $adjustableNumber.find('input[type=range]');
                            var command = button.command;
                            $slider.on('change',function(){
                                tab['buttons'][j]['args'][adjustmentKey] = $(this).val();
                                sendButtonCommand(i,j);
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
                                                
                                                tab['buttons'][j]['args'][adjustmentKey] = commandResponse;
                                                $slider.val(commandResponse);
                                        }
                                    });
                                }
                            });  
                        }
                        else if(adjustmentType=='text'){
                            
                            var $adjustableText = $($('#adjustableTextTemplate').html());
                            $newButton.append(
                                $adjustableText
                            )
                            tab['buttons'][j]['args'][adjustmentKey] = text.value;
                            var $input = $adjustableText.find('input[type=text]');
                            $input.on('input change',function(){
                                sendButtonCommand(i,j);
                            })
                        }

                    }else{
                        
                        $newButton.on('click',function(){
                            console.log('clicking')
                            sendButtonCommand(i,j);
                        })   
                    }
                    $tabPanel.append($newButton);
                })




            });
            if($('.bb-active').length<1){
                SetCurrentTab(0);
            }
            if(getParameterByName('kiosk')!="true"){
                $('body').removeClass('is-kiosk')
            }
        }
    });

})

var getForegroundColor = function(hexcolor) {
    hexcolor = hexcolor.replace("#", "");
    var r = parseInt(hexcolor.substr(0,2),16);
    var g = parseInt(hexcolor.substr(2,2),16);
    var b = parseInt(hexcolor.substr(4,2),16);
    var yiq = ((r*299)+(g*587)+(b*114))/1000;
    return (yiq >= 128) ? '000' : 'fff';
}

</script>
<style>

</style>
</head>
<body class="is-kiosk" data-fpp-version-triplet="<?=getFPPVersionTriplet();?>">
<template id="adjustableTextTemplate">
<div class="bb-buttonTextInput">
    <input type="text" />
</div>
</template>
<template id="adjustableNumberTemplate">
<div class="bb-buttonSlider">
    <input type="range" />
</div>
</template>
<template id="buttonTemplate">
    <div class="bb-button">
        <div class="bb-buttonDescription">
        </div>
        
    </div>
</template>
<div class="bb-header">
    <div id="bb-nav">
    
    </div>
</div>
<div id="bb-tabs">
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
        font-size:16px;
    }
    body.is-kiosk .bb-header{
        display:none;
    }
    #bb-buttonList{
        display: flex;
        margin:1%;
    }
    .bb-tab-panel{
        padding:4px;
    }
    .bb-buttonDescription{
        text-align:center;
    }
    .bb-button{
        flex:1;
        flex-direction:column;
        cursor:pointer;
        height:100px;
        border-radius:12px;
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff;
    }
    .bb-header{
        text-align:center;
        border-bottom:1px solid #d2d2d2;
        font-size:1.2em;
        color:#393939;
        background-color:#f4f4f4;
    }
    .bb-nav-item{
        appearance:none;
        padding:1em 0.5em;
        background-color:transparent;
        border:none;
        font-weight:bold;
        font-size:16px;    
        cursor:pointer;
        opacity:0.75;
    }
    .bb-nav-item:hover {
        opacity:0.9;
    }
    #bb-tabs {
        padding:8px;
    }
    .bb-tab-panel{
        display:none;
    }
    .bb-tab-panel.bb-active{
        display:grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    .bb-nav-item.bb-active{
        border-bottom:2px solid #F63939;
        opacity:1;
    }
</style>

</body>
</html>
