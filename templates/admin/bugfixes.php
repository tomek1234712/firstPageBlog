<?php 
if( !defined( 'ADMIN_PAGE' ) )
  exit( 'Script by OpenSolution.org' );

require_once 'core/bugfixes-admin.php';

$sSelectedMenu = 'tools';
require_once 'templates/admin/_header.php';
require_once 'templates/admin/_menu.php';
?>
<section id="body" class="bugfixes">
  <h1><?php echo $lang['Bugfixes']; ?></h1>
  <?php if( isset( $config['manual_link'] ) ){
    echo '<div class="manual"><a href="'.$config['manual_link'].'instruction#bugfixes" title="'.$lang['Help'].'" target="_blank"></a></div>';
  }
  if( isset( $_GET['sOption'] ) ){
    echo '<h2 class="msg">'.$lang['Operation_completed'].'</h2>';
  }?>

  <?php 
  $mBugfixesList = listBugFixes( );
  if( isset( $mBugfixesList ) && $mBugfixesList !== true ){
  ?>
  <form action="#" method="get" class="search" onsubmit="return false;">
    <fieldset>
      <label for="sSearch"><?php echo $lang['search']; ?></label> <input type="text" name="sSearch" id="sSearch" class="search" placeholder="<?php echo $lang['search']; ?>" value="" size="50" onkeyup="listSearch( this, 'list' )" />
    </fieldset>
  </form>

  <table class="list bugfixes" id="list">
    <thead>
      <tr>
        <th class="name"><?php echo $lang['Description']; ?></th>
        <th class="status"><?php echo $lang['Status']; ?></th>
        <th class="options">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php echo $mBugfixesList; ?>
    </tbody>
  </table>
  <?php
    }
    else{
      if( $mBugfixesList === true )
        echo '<h2 class="msg">'.$lang['Bugfixes_ok'].'</h2>';
      else
        echo '<h2 class="msg error">'.$lang['Error_getting_data'].'</h2>';
    }
  ?>
</section>
<?php
require_once 'templates/admin/_footer.php';
?>