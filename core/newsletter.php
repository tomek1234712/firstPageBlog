<?php
/**
* Adds email to newsletter
* @return bool
* @param string $sEmail
* @param bool $bCheckMarkDb
*/
function addEmailToNewsletter( $sEmail, $bCheckMarkDb = true ){
  global $lang, $config;
  $oSql = Sql::getInstance( );
  
  $sEmail = trim( strtolower( $sEmail ) );
  $iCheck = $oSql->getColumn( 'SELECT iStatus FROM newsletter WHERE sEmail = '.$oSql->quote( $sEmail ) );
  if( ( !isset( $bCheckMarkDb ) || $oSql->checkMarkFile( ) ) && empty( $iCheck ) ){
    $iStatus = 1;
    if( isset( $config['newsletter_confirm_email'] ) ){
      throwSiteUrls();
      $iStatus = 0;
      sendEmail( $lang['Newsletter_confirmation_mail_topic'], parseContent( $lang['Newsletter_confirmation_mail_content'], Array( '[LINK_SUBSCRIBE]' => $config['url_domain'].'?p=newsletter&sVerify='.md5( $sEmail ), '[LINK_UNSUBSCRIBE]' => $config['url_domain'].'?p=newsletter&sVerify=2'.md5( $sEmail ) ) ), $sEmail, $config['contact_email'] );
    }

    $oSql->insert( 'newsletter', Array( 'sEmail' => $sEmail, 'iStatus' => $iStatus, 'iTime' => time( ) ), null, true );
  }
} // end function addEmailToNewsletter

/**
* Newsletter confirmation
* @return string
* @param string $sEmailEncrypted
*/
function confirmEmailInNewsletter( $sEmailEncrypted ){
  global $lang;

  $oSql = Sql::getInstance( );
  $iLength = strlen( $sEmailEncrypted );
  if( $iLength == 33 )
    $sEmailEncrypted = substr( $sEmailEncrypted, 1 );
  elseif( $iLength != 32 )
    return null;

  $oSql->sqliteCreateFunction( 'md5', 'md5', 1 );
  $aData = $oSql->throwAll( 'SELECT iStatus, sEmail FROM newsletter WHERE md5( sEmail ) = '.$oSql->quote( $sEmailEncrypted ) );
  if( isset( $aData ) ){
    if( $iLength == 33 ){
      $oSql->query( 'DELETE FROM newsletter WHERE sEmail = '.$oSql->quote( $aData['sEmail'] ) );
      $oSql->addMarkToFile( );
      return '<div class="msg newsletter-msg"><h1>'.$lang['Email_deleted'].'</h1></div>';
    }
    else{
      if( $aData['iStatus'] == 0 ){
        $oSql->query( 'UPDATE newsletter SET iStatus = 1 WHERE sEmail = '.$oSql->quote( $aData['sEmail'] ) );
        $oSql->addMarkToFile( );
      }
      return '<div class="msg newsletter-msg"><h1>'.$lang['Email_added'].'</h1></div>';
    }
  }
} // end function confirmEmailInNewsletter
?>