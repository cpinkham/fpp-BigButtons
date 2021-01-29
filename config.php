

<?
require 'bb-common.php';
$pluginJson = convertAndGetSettings();
?>


<div id="global" class="settings">

<link  rel="stylesheet" href="/jquery/colpick/css/colpick.css"/>
<script src="/jquery/colpick/js/colpick.js"></script>
<script>
let bigButtonsConfig=null;


function UpdateBigButtonConfig(config) {
    bigButtonsConfig = config;
    console.log(bigButtonsConfig);
}
function SaveBigButtonConfig(config) {
    var data = JSON.stringify(config);
    console.log(config);
    $.ajax({
        type: "POST",
        url: 'fppjson.php?command=setPluginJSON&plugin=fpp-BigButtons',
        dataType: 'json',
        async: false,
        data: data,
        processData: false,
        contentType: 'application/json',
        success: function (data) {
   
           $('#saveBigButtonConfigButton').addClass('success');
           setTimeout(function(){$('#saveBigButtonConfigButton').removeClass('success')},3000);
        }
    });
}

// function buttonFontSizeChanged() {
//     bigButtonsConfig['fontSize'] = parseInt($('#buttonFontSize').val());
//     $('.buttonTitle').css({
//         fontSize:parseInt($('#buttonFontSize').val())
//     });
// }

function GetButton(i) {
    
    var button = {
        "description": $('#button_' + i + '_Title').val(),
        "color": $('#button_' + i + '_color').val()
    };
    CommandToJSON('button_' + i + '_Command', 'tableButton' + i, button);
    return button;
}
function SaveButtons() {
    


    bigButtonsConfig["buttons"]=[];
    
    $.each($('#buttonList li'),function(i,v){
        //console.log([v,$('#button_' + i + '_Title').val()]);
        var key = ""+i;
        var button = GetButton(i);
        
        bigButtonsConfig["buttons"][key] = button;

    });
    
    
    
    SaveBigButtonConfig(bigButtonsConfig);
}
function updateButtonRow(i,v){

var $newButtonRow = $(v);
var newButtonRowColor = 'button_'+i+'_color';
var newButtonRowCommand = 'button_'+i+'_Command';
var newButtonRowTitle = 'button_'+i+'_Title';
var newButtonRowTable = 'tableButton'+i;
$newButtonRow.data('bbKey',i);
$newButtonRow.find('.buttonCommand').attr('id',newButtonRowCommand);
$newButtonRow.find('.buttonTitle').attr('id',newButtonRowTitle);
$newButtonRow.find('.buttonColor').attr('id',newButtonRowColor);
$newButtonRow.find('[id^="tableButton"]').each(function(){
    var oldId = $(this).prop('id')
    var idArr = oldId.split('_');
    idArr[0]=newButtonRowTable
    $(this).attr('id',idArr.join('_'))
    //console.log(idArr.join('_'));
})

return $newButtonRow;
}
var getForegroundColor = function(hexcolor) {
    hexcolor = hexcolor.replace("#", "");
    var r = parseInt(hexcolor.substr(0,2),16);
    var g = parseInt(hexcolor.substr(2,2),16);
    var b = parseInt(hexcolor.substr(4,2),16);
    var yiq = ((r*299)+(g*587)+(b*114))/1000;
    return (yiq >= 128) ? '000' : 'fff';
}

function setRowColor($row,hex){
    $row.css({'background-color': '#'+hex}).data('row-color','#'+hex);
    $row.find('.buttonColor').css({'background-color': '#'+hex,'color': '#'+hex}).colpickHide().val('#'+hex);
    $row.find('.bb_commandTableCrop').css({'background-color': '#'+hex});

}
function createButtonRow(i,v){

    var $newButtonRow = $($(".configRowTemplate").html());
    var newButtonRowColor = 'button_'+i+'_color';
    var newButtonRowCommand = 'button_'+i+'_Command';
    var newButtonRowTitle = 'button_'+i+'_Title';
    var newButtonRowTable = 'tableButton'+i;
    $newButtonRow.data('bbKey',i);
    $newButtonRow.find('.buttonCommand').attr('id',newButtonRowCommand).on('change',function(){
        CommandSelectChanged(newButtonRowCommand, "tableButton"+$(this).closest('.bb_configRow').data('bbKey'), true);
    })
    $newButtonRow.find('.buttonTitle').attr('id',newButtonRowTitle).css({
        fontSize:bigButtonsConfig.fontSize
    });
    $newButtonRow.find('.buttonColor').attr('id',newButtonRowColor);
    $newButtonRow.find('.tableButton').attr('id',newButtonRowTable);
    $newButtonRow.find('.buttonDelete').click(function(){
        $(this).closest('.bb_configRow').remove();
        $.each($('#buttonList li'), function(i, v) {
            updateButtonRow(i,v);
        });
    });

    // var $newButtonModal = $newButtonRow.find('.bb_modal');
    // $newButtonRow.find('.bb_modal').dialog({
    //     height: 440,
    //     width: 600,
    //     title: "File Chooser",
    //     modal: true,
    //     autoOpen: false,
    //     show: { effect: "puff",easing: "easeOutExpo", duration: 200, percent:110 },
    //     hide: { effect: "puff", duration: 100, percent:110 }
    // });
    // $newButtonRow.find('.bb_launchModal').click(function(){
    //     $newButtonModal.dialog( "open" );
    // });




    $('#buttonList').append($newButtonRow);
    LoadCommandList('button_'+i+'_Command');
    var hex = "ff8800";
    if(v){
        hex=v.color;
    }
    $newButtonRow.find('.buttonColor').colpick({
        colorScheme:'flat',
        layout:'rgbhex',
        color:hex,
        onSubmit:function(hsb,newHex,rgb,el) {
            setRowColor($newButtonRow,newHex);
        }
    });
    setRowColor($newButtonRow,hex);
    $newButtonRow.hover(function(){
        $newButtonRow.css({'background-color': '#e2e2e2', zIndex:3});
        $newButtonRow.find('.bb_commandTableCrop').css({'background-color': '#e2e2e2'});
        $newButtonRow.find('td').css({'color': '#000' });
        
    },
    function(){
 
        $newButtonRow.css({'background-color': $newButtonRow.data('row-color'), zIndex:2});
        $newButtonRow.find('.bb_commandTableCrop').css({'background-color': $newButtonRow.data('row-color')});

    });
    return $newButtonRow;
}
$( function() {

    $('#saveBigButtonConfigButton').click(function(){
        SaveButtons();
    });
 
    $('#buttonTitle').on('change keydown paste input', function() {
        bigButtonsConfig['title'] = $(this).val();
    });

   
    $.ajax({
        type: "GET",
        url: 'fppjson.php?command=getPluginJSON&plugin=fpp-BigButtons',
        dataType: 'json',
        contentType: 'application/json',
        success: function (data) {
            bigButtonsConfig = $.parseJSON(data);
            
            if(!bigButtonsConfig){
                bigButtonsConfig={ "title": "", "fontSize": 12, "buttons": { "1": {}}}
            }

            $.each(bigButtonsConfig.buttons,function(i,v){                   
                $newButtonRow=createButtonRow(i,v);
         
                $newButtonRow.find('.buttonTitle').val(v.description);
                $newButtonRow.find('.buttonColor').val(v.color);
                PopulateExistingCommand(v, 'button_'+i+'_Command', 'tableButton'+i, true);
            })
            $('#buttonFontSize').val(bigButtonsConfig.fontSize).on('input change',function(){
                $('.bb_fontSizeDisplay').html($(this).val());
                bigButtonsConfig['fontSize']=$(this).val();
                $('.buttonTitle').css({
                    fontSize:parseInt($('#buttonFontSize').val())
                });
            });
            $('.bb_fontSizeDisplay').html(bigButtonsConfig.fontSize);


        }
    });

    $( "#buttonList" ).sortable({
        update:function(){
            var newBigButtonsConfig = Object.assign({}, bigButtonsConfig);
            newBigButtonsConfig.buttons=[];
            $.each($('#buttonList li'), function(i, v) {
                $(this).removeClass('bb_newButton');
    
            //     newBigButtonsConfig.buttons[""+i]= bigButtonsConfig.buttons[$(v).data('bbKey')];
            // });
            // console.log(bigButtonsConfig)
            // UpdateBigButtonConfig(newBigButtonsConfig);

            
            
            // $.each($('#buttonList li'), function(i, v) {
              updateButtonRow(i,v);
            })
        }
  
    });
    $( "#buttonList" ).disableSelection();

    $("#fppBBAddNewButton").click(function(){
        var i=$( "#buttonList" ).children().length;
        var $newButtonRow = $newButtonRow=createButtonRow(i);
        $newButtonRow.addClass('bb_newButton')
        
    });
} );


</script>

<template class="configRowTemplate">
    <li class="ui-state-default bb_configRow">

        <div class="bb_buttonTitleWrap">
            <input type='text' class="buttonTitle" placeholder="Name Your Button" id='button_TPL_Title' maxlength='80'  value='<?=$description;?>'></input>
        </div>

            <div class="buttonCommandWrap">
            <select id='button_TPL_Command' class="buttonCommand"><option value="" disabled selected>Select a Command</option></select>
            </div>

        

        <div class="bb_commandTableWrap">
            <div class="bb_commandTableCrop">
            <table border=0 id='tableButtonTPL' class="tableButton">

            </table>            
            </div>
        </div>

        <div class="bb_buttonActions">
            <input id='button_TPL_color' class="buttonColor" type="button" />
            <button class="buttonDelete">Delete</button>
        </div>
        
    </li>
</template>
<div class="bb_pageSettings">
    <div class="row">
        <div class="bb_pageSettingsTitleCol"><input type='text' id='buttonTitle' placeholder="Name Your Page" maxlength='80' value='<? echo $pluginJson["title"] ?>'></input>
        </div>

    </div>
</div>
<div class="row">
    <div>
        <div class="labelHeading">Text Font Size </div>
        <div class="bb_fontSizeControls">
            <span class='bb_fontSizeDisplay'></span>
            <div class="bb_fontSizeControlsInputCol"><input  type="range" min=10 max=64 id='buttonFontSize'></div>
        </div>
    </div>
    <div class="bb_actionButtons">
        <button id="fppBBAddNewButton" class="buttons">Add a New Button</button>
        <input type="button" value="Save Buttons" class="buttons" id="saveBigButtonConfigButton">

    </div>

</div>



<ul id="buttonList">
</ul>

<style type="text/css">
*, *:before, *:after {
  box-sizing: border-box;
}
#buttonList li,#buttonList{
    margin:0;
    list-style-type:none;
    padding:0;
    box-sizing: border-box;
}
#buttonList{
    margin-left:-1%;
    margin-right:-1%;
    display:flex;
    flex-wrap:wrap;
}
#buttonList:after {
  content: "";
  display: table;
  clear: both;
}
#buttonList li{
    width:48%;
    float:left;
    margin:1%;
    border-radius:12px;
    transition: 0.2s all cubic-bezier(.01,.79,.32,.99);
    position:relative;
    border:none;
}
#buttonList li:hover {
    box-shadow: 0px 8px 15px 3px rgba(0,0,0,0.15);
    transform:translateY(-20px);
}
#buttonList li.bb_newButton{
    -webkit-animation: scale-up-center 0.4s cubic-bezier(.01,.79,.32,.99) both;
	        animation: scale-up-center 0.4s cubic-bezier(.01,.79,.32,.99) both;
}
#buttonList li.ui-sortable-helper {
    transform:scale(1.05);
    box-shadow: 10px 10px 30px 5px rgba(0,0,0,0.1);
    transition: 0.2s transform cubic-bezier(.01,.79,.32,.99),0.2s box-shadow cubic-bezier(.01,.79,.32,.99);
}
#buttonList li td{
    padding-top:0.2em;
    padding-bottom:0.2em;
}
.bb_actionButtons{
    text-align:right;
    flex:1;
    padding-bottom:1.2em;
}
.bb_actionButtons .buttons{
    margin-left:0.5em;
}
.bb_buttonTitleWrap{
    text-align:center;
    margin-top:0.5em;
}
.bb_commandTableWrap{
    min-height:50px;
    position:relative;
}
.bb_commandTableCrop{
    min-height:50px;
    height:50px;
    overflow:hidden;
    position:absolute;
    width:100%;
    z-index:2;
     border-radius:12px;
     transition: 0.2s all cubic-bezier(.01,.79,.32,.99);
    padding-left:1.7em;
    padding-right:1.7em;
}
#buttonList li:hover .bb_commandTableCrop{
    height:auto;
    background-color:#ECECEC;
    box-shadow: 0px 20px 15px 3px rgba(0,0,0,0.1);
    
}
#buttonList li:hover.ui-sortable-helper .bb_commandTableCrop {
    height:50px;
}


#buttonTitle{
    border:0px;
    border-bottom: 1px solid #D2D2D2;
    border-radius:0px;
    padding-left:0px;
    font-size:1.8em;
    width:100%;
    text-align:center;
    font-weight:bold;
    margin-bottom:0.5em;
}
#buttonTitle:hover {
    border-bottom: 1px solid #2E4260;
}
#buttonTitle:focus, .buttonTitle:focus {
  outline-style: none;
  border-bottom: 1px solid #fff;
  box-shadow: 0px 1px 0px 0px #fff;
}
#buttonList li:hover  .buttonTitle:focus {
    border-bottom: 1px solid #2E4260;
    box-shadow: 0px 1px 0px 0px #2E4260;
}
.buttonTitle::-webkit-input-placeholder { /* Edge */
  color: rgba(0,0,0,0.4);
}

.buttonTitle:-ms-input-placeholder { /* Internet Explorer 10-11 */
    color: rgba(0,0,0,0.4);
}

.buttonTitle::placeholder {
    color: rgba(0,0,0,0.4);
}
#buttonList li input.buttonTitle:placeholder-shown {
    border-bottom: 1px solid rgba(0,0,0,0.25);
}
#saveBigButtonConfigButton{
    background-color:#2E4260;
    color:#fff;
    background-image: url("data:image/svg+xml,%3Csvg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='12.3px' height='9.1px' viewBox='0 0 12.3 9.1' style='enable-background:new 0 0 12.3 9.1;' xml:space='preserve'%3E%3Cstyle type='text/css'%3E .st0%7Bfill:%23FFFFFF;%7D%0A%3C/style%3E%3Cdefs%3E%3C/defs%3E%3Cpath class='st0' d='M3.5,8.8L0.3,5.7c-0.5-0.5-0.5-1.2,0-1.7l0,0C0.8,3.5,1.6,3.5,2,4l3.1,3.1c0.5,0.5,0.5,1.2,0,1.7l0,0 C4.7,9.3,4,9.3,3.5,8.8z'/%3E%3Cpath class='st0' d='M5.2,8.8L11.9,2c0.5-0.5,0.5-1.2,0-1.7l0,0c-0.5-0.5-1.2-0.5-1.7,0L3.5,7.1C3,7.6,3,8.3,3.5,8.8l0,0 C4,9.3,4.7,9.3,5.2,8.8z'/%3E%3C/svg%3E%0A");
    background-position: right 20px top 39px;
	background-size: 13px;
    background-repeat:no-repeat;
    transition: 0.2s all cubic-bezier(.01,.79,.32,.99);
    width: 150px;
    padding-right: 16px;
}
#saveBigButtonConfigButton.success{
    animation:success-animation 3s linear both;
    border-color:#56B760;  

}
.bb_pageSettingsTitleCol{
    flex:1;
}
.bb_fontSizeControls{
    display:flex;
}
.bb_fontSizeControlsInputCol{
    margin-left: 1em;
	margin-top: 0.5em;
}
#fppBBAddNewButton {
    background-position: right 20px center;
	background-size: 10px;
    background-repeat:no-repeat;
    background-image: url("data:image/svg+xml,%3Csvg version='1.1' id='Layer_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 16.2 16.2' style='enable-background:new 0 0 16.2 16.2;' xml:space='preserve'%3E%3Cstyle type='text/css'%3E .st0%7Bfill:%23171720;%7D%0A%3C/style%3E%3Cpath class='st0' d='M8.1,16.2L8.1,16.2c-0.6,0-1.1-0.5-1.1-1.1V1.1C7,0.5,7.5,0,8.1,0h0c0.6,0,1.1,0.5,1.1,1.1v13.9 C9.2,15.7,8.7,16.2,8.1,16.2z'/%3E%3Cpath class='st0' d='M0,8.1L0,8.1C0,7.5,0.5,7,1.1,7h13.9c0.6,0,1.1,0.5,1.1,1.1v0c0,0.6-0.5,1.1-1.1,1.1H1.1C0.5,9.2,0,8.7,0,8.1z' /%3E%3C/svg%3E%0A");   
    padding-right:40px ;
}
.bb_fontSizeDisplay{
    display: block;
	font-size: 1.3em;
	font-weight: bold;
}
#buttonList li input.buttonTitle{
    text-align:center;
    background-color:transparent;
    border-radius:0;
    border:0;
    border-bottom:1px solid rgba(0,0,0,0);
    color:#fff;
    max-width:95%;
    font-weight:bold;
}
#buttonList li:hover input.buttonTitle{
    color:#000;
    border-bottom:1px solid rgba(0,0,0,1);
}
.buttonColor {
    display:block;
    appearance:none;
	width:30px;
	height:30px;
	border: 1px solid white;
    border-radius:50%;
    cursor:pointer;
}
.buttonCommandWrap{
    text-align:center;
    margin-top:0.5em;
}
#buttonList li  .buttonCommand{
    background-image:url(/images/redesign/chevron-down-white.svg);
    border:1px solid rgba(255,255,255,0.4);
    background-color:transparent;
    color:#fff;
}
#buttonList li  .buttonCommand option{
    color:#000;
}
#buttonList li:hover  .buttonCommand{
    background-image:url(/images/redesign/chevron-down-grey.svg);
    border:1px solid rgba(0,0,0,0.4);
    background-color:#fff;
    color:#000;
}
#buttonList li td{
    color:#fff;
    vertical-align:middle;
}
#buttonList li td * {

    vertical-align:middle;
}
#buttonList li:hover td{
    color:#000;
}
#buttonList li .tableButton {
 opacity:0;
 transition: 0.2s all cubic-bezier(.01,.79,.32,.99);  
 max-width:100%;
 padding-bottom:1.7em;
}
#buttonList li:hover .tableButton {
    opacity:1;
    
}

#buttonList li .tableButton select{
    padding-right:3em;
}
.bb_buttonActions{
    top:1em;
    right:1em;
    position:absolute;
    display:flex;

}

.buttonDelete{
    display:block;
    appearance:none;
	width:30px;
	height:30px;

    border:0;
    border-radius:50%;
    background-color:#F63939;
    background-image: url("data:image/svg+xml,%3C!-- Generator: Adobe Illustrator 24.1.1, SVG Export Plug-In --%3E%3Csvg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='14px' height='18.3px' viewBox='0 0 14 18.3' style='enable-background:new 0 0 14 18.3;' xml:space='preserve'%3E%3Cstyle type='text/css'%3E .st0%7Bfill:%23FFFFFF;%7D%0A%3C/style%3E%3Cdefs%3E%3C/defs%3E%3Cg%3E%3Cpath class='st0' d='M1,16.3c0,1.1,0.9,2,2,2h8c1.1,0,2-0.9,2-2v-12H1V16.3z M9.9,7.9c0-0.3,0.3-0.6,0.6-0.6c0.3,0,0.6,0.3,0.6,0.6 v6.5c0,0.3-0.3,0.6-0.6,0.6c-0.3,0-0.6-0.3-0.6-0.6V7.9z M6.5,7.9c0-0.3,0.3-0.6,0.6-0.6c0.3,0,0.6,0.3,0.6,0.6v6.5 c0,0.3-0.3,0.6-0.6,0.6c-0.3,0-0.6-0.3-0.6-0.6V7.9z M3.3,7.9c0-0.3,0.3-0.6,0.6-0.6s0.6,0.3,0.6,0.6v6.5c0,0.3-0.3,0.6-0.6,0.6 s-0.6-0.3-0.6-0.6V7.9z'/%3E%3Cpolygon class='st0' points='10.5,1 9.5,0 4.5,0 3.5,1 0,1 0,3 14,3 14,1 '/%3E%3C/g%3E%3C/svg%3E%0A");    background-position:center;

    background-repeat:no-repeat;
    color:rgba(0,0,0,0);

}
#buttonList li .bb_buttonActions button{
    opacity:0.0;
    transition: 0.2s all cubic-bezier(.01,.79,.32,.99);  
    transform:scale(0.5);
    margin-left:0.3em;

}
#buttonList li:hover .bb_buttonActions button{

    cursor:pointer;
    opacity:1;
    transform:scale(1);
}

.scale-up-center {
	-webkit-animation: scale-up-center 0.4s cubic-bezier(0.390, 0.575, 0.565, 1.000) both;
	        animation: scale-up-center 0.4s cubic-bezier(0.390, 0.575, 0.565, 1.000) both;
}
.colpick{
    z-index:4;
}

@-webkit-keyframes scale-up-center {
  0% {
    -webkit-transform: scale(0.5);
            transform: scale(0.5);
  }
  100% {
    -webkit-transform: scale(1);
            transform: scale(1);
  }
}
@keyframes scale-up-center {
  0% {
    -webkit-transform: scale(0.5);
            transform: scale(0.5);
  }
  100% {
    -webkit-transform: scale(1);
            transform: scale(1);
  }
}
@keyframes success-animation {
  0% {
    background-position: right 20px top 39px;
    background-color:#2E4260;
    width: 150px;
    padding-right: 16px;
  }
  4% {
    background-position: right 20px top 14px;
    background-color:#56B760;
    width: 162px;
    padding-right: 40px;
  }
  96% {
    background-position: right 20px top 14px;
    background-color:#56B760;
    width: 162px;
    padding-right: 40px;
  }
  100% {
    background-position: right 20px top -10px;
    background-color:#2E4260;
    width: 150px;
    padding-right: 16px;
  }
}


</style>



