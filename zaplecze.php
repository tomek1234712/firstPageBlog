<?php
/*
* www.pantomasz.eu
*/
$_SERVER['REQUEST_URI'] = htmlspecialchars( strip_tags( $_SERVER['REQUEST_URI'] ) );
$_GET['p'] = isset( $_GET['p'] ) ? htmlspecialchars( strip_tags( $_GET['p'] ) ) : null;
$_GET['sSearch'] = isset( $_GET['sSearch'] ) && !empty( $_GET['sSearch'] ) ? trim( htmlspecialchars( stripslashes( strip_tags( $_GET['sSearch'] ) ) ) ) : null;
$_GET['sSort'] = isset( $_GET['sSort'] ) && !empty( $_GET['sSort'] ) ? htmlspecialchars( stripslashes( strip_tags( $_GET['sSort'] ) ) ) : null;

session_start( );

define( 'ADMIN_PAGE', true );
require_once 'database/config.php';

if( isset( $config['allowed_ips_admin_panel'] ) && ( ( is_array( $config['allowed_ips_admin_panel'] ) && !in_array( $_SERVER['REMOTE_ADDR'], $config['allowed_ips_admin_panel'] ) ) || ( !is_array( $config['allowed_ips_admin_panel'] ) && $config['allowed_ips_admin_panel'] != $_SERVER['REMOTE_ADDR'] ) ) && ( !isset( $config['developer_login_ip'] ) || ( isset( $config['developer_login_ip'] ) && $config['developer_login_ip'] != md5( $_SERVER['REMOTE_ADDR'] ) ) ) ){
  header( 'Location: ./' );
  exit;
}

header( 'Content-Type: text/html; charset=utf-8' );

require_once 'core/libraries/file-jobs.php';
require_once 'core/libraries/image-jobs.php';
$oIJ = ImageJobs::getInstance( );

if( $_GET['p'] == 'backup' && isset( $_GET['sOption'] ) && $_GET['sOption'] == 'restore' && isset( $_GET['sFile'] ) && is_file( 'files/backup/'.$_GET['sFile'] ) && strstr( $_GET['sFile'], '.zip' ) && function_exists( 'gzcompress' ) ){
  require_once 'core/common-admin.php';
  require_once 'plugins/class-pclzip.php';
  restoreBackup( basename( $_GET['sFile'] ) );
  header( 'Location: '.$config['admin_file'].'?p=backup&sOption=restore' );
  exit;
}

require_once 'core/libraries/trash.php';
require_once 'core/libraries/forms-validate.php';
require_once 'core/libraries/sql.php';
$oSql = Sql::getInstance( );

require_once 'core/common.php';
require_once 'core/common-admin.php';
getBinValues( true );
loginActions( );

require_once 'core/pages.php';
require_once 'core/pages-admin.php';
$oPage = PagesAdmin::getInstance( );

require_once 'core/files.php';
require_once 'core/files-admin.php';
$oFile = FilesAdmin::getInstance( );

require_once 'core/lang-admin.php';

require_once 'plugins/plugins-admin.php';

listMessagesNews( );
listMessagesNotices( );
if( ( !empty( $_SERVER['HTTP_REFERER'] ) && !strstr( $_SERVER['HTTP_REFERER'], $_SERVER['SCRIPT_NAME'] ) && ( isset( $_GET['iItemDelete'] ) || isset( $_GET['sItemDelete'] ) || count( $_POST ) > 0 ) ) && ( !isset( $_GET['sVerify'] ) || ( isset( $_GET['sVerify'] ) && $_GET['sVerify'] != md5( $config['session_key_name'] ) ) ) ){
  header( 'Location: '.$config['admin_file'].'?p=dashboard' );
  exit;
}

if( strstr( $_GET['p'], 'ajax-' ) ){
  if( $_GET['p'] == 'ajax-files-upload' && !empty( $_GET['sFileName'] ) ){
    echo $oFile->uploadFile( $_GET['sFileName'] );
  }
  elseif( $_GET['p'] == 'ajax-files-in-dir' ){
    header( 'Cache-Control: no-cache' );
    header( 'Content-type: text/html' );
    echo $oFile->listFilesInDir( Array( 'sSort' => 'time' ) );
  }
  elseif( $_GET['p'] == 'ajax-files-all' ){
    echo $oFile->listFilesInDir( Array( 'bDisplayAll' => true ) );
  }
  elseif( $_GET['p'] == 'ajax-files-thumb' && isset( $_GET['sFileName'] ) ){
    echo $oFile->getImageThumb( $_GET['sFileName'], ( ( isset( $_GET['iSize'] ) && $_GET['iSize'] > 1 ) ? $_GET['iSize'] : null ) );
  }
  elseif( $_GET['p'] == 'ajax-select-pages-all' ){
    echo '<option value=""'.( ( !isset( $_GET['iPageParent'] ) || $_GET['iPageParent'] == 0 ) ? ' selected="selected"' : null ).'>'.$lang['none'].'</option>'.$oPage->listPagesSelectAdmin( ( ( isset( $_GET['iPageParent'] ) && is_numeric( $_GET['iPageParent'] ) ) ? $_GET['iPageParent'] : null ), null );
  }
  elseif( $_GET['p'] == 'ajax-messages-news' && isset( $_SESSION['sMessagesNews'] ) ){
    echo $_SESSION['sMessagesNews'].( $_SESSION['iMessagesNewsNumber'] > 0 ? '<footer><a href="#" onclick="clearMessages( \'news\' );return false;">'.$lang['Mark_as_read'].'</a></footer>' : null );
  }
  elseif( $_GET['p'] == 'ajax-messages-notices-clear' ){
    $_SESSION['iMessagesNoticesNumber'] = 0;
    updateBin( 'failed_logs', 0 );
    updateBin( 'failed_email_password_remind', 0 );
  }
  elseif( $_GET['p'] == 'ajax-verify-login' ){
    if( isset( $_GET['sVerifyemail'] ) && isset( $_GET['sVerifypass'] ) && changeSpecialChars( $_GET['sVerifyemail'] ) == $config['login_email'] && changeSpecialChars( str_replace( '"', '&quot;', $_GET['sVerifypass'] ) ) == $config['login_pass'] ){
      echo 'true';
    }
  }
  elseif( $_GET['p'] == 'ajax-messages-notices' && isset( $_SESSION['sMessagesNotices'] ) ){
    echo $_SESSION['sMessagesNotices'].( $_SESSION['iMessagesNoticesNumber'] > 0 ? '<footer><a href="#" onclick="clearMessages( \'notices\' );return false;">'.$lang['Mark_as_read'].'</a></footer>' : null );
  }
  elseif( $_GET['p'] == 'ajax-plugin-install' && isset( $_GET['sPlugin'] ) ){
    require_once 'plugins/class-pclzip.php';
    require_once 'core/plugins-admin.php';
    echo installPlugin( $_GET['sPlugin'] );
  }
  // end ajax requests
  exit;
}
elseif( !empty( $_GET['p'] ) && preg_match( '/[a-z-]+/', $_GET['p'] ) && is_file( 'templates/admin/'.$_GET['p'].'.php' ) )
  require 'templates/admin/'.$_GET['p'].'.php';
else
  require_once 'templates/admin/dashboard.php';
?>