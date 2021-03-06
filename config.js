var bb={};
var bigButtonsConfig=null;
var $bbSelectedRow=null;
var legacyColorNames={"aqua":'00FFFF',
"blue":'0000FF',
"chocolate":'D2691E',
"coral":'FF7F50',
"cyan":'00FFFF',
"darkcyan":'008B8B',
"green":'008000',
"grey":'808080',
"ivory":'FFFFF0',
"lightblue":'ADD8E6',
"lightcoral":'F08080',
"lightcyan":'E0FFFF',
"lightgrey":'D3D3D3',
"lightgreen":'90EE90',
"lightpink":'FFB6C1',
"lightyellow":'FFFFE0',
"olive":'808000',
"orange":'FFA500',
"pink":'FFC0CB',
"plum":'DDA0DD',
"purple":'800080',
"red":'FF0000',
"slategrey":'708090',
"tan":'D2B48C',
"white":'FFF5EE',
"whitesmoke":'F5F5F5',
"yellow":'FFFF00'}


function SaveBigButtonConfig(config) {
    var data = JSON.stringify(config);
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

function GetButton(i,tab_i,v) {
    
    var button = {
        "description": $('#button_'+tab_i+'-'+i+'_Title').val(),
        "color": $('#button_'+tab_i+'-'+i+'_color').val(),
        "buttonWidthRatio":$(v).data('button-width-ratio'),
        "buttonHeightValue":$(v).data('button-height-value'),
        "icon": $(v).find('.bb_icon').data('icon')
    };
    CommandToJSON('button_'+tab_i+'-'+i+'_Command', 'tableButton'+tab_i+'-'+i, button);
    return button;
}
function SaveButtons() {
    

    $.each($('.buttonList'),function(tab_i,tab_v){
        bigButtonsConfig[tab_i]={
            title: $('.buttonTabs .buttonPageTitleValue').eq(tab_i).html(),
            color: $('.buttonList[data-tab-id='+tab_i+']').data('color'),
            buttons:[],
            fontSize: $('#buttonFontSize').val()
        };
        $.each($(tab_v).children(),function(i,v){
            var key = ""+i;
            var button = GetButton(i,tab_i,v);
            bigButtonsConfig[tab_i]["buttons"][key] = button;

        });
    }); 
    SaveBigButtonConfig(bigButtonsConfig);
}

function updateButtonRow(i,v,tab_i){
    var $newButtonRow = $(v);
    var newButtonRowColor = 'button_'+tab_i+'-'+i+'_color';
    var newButtonRowCommand = 'button_'+tab_i+'-'+i+'_Command';
    var newButtonRowTitle = 'button_'+tab_i+'-'+i+'_Title';
    var newButtonRowTable = 'tableButton'+tab_i+'-'+i;
    $newButtonRow.data('bbKey',i);
    $newButtonRow.find('.buttonCommand').attr('id',newButtonRowCommand);
    $newButtonRow.find('.buttonTitle').attr('id',newButtonRowTitle);
    $newButtonRow.find('.buttonColor').attr('id',newButtonRowColor);
    $newButtonRow.find('[id^="tableButton"]').each(function(){
        var oldId = $(this).prop('id')
        var idArr = oldId.split('_');
        idArr[0]=newButtonRowTable
        $(this).attr('id',idArr.join('_'))

    });
    return $newButtonRow;
}
function updateButtonLists() {
    $.each($('.buttonList'), function() {
        $.each($(this).children(), function(iteration, value) {
            $(this).removeClass('bb_newButton');
            updateButtonRow(iteration,value,$(this).parent().data('tab-id'));
        });           
    })
}
function setButtonCommandSummaryTitle($row,value){
    if(!value){
        value='Select a Command'
    }
    $row.find('.bb_commandSummaryTitle').html(value);
}
function setButtonWidthRatio($row,ratio){
    ratio=Math.min(1,ratio)
    $row.data('button-width-ratio',ratio).removeClass (function (index, className) {
        return (className.match (/(^|\s)bbw-\S+/g) || []).join(' ');
    }).addClass('bbw-'+Math.round(ratio*100));
}
function setIcon($row,icon){
    //console.log(icon);
    $row.find('.bb_icon').removeClass(function (index, className) {
        return (className.match (/(^|\s)fa\S+/g) || []).join(' ');
    }).data('icon',icon);  
    if(icon!=''){
        $row.find('.bb_icon').addClass('fas fa-'+icon);
    }
}
function setButtonHeightValue($row,value){
    $row.data('button-height-value',value).removeClass (function (index, className) {
        return (className.match (/(^|\s)bbh-\S+/g) || []).join(' ');
    }).addClass('bbh-'+Math.round(value));
}
function setRowColor($row,hex){
    $row.css({'background-color': '#'+hex}).data('row-color','#'+hex);
    $row.find('.buttonColor').css({'background-color': '#'+hex}).colpickHide().val('#'+hex);
}
function setTabColor($buttonList,hex){
  $('.bb_setButtonTabColorSwatch').css({'color': '#'+hex}).val('#'+hex);
  $('.buttonListsPanel').css({'background-color': '#'+hex});
  $buttonList.data('color',hex);
  $('.bb_setButtonTabColor').colpickSetColor(hex)
}
function launchButtonConfigModal($buttonRow){
    $buttonRow.find('.buttonCommandWrap').fppDialog({
        title: 'Command for '+($buttonRow.find('.buttonTitle').val()==''?'New Button':$buttonRow.find('.buttonTitle').val()),
        buttons:{
            Done:{
                click:function(){
                    $buttonRow.find('.buttonCommandWrap').fppDialog('close');
                },
                class:'btn-success'
            }
        }
    })
}
function createButtonRow(i,v,tab_i){
    var $newButtonRow = $($(".configRowTemplate").html());
    var newButtonRowColor = 'button_'+tab_i+'-'+i+'_color';
    var newButtonRowCommand = 'button_'+tab_i+'-'+i+'_Command';
    var newButtonRowTitle = 'button_'+tab_i+'-'+i+'_Title';
    var newButtonRowTable = 'tableButton'+tab_i+'-'+i;
    $newButtonRow.data('bbKey',i);
    $newButtonRow.find('.buttonCommand').attr('id',newButtonRowCommand).on('change',function(){
        CommandSelectChanged(newButtonRowCommand, newButtonRowTable, true);
        setButtonCommandSummaryTitle($newButtonRow,$(this).val());
    })
    
    $newButtonRow.find('.buttonTitle, .bb_icon').attr('id',newButtonRowTitle).css({
        fontSize:bigButtonsConfig[0].fontSize
    });
    
    $newButtonRow.find('.bb_commandSummary').click(function(){
        launchButtonConfigModal($newButtonRow);
    });
    $newButtonRow.find('.buttonColor').attr('id',newButtonRowColor);
    $newButtonRow.find('.tableButton').attr('id',newButtonRowTable);

    $newButtonRow.find('.buttonDelete').click(function(){
        $(this).closest('.bb_configRow').remove();
        $.each($('.buttonList'), function(tab_iteration, tab_value) {
            $.each($(this).find('li'), function(iteration, value) {
                $(this).removeClass('bb_newButton');
                updateButtonRow(iteration,value,tab_iteration);
            });           
        })
    });

    $('.buttonLists').children().eq(tab_i).append($newButtonRow);
    LoadCommandList('button_'+tab_i+'-'+i+'_Command');
    var buttonWidthRatio = 0.5;
    var buttonHeightValue = 18;
    var hex = "ff8800";
    var icon='';
    if(v){
        if(v.buttonWidthRatio){
            buttonWidthRatio = v.buttonWidthRatio
        }
        if(v.buttonHeightValue){
            buttonHeightValue = v.buttonHeightValue
        }
        if(v.icon){
            icon = v.icon;
        }
        hex=v.color;
    }

    setIcon($newButtonRow,icon);
    $newButtonRow.find('.bb_icon').click(function(){
        $bbSelectedRow = $newButtonRow;
        $('.bb_iconSelector').fppDialog({
            width:1200,
            title: 'Select an Icon'
        });
    })
    setButtonWidthRatio($newButtonRow,buttonWidthRatio);
    setButtonHeightValue($newButtonRow,buttonHeightValue);
    var buttonsOnSameRow =[];
    var heightBeforeResize;
    $newButtonRow.resizable({
      grid: [bb.pageContentWidth/48,1],
      start:function(event,ui){
        if(!$(event.originalEvent.target).hasClass('ui-resizable-e')){ //dont touch the height if we are resizing width
            heightBeforeResize = $(this).height();
            var originY = $newButtonRow.position().top;
            $(this).removeClass (function (index, className) {
                return (className.match (/(^|\s)bbh-\S+/g) || []).join(' ');
            })
            buttonsOnSameRow =[];
            $(this).siblings().each(function(){
                if(originY == $(this).position().top ){
                    buttonsOnSameRow.push($(this));
                    $(this).removeClass (function (index, className) {
                        return (className.match (/(^|\s)bbh-\S+/g) || []).join(' ');
                    })
                }
            })
        }

      },
      stop: function( event, ui ) {
        setButtonWidthRatio( $newButtonRow,1/(bb.pageContentWidth/$newButtonRow.width()));
        if(!$(event.originalEvent.target).hasClass('ui-resizable-e')){ //dont touch the height if we are resizing width
            $.each(buttonsOnSameRow, function(i,$sameRowButton){
                setButtonHeightValue( $sameRowButton,$newButtonRow.height()/10);
            });
            setButtonHeightValue( $newButtonRow,$newButtonRow.height()/10);
            $newButtonRow.height('');
        }
        $newButtonRow.width('');
      }
    });

    $newButtonRow.find('.buttonColor').colpick({
        colorScheme:'flat',
        layout:'rgbhex',
        color:hex,
        onSubmit:function(hsb,newHex,rgb,el) {
            setRowColor($newButtonRow,newHex);
        }
    });
    setRowColor($newButtonRow,hex);

    return $newButtonRow;
}
function bbHandleWindowResize(){
    bb.pageContentWidth = $('.buttonLists').width();
}
$( function() {

    $(window).resize(bbHandleWindowResize);
    bbHandleWindowResize();
    $.each(faIcons,function(i,v){
        var icon = $('<i class="'+v.title+'"/>').data('title',v.title).data('terms',v.searchTerms).click(function(){
            var iconName = $(this).data('title').replace('fas fa-','');
            setIcon($bbSelectedRow,iconName);
            $('.bb_iconSelector').fppDialog('close');
        })
        $('.bb_iconSelectorIcons').append(icon)
    });
    $('.bb_iconSelectorSearch').on('input',function(){

        $('.bb_iconSelectorIcons i').each(function(){
            var term = $('.bb_iconSelectorSearch').val();
            var title = $(this).data('title');
            var terms = $(this).data('terms');
            var hasTermMatch=false;
            
            for(var i=0;i<terms.length;i++){
              if(terms.includes(term)){
                return hasTermMatch = true;
              }
            }
            if(title.replace('fas fa-','').includes(term) || hasTermMatch){
                $(this).removeClass('hidden');
            }else{
                $(this).addClass('hidden');
            }
        })

    })
    
    $('#saveBigButtonConfigButton').click(function(){
        SaveButtons();
    });
 
    $('#buttonTitle').on('change keydown paste input', function() {
        bigButtonsConfig[0]['title'] = $(this).val();
    });

    $.ajax({
        type: "GET",
        url: 'fppjson.php?command=getPluginJSON&plugin=fpp-BigButtons',
        //url: 'legacyBigButtonsSampleConfig.json',
        dataType: 'json',
        contentType: 'application/json',
        success: function (data) {

            if(typeof data==="string"){
                bigButtonsConfig = $.parseJSON(data);
            }else{
                bigButtonsConfig = data;
            }
            if(!Array.isArray(bigButtonsConfig)){
                // if the json is a flat array, it is a legacy config
                // so we need to upgrade to support multiple tabs
                $.each(bigButtonsConfig.buttons,function(i,v){
             
                    bigButtonsConfig.buttons[i].color=legacyColorNames[v.color]
                })
                bigButtonsConfig = [bigButtonsConfig];
            }

            if(bigButtonsConfig.length<1){
                bigButtonsConfig.push([{ "title": "", "fontSize": 12, "buttons": { "1": {}}}])
            }
            
            $.each(bigButtonsConfig,function(tab_i,tab_v){
                var tab = createTab(tab_v.title,tab_i,tab_v);
                $.each(bigButtonsConfig[tab_i].buttons,function(i,v){                   
                    $newButtonRow=createButtonRow(i,v,tab_i);
                    $newButtonRow.find('.buttonTitle').val(v.description);
                    $newButtonRow.find('.buttonColor').val(v.color);
                    PopulateExistingCommand(v, 'button_'+tab_i+'-'+i+'_Command',  'tableButton'+tab_i+'-'+i, true);
                    setButtonCommandSummaryTitle($newButtonRow,$('#button_'+tab_i+'-'+i+'_Command').val());
    
                })
                $('#buttonFontSize').val(bigButtonsConfig[tab_i].fontSize).on('input change',function(){
           
                    bigButtonsConfig[tab_i]['fontSize']=$(this).val();
                    $('.buttonTitle, .bb_icon').css({
                        fontSize:parseInt($('#buttonFontSize').val())
                    });
                });
                $('.bb_fontSizeDisplay').html(bigButtonsConfig[tab_i].fontSize);           
            });
            $( ".buttonList" ).disableSelection();
            $('.buttonTabs').children().eq(0).addClass('bb-active');
            $('.buttonLists').children().eq(0).addClass('bb-active');

            setTabColor($('.buttonList.bb-active'),$('.buttonList.bb-active').data('color'));
            $('.bb_setButtonTabColor').colpick({
                colorScheme:'flat',
                layout:'rgbhex',
                color:$('.buttonList.bb-active').data('color'),
                onSubmit:function(hsb,newHex,rgb,el) {
                  setTabColor($('.buttonList.bb-active'),newHex);
                  $(el).colpickHide()
                }
            });
        }
    });



    function createTab(title,tab_i,tab_v){
        var $buttonTab = $($('.buttonTabTemplate').html());
        $buttonTab.find('.buttonPageTitleValue').html(title);
        $buttonTab.attr('data-tab-id',tab_i);
        var $newButtonList = $('<ul class="buttonList"></ul>').attr('data-tab-id',tab_i);
        $buttonTab.find('.buttonPageTitleValue').click(function(){
            $buttonTab.addClass('bb-active').siblings().removeClass('bb-active');
            $newButtonList.addClass('bb-active').siblings().removeClass('bb-active');
            setTabColor($('.buttonList.bb-active'),$newButtonList.data('color'));
        });
        $buttonTab.find('.toggleButtonPageTitle').click(function(){
            if($buttonTab.find('.buttonPageTitleValue').is("[contenteditable]")){
                $buttonTab.removeClass('editable');
                $buttonTab.find('.buttonPageTitleValue').removeAttr('contenteditable');
            }else{
                $buttonTab.addClass('editable');
                $buttonTab.find('.buttonPageTitleValue').attr('contenteditable','').focus();
            }
        });

        $newButtonList.sortable({
            handle: ".bb_configRowHandle",
        
            update:function(){
                updateButtonLists();
            }
        });
        $buttonTab.droppable({
            tolerance:"pointer",
            hoverClass:'droppable-hovered',
            drop:function(event,ui){
                dropped = true;
                //$(ui.draggable).css('border','1px solid red');
                
                var $targetButtonList=$('.buttonList[data-tab-id='+$(event.target).data('tab-id')+']');
                var targetTabId = $(event.target).data('tab-id');
                var sourceTabId = $(ui.draggable).parent().data('tab-id');
                var bbKey = ui.draggable.data('bbKey');

                var v = GetButton(bbKey,sourceTabId,ui.draggable);
                var i = $targetButtonList.length+1;
                $newButtonRow=createButtonRow(i,v,targetTabId);
                $newButtonRow.find('.buttonTitle').val(v.description);
                $newButtonRow.find('.buttonColor').val(v.color);
                PopulateExistingCommand(v, 'button_'+targetTabId+'-'+i+'_Command', 'tableButton'+targetTabId+'-'+i, true);
                
                ui.draggable.remove();
                updateButtonLists();
                //$(event.target).addClass('droppable-dropped');
            }
        });
        var tabColor = 'f5f5f5';
        if(tab_v){
          
          if(tab_v.color){
            tabColor = tab_v.color;
          }
        }
        $newButtonList.data('color',tabColor);
        $('.buttonTabs').append($buttonTab);
        $('.buttonLists').append($newButtonList );
        return {$tab:$buttonTab,$list:$newButtonList};
    }
    $("#bb_addNewButton").click(function(){
        var i=$( ".bb-active.buttonList" ).children().length;
        var tab_i = $( ".bb-active.buttonList" ).data('tab-id');
        var $newButtonRow = createButtonRow(i,null,tab_i);
        $newButtonRow.addClass('bb_newButton').one('animationend',function(){
            $newButtonRow.removeClass('bb_newButton');
            launchButtonConfigModal($newButtonRow);
        });
        
    });
    $("#bb_addNewTab").click(function(){
        var tab = createTab('New Tab',$( ".buttonTabs" ).children().length);     
    });
});
