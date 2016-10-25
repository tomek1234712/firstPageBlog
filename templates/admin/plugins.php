<?php 
if( !defined( 'ADMIN_PAGE' ) || isset( $config['disable_plugins'] ) )
  exit( 'Script by OpenSolution.org' );

require_once 'core/plugins-admin.php';

$sSelectedMenu = 'plugins';
require_once 'templates/admin/_header.php';
require_once 'templates/admin/_menu.php';
?>
<script>
  aQuick['sSelectPlugins'] = '<?php echo $lang['Select_plugins']; ?>';
  aQuick['sOperationCompleted'] = '<?php echo $lang['Operation_completed']; ?>';
  aQuick['sInstallationError'] = '<?php echo $lang['Installation_error']; ?>';
  aQuick['sMore'] = '<?php echo $lang['More']; ?>';
  aQuick['sRefreshPanel'] = '<?php echo $lang['Refresh_panel']; ?>';
  var aPlugins = [];
</script>
<section id="body" class="plugins">
  <h1><?php echo $lang['Plugins']; ?></h1>

  <?php if( isset( $config['manual_link'] ) ){
    echo '<div class="manual"><a href="'.$config['manual_link'].'instruction#plugins" title="'.$lang['Help'].'" target="_blank"></a></div>';
  }
  echo '<div class="important-messages-tab main-form"><a href="#" class="expand">'.$lang['Plugins_info'].'</a></div><div class="important-messages"><h2>'.$lang['Plugins_info'].'</h2><ul><li>'.$lang['Plugins_read_manual'].' <a href="'.$config['manual_link'].'install#plugins" target="_blank">'.$lang['More'].' &raquo;</a></li><li>'.$lang['Plugins_license'].' <a href="'.'http://opensolution.org/'.( $config['admin_lang'] == 'pl' ? 'licencja-dodatki.pdf' : 'license-plugins.pdf' ).'" target="_blank">'.'http://opensolution.org/'.( $config['admin_lang'] == 'pl' ? 'licencja-dodatki.pdf' : 'license-plugins.pdf' ).'</a></li>'.( ( !isset( $config['hide_plugins_install'] ) && !checkBinaryMode( ) ) ? '<li>'.$lang['Plugins_upload_script_binary_mode'].' <a href="'.$config['manual_link'].'install" target="_blank">'.$lang['More'].' &raquo;</a></li>' : null ).'</ul><div class="close"><a href="#">'.$lang['Close'].'</a></div></div>';

  $aPluginsList = listPlugins( );
  if( isset( $aPluginsList ) ){
  ?>
  <form action="#" method="get" class="search box" onsubmit="return false;">
    <fieldset>
      <label for="sSearch"><?php echo $lang['search']; ?></label> <input type="text" name="sSearch" id="sSearch" class="search" placeholder="<?php echo $lang['search']; ?>" value="" size="50" onkeyup="listSearch( this, 'list' )" />
    </fieldset>
  </form>

  <form action="#" name="form" method="post" class="main-form" onsubmit="pluginsInstall();return false;">
    <fieldset>

      <ul class="buttons<?php echo ( isset( $config['disable_plugins_install'] ) ? ' hide' : null ); ?>">
        <li><input type="submit" name="sOption" class="main" value="<?php echo $lang['Install']; ?>" /></li>
      </ul>

      <?php echo $aPluginsList[0]; ?>
  
      <table class="list plugins" id="list">
        <thead>
          <tr>
            <th class="install"><?php echo $lang['Install']; ?></th>
            <th class="screenshot"><?php echo $lang['Screenshots']; ?></th>
            <th class="name"><?php echo $lang['Name']; ?></th>
            <th class="description"><?php echo $lang['Description']; ?></th>
            <th class="options">&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          <?php echo $aPluginsList[1]; ?>
        </tbody>
      </table>

      <ul class="buttons bottom<?php echo ( isset( $config['disable_plugins_install'] ) ? ' hide' : null ); ?>">
        <li><input type="submit" name="sOption" class="main" value="<?php echo $lang['Install']; ?>" /></li>
      </ul>
    <a href="" class="quickbox triger" data-quickbox-msg="install-panel"></a>
    </fieldset>
  </form>
  <?php
    }
    else{
      echo '<h2 class="msg error">'.$lang['Data_not_found'].'</h2>';
    }
  ?>

</section>

<script>
  $(function(){
    if( $( 'table.plugins tbody td.install input:checkbox' ).length == 0 ){
      $( '.main-form ul.buttons' ).hide();
      $('#body.plugins').addClass('install-disabled');
    }
    $( 'div.important-messages .close a' ).click( function(e){ e.preventDefault(); $( 'div.important-messages' ).animate({marginTop:'-10px',width:'200px',height:'0px',opacity:0}, 400, function(){$(this).hide();}); $( 'div.important-messages-tab' ).slideDown(); createCookie( 'iPluginsMessagesClosed', 1, 180 ); } );
    $( 'div.important-messages-tab a.expand' ).click( function(e){ e.preventDefault(); $( 'div.important-messages' ).css({height:'auto',width:'auto'}).slideDown().animate({marginTop:'20px',opacity:1}); $( 'div.important-messages-tab' ).slideUp(); delCookie( 'iPluginsMessagesClosed' ) } );
    if( getCookie( 'iPluginsMessagesClosed' ) == 1 ){
      $( 'div.important-messages' ).hide();
      $( 'div.important-messages-tab' ).show();
    }
    if( typeof aPluginsInstalled !== 'undefined' ){
      $.each( aPluginsInstalled, function( i, sPlugin ){
        $( '#aInstall-'+sPlugin ).prop( {disabled: true, checked: true} );
        $( '#'+sPlugin ).addClass( 'installed' );
      });
    }
    <?php if( isset( $config['disable_plugins_install'] ) ){ ?>
      $('#body.plugins').addClass('install-disabled');
      $('table.plugins tr:not(.installed) td.install input[name^="aInstall"]').prop( {disabled: true} );
    <?php } ?>
  });
</script>
<?php
require_once 'templates/admin/_footer.php';
exit;
?>