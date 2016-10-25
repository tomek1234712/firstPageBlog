<?php
if( !defined( 'ADMIN_PAGE' ) )
  exit( 'Script by OpenSolution.org' );

if( isset( $_POST['restore_backup'] ) ){
  saveVariables( $_POST, $config['dir_database'].'config.php' );
  header( 'Location: '.$config['admin_file'].'?p=backup&sOption=save' );
  exit;
}
elseif( isset( $_GET['sItemDelete'] ) && is_file( 'files/backup/'.$_GET['sItemDelete'] ) && strstr( $_GET['sItemDelete'], '.zip' ) ){
  unlink( 'files/backup/'.$_GET['sItemDelete'] );
  header( 'Location: '.$config['admin_file'].'?p=backup&sOption=del' );
  exit;
}
elseif( isset( $_GET['sOption'] ) && $_GET['sOption'] == 'create' && function_exists( 'gzcompress' ) ){
  unset( $_SESSION['sMessagesNotices'] );
  require_once 'plugins/class-pclzip.php';
  if( !is_dir( 'files/backup/' ) )
    mkdir( 'files/backup/' );
  $sFile = 'files/backup/backup_'.date('Y-m-d_H-i').'_'.rand( 10000, 99999 ).'.zip';
  $oBackup = new PclZip( $sFile );
  $oBackup->create( $config['dir_database'] );
  header( 'Location: '.$sFile );
  exit;
}

$sSelectedMenu = 'backup';
require_once 'templates/admin/_header.php';
require_once 'templates/admin/_menu.php';

?>
<section id="body" class="backups">
  <h1><?php echo $lang['Backup']; ?></h1>
  <?php 

if( isset( $config['manual_link'] ) ){
  echo '<div class="manual"><a href="'.$config['manual_link'].'instruction#backup" title="'.$lang['Help'].'" target="_blank"></a></div>';
}

if( isset( $_GET['sOption'] ) ){
  echo '<h2 class="msg">'.$lang['Operation_completed'].'</h2>';
}

// get list of backup files
$sBackupFiles = listBackupFiles( );

// display backup files in the table list
if( isset( $sBackupFiles ) ){
  ?>
  <form action="#" method="get" class="search box" onsubmit="return false;">
    <fieldset>
      <label for="sSearch"><?php echo $lang['search']; ?></label> <input type="text" name="sSearch" id="sSearch" class="search" placeholder="<?php echo $lang['search']; ?>" value="" size="50" onkeyup="listSearch( this, 'list' )" />
    </fieldset>
  </form>
  <form action="#" method="post" class="restore-backup main-form">
    <fieldset class="buttons">
      <?php echo $lang['Restore_data']; ?>:
      <select name="restore_backup"><?php echo getSelectFromArray( Array( $lang['Restore_all'], $lang['Settings'], $lang['Pages'].', '.$lang['Sliders'].', '.$lang['Widgets'].', etc.' ), $config['restore_backup'] ); ?></select>
      <input type="submit" value="<?php echo $lang['save']; ?> &raquo;" />
    </fieldset>
  </form>
  <table class="list backups" id="list">
    <thead>
      <tr>
        <th class="name"><?php echo $lang['Name']; ?></th>
        <th class="date"><?php echo $lang['Date']; ?></th>
        <th class="size"><?php echo $lang['Size']; ?></th>
        <th class="options">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php echo $sBackupFiles; ?>
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