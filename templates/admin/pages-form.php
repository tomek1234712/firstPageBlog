<?php 
if( !defined( 'ADMIN_PAGE' ) )
  exit( 'Script by OpenSolution.org' );

require_once 'core/tags-admin.php';

if( isset( $_POST['sName'] ) ){
  $iPage = $oPage->savePage( $_POST );
  if( isset( $_POST['sOptionList'] ) )
    header( 'Location: '.( ( isset( $_SESSION['sRedirectUrl'] ) && isset( $config['redirect_to_last_used_list'] ) ) ? str_replace( '&amp;', '&', $_SESSION['sRedirectUrl'] ).( !strstr( $_SESSION['sRedirectUrl'], 'sOption=' ) ? '&sOption=save' : null ) : $config['admin_file'].'?p=pages&sOption=save' ) );
  elseif( isset( $_POST['sOptionAddNew'] ) )
    header( 'Location: '.$config['admin_file'].'?p=pages-form&sOption=save' );
  elseif( isset( $_POST['sOptionClone'] ) )
    header( 'Location: '.$config['admin_file'].'?p=pages-form&sOption=save&iPage='.$oPage->clonePage( $iPage ) );
  else
    header( 'Location: '.$config['admin_file'].'?p=pages-form&sOption=save&iPage='.$iPage );
  exit;
}

$sFilesList = null;
if( isset( $_GET['iPage'] ) && is_numeric( $_GET['iPage'] ) ){
  $aData = $oPage->throwPageAdmin( $_GET['iPage'] );
  if( isset( $aData ) && is_array( $aData ) ){
    $sFilesList = $oFile->listAllFiles( $aData['iPage'] );
  }
}

if( !isset( $sFilesList ) )
  $sFilesList = '<h2 class="msg error">'.$lang['Data_not_found'].'</h2>';

$sSelectedMenu = 'pages';
require_once 'templates/admin/_header.php';
require_once 'templates/admin/_menu.php';
?>

<section id="body" class="pages">

  <h1><?php echo ( isset( $aData['iPage'] ) ) ? $lang['Pages_form'].': '.$aData['sName'] : $lang['New_page']; ?></h1>
  <?php if( isset( $config['manual_link'] ) ){
    echo '<div class="manual"><a href="'.$config['manual_link'].'instruction#pages-form" title="'.$lang['Help'].'" target="_blank"></a></div>';
  }
  if( isset( $_GET['sOption'] ) ){
    echo '<h2 class="msg">'.$lang['Operation_completed'].'</h2>';
  }?>

  <form action="?p=<?php echo $_GET['p']; ?>" name="form" method="post" class="main-form" onsubmit="return checkParentForm()">
    <fieldset>
      <input type="hidden" name="iPage" id="iPage" value="<?php if( isset( $aData['iPage'] ) ) echo $aData['iPage']; ?>" />

      <?php if( isset( $aData['iPage'] ) ){ ?>
      <ul class="options">
        <li class="preview"><a href="./<?php echo ( ( $config['start_page'] == $aData['iPage'] ) ? '?sLanguage='.$config['language'] : $oPage->aLinksIds[$aData['iPage']] ); ?>" target="_blank" title="<?php echo $lang['Preview']; ?>"><?php echo $lang['Preview']; ?></a></li>
        <li class="delete"><a href="?p=pages&amp;iItemDelete=<?php echo $aData['iPage']; ?>" title="<?php echo $lang['Delete']; ?>" onclick="return del( )"><?php echo $lang['Delete']; ?></a></li>
      </ul>
      <?php } ?>
      <ul class="buttons">
        <li class="save"><input type="submit" name="sOption" class="main" value="<?php echo $lang['save']; ?>" /></li>
        <li class="options"><input type="submit" value="<?php echo $lang['save_add_new']; ?>" name="sOptionAddNew" />
          <ul>
            <li><input type="submit" value="<?php echo $lang['save_list']; ?>" name="sOptionList" /></li>
              <li><input type="submit" value="<?php echo $lang['save_and_clone']; ?>" name="sOptionClone" /></li>
          </ul>
        </li>
      </ul>

      <ul class="tabs">
        <!-- tabs start -->
        <li id="content" class="selected"><a href="#content"><?php echo $lang['Content']; ?></a></li>
        <li id="options"><a href="#options"><?php echo $lang['Options']; ?></a></li>
        <li id="seo"><a href="#seo"><?php echo $lang['Seo']; ?></a></li>
        <li id="add-files"><a href="#add-files"><?php echo $lang['Add_files']; ?></a></li>
        <li id="files"><a href="#files"><?php echo $lang['Files']; ?></a></li>
        <li id="advanced"><a href="#advanced"><?php echo $lang['Advanced']; ?></a></li>
        <li id="tags"><a href="#tags"><?php echo $lang['Tags']; ?></a></li>
        <!-- tabs end -->
      </ul>

      <ul id="tab-content" class="forms full">
        <li>
          <label for="sName"><?php echo $lang['Name']; ?></label>
          <input type="text" name="sName" id="sName" value="<?php if( isset( $aData['sName'] ) ) echo $aData['sName']; ?>" placeholder="<?php echo $lang['only_this_field_is_required']; ?>" data-form-check="required" />
        </li>

        <li class="short-description">
          <label for="sDescriptionShort"><?php echo $lang['Short_description']; ?><a href="#" class="expand description-short"><span class="display"><?php echo $lang['Display']; ?></span><span class="hide"><?php echo $lang['Hide']; ?></span></a></label>

          <div class="toggle"><?php echo getTextarea( 'sDescriptionShort', isset( $aData['sDescriptionShort'] ) ? $aData['sDescriptionShort'] : null, Array( 'iHeight' => '120' ) ); ?></div>
        </li>

        <li>
          <label for="sDescriptionFull"><?php echo $lang['Full_description']; ?></label>
          <script>
          <?php
          if( !isset( $config['disable_adding_widgets_to_page_description'] ) ){
            echo 'aQuick.sWidgetType = "'.$lang['Widgets'].'";';
            require_once 'core/widgets-admin.php';
            echo listWidgetsEditor();
          }
          if( !isset( $config['disable_adding_images_to_page_description'] ) ){
            echo 'aQuick.sGalleryType = "'.$lang['Gallery_type'].'";';
            echo $oFile->listImagesTypeEditor();
          }
          ?>
          </script>
          <?php echo getTextarea( 'sDescriptionFull', isset( $aData['sDescriptionFull'] ) ? $aData['sDescriptionFull'] : null, Array( 'iHeight' => '300', 'sToolbar' => '| break gallery quickwidget', 'sPlugins' => ', customButtons', 'sClassName' => 'text-editor full-description' ) ); ?>
        </li>
        <!-- tab content -->
      </ul>

      <ul id="tab-options" class="forms list">
        <li class="custom">
          <span class="label">&nbsp;</span>
          <?php echo getYesNoBox( 'iStatus', isset( $aData['iStatus'] ) ? $aData['iStatus'] : 1 ); ?>
          <label for="iStatus"><?php echo $lang['Status']; ?></label>
        </li>
        <li class="parent">
          <label for="iPageParent"><?php echo $lang['Parent_page']; ?></label>
          <div>
            <div id="pageParentSearch"><input type="text" value="" size="15" class="search" placeholder="<?php echo $lang['search']; ?>" onkeyup="listOptionsSearch( this, 'iPageParent', 'pageParent2' )" /></div>
            <div id="pageParentCtn"><select name="iPageParent" onchange="checkType( );" id="iPageParent" size="15" onclick="cloneClick( this, 'pageParent2' )"><option value=""<?php if( !isset( $aData['iPageParent'] ) || $aData['iPageParent'] == 0 ) echo ' selected="selected"'; ?>><?php echo $lang['none']; ?></option><?php echo $oPage->listPagesSelectAdmin( ( isset( $aData['iPageParent'] ) ? $aData['iPageParent'] : null ) ); ?></select></div>
            <div id="pageParent2Ctn"></div>
            <?php if( isset( $config['main_only'] ) ){ ?>
            <div class="all"><a href="#" class="expand"><?php echo $lang['Display_all']; ?></a></div>
            <?php } ?>
          </div>
        </li>
        <li>
          <label for="iPosition"><?php echo $lang['Position']; ?></label>
          <input type="text" id="iPosition" name="iPosition" value="<?php echo isset( $aData['iPosition'] ) ? $aData['iPosition'] : 0; ?>" class="numeric" size="3" maxlength="4" />
        </li>
        <li>
          <label for="iMenu"><?php echo $lang['Menu']; ?></label>
          <select name="iMenu" id="iMenu"><?php echo getSelectFromArray( $config['pages_menus'], isset( $aData['iMenu'] ) ? $aData['iMenu'] : $config['default_pages_menu'] ); ?></select>
        </li>
        <li>
          <label for="sDate"><?php echo $lang['Date']; ?></label>
          <input type="<?php echo isset( $config['datetime_format_in_page_form'] ) ? 'text' : 'date'; ?>" name="sDate" value="<?php if( isset( $aData['iTime'] ) && !empty( $aData['iTime'] ) ){ echo date( 'Y-m-d'.( isset( $config['datetime_format_in_page_form'] ) ? ' H:i' : null ), $aData['iTime'] ); } ?>" id="sDate" size="<?php echo isset( $config['datetime_format_in_page_form'] ) ? '17' : '13'; ?>" maxlength="<?php echo isset( $config['datetime_format_in_page_form'] ) ? '16' : '10'; ?>" /> <em class="help"><?php echo $lang['example']; ?> 2015-01-25<?php echo isset( $config['datetime_format_in_page_form'] ) ? ' 15:54' : null; ?></em>
        </li>
        <!-- tab options -->
      </ul>

      <ul id="tab-seo" class="forms list">
        <li>
          <label for="sTitle"><?php echo $lang['Page_title']; ?></label>
          <input type="text" name="sTitle" value="<?php if( isset( $aData['sTitle'] ) ) echo $aData['sTitle']; ?>" id="sTitle" size="75" maxlength="60" />
        </li>
        <li>
          <label for="sUrl"><?php echo $lang['Url_name']; ?></label>
          <input type="text" name="sUrl" value="<?php if( isset( $aData['sUrl'] ) ) echo $aData['sUrl']; ?>" id="sUrl" size="75" />
        </li>
        <li>
          <label for="sDescriptionMeta"><?php echo $lang['Meta_description']; ?></label>
          <input type="text" name="sDescriptionMeta" value="<?php if( isset( $aData['sDescriptionMeta'] ) ) echo $aData['sDescriptionMeta']; ?>" id="sDescriptionMeta" size="75" maxlength="160" />
        </li>
        <li<?php echo ( isset( $config['disable_page_robots_selecting'] ) ? ' class="hide"' : null ); ?>>
          <label for="iMetaRobots"><?php echo $lang['Meta_robots']; ?></label>
          <select name="iMetaRobots" id="iMetaRobots">
            <?php echo getSelectFromArray( $config['meta_robots_options'], ( isset( $aData['iMetaRobots'] ) ? $aData['iMetaRobots'] : $config['default_robots_option'] ) ); ?>
          </select>
        </li>
        <li class="custom<?php echo ( isset( $config['disable_page_link_id_selecting'] ) ? ' hide' : null ); ?>">
          <span class="label">&nbsp;</span>
          <?php echo getYesNoBox( 'iIdInLink', isset( $aData['iIdInLink'] ) ? $aData['iIdInLink'] : ( isset( $config['default_page_id_in_link'] ) ? 1 : 0 ) ); ?>
          <label for="iIdInLink"><?php echo $lang['Page_id_in_link']; ?></label>
        </li>
        <!-- tab seo -->
      </ul>

      <ul id="tab-advanced" class="forms list">
        <li>
          <label for="iSubpages"><?php echo $lang['Subpages_list_types']; ?></label>
          <select name="iSubpages" id="iSubpages"><?php echo getSelectFromArray( $config['subpages_list_types'], isset( $aData['iSubpages'] ) ? $aData['iSubpages'] : $config['default_subpages_list_type'] ); ?></select>
          <a href="#" class="expand subpages<?php echo ( isset( $config['disable_page_list_function_selecting'] ) ? ' hide' : null ); ?>"><span class="display"><?php echo $lang['More']; ?></span><span class="hide"><?php echo $lang['Hide']; ?></span></a>
        </li>
        <li class="adv-function<?php echo ( isset( $config['disable_page_list_function_selecting'] ) ? ' hide' : null ); ?>">
          <label for="sListFunction"><?php echo $lang['Subpages_list_function']; ?></label>
          <select name="sListFunction" id="sListFunction"><option value=""><?php echo $lang['Default_setting']; ?></option><?php echo listListFunctions( isset( $aData['sListFunction'] ) ? $aData['sListFunction'] : null, 'listPagesView' ); ?></select>
        </li>
        <li class="custom adv-function<?php echo ( isset( $config['disable_page_list_function_selecting'] ) ? ' hide' : null ); ?>"">
          <span class="label">&nbsp;</span>
          <?php echo getYesNoBox( 'iHideSubpagesList', isset( $aData['iHideSubpagesList'] ) ? $aData['iHideSubpagesList'] : ( isset( $config['default_hide_subpages_list'] ) ? 1 : 0 ) ); ?>
          <label for="iHideSubpagesList"><?php echo $lang['Subpages_backend_separate_list']; ?></label>
        </li>
        <li<?php echo ( isset( $config['disable_page_theme_selecting'] ) ? ' class="hide"' : null ); ?>>
          <label for="iTheme"><?php echo $lang['Templates']; ?></label>
          <select name="iTheme" id="iTheme"><?php echo getThemesSelect( isset( $aData['iTheme'] ) ? $aData['iTheme'] : $config['default_theme'] ); ?></select>
        </li>
        <li<?php echo ( isset( $config['disable_page_menu_name'] ) ? ' class="hide"' : null ); ?>>
          <label for="sNameMenu"><?php echo $lang['Name_menu']; ?></label>
          <input type="text" name="sNameMenu" value="<?php if( isset( $aData['sNameMenu'] ) && !empty( $aData['sNameMenu'] ) ){ echo $aData['sNameMenu']; } ?>" id="sNameMenu" size="75" />
        </li>
        <li>
          <label for="sKeywords"><?php echo $lang['Key_words']; ?></label>
          <input type="text" name="sKeywords" value="<?php if( isset( $aData['sKeywords'] ) && !empty( $aData['sKeywords'] ) ){ echo $aData['sKeywords']; } ?>" id="sKeywords" size="75" />
        </li>
        <li>
          <label for="sRedirect"><?php echo $lang['Address']; ?></label>
          <div class="redirect">
            <div>
              <input type="text" name="sRedirect" value="<?php if( isset( $aData['sRedirect'] ) && !is_numeric( $aData['sRedirect'] ) ) echo $aData['sRedirect']; ?>" id="sRedirect" size="75" /><a href="#" class="expand redirect"><span class="display"><?php echo $lang['More']; ?></span><span class="hide"><?php echo $lang['Hide']; ?></span></a>
            </div>
            <div class="adv-redirect">
              <label for="iRedirect"><?php echo $lang['Choose_page_redirect']; ?></label>
              <select name="iRedirect" id="iRedirect">
                <option value=""><?php echo $lang['none']; ?></option>
                <?php echo $oPage->listPagesSelectAdmin( ( !empty( $aData['sRedirect'] ) && is_numeric( $aData['sRedirect'] ) ) ? $aData['sRedirect'] : null ); ?>
              </select>
              <a href="#" class="expand"><?php echo $lang['Display_all']; ?></a>
            </div>
          </div>
        </li>
        <!-- tab advanced -->
      </ul>

      <ul id="tab-tags" class="forms list">
        <li class="tag">
          <label for="iTags"><?php echo $lang['Tags']; ?></label>
          <div>
            <div id="tags"><select name="aTags[]" id="iTags" multiple="multiple" size="15"><?php echo listTagsSelect( isset( $aData['iPage'] ) ? $aData['iPage'] : null ); ?></select></div>
            <div class="add"><a href="?p=tags-form" target="_blank" class="expand open-popup"><?php echo $lang['New_tag']; ?></a></div>
          </div>
        </li>
        <!-- tab tags -->
      </ul>

      <section id="tab-add-files" class="forms files">

        <script src="plugins/valums-file-uploader/fileuploader.min.js"></script>
        <div id="fileUploader">		
        </div>
        <div id="attachingFilesInfo"><?php echo $lang['Choose_files_to_attach']; ?></div>
        <ul id="files-dir">
        <?php echo $oFile->listFilesInDir( Array( 'sSort' => 'time' ) ); ?>
        </ul>
      </section>

      <section id="tab-files" class="forms files">
        <?php echo $sFilesList; ?>
      </section>
      <script>$(function(){ bindCheckAll( '#delete-all', 'delete' ); });</script>

      <ul class="buttons bottom">
        <li class="save"><input type="submit" name="sOption" class="main" value="<?php echo $lang['save']; ?>" /></li>
        <li class="options"><input type="submit" value="<?php echo $lang['save_add_new']; ?>" name="sOptionAddNew" />
          <ul>
            <li><input type="submit" value="<?php echo $lang['save_list']; ?>" name="sOptionList" /></li>
              <li><input type="submit" value="<?php echo $lang['save_and_clone']; ?>" name="sOptionClone" /></li>
          </ul>
        </li>
      </ul>

    </fieldset>
  </form>

</section>
<script>
  var sTypesSelect = '<?php echo getSelectFromArray( $config['images_locations'], $config['default_image_location'] ); ?>',
      sCropSelect = '<?php echo getSelectFromArray( $config['crop_options'], $config['default_image_crop'] ); ?>',
      sSizeSelect = '<?php echo getThumbnailsSelect( $config['default_image_size'] ); ?>';
  $(function(){
    var uploader = new qq.FileUploader({
      element: document.getElementById('fileUploader'),
      action: aQuick['sPhpSelf']+'?p=ajax-files-upload',
      inputName: 'sFileName',
      uploadButtonText: '<?php echo addslashes( $lang['Files_from_computer'] ); ?>',
      cancelButtonText: '<?php echo addslashes( $lang['Cancel'] ); ?>',
      failUploadText: '<?php echo addslashes( $lang['Upload_failed'] ); ?>',
      onComplete: function(id, fileName, response){
        if (!response.success){
          return;
        }
        if( uploader.getInProgress() == 0 )
          refreshFiles( );
        if( response.size_info ){
          qq.addClass(uploader._getItemByFileId(id), 'qq-upload-maxdimension');
          uploader._getItemByFileId(id).innerHTML += '<span class="qq-upload-warning"><?php echo addslashes( $lang['Image_over_max_dimension'] ); ?></span>';
        }
      }
    });

    displayTabInit();
    checkType();
    checkChangedFile( );
    $( ".main-form" ).quickform();
    cacheSelect( 'iPageParent', 'pageParent2', 'pageParent2Ctn' );
    customCheckbox();

    $( '#tab-content li.short-description label a.expand.description-short' ).click( function(e){ e.preventDefault(); displayMore( this, '#tab-content li.short-description .toggle', 'bHideShortDescription' ) } );
    $( '#tab-advanced li a.expand.subpages' ).click( function(e){ e.preventDefault(); displayMore( this, '#tab-advanced li.adv-function', 'bTempSubpagesFunctions' ) } );
    $( '#tab-advanced li a.expand.redirect' ).click( function(e){ e.preventDefault(); displayMore( this, '#tab-advanced div.adv-redirect' ) } );
    $( '#tab-files caption a' ).click( function(e){ e.preventDefault(); displayMore( this, '#tab-files .adv', 'iFilesAdv' ); } );

    displayMore( '#tab-content li.short-description label a.expand.description-short', '#tab-content li.short-description .toggle', 'bHideShortDescription', <?php echo !empty( $aData['sDescriptionShort'] ) ? 'true' : 'false'; ?> );
    displayMore( '#tab-advanced li a.expand.subpages', '#tab-advanced li.adv-function', 'bTempSubpagesFunctions', <?php echo !empty( $aData['sListFunction'] ) ? 'true' : 'false'; ?> );
    displayMore( '#tab-advanced li a.expand.redirect', '#tab-advanced div.adv-redirect', null, <?php echo ( !empty( $aData['sRedirect'] ) && is_numeric( $aData['sRedirect'] ) ) ? 'true' : 'false'; ?> );
    displayMore( '#tab-files caption a', '#tab-files .adv', 'iFilesAdv', ( ( getCookie( 'iFilesAdv' ) == 1 ) ? true : false ) );


    $( '#tab-files tbody .thumb.adv-toogle select' ).change( function(){ setThumbAllSizes( this ); } );
    $( '#tab-files tbody .thumb.adv select' ).change( function(){ checkThumbAllSizesSelect( this ); } );

    $( 'li.parent .all a' ).click( function(e){ e.preventDefault(); allPagesInSelect( this, 'iPageParent', <?php echo isset( $aData['iPageParent'] ) ? $aData['iPageParent'] : '\'\'' ?> );} );
    $( '.redirect .adv-redirect a.expand' ).click( function(e){ e.preventDefault(); allPagesInSelect( this, 'iRedirect', <?php echo ( !empty( $aData['sRedirect'] ) && is_numeric( $aData['sRedirect'] ) ) ? $aData['sRedirect'] : '\'\'' ?> );} );
    $( 'th.display-files-all a' ).click( function(e){e.preventDefault();allServerFiles( );} );
    filesFromServerEvents( );
    $( '#tab-files td.name' ).hover( displayThumbPreview, clearThumbPreview );
    $( 'ul.buttons input[name="sOptionAddNew"]' ).click( function(){ delCookie( 'sSelectedTab' ); } );
  });
</script>
<?php
require_once 'templates/admin/_footer.php';
?>