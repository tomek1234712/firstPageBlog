<?php 
if( !defined( 'ADMIN_PAGE' ) )
  exit( 'Script by OpenSolution.org' );

require_once 'core/newsletter-admin.php';

if( isset( $_GET['sItemDelete'] ) && !empty( $_GET['sItemDelete'] ) ){
  deleteNewsletterEmail( $_GET['sItemDelete'] );
  header( 'Location: '.$_SERVER['admin_file'].'?p=newsletter&sOption=del' );
  exit;
}
elseif( isset( $_GET['sOption'] ) && $_GET['sOption'] == 'get-list' ){
  header( 'Content-Type: text/plain' );
  header( 'Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT' );
  header( 'Content-Disposition: attachment; filename="newsletter_emails_'.date( 'Y-m-d' ).'.txt"' );
  header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
  header( 'Pragma: public' );
  echo listNewsletterEmailsTxt( );
  exit;
}

$sSelectedMenu = 'settings';
require_once 'templates/admin/_header.php';
require_once 'templates/admin/_menu.php';
?>
<section id="body" class="newsletter">
  <h1><?php echo $lang['Newsletter']; ?></h1>
  <?php if( isset( $config['manual_link'] ) ){
    echo '<div class="manual"><a href="'.$config['manual_link'].'instruction#newsletter" title="'.$lang['Help'].'" target="_blank"></a></div>';
  }
  if( isset( $_GET['sOption'] ) ){
    echo '<h2 class="msg">'.$lang['Operation_completed'].'</h2>';
  }?>

  <?php 
  $sNewsletterList = listNewsletterEmails( );
  if( isset( $sNewsletterList ) ){
  ?>
  <form action="#" method="get" class="search" onsubmit="return false;">
    <fieldset>
      <label for="sSearch"><?php echo $lang['search']; ?></label> <input type="text" name="sSearch" id="sSearch" class="search" placeholder="<?php echo $lang['search']; ?>" value="" size="50" onkeyup="listSearch( this, 'list' )" />
    </fieldset>
  </form>
  <div class="get-list">
    <a href="?p=newsletter&amp;sOption=get-list"><?php echo $lang['Newsletter_get_list']; ?></a>
  </div>
  <table class="list newsletter" id="list">
    <thead>
      <tr>
        <th class="email"><a href="?p=newsletter&amp;sSort=email" class="sort"><?php echo $lang['Email']; ?></a></th>
        <th class="status"><a href="?p=newsletter&amp;sSort=status" class="sort"><?php echo $lang['Confirmed']; ?></a></th>
        <th class="date"><a href="?p=newsletter" class="sort"><?php echo $lang['Date']; ?></a></th>
        <th class="options">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php echo $sNewsletterList; ?>
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
