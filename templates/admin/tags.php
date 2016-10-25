<?php 
if( !defined( 'ADMIN_PAGE' ) )
  exit( 'Script by OpenSolution.org' );

require_once 'core/tags-admin.php';

if( isset( $_GET['iItemDelete'] ) && is_numeric( $_GET['iItemDelete'] ) ){
  deleteTag( $_GET['iItemDelete'] );
  header( 'Location: '.$config['admin_file'].'?p=tags&sOption=del' );
  exit;
}

$sSelectedMenu = 'pages';
require_once 'templates/admin/_header.php';
require_once 'templates/admin/_menu.php';
?>
<section id="body" class="tags">
  <h1><?php echo $lang['Tags']; ?></h1>
  <?php if( isset( $config['manual_link'] ) ){
    echo '<div class="manual"><a href="'.$config['manual_link'].'instruction#tags" title="'.$lang['Help'].'" target="_blank"></a></div>';
  }
  if( isset( $_GET['sOption'] ) ){
    echo '<h2 class="msg">'.$lang['Operation_completed'].'</h2>';
  }?>

  <?php 
  $sTagsList = listTagsAdmin( );
  if( isset( $sTagsList ) ){
  ?>
  <form action="#" method="get" class="search" onsubmit="return false;">
    <fieldset>
      <label for="sSearch"><?php echo $lang['search']; ?></label> <input type="text" name="sSearch" id="sSearch" class="search" placeholder="<?php echo $lang['search']; ?>" value="" size="50" onkeyup="listSearch( this, 'list' )" />
    </fieldset>
  </form>

  <table class="list tags" id="list">
    <thead>
      <tr>
        <th class="id"><?php echo $lang['Id']; ?></th>
        <th class="name"><?php echo $lang['Name']; ?></th>
        <th class="url"><?php echo $lang['Url_name']; ?></th>
        <th class="position"><?php echo $lang['Position']; ?></th>
        <th class="pages"><?php echo $lang['Pages_with_tag']; ?></th>
        <th class="options">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php echo $sTagsList; ?>
    </tbody>
  </table>
  <?php
    }
    else{
      echo '<h2 class="msg error">'.$lang['Data_not_found'].'</h2>';
    }
  ?>

</section>
<?php
require_once 'templates/admin/_footer.php';
?>
