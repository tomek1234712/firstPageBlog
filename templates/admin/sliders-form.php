<?php 
if( !defined( 'ADMIN_PAGE' ) || isset( $config['disable_sliders'] ) )
  exit( 'Script by OpenSolution.org' );

require_once 'core/sliders-admin.php';

if( isset( $_POST['sDescription'] ) ){
  $iSlider = saveSlider( $_POST );
  if( isset( $_POST['sOptionList'] ) )
    header( 'Location: '.$config['admin_file'].'?p=sliders&sOption=save' );
  elseif( isset( $_POST['sOptionAddNew'] ) )
    header( 'Location: '.$config['admin_file'].'?p=sliders-form&sOption=save' );
  else
    header( 'Location: '.$config['admin_file'].'?p=sliders-form&sOption=save&iSlider='.$iSlider );
  exit;
}

if( isset( $_GET['iSlider'] ) && is_numeric( $_GET['iSlider'] ) ){
  $aData = throwSlider( $_GET['iSlider'] );
}

$sSelectedMenu = 'sliders';
require_once 'templates/admin/_header.php';
require_once 'templates/admin/_menu.php';
?>

<section id="body" class="sliders">

  <h1><?php echo ( isset( $aData['iSlider'] ) ) ? $lang['Sliders_form'] : $lang['New_slider']; ?></h1>
  <?php if( isset( $config['manual_link'] ) ){
    echo '<div class="manual"><a href="'.$config['manual_link'].'instruction#sliders-form" title="'.$lang['Help'].'" target="_blank"></a></div>';
  }
  if( isset( $_GET['sOption'] ) ){
    echo '<h2 class="msg">'.$lang['Operation_completed'].'</h2>';
  }?>

  <form action="?p=<?php echo $_GET['p']; ?>" enctype="multipart/form-data" name="form" method="post" class="main-form no-tabs" onsubmit="return checkSliderFields();">
    <fieldset>
      <input type="hidden" name="iSlider" value="<?php if( isset( $aData['iSlider'] ) ) echo $aData['iSlider']; ?>" />

      <?php if( isset( $aData['iSlider'] ) ){ ?>
      <ul class="options">
        <li class="delete"><a href="?p=sliders&amp;iItemDelete=<?php echo $aData['iSlider']; ?>" title="<?php echo $lang['Delete']; ?>" onclick="return del( )"><?php echo $lang['Delete']; ?></a></li>
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
        <?php if( !empty( $aData['sFileName'] ) ){ ?>
        <li>
          <label><?php echo $lang['Image']; ?></label>
          <?php echo '<a href="files/'.$aData['sFileName'].'" target="_blank">'.$aData['sFileName'].'</a>'; ?>
        </li>
        <?php }
        else{ ?>
        <li class="help"><?php echo $lang['Slider_required_fields']; ?></li>
        <li>
          <label for="sFileName"><?php echo $lang['Image']; ?></label>
          <input type="file" name="aFile" id="sFileName" data-form-check="ext;<?php echo $config['allowed_image_extensions']; ?>" data-form-if="true" /> <span class="ext"><?php echo str_replace( '|', ' | ', $config['allowed_image_extensions'] ); ?></span>
        </li>
        <?php } ?>
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
        <li>
          <label for="sDescription"><?php echo $lang['Description']; ?></label>
          <?php echo getTextarea( 'sDescription', isset( $aData['sDescription'] ) ? $aData['sDescription'] : null ); ?>
        </li>
        <li>
          <label for="iType"><?php echo $lang['Type']; ?></label>
          <select name="iType" id="iType"><?php echo getSelectFromArray( $config['sliders_types'], isset( $aData['iType'] ) ? $aData['iType'] : $config['default_sliders_type'] ); ?></select>
        </li>
        <li>
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
  customCheckbox();
});
$( '#tab-content li a.expand.redirect' ).click( function(){ displayMore( this, '#tab-content div.adv-redirect', 'bSliderRedirect' ) } );
displayMore( '#tab-content li a.expand.redirect', '#tab-content div.adv-redirect', 'bSliderRedirect', <?php echo ( !empty( $aData['sRedirect'] ) && is_numeric( $aData['sRedirect'] ) ) ? 'true' : 'false'; ?> );
$( '#tab-content div.adv-redirect a.expand' ).click( function(e){ e.preventDefault(); allPagesInSelect( this, 'iRedirect', <?php echo ( !empty( $aData['sRedirect'] ) && is_numeric( $aData['sRedirect'] ) ) ? $aData['sRedirect'] : '\'\'' ?> );} );
</script>
<?php
require_once 'templates/admin/_footer.php';
?>
