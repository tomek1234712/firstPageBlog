<?php 
if( !defined( 'ADMIN_PAGE' ) || isset( $config['disable_widgets'] ) )
  exit( 'Script by OpenSolution.org' );

require_once 'core/widgets-admin.php';

if( isset( $_GET['iItemDelete'] ) && is_numeric( $_GET['iItemDelete'] ) ){
  deleteWidget( $_GET['iItemDelete'] );
  header( 'Location: '.$config['admin_file'].'?p=widgets&sOption=del' );
  exit;
}

$sSelectedMenu = 'widgets';
require_once 'templates/admin/_header.php';
require_once 'templates/admin/_menu.php';
?>
<section id="body" class="widgets">
  <h1><?php echo $lang['Widgets']; ?></h1>
  <?php if( isset( $config['manual_link'] ) ){
    echo '<div class="manual"><a href="'.$config['manual_link'].'instruction#widgets" title="'.$lang['Help'].'" target="_blank"></a></div>';
  }
  if( isset( $_GET['sOption'] ) ){
    echo '<h2 class="msg">'.$lang['Operation_completed'].'</h2>';
  }?>

  <?php 
  $sWidgetsList = listWidgetsAdmin( );
  if( isset( $sWidgetsList ) ){
    if( !isset( $config['enabled_widgets'] ) ){
      echo '<h2 class="msg">'.$lang['Turn_on_widgets_to_see_in_front_end'].'</h2>';
    }
  ?>
  <form action="#" method="get" class="search" onsubmit="return false;">
    <fieldset>
      <label for="sSearch"><?php echo $lang['search']; ?></label> <input type="text" name="sSearch" id="sSearch" class="search" placeholder="<?php echo $lang['search']; ?>" value="" size="50" onkeyup="listSearch( this, 'list' )" />
    </fieldset>
  </form>

  <table class="list widgets" id="list">
    <thead>
      <tr>
        <th class="id"><a href="?p=widgets&amp;sSort=id" class="sort"><?php echo $lang['Id']; ?></a></th>
        <th class="name"><a href="?p=widgets&amp;sSort=name" class="sort"><?php echo $lang['Name']; ?></a></th>
        <th class="status"><?php echo $lang['Status']; ?></th>
        <th class="position"><a href="?p=widgets&amp;sSort=position" class="sort"><?php echo $lang['Position']; ?></a></th>
        <th class="location"><a href="?p=widgets" class="sort"><?php echo $lang['Location']; ?></a></th>
        <th class="display"><a href="?p=widgets&amp;sSort=content-type" class="sort"><?php echo $lang['Display_option']; ?></a></th>
        <th class="options">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php echo $sWidgetsList; ?>
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
