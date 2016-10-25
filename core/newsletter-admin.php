<?php
/**
* List newsletter
* @return string
*/
function listNewsletterEmails( ){
  global $lang, $config;

  $oSql = Sql::getInstance( );
  if( isset( $_GET['sSort'] ) ){
    if( $_GET['sSort'] == 'email' )
      $sSort = 'sEmail ASC';
    elseif( $_GET['sSort'] == 'status' )
      $sSort = 'iStatus ASC, iTime DESC';
  }
  if( !isset( $sSort ) )
    $sSort = 'iTime DESC';

  $oQuery = $oSql->getQuery( 'SELECT * FROM newsletter ORDER BY '.$sSort );
  $i = 0;
  $content = null;
  while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
    $content .= '<tr class="l'.( ( ++$i % 2 ) ? 0: 1 ).'"><td><a href="mailto:'.$aData['sEmail'].'">'.$aData['sEmail'].'</a><td>'.getYesNoTxt( $aData['iStatus'] ).'</td><td>'.displayDate( $aData['iTime'], $config['date_format_admin_default'] ).'</td><td class="options"><a href="?p=newsletter&amp;sItemDelete='.$aData['sEmail'].'" onclick="return del( )" class="delete">'.$lang['Delete'].'</a></td></tr>';
  } // end while

  return $content;
} // end function listNewsletterEmails

/**
* Gets list of emails separated by "\n"
* @return string
*/
function listNewsletterEmailsTxt( ){
  $oSql = Sql::getInstance( );
  $oQuery = $oSql->getQuery( 'SELECT sEmail FROM newsletter WHERE iStatus = 1 ORDER BY sEmail ASC' );
  while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
    $aEmails[] = $aData['sEmail'];
  } // end while

  if( isset( $aEmails ) ){
    return implode( "\n", $aEmails );
  }
} // end function listNewsletterEmailsTxt

/**
* Delete email from newsletter database
* @return void
* @param string $sEmail
*/
function deleteNewsletterEmail( $sEmail ){
  $oSql = Sql::getInstance( );
  $oSql->query( 'DELETE FROM newsletter WHERE sEmail = '.$oSql->quote( $sEmail ) );
} // end function deleteNewsletterEmail
?>