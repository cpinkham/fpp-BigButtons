<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<?
require_once("config.php");
require_once("common.php");

require_once("bb-common.php");

require_once("fppversion.php");

include 'common/menuHead.inc';
?>

<script type="text/javascript">
var pluginJson;

function sendButtonCommand(tab_i,j)
{    
    if (pluginJson[tab_i]["buttons"][j]["command"] == "Browser URL") {
        var url = pluginJson[tab_i]["buttons"][j]["args"][0];
        window.location.href = url;
    } else {
        var url = "api/command/";
        var data = new Object();
        data["command"] = pluginJson[tab_i]["buttons"][j]["command"];
		data["args"] = pluginJson[tab_i]["buttons"][j]["args"];
		data["multisyncCommand"]  = pluginJson[tab_i]["buttons"][j]["multisyncCommand"];
		if (data["multisyncCommand"]  = pluginJson[tab_i]["buttons"][j]["multisyncCommand"] == true){
			data["multisyncHosts"] = pluginJson[tab_i]["buttons"][j]["multisyncHosts"];
		}        
       
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
    $('body').css({
      backgroundColor:'#'+$('.bb-tab-panel[data-tab-index='+i+']').data('color')
    })
    document.title = $('.bb-nav-item[data-tab-index='+i+']').html();
}
$(function(){
    $.get('api/configfile/plugin.fpp-BigButtons.json')
    .done(function(data) {
        processBigButtonConfig(data);
    })
    .fail(function(data) {
        processBigButtonConfigFail([]);
    });
	
    function processBigButtonConfigFail(data) {
        var link = $('<a>', {
        href: 'plugin.php?_menu=content&plugin=fpp-BigButtons&page=config.php',
        text: 'Big Buttons is unconfigured, click me to go to the configuration page'
        });
	    
        // Append the link to the body
        $('body').append(link);
    }
	
    function processBigButtonConfig(data) {
        if (typeof data==="string"){
            pluginJson = $.parseJSON(data);
        }else{
            pluginJson = data;
        }
    
        $(".bb_heading").html(pluginJson.title);
        $(document).prop('title', pluginJson.title);
        
        $.each(pluginJson,function(i,tab){
            $navButton = $('<button class="bb-nav-item">'+tab.title+'</button>').attr('data-tab-index',i);
            $tabPanel = $('<div class="bb-tab-panel"></div>').attr('data-tab-index',i);
            $tabPanelTabContainer = $('<div class="bb-tab-panel-tab-container"></div>');
            if(tab.description){
                $tabPanelDescription = $('<div class="bb-tab-panel-description">'+tab.description+'</div>');
                $tabPanel.append($tabPanelDescription);              
            }

            $tabPanel.append($tabPanelTabContainer);
            $navButton.click(function(){
                SetCurrentTab(i);
            });
            var tabColor = 'f5f5f5';
            if (tab.color) {
                tabColor=tab.color;
            }
            $tabPanel.data('color',tabColor);
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
                }).addClass('bbw-'+Math.round(button.buttonWidthRatio*100))
                .addClass('bbh-'+Math.round(button.buttonHeightValue));

                if(button.icon!=''){
                    $newButton.find('.bb-iconWrap').prepend('<i class="fas fa-'+button.icon+'"/>');
                }
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
                            tab['buttons'][j]['args'][adjustmentKey] = $(this).val();
                            $newButton.find('#sliderValue').html($(this).val()); 
                            $.jGrowl(button.description + " has been activated",{themeState:'success'});
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
                                            $newButton.find('#sliderValue').html(commandResponse); 
                                    }
                                });
                            }
                        });  
                    } else if(adjustmentType=='text'){
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
                } else {
                    $newButton.on('click',function(){
                        $.jGrowl(button.description + " has been activated",{themeState:'success'});
                        sendButtonCommand(i,j);
                    })   
                }
                $tabPanelTabContainer.append($newButton);
            })
        });
        if($('.bb-active').length<1){
            SetCurrentTab(0);
        }
        if(getParameterByName('kiosk')!="true"){
            $('body').removeClass('is-kiosk')
        }
    };
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
    <p>Value: <span id="sliderValue"></span></p>
</div>
</template>
<template id="buttonTemplate">
    <div class="bb-button">
        <div class="bb-iconWrap"></div>
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
        font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;
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
    .bb-iconWrap{
      margin-bottom:0.2em;
    }
    .bb-tab-panel{
        padding:4px;
    }
    .bb-buttonDescription{
        text-align:center;
    }
    .bb-tab-panel-description{
      padding-top:0.5em;
      padding-bottom:1em;
      width:100%;
      text-align:center;
    }
    .bb-button{
        flex-direction:column;
        cursor:pointer;
        height:100px;
        border-radius:12px;
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff;
        margin:0.5%;
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
        padding:0.5% 1%;
    }
    .bb-tab-panel{
        display:none;

    }
    .bb-tab-panel-tab-container{
        flex:1;
        flex-wrap:wrap;
        display:flex;
        margin-left:-0.5%;
        margin-right:-0.5%;
    }
    .bb-tab-panel.bb-active {

        display:block;

    }
    .bb-nav-item.bb-active{
        border-bottom:2px solid #F63939;
        opacity:1;
    }
    .bbw-1 {
  width: 0%;
}

.bbw-2 {
  width: 1%;
}

.bbw-3 {
  width: 2%;
}

.bbw-4 {
  width: 3%;
}

.bbw-5 {
  width: 4%;
}

.bbw-6 {
  width: 5%;
}

.bbw-7 {
  width: 6%;
}

.bbw-8 {
  width: 7%;
}

.bbw-9 {
  width: 8%;
}

.bbw-10 {
  width: 9%;
}

.bbw-11 {
  width: 10%;
}

.bbw-12 {
  width: 11%;
}

.bbw-13 {
  width: 12%;
}

.bbw-14 {
  width: 13%;
}

.bbw-15 {
  width: 14%;
}

.bbw-16 {
  width: 15%;
}

.bbw-17 {
  width: 16%;
}

.bbw-18 {
  width: 17%;
}

.bbw-19 {
  width: 18%;
}

.bbw-20 {
  width: 19%;
}

.bbw-21 {
  width: 20%;
}

.bbw-22 {
  width: 21%;
}

.bbw-23 {
  width: 22%;
}

.bbw-24 {
  width: 23%;
}

.bbw-25 {
  width: 24%;
}

.bbw-26 {
  width: 25%;
}

.bbw-27 {
  width: 26%;
}

.bbw-28 {
  width: 27%;
}

.bbw-29 {
  width: 28%;
}

.bbw-30 {
  width: 29%;
}

.bbw-31 {
  width: 32.33%;
}

.bbw-32 {
  width: 32.33%;
}

.bbw-33 {
  width: 32.33%;
}

.bbw-34 {
  width: 32.33%;
}

.bbw-35 {
  width: 32.33%;
}

.bbw-36 {
  width: 32.33%;
}

.bbw-37 {
  width: 36%;
}

.bbw-38 {
  width: 37%;
}

.bbw-39 {
  width: 38%;
}

.bbw-40 {
  width: 39%;
}

.bbw-41 {
  width: 40%;
}

.bbw-42 {
  width: 41%;
}

.bbw-43 {
  width: 42%;
}

.bbw-44 {
  width: 43%;
}

.bbw-45 {
  width: 44%;
}

.bbw-46 {
  width: 45%;
}

.bbw-47 {
  width:49%;
}

.bbw-48 {
  width:49%;
}

.bbw-49 {
  width:49%;
}

.bbw-50 {
  width:49%;
}

.bbw-51 {
  width:49%;
}

.bbw-52 {
  width:49%;
}

.bbw-53 {
  width:49%;
}

.bbw-54 {
  width: 53%;
}

.bbw-55 {
  width: 54%;
}

.bbw-56 {
  width: 55%;
}

.bbw-57 {
  width: 56%;
}

.bbw-58 {
  width: 57%;
}

.bbw-59 {
  width: 58%;
}

.bbw-60 {
  width: 59%;
}

.bbw-61 {
  width: 60%;
}

.bbw-62 {
  width: 61%;
}

.bbw-63 {
  width: 62%;
}

.bbw-64 {
  width: 63%;
}

.bbw-65 {
  width: 65.666%;
}

.bbw-66 {
  width: 65.666%;
}

.bbw-67 {
  width: 65.666%;
}

.bbw-68 {
  width: 65.666%;
}

.bbw-69 {
  width: 65.666%;
}

.bbw-70 {
  width: 65.666%;
}

.bbw-71 {
  width: 70%;
}

.bbw-72 {
  width: 71%;
}

.bbw-73 {
  width: 72%;
}

.bbw-74 {
  width: 73%;
}

.bbw-75 {
  width: 74%;
}

.bbw-76 {
  width: 75%;
}

.bbw-77 {
  width: 76%;
}

.bbw-78 {
  width: 77%;
}

.bbw-79 {
  width: 78%;
}

.bbw-80 {
  width: 79%;
}

.bbw-81 {
  width: 80%;
}

.bbw-82 {
  width: 81%;
}

.bbw-83 {
  width: 82%;
}

.bbw-84 {
  width: 83%;
}

.bbw-85 {
  width: 84%;
}

.bbw-86 {
  width: 85%;
}

.bbw-87 {
  width: 86%;
}

.bbw-88 {
  width: 87%;
}

.bbw-89 {
  width: 88%;
}

.bbw-90 {
  width: 89%;
}

.bbw-91 {
  width: 90%;
}

.bbw-92 {
  width: 91%;
}

.bbw-93 {
  width: 92%;
}

.bbw-94 {
  width: 93%;
}

.bbw-95 {
  width: 94%;
}

.bbw-96 {
  width: 95%;
}

.bbw-97 {
  width: 96%;
}

.bbw-98 {
  width: 100%;
}

.bbw-99 {
  width: 100%;
}

.bbw-100 {
  width: 100%;
}

.bbh-1 {
  height: 10px;
}

.bbh-2 {
  height: 20px;
}

.bbh-3 {
  height: 30px;
}

.bbh-4 {
  height: 40px;
}

.bbh-5 {
  height: 50px;
}

.bbh-6 {
  height: 60px;
}

.bbh-7 {
  height: 70px;
}

.bbh-8 {
  height: 80px;
}

.bbh-9 {
  height: 90px;
}

.bbh-10 {
  height: 100px;
}

.bbh-11 {
  height: 110px;
}

.bbh-12 {
  height: 120px;
}

.bbh-13 {
  height: 130px;
}

.bbh-14 {
  height: 140px;
}

.bbh-15 {
  height: 150px;
}

.bbh-16 {
  height: 160px;
}

.bbh-17 {
  height: 170px;
}

.bbh-18 {
  height: 180px;
}

.bbh-19 {
  height: 190px;
}

.bbh-20 {
  height: 200px;
}

.bbh-21 {
  height: 210px;
}

.bbh-22 {
  height: 220px;
}

.bbh-23 {
  height: 230px;
}

.bbh-24 {
  height: 240px;
}

.bbh-25 {
  height: 250px;
}

.bbh-26 {
  height: 260px;
}

.bbh-27 {
  height: 270px;
}

.bbh-28 {
  height: 280px;
}

.bbh-29 {
  height: 290px;
}

.bbh-30 {
  height: 300px;
}

.bbh-31 {
  height: 310px;
}

.bbh-32 {
  height: 320px;
}

.bbh-33 {
  height: 330px;
}

.bbh-34 {
  height: 340px;
}

.bbh-35 {
  height: 350px;
}

.bbh-36 {
  height: 360px;
}

.bbh-37 {
  height: 370px;
}

.bbh-38 {
  height: 380px;
}

.bbh-39 {
  height: 390px;
}

.bbh-40 {
  height: 400px;
}
</style>

</body>
</html>
