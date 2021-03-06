<?
require 'bb-common.php';
?>

<div id="global" class="settings">
<link  rel="stylesheet" href="/jquery/colpick/css/colpick.css"/>
<link  rel="stylesheet" href="/plugin.php?plugin=fpp-BigButtons&page=config.css&nopage=1"/>
<script src="/jquery/colpick/js/colpick.js"></script>
<script src="/plugin.php?plugin=fpp-BigButtons&page=fa-icons.js&nopage=1"></script>
<script src="/plugin.php?plugin=fpp-BigButtons&page=config.js&nopage=1"></script>

<template class="buttonTabTemplate">
    <li class="buttonTab">
        <span class="buttonPageTitleValue"></span>
        <span  class="toggleButtonPageTitleWrap">
            <button class="bb_circleButton toggleButtonPageTitle"><i class="fpp-icon-edit"></i><i class="fpp-icon-check"></i></button>   
        </span>
         
    </li>
</template>
<template class="configRowTemplate">
    <li class="ui-state-default bb_configRow">
        <div class="bb_configRowHandle">
            <div class="rowGrip">
                  <i class="rowGripIcon fpp-icon-grip"></i>
            </div>
        </div>
        
        <div class="bb_configRowBody">
            <div class="bb_iconWrap">
              <i class="bb_icon"><span class="bb_iconPlaceholder">Add an icon</span></i>
            </div>
            <div class="bb_buttonTitleWrap">
                <input type='text' class="buttonTitle" placeholder="Name Your Button" id='button_TPL_Title' maxlength='80'  value='<?=$description;?>'></input>
            </div>
            <div class="bb_commandSummary">
                <i class="fas fa-fw fa-terminal fa-nbsp"></i><strong class="bb_commandSummaryTitle"></strong><button class="buttons btn-outline-light bb_commandEditButton"><i class="fas fa-cog"></i></button>
            </div>
        </div>

        <div class="buttonCommandWrap">
            <select id='button_TPL_Command' class="buttonCommand"><option value="" disabled selected>Select a Command</option></select>
            <div class="bb_commandTableWrap">
                <div class="bb_commandTableCrop">
                <table border=0 id='tableButtonTPL' class="tableButton">

                </table>            
                </div>
            </div>
        </div>


        <div class="bb_buttonActions">
            <button id='button_TPL_color' class="bb_circleButton buttonColor" type="button"><i class="fas fa-paint-brush"></i></button>
            <button class="bb_circleButton buttonDelete">Delete</button>
        </div>
        
    </li>
</template>

<div class="row tablePageHeader">
    <div class="col-md">
      <div class="buttonTabWrapper">
        <ul class="buttonTabs">
        
        </ul>
        <div>
            <button id="bb_addNewTab"><i class="fas fa-plus"></i></button>
        </div>
        
      </div>
    </div>
    <div class="col-md-auto ml-lg-auto">
      <div class="bb_actionButtons ">
          <input type="button" value="Save Buttons" class="buttons btn-success" id="saveBigButtonConfigButton">

      </div>   
    </div>


</div>
<hr>
<div class="buttonListsPanelTop row">
  <div class="col-md">
    <div class="bb_fontSizeControls">
        <span><i class="fas fa-text-width"></i></span>
        
        <div class="bb_fontSizeControlsInputCol"><input  type="range" min=10 max=64 id='buttonFontSize'></div>
    </div>
  </div>
  <div class="col-md-auto ml-lg-auto bb_tabActions">
    <button class="bb_setButtonTabColor " type="button"><i class="fas fa-circle bb_setButtonTabColorSwatch"></i>Background</button>
    <button id="bb_addNewButton" class="buttons btn-outline-success btn-rounded">
    <i class="fas fa-plus"></i> Add a Button
    </button>
  </div>
</div>

<div class="buttonListsPanel">
  <div class="buttonLists">
  
  </div>
</div>
<div class="bb_iconSelector hidden">
  <input type="text" class="form-control bb_iconSelectorSearch" placeholder="Find an Icon" />
  <div class="bb_iconSelectorIcons">

  </div>
</div>