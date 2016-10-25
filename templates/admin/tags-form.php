<?php 
if( !defined( 'ADMIN_PAGE' ) )
  exit( 'Script by OpenSolution.org' );

require_once 'core/tags-admin.php';

if( isset( $_POST['sName'] ) ){
  $mReturn = saveTag( $_POST );
  if( is_numeric( $mReturn ) ){
    if( isset( $_POST['sOptionList'] ) )
      header( 'Location: '.$config['admin_file'].'?p=tags&sOption=save' );
    elseif( isset( $_POST['sOptionAddNew'] ) )
      header( 'Location: '.$config['admin_file'].'?p=tags-form&sOption=save' );
    else
      header( 'Location: '.$config['admin_file'].'?p=tags-form&sOption=save&iTag='.$mReturn );
  }
  else{
    header( 'Location: '.$config['admin_file'].'?p=tags-form&sOption=error'.( isset( $_POST['iTag'] ) && is_numeric( $_POST['iTag'] ) ? '&iTag='.$_POST['iTag'] : null ) );
  }
  exit;
}

if( isset( $_GET['iTag'] ) && is_numeric( $_GET['iTag'] ) ){
  $aData = throwTag( $_GET['iTag'] );
}

$sSelectedMenu = 'pages';
require_once 'templates/admin/_header.php';
require_once 'templates/admin/_menu.php';
?>
<section id="body" class="tags">

  <h1><?php echo ( isset( $aData['iTag'] ) ) ? $lang['Tags_form'].': '.$aData['sName'] : $lang['New_tag']; ?></h1>
  <?php if( isset( $config['manual_link'] ) ){
    echo '<div class="manual"><a href="'.$config['manual_link'].'instruction#tags-form" title="'.$lang['Help'].'" target="_blank"></a></div>';
  }
  if( isset( $_GET['sOption'] ) ){
    if( $_GET['sOption'] == 'error' )
      echo '<h2 class="msg error">'.$lang['Tag_url_exists'].'</h2>';
    else
      echo '<h2 class="msg">'.$lang['Operation_completed'].'</h2>';
  }?>

  <form action="?p=<?php echo $_GET['p']; ?>" name="form" method="post" class="main-form no-tabs">
    <fieldset>
      <input type="hidden" name="iTag" value="<?php if( isset( $aData['iTag'] ) ) echo $aData['iTag']; ?>" />

      <?php if( isset( $aData['iTag'] ) ){ ?>
      <ul class="options">
        <li class="delete"><a href="?p=tags&amp;iItemDelete=<?php echo $aData['iTag']; ?>" title="<?php echo $lang['Delete']; ?>" onclick="return del( )"><?php echo $lang['Delete']; ?></a></li>
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
          <input type="text" name="sName" id="sName" size="75" value="<?php if( isset( $aData['sName'] ) ) echo $aData['sName']; ?>" placeholder="<?php echo $lang['required']; ?>" data-form-check="required" />
        </li>
        <li>
          <label for="sUrl"><?php echo $lang['Url_name']; ?></label>
          <input type="text" name="sUrl" id="sUrl" size="75" value="<?php if( isset( $aData['sUrl'] ) ) echo $aData['sUrl']; ?>" />
        </li>
        <li>
          <label for="sTitle"><?php echo $lang['Page_title']; ?></label>
          <input type="text" name="sTitle" value="<?php if( isset( $aData['sTitle'] ) ) echo $aData['sTitle']; ?>" id="sTitle" size="75" maxlength="60" />
        </li>
        <li>
          <label for="sDescriptionMeta"><?php echo $lang['Meta_description']; ?></label>
          <input type="text" name="sDescriptionMeta" value="<?php if( isset( $aData['sDescriptionMeta'] ) ) echo $aData['sDescriptionMeta']; ?>" id="sDescriptionMeta" size="75" maxlength="160" />
        </li>
        <li>
          <label for="iPosition"><?php echo $lang['Position']; ?></label>
          <input type="text" id="iPosition" name="iPosition" value="<?php echo isset( $aData['iPosition'] ) ? $aData['iPosition'] : 0; ?>" class="numeric" size="3" maxlength="4" />
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
} );
</script>
<?php
require_once 'templates/admin/_footer.php';
?>
