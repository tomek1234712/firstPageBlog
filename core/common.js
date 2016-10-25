/* 
License:
  Code in this file (or any part of it) can be used only as part of Quick.Cms.Ext v6.0 or later. All rights reserved by OpenSolution.
*/

function checkAll( sClass, bChecked ){
  $( 'input:checkbox.'+sClass ).prop( 'checked', bChecked );
}

function bindCheckAll( sSelector, sClass ){
  $( sSelector ).click( function(){ checkAll( ( typeof sClass === 'undefined' ? 'status' : sClass ), $( this ).prop( 'checked' ) ) } );
}

function createCookie( sName, sValue, iDays ){
  sValue = encodeURIComponent( sValue );
  if( iDays ){
    var oDate = new Date();
    oDate.setTime( oDate.getTime() + ( iDays*24*60*60*1000 ) );
    var sExpires = "; expires="+oDate.toGMTString();
  }
  else
    var sExpires = "";
  document.cookie = sName+"="+sValue+sExpires+"; path=/";
}

function getCookie( sName ){
  var sNameEQ = sName + "=";
  var aCookies = document.cookie.split( ';' );
  for( var i=0; i < aCookies.length; i++ ){
    var c = aCookies[i];
    while( c.charAt(0) == ' ' )
      c = c.substring( 1, c.length );
    if( c.indexOf( sNameEQ ) == 0 )
      return decodeURIComponent( c.substring( sNameEQ.length, c.length ) );
  } // end for
  return null;
}

function delCookie( sName ){
  createCookie( sName, "", -1 );
}

function del( mInfo ){
  if( typeof mInfo === 'object' ){
    var mInfo = $( mInfo ).closest( 'tr' ).find( 'th.name a:first-child' ).length > 0 ? ' "'+$( mInfo ).closest( 'tr' ).find( 'th.name a:first-child' ).text()+'"' : ' "'+$( mInfo ).closest( 'tr' ).find( 'th.name' ).text()+'"';
  }
  else if( typeof mInfo === 'string' ){}
  else
    mInfo = '';
  if( confirm( (typeof aQuick === 'undefined' ? '' : aQuick['sDelShure'])+mInfo+' ?' ) ) 
    return true;
  else 
    return false
}

/* PLUGINS */

function displayNotice( iId, iLocation, bOnce ){
  var iNoticeClosed = getCookie( 'iNoticeClosed-'+iId );
  if( typeof iNoticeClosed === 'undefined' || iNoticeClosed != 1 ){
    if( iLocation == 3 ){
      $('body').append( '<div class="widget-dark-background">&nbsp;</div>' );
      $('.widget-dark-background').delay(400).fadeIn('fast');
      $( '.widget.id-'+iId ).delay(500).fadeIn();
    }
    else
      $( '.widget.id-'+iId ).delay(500).slideDown();
    $( '.widget.id-'+iId+' .close' ).click( function(){
      $( '.widget.id-'+iId ).slideUp('fast', function(){ $('.widget-dark-background').fadeOut('fast',function(){this.remove();}); } )
      createCookie( 'iNoticeClosed-'+iId, 1 );
      return false;
    });
    if( bOnce )
      createCookie( 'iNoticeClosed-'+iId, 1 );
  }
}

function backToTopInit(){
  var fDisplayLink = function(){
    if( $(this).scrollTop() > 100 )
      $('.back-to-top-widget').fadeIn('slow');
    else
      $('.back-to-top-widget').stop(true).fadeOut();
  };
  $('.back-to-top-widget').hide();
  $(window).scroll( fDisplayLink );
  $('.back-to-top-widget a').click( function(){
    $('body,html').animate( {scrollTop:0}, 600 );
    return false;
  } );
  fDisplayLink();
}
