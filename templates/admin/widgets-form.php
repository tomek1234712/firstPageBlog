<?php 
if( !defined( 'ADMIN_PAGE' ) || isset( $config['disable_widgets'] ) )
  exit( 'Script by OpenSolution.org' );

require_once 'core/widgets-admin.php';
require_once 'core/tags-admin.php';

if( isset( $_POST['sName'] ) ){
  $iWidget = saveWidget( $_POST );
  if( isset( $_POST['sOptionList'] ) )
    header( 'Location: '.$config['admin_file'].'?p=widgets&sOption=save' );
  elseif( isset( $_POST['sOptionAddNew'] ) )
    header( 'Location: '.$config['admin_file'].'?p=widgets-form&sOption=save' );
  else
    header( 'Location: '.$config['admin_file'].'?p=widgets-form&sOption=save&iWidget='.$iWidget );
  exit;
}

if( isset( $_GET['iWidget'] ) && is_numeric( $_GET['iWidget'] ) ){
  $aData = throwWidget( $_GET['iWidget'] );
}

$sSelectedMenu = 'widgets';
require_once 'templates/admin/_header.php';
require_once 'templates/admin/_menu.php';
?>
<section id="body" class="widgets">

  <h1><?php echo ( isset( $aData['iWidget'] ) ) ? $lang['Widgets_form'].': '.$aData['sName'] : $lang['New_widget']; ?></h1>
  <?php if( isset( $config['manual_link'] ) ){
    echo '<div class="manual"><a href="'.$config['manual_link'].'instruction#widgets-form" title="'.$lang['Help'].'" target="_blank"></a></div>';
  }
  if( isset( $_GET['sOption'] ) ){
    echo '<h2 class="msg">'.$lang['Operation_completed'].'</h2>';
  }?>

  <form action="?p=<?php echo $_GET['p']; ?>" name="form" method="post" class="main-form no-tabs">
    <fieldset>
      <input type="hidden" name="iWidget" value="<?php if( isset( $aData['iWidget'] ) ) echo $aData['iWidget']; ?>" />

      <?php if( isset( $aData['iWidget'] ) && !isset( $config['disable_widgets_delete'] ) ){ ?>
      <ul class="options">
        <li class="delete"><a href="?p=widgets&amp;iItemDelete=<?php echo $aData['iWidget']; ?>" title="<?php echo $lang['Delete']; ?>" onclick="return del( )"><?php echo $lang['Delete']; ?></a></li>
      </ul>
      <?php } ?>
      <ul class="buttons">
        <li class="save"><input type="submit" name="sOption" class="main" value="<?php echo $lang['save']; ?>" /></li>
        <li class="options"><input type="submit" value="<?php echo $lang['save_add_new']; ?>" name="sOptionAddNew" />
          <ul>
            <li><input type="submit" value="<?php echo $lang['save_list']; ?>" name="sOptionList" /></li>
          </ul>
        </li>
      </ul>

      <ul id="tab-content" class="forms list">
        <li>
          <label for="sName"><?php echo $lang['Name']; ?></label>
          <input type="text" name="sName" id="sName" size="40" value="<?php if( isset( $aData['sName'] ) ) echo $aData['sName']; ?>" placeholder="<?php echo $lang['required']; ?>" data-form-check="required" /> <span class="custom"><?php echo getYesNoBox( 'iDisplayName', isset( $aData['iDisplayName'] ) ? $aData['iDisplayName'] : 1 ); ?><label for="iDisplayName"><?php echo $lang['Display']; ?></label></span>
        </li>
        <li>
          <label for="sDescription"><?php echo $config['widgets_contents'][1]; ?></label>
          <?php echo getTextarea( 'sDescription', isset( $aData['sDescription'] ) ? $aData['sDescription'] : null ); ?>
        </li>
        <li>
          <label for="iType"><?php echo $lang['Type']; ?></label>
          <select name="iType" id="iType"><?php echo getSelectFromArray( $config['widgets_types'], ( isset( $aData['iType'] ) ? $aData['iType'] : $config['default_widget_type'] ) ); ?></select>
        </li>
        <li>
          <label for="iContentType"><?php echo $lang['Display_option']; ?></label>
          <select name="iContentType" id="iContentType" onchange="displayContentTypeOptions( this, 'content-type-' )"><?php echo getSelectFromArray( $config['widgets_contents'], ( isset( $aData['iContentType'] ) ? $aData['iContentType'] : $config['default_widget_content'] ) ); ?></select>
        </li>
        <li class="content-type-2-options page">
          <label for="iPage"><?php echo $config['widgets_contents'][2]; ?></label>
          <select name="iPage" id="iPage">
            <option value=""><?php echo $lang['none']; ?></option>
            <?php echo $oPage->listPagesSelectAdmin( ( !empty( $aData['iPage'] ) && is_numeric( $aData['iPage'] ) ) ? $aData['iPage'] : null ); ?>
          </select>
          <?php if( isset( $config['main_only'] ) ){ ?>
          <a href="#" class="expand"><?php echo $lang['Display_all']; ?></a>
          <?php } ?>
        </li>
        <li class="content-type-2-options">
          <label for="sPageElements"><?php echo $lang['Elements']; ?></label>
          <input type="text" name="sPageElements" size="40" id="sPageElements" value="<?php echo ( !empty( $aData['sPageElements'] ) ? implode( ', ', array_keys( unserialize( $aData['sPageElements'] ) ) ) : implode( ', ', $config['default_widgets_page_elements'] ) ); ?>" /> <?php echo $lang['example']; ?> image, name, description, date, more
        </li>
        <li class="content-type-3-options">
          <label for="iSliderType"><?php echo $config['widgets_contents'][3]; ?></label>
          <select name="iSliderType" id="iSliderType"><?php echo getSelectFromArray( $config['sliders_types'], ( isset( $aData['iSliderType'] ) ? $aData['iSliderType'] : $config['default_widget_slider_type'] ) ); ?></select>
        </li>
        <li class="content-type-4-options">
          <label for="iMenu"><?php echo $config['widgets_contents'][4]; ?></label>
          <select name="iMenu" id="iMenu"><?php echo getSelectFromArray( $config['pages_menus'], isset( $aData['iMenu'] ) ? $aData['iMenu'] : $config['default_pages_menu'] ); ?></select>
        </li>
        <li class="content-type-5-options content-type-6-options subpages">
          <label for="iSubpages"><?php echo $lang['Display_subpages']; ?></label>
          <select name="iSubpages" id="iSubpages">
            <option value=""><?php echo $lang['none']; ?></option>
            <?php echo $oPage->listPagesSelectAdmin( ( !empty( $aData['iSubpages'] ) && is_numeric( $aData['iSubpages'] ) ) ? $aData['iSubpages'] : null ); ?>
          </select>
          <a href="#" class="expand"><?php echo $lang['Display_all']; ?></a>
        </li>
        <li class="content-type-10-options">
            <label for="iNoticeType"><?php echo $lang['Notice_location']; ?></label>
            <select name="iNoticeType" id="iNoticeType"><?php echo getSelectFromArray( $config['widgets_notices_types'], isset( $aData['iNoticeType'] ) ? $aData['iNoticeType'] : null ); ?></select>
          </li>
          <li class="content-type-10-options custom">
            <span class="label"><?php echo $lang['Notice_once']; ?></span>
            <?php echo getYesNoBox( 'iOnce', isset( $aData['iOnce'] ) ? $aData['iOnce'] : 1 ); ?>
            <label for="iOnce"><?php echo $lang['Notice_once']; ?></label>
          </li>
          <li class="content-type-23-options">
          <label for="aTags"><?php echo $lang['Display_tags']; ?></label>
          <select name="aTags[]" id="aTags" multiple="multiple" size="15">
            <?php echo listTagsSelect( ( isset( $aData['aTags'] ) ) ? $aData['aTags'] : null ); ?>
          </select>
        </li>
        <li class="position">
          <label for="iPosition"><?php echo $lang['Position']; ?></label>
          <input type="text" id="iPosition" name="iPosition" value="<?php echo isset( $aData['iPosition'] ) ? $aData['iPosition'] : 0; ?>" class="numeric" size="3" maxlength="4" />
        </li>
        <li class="custom">
          <span class="label">&nbsp;</span>
          <?php echo getYesNoBox( 'iStatus', isset( $aData['iStatus'] ) ? $aData['iStatus'] : 1 ); ?>
          <label for="iStatus"><?php echo $lang['Status']; ?></label>
        </li>
        <!-- tab content -->
      </ul>


      <ul class="buttons bottom">
        <li class="save"><input type="submit" name="sOption" class="main" value="<?php echo $lang['save']; ?>" /></li>
        <li class="options"><input type="submit" value="<?php echo $lang['save_add_new']; ?>" name="sOptionAddNew" />
          <ul>
            <li><input type="submit" value="<?php echo $lang['save_list']; ?>" name="sOptionList" /></li>
          </ul>
        </li>
      </ul>

    </fieldset>
  </form>

</section>
<script>
$(function(){
  $('.main-form').quickform();
  displayContentTypeOptions( gEBI( 'iContentType' ), 'content-type-', true );
  customCheckbox();
} );
$( '#tab-content li.page a.expand' ).click( function(e){ e.preventDefault(); allPagesInSelect( this, 'iPage', <?php echo ( !empty( $aData['iPage'] ) && is_numeric( $aData['iPage'] ) ) ? $aData['iPage'] : '\'\'' ?> );} );
$( '#tab-content li.subpages a.expand' ).click( function(e){ e.preventDefault(); allPagesInSelect( this, 'iSubpages', <?php echo ( !empty( $aData['iSubpages'] ) && is_numeric( $aData['iSubpages'] ) ) ? $aData['iSubpages'] : '\'\'' ?> );} );
</script>
<?php
require_once 'templates/admin/_footer.php';
?>
