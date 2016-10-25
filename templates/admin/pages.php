<?php 
if( !defined( 'ADMIN_PAGE' ) )
  exit( 'Script by OpenSolution.org' );

if( isset( $_POST['sOption'] ) ){
  $oPage->savePages( $_POST );
  header( 'Location: '.str_replace( '&amp;', '&', $_SERVER['REQUEST_URI'] ).( strstr( $_SERVER['REQUEST_URI'], 'sOption=' ) ? null : '&sOption=' ) );
  exit;
}

if( isset( $_GET['iItemDelete'] ) && is_numeric( $_GET['iItemDelete'] ) ){
  $oPage->deletePage( $_GET['iItemDelete'] );
  header( 'Location: '.$config['admin_file'].'?p=pages&sOption=del' );
  exit;
}

$sSelectedMenu = 'pages';
require_once 'templates/admin/_header.php';
require_once 'templates/admin/_menu.php';

if( isset( $_GET['iPageNews'] ) && is_numeric( $_GET['iPageNews'] ) ){
  $aData = $oPage->throwPageAdmin( $_GET['iPageNews'] );
}
?>

<section id="body" class="pages">

  <h1><?php echo $lang['Pages'].( isset( $aData['sName'] ) ? ' - '.$aData['sName'] : null ); ?></h1>
  <?php if( isset( $config['manual_link'] ) ){
    echo '<div class="manual"><a href="'.$config['manual_link'].'instruction#pages" title="'.$lang['Help'].'" target="_blank"></a></div>';
  }
  if( isset( $_GET['sOption'] ) ){
    echo '<h2 class="msg">'.$lang['Operation_completed'].'</h2>';
  }
  ?>
  <form action="#" method="get" class="search box">
    <fieldset>
      <input type="hidden" name="p" value="<?php if( isset( $_GET['p'] ) ) echo $_GET['p']; ?>" />
      <input type="hidden" name="sSort" value="<?php if( isset( $_GET['sSort'] ) ) echo $_GET['sSort']; ?>" />
      <label for="sSearch"><?php echo $lang['search']; ?></label> <input type="text" name="sSearch" id="sSearch" class="search" value="<?php if( isset( $_GET['sSearch'] ) ) echo $_GET['sSearch']; ?>" size="50" />
      <input type="submit" value="<?php echo $lang['search']; ?> &raquo;" />
    </fieldset>
  </form>
  <?php
  if( isset( $_GET['sSearch'] ) && !empty( $_GET['sSearch'] ) ){
    $sPagesList = $oPage->listPagesAdminSearch( $_GET['sSearch'] );
  }
  elseif( isset( $_GET['iPageNews'] ) && is_numeric( $_GET['iPageNews'] ) ){
    $sPagesList = $oPage->listPagesAdmin( Array( 'iPageNews' => $_GET['iPageNews'] ) );
  }
  else{
    $sPagesList = null;
    foreach( $config['pages_menus'] as $iMenu => $sMenu ){
      $sPagesList .= $oPage->listPagesAdmin( Array( 'iMenu' => $iMenu ) );
    } // end foreach
  }

  if( !empty( $sPagesList ) ){
  ?>
  <form action="?p=pages<?php if( isset( $_GET['sSort'] ) ) echo '&amp;sSort='.$_GET['sSort']; if( isset( $_GET['sSearch'] ) ) echo '&amp;sSearch='.$_GET['sSearch']; if( isset( $_GET['iPageNews'] ) ) echo '&amp;iPageNews='.$_GET['iPageNews']; ?>" name="form" method="post" class="main-form">
    <fieldset>

      <ul class="buttons">
        <li class="save"><input type="submit" name="sOption" class="main" value="<?php echo $lang['save']; ?>" /></li>
      </ul>

      <script>$(function(){ bindCheckAll( '#status-all' ); });</script>

      <table class="list pages">
        <thead>
          <tr>
            <th class="id"><a href="?p=pages&amp;sSort=id<?php if( isset( $_GET['sSearch'] ) ) echo '&amp;sSearch='.$_GET['sSearch']; if( isset( $_GET['iPageNews'] ) ) echo '&amp;iPageNews='.$_GET['iPageNews']; ?>" class="sort"><?php echo $lang['Id']; ?></a></th>
            <th class="name"><a href="?p=pages&amp;sSort=name<?php if( isset( $_GET['sSearch'] ) ) echo '&amp;sSearch='.$_GET['sSearch']; if( isset( $_GET['iPageNews'] ) ) echo '&amp;iPageNews='.$_GET['iPageNews']; ?>" class="sort"><?php echo $lang['Name']; ?></a><ul><li class="status custom"><input type="checkbox" name="status-all" id="status-all" /><label for="status-all"><?php echo $lang['Status']; ?></label></li></ul></th>
            <th class="position"><a href="?p=pages<?php if( isset( $_GET['sSearch'] ) ) echo '&amp;sSearch='.$_GET['sSearch']; if( isset( $_GET['iPageNews'] ) ) echo '&amp;sSort=position&amp;iPageNews='.$_GET['iPageNews']; ?>" class="sort"><?php echo $lang['Position']; ?></a></th>
            <th class="options">&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          <?php echo $sPagesList; ?>
        </tbody>
      </table>
      <ul class="buttons bottom">
        <li class="save"><input type="submit" name="sOption" class="main" value="<?php echo $lang['save']; ?>" /></li>
      </ul>

    </fieldset>
  </form>
  <?php
    }
    else{
      echo '<h2 class="msg error">'.$lang['Data_not_found'].'</h2>';
    }

    if( isset( $config['redirect_to_last_used_list'] ) ){
      $_SESSION['sRedirectUrl'] = $_SERVER['REQUEST_URI'];
    }
  ?>
</section>
<?php
require_once 'templates/admin/_footer.php';
?>
