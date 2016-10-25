/* 
License:
  Code in this file (or any part of it) can be used only as part of Quick.Cms.Ext v6.0 or later. All rights reserved by OpenSolution.
*/

var sLastSearchPhrase = '',
    aSelectCache = {},
    aSelectCacheMap = {},
    aSelectCacheAttr = {},
    bHideShortDescription = getCookie( 'bHideShortDescription' ),
    oParent = null,
    bDisplayAll = null;

function customCheckbox(){
  $('.custom input[type=checkbox] ~ label').each( function(){
    $( this ).siblings( '.label' ).text( $( this ).text() );
    $( this ).siblings( '.label' ).click( {oObj:this}, function(e){ $( e.data.oObj ).trigger( 'click' ); } );
  });
}

function displayTab( oObj, bInit ){
  var sBlock = getCookie( 'sSelectedTab' );
  if( bInit && window.location.hash.replace( '#', '' ) != '' ){
    sBlock = window.location.hash.replace( '#', '' );
    if( sBlock.indexOf( 'link-' ) == 0 )
      sBlock = sBlock.replace( 'link-', '' );
  }
  else if( bInit && sBlock && $( '#body > h2.msg:not(.error)' ).length > 0 ){
    if( sBlock == 'add-files' )
      sBlock = 'files';
    delCookie( 'sSelectedTab' );
  }
  else
    sBlock = $( oObj ).attr( 'id' );
  if( $('#'+sBlock).length > 0 ){
    $( '.tabs li' ).removeClass( 'selected' );
    $( '.tabs li#'+sBlock ).addClass( 'selected' );
    $( '.forms' ).hide();
    $( '#tab-'+sBlock ).show();
    createCookie( 'sSelectedTab', sBlock, 2 );
    if( typeof bInit === 'undefined' )
      window.location.hash = 'link-'+sBlock;
    return sBlock;
  }
}

function displayTabInit( sCallback ){
  $( '.tabs li' ).click( { 'sCallback': sCallback }, function( e ){ e.preventDefault(); displayTab( this ); if( typeof e.data.sCallback === 'function' ) e.data.sCallback( this ); } );
  return displayTab( $( '#'+$( '.tabs li' ).first().attr( 'id' ) ), true );
}

function focusCursor( aFields ){
  var bFound = false;
  $.each( aFields, function( iIndex, sValue ){
    if( $( "[name='"+sValue+"']" ).val() == "" ){
      $( "[name='"+sValue+"']" ).focus( );
      bFound = true;
      return false;
    }
  });
  if( bFound === false )
    $( "[name='"+aFields[aFields.length-1]+"']" ).focus( );
}

function allPagesInSelect( oLink, sElementId, iId, sUrl ){
  if( typeof sUrl === 'undefined' )
    var sUrl = 'pages-all';
  $( oLink ).append( '<span class="loading"><img src="templates/admin/img/loading-horizontal.gif" alt="Loading..." /></span>' );
  $( '#'+sElementId ).load( aQuick['sPhpSelf']+'?p=ajax-select-'+sUrl+'&iId='+iId, function(){
    $( oLink ).remove();
    if( sElementId == 'iPageParent' )
      cacheSelect( 'iPageParent', 'pageParent2', 'pageParent2Ctn' );
  });
}

function allServerFiles( ){
  $( '#files-dir .files-dir-body' ).html( '<div class="loading"><img src="templates/admin/img/loading-big.gif" alt="Loading..." /></div>' );
  $.ajax({url: aQuick['sPhpSelf']+'?p=ajax-files-all',
    dataType: 'html'
  })
  .done(function(sResult){
    $('#files-dir').html( sResult );
    bDisplayAll = true;
    filesFromServerEvents( );
  });
}

function filesFromServerEvents( ){
  $( '#files-dir-table td.select input[type=checkbox]' ).click( function(){displayFilesOptions( this );} );
  $( '#files-dir-table td.file' ).hover( displayThumbPreview, clearThumbPreview );
}


function refreshFiles(){
  $( '#files-dir' ).html( '<div class="loading"><img src="templates/admin/img/loading.gif" alt="Loading..." /></div>' );
  $( '#files-dir' ).load( aQuick['sPhpSelf']+'?p=ajax-files-in-dir', function(){
    $( '#files-dir-table input:checked' ).each( function(){
      displayFilesOptions( this );
    } );
    filesFromServerEvents( );
  });
  $( '#attachingFilesInfo' ).show();
}

function displayFilesOptions( oObj ){
  var iFile = $( oObj ).attr( 'data-i' ),
      iSize = $( oObj ).attr( 'data-img' );
  $( '.files-dir-head th' ).removeClass( 'hidden' );
  $( '#fileTr'+iFile+' .position' ).html( '<input type="text" name="aDirFilesPositions['+iFile+']" value="0" maxlength="4" class="numeric" />' );
  $( '#fileTr'+iFile+' .description' ).html( '<input type="text" name="aDirFilesDescriptions['+iFile+']" class="input" />' );
  if( iSize > 0 ){
    $( '#fileTr'+iFile+' .location' ).html( '<select name="aDirFilesTypes['+iFile+']">'+sTypesSelect+'</select>' );
    $( '#fileTr'+iFile+' .thumb' ).html( '<select name="aDirFilesSizes['+iFile+']">'+sSizeSelect+'</select>' );
    $( '#fileTr'+iFile+' .crop' ).html( '<select name="aDirFilesCrop['+iFile+']">'+sCropSelect+'</select>' );
  }
}

function displayThumbPreview(){
  var iSize = $( this ).closest( 'tr' ).find( 'td > input[type=checkbox]' ).attr( 'data-img' );
  if( iSize > 0 ){
    $( this ).append( '<span class="thumb-preview"><img src="templates/admin/img/loading-horizontal.gif" class="loading" alt="Loading..." /></span>' );
    oTempEl = $( this ).find( 'span' );
    oTempEl.css( 'left', $( this ).find( 'a' ).outerWidth()+10 );
    oLoad = $.ajax({
      url: aQuick['sPhpSelf']+'?p=ajax-files-thumb'+(bDisplayAll===true?'&bDisplayAll=1':'')+'&iSize='+iSize+'&sFileName='+$( this, 'a' ).text(),
      success: function( sResult ){
        oTempEl.html( sResult );
      }
    });
  }
}

function clearThumbPreview(){
  if( typeof oTempEl !== 'undefined' || typeof oLoad !== 'undefined' ){
    oTempEl.remove( );
    oLoad.abort();
  }
}

function checkType( ){
  if( $( '#iMenu' ).length > 0 ){
    if( $.isNumeric( $( '#iPageParent' ).val() ) ){
      $( '#iMenu' ).closest( 'li' ).hide();
    }
    else{
      $( '#iMenu' ).closest( 'li' ).show();
    }
  }
}

function listSearch( oPhrase, sId, bInputs ){
  if( sLastSearchPhrase != $( oPhrase ).val() ){
    sLastSearchPhrase = $( oPhrase ).val();
    var aPhrases = $( oPhrase ).val().toLowerCase().split(" "),
        iPhrases = aPhrases.length,
        oContainer = ( $( '#'+sId +" tbody").length ) ? $( '#'+sId+' tbody' ) : $( '#'+sId ),
        oRows = $( oContainer ).children(':not(.no-search)'),
        iRows = oRows.length,
        oRow = null,
        sDisplay = null;
    if( typeof bInputs === 'undefined' )
      bInputs = false;
    for( var i = 0; i < iRows; i++ ){
      sDisplay = '';
      oRow = oRows.eq(i);
      var sText = oRow.children().not(":has(select), .no-search, .options").text().toLowerCase();
      for( var j = 0; j < iPhrases; j++ ){
        if( sText.indexOf( aPhrases[j] ) < 0 && ( !bInputs || oRow.children( 'input' ).val().toLowerCase().indexOf( aPhrases[j] ) < 0 ) ){
          sDisplay = 'none';
        }
      }
      oRows.eq(i).css( 'display', sDisplay );
    }
  }
}

function changeSearchAttr( oObj ){
  $( '.search input' ).attr( 'onkeyup', " listSearch( this, 'tab-"+ $( oObj ).attr( 'id' ) +"', true )" );
  $( '#tab-'+$( oObj ).attr( 'id' ) ).children().show();
  $( '.search input' ).val( '' );
}

function cacheSelect( sId, sClone, sCloneCnt ){
  var oSelect = gEBI( sId ),
      iLength = oSelect.options.length;
  aSelectCache[sId] = [];
  aSelectCacheMap[sId] = [];
  aSelectCacheAttr[sId] = {};
  for( var i = 0; i < iLength; i++ ){
    aSelectCache[sId][oSelect.options[i].value] = oSelect.options[i].innerHTML;
    aSelectCacheMap[sId][i] = oSelect.options[i].value;
  } // end for
  aSelectCacheAttr[sId]['name'] = oSelect.name;
  aSelectCacheAttr[sId]['size'] = oSelect.size;

  gEBI( sCloneCnt ).innerHTML = gEBI( sId ).parentNode.innerHTML;
  gEBI( sCloneCnt ).children[0].id = sClone;
  gEBI( sClone ).name = null;
  gEBI( sClone ).title = null;
}

function listOptionsSearch( sSearch, sSelectId, sClone ){
  oParent = gEBI( sSelectId ).parentNode;
  oParent.innerHTML = gEBI( sClone ).parentNode.innerHTML;
  oParent.children[0].id = sSelectId;
  oParent.children[0].size = aSelectCacheAttr[sSelectId]['size'];
  oParent.children[0].name = aSelectCacheAttr[sSelectId]['name'];
	var aPhrases = sSearch.value.toLowerCase().split(" "),
      aSelect = aSelectCache[sSelectId],
      aHide = [],
      iId = null,
      oObj = gEBI( sSelectId ),
      oClone = gEBI( sClone );
	for( iId in aSelect ){
		aHide[iId] = false;
		for( var j = 0; j < aPhrases.length; j++ ){
			if( aSelect[iId].replace( /^(&nbsp;)+/g, '' ).toLowerCase().indexOf( aPhrases[j] ) < 0 ){
    		aHide[iId] = true;
        break;
      }
		}
	} // end for
	for( var i = aSelectCacheMap[sSelectId].length-1; i >= 0; i-- ){
    iId = aSelectCacheMap[sSelectId][i];
    if( aHide[iId] && aHide[iId] === true && oClone.options[i].selected != true ){
      oObj.remove( i );
    }
	} // end for
  cloneClick( oClone, sSelectId );
}

function cloneClick( oObj, iIdClone ){
  var aSelected = [];
  for( var i = 0; i < oObj.options.length; i++ ){
    if( oObj.options[i].selected == true )
      aSelected[oObj.options[i].value] = true;
  } // end for
  var oClone = gEBI( iIdClone );
  for( var i = 0; i < oClone.options.length; i++ ){
    if( aSelected[oClone.options[i].value] )
      oClone.options[i].selected = true;
    else
      oClone.options[i].selected = false;
  } // end for
}

function gEBI( objId ){
  return document.getElementById( objId );
}

function throwMessages( sMessages ){
  $( '#messages > li > section' ).hide();
  if( $( '#messages .'+sMessages+' .loading' ).length > 0 ){
    $( '#messages .'+sMessages+' .loading' ).append( '<img src="templates/admin/img/loading-horizontal.gif" alt="Loading..." />' );
    $( '#messages .'+sMessages+' header' ).append( '<a href="#" class="close">x</a>' );
    $( '#messages .'+sMessages+' section > div' ).load( aQuick['sPhpSelf']+'?p=ajax-messages-'+sMessages );
  }
  $( '#messages .'+sMessages+' section').show();
  $( 'body' ).append( '<div id="closeLayer"></div>' );
  $( '#closeLayer').show();
  $( '#closeLayer').css( 'height', $( document ).height() );
  $( '#messages .close, #closeLayer' ).click(function(){
    $( '#messages > li > section' ).hide();
    $( '#closeLayer').hide();
  });
}

function clearMessages( sMessages ){
  if( sMessages == 'news' ){
    createCookie( 'iMessagesNewsTime', parseInt( $.now()/1000 ), 365 );
    createCookie( 'bMessagesNewsClear', 1 );
    $( '#messages .'+sMessages+' li' ).removeClass('unread');
  }
  else if( sMessages == 'notices' )
    $.get( aQuick['sPhpSelf']+'?p=ajax-messages-notices-clear' );
  $( '#messages .'+sMessages+' > a strong' ).html('0');
  $( '#messages .'+sMessages+' footer' ).remove();
}

function checkChangedFile( ){
  if( $( '#iChangedFiles' ).length > 0 ){
    $( '#iChangedFiles' ).val( 0 );
    $(function(){ $( '#tab-files input:not([name*="aFilesDelete"]), #tab-files select' ).change( function(){ $( '#iChangedFiles' ).val( 1 ); } ) } );
  }
}

function displayFullPluginDescription( iPlugin ){
  $( '#d'+iPlugin ).hide();
  $( '#df'+iPlugin ).show();
}

function changeLoginData( sField ){
  var sEl = '#tab-loging li.old';
  if( $( '#tab-loging li.new #sLoginEmailNew' ).val() != '' || $( '#tab-loging li.new #sLoginPassNew' ).val() != '' ){
    $( sEl ).slideDown();
    $( '#tab-loging li.old input' ).attr( 'data-form-check', 'required' );
  }
  else{
    $( sEl ).slideUp();
    $( sEl+' input' ).val('');
    $( sEl+' input' ).removeAttr( 'data-form-check' );
  }
}

function checkLoginChange( oForm ){
  var sEl = '#tab-loging li.old';
  $( sEl+' span.check' ).remove();
  if( typeof aLoginAjax['login'] !== 'undefined' )
    aLoginAjax['login'].abort();
  var bFormCorrect = false;

  if( $( sEl+' #sLoginPassOld' ).val() != '' && $( sEl+' #sLoginEmailOld' ).val() != '' ){
    $( sEl ).append( '<span class="check"><span class="loading"><img src="templates/admin/img/loading-horizontal.gif" alt="Loading..." /></span></span>' );
    $( '.buttons li.save input' ).css({ 'background-image': 'url("./templates/admin/img/loading-horizontal.gif")', 'background-position': '10px center' });

    aLoginAjax['login'] = $.ajax({
      url: aQuick['sPhpSelf']+'?sVerifypass='+$( sEl+' #sLoginPassOld' ).val()+'&sVerifyemail='+$( sEl+' #sLoginEmailOld' ).val()+'&p=ajax-verify-login',
      async: false
    }).done(function( sResult ){
      $( sEl+' span.check' ).html( ( sResult == 'true' ? '<img src="templates/admin/img/ok.png" alt="Ok" />' : aQuick['sIncorrectData'] ) );
      $( '.buttons li.save input' ).css({ 'background-image': 'url("./templates/admin/img/save.png")', 'background-position': 'none' });
      bFormCorrect = ( sResult == 'true' ) ? true : false;
    });
    return bFormCorrect;
  }
  else
    return true;
}

function displayMore( oObj, sElementToDisplay, sCookie, bShowOnload ){
  if( typeof bShowOnload === 'undefined' ){
    if( $( sElementToDisplay+':visible' ).length > 0 ){
      $( sElementToDisplay ).hide();
      if( $( sElementToDisplay+'-toogle' ).length > 0 )
        $( sElementToDisplay+'-toogle' ).show();
      $( oObj ).find( '.display' ).show();
      $( oObj ).find( '.hide' ).hide();
      $( oObj ).removeClass( 'minus' );
      delCookie( sCookie );
    }
    else{
      $( sElementToDisplay ).show();
      if( $( sElementToDisplay+'-toogle' ).length > 0 )
        $( sElementToDisplay+'-toogle' ).hide();
      $( oObj ).find( '.display' ).hide();
      $( oObj ).find( '.hide' ).show();
      $( oObj ).addClass( 'minus' );
      if( typeof sCookie === 'string' && sCookie != '' ){
        if( sCookie.indexOf( 'bTemp' ) === 0 )
          createCookie( sCookie, 1 );
        else
          createCookie( sCookie, 1, 180 );
      }
    }
  }
  else if( ( typeof bShowOnload !== 'undefined' && bShowOnload === true ) || getCookie( sCookie ) == 1 ){
    $( sElementToDisplay ).show();
    if( $( sElementToDisplay+'-toogle' ).length > 0 )
      $( sElementToDisplay+'-toogle' ).hide();
    $( oObj ).find( '.display' ).hide();
    $( oObj ).find( '.hide' ).show();
    $( oObj ).addClass( 'minus' );
  }
}

function checkThumbAllSizesSelect( oObj ){
  var oTr = $( oObj ).closest( 'tr' ),
      iId = oTr.find( 'input[name="iDefaultImage"]' ).val(),
      oDefaultSize = oTr.find( 'select[name="aFilesSizes['+iId+']"]' ),
      iDetailsSize = oTr.find( 'select[name="aFilesSizesDetails['+iId+']"]' ).val(),
      iListsSize = oTr.find( 'select[name="aFilesSizesLists['+iId+']"]' ).val(),
      iOtherSize = oTr.find( 'select[name="aFilesSizesOther['+iId+']"]' ).val();
  deleteEmptyOption( oDefaultSize );
  if( iDetailsSize == iListsSize && iListsSize == iOtherSize ){
    oDefaultSize.find( 'option[value="'+iDetailsSize+'"]' ).prop( 'selected', true );
  }
  else{
    $( oDefaultSize ).append( $('<option></option>').val('').html(aQuick['sCustom']).prop( 'selected', true ).prop( 'readonly', true ) );
  }
}

function setThumbAllSizes( oObj ){
  var oTr = $( oObj ).closest( 'tr' ),
      iId = oTr.find( 'input[name="iDefaultImage"]' ).val();
  if( oObj.value != '' )
    deleteEmptyOption( oObj );
  iDetailsSize = oTr.find( 'select[name="aFilesSizesDetails['+iId+']"]' ).val(oObj.value);
  iListsSize = oTr.find( 'select[name="aFilesSizesLists['+iId+']"]' ).val(oObj.value);
  iOtherSize = oTr.find( 'select[name="aFilesSizesOther['+iId+']"]' ).val(oObj.value);
}

function deleteEmptyOption( oObj ){
  $( oObj ).find( 'option' ).filter(function(){ return !this.value || $.trim(this.value).length == 0; }).remove();
}

function checkSliderFields( ){
  var sDescription = ( typeof tinyMCE !== 'undefined' ) ? tinyMCE.get( 'sDescription' ).getContent() : $( '#sDescription' ).val();
  if( sDescription == '' && $( '#sFileName' ).length && $( '#sFileName' ).val() == '' ){
    $( '#tab-content .help' ).addClass( 'required' );
    alert( aCF['sWarning'] );
    return false;
  }
  else
    $( '#tab-content .help' ).removeClass( 'required' );
}

function checkParentForm( ){
  if( $( '#iPageParent' ).val() != '' && $( '#iPageParent' ).val() == $( '#iPage' ).val() ){
    displayTab( '#options' );
    alert( $( '#tab-options .parent label' ).text()+' - '+aCF['sInt'] );
    $( '#iPageParent' ).focus( );
    return false;
  }
}

function pluginsInstall( ){
  var sLoading = '<img src="templates/admin/img/loading-horizontal.gif" alt="Loading..." />',
      sPanel = '#body.plugins .install-panel',
      sPlugins = '';
  if( $( 'table.plugins td.install input:checked:not([disabled])' ).length > 0 ){
    aPlugins = [];
    if( $( sPanel ).length == 0 )
      $( '#body.plugins' ).append( '<div class="install-panel"></div>' );
    $( sPanel ).html( sLoading );
    $( 'table.plugins td.install input:checked:not([disabled])' ).each( function(){
      sPlugins += '<li class="'+$( this ).val()+'"><strong>'+$( this ).val()+'</strong><span class="progress">'+sLoading+'</span></li>';
      aPlugins.push( $( this ).val() );
    } );
    $( sPanel ).html( '<ol>'+sPlugins+'</ol><div class="refresh"><a href="'+location.search+'">'+aQuick['sRefreshPanel']+'</a></div>' );
    $( '#body.plugins a.triger' ).trigger( "click" );

    pluginsInstallRequest( 0 );
  }
  else{
    alert( aQuick['sSelectPlugins'] );
  }
}
function pluginsInstallRequest( iI ){
  if( typeof aPlugins[iI] !== 'undefined' ){
    oLoad = $.ajax({
      url: aQuick['sPhpSelf']+'?p=ajax-plugin-install&sPlugin='+aPlugins[iI],
      complete: function( o, sStatus ){
        if( o.responseText == 'success' ){
          $( '.install-panel .'+aPlugins[iI]+' .progress' ).html( aQuick['sOperationCompleted'] );
          $( '.install-panel .'+aPlugins[iI] ).addClass( 'complete' );
        }
        else{
          $( '.install-panel .'+aPlugins[iI]+' .progress' ).html( '<span>'+aQuick['sInstallationError']+'</span><a href="" onclick="return displayNotice(this)" class="more">'+aQuick['sMore']+' &raquo;</a>' );
          $( '.install-panel .'+aPlugins[iI] ).addClass( 'error' );
          $( '.install-panel .'+aPlugins[iI] ).append( '<div class="notice">'+o.responseText+'</div>' );
        }
        $( '#quick-box .quick-box-container' ).css( 'top', "+=1" ); $( '#quick-box .quick-box-container' ).css( 'top', "-=1" ); //fix for Opera 12
        pluginsInstallRequest( iI+1 );
      }
    });
  }
  else{
    $( '#quick-box .install-panel .refresh' ).fadeTo( 1, 100 );
  }
}
function displayNotice( oObj ){
  $( oObj ).hide();
  $( oObj ).closest( 'li' ).find( '.notice' ).show();
  changeContentPosition();
  return false;
}
function displayContentTypeOptions( oObj, sClassName, bInit ){
  var oForm = $( oObj ).closest( 'form' );
  $( oForm ).find( 'li[class*="'+sClassName+'"]' ).hide();
  if( typeof bInit === 'undefined' )
    $( oForm ).find( 'li[class*="'+sClassName+oObj.value+'-options"]' ).fadeIn( );
  else
    $( oForm ).find( 'li[class*="'+sClassName+oObj.value+'-options"]' ).show( );
}

function newPopupWindowOpen( ){
  $( '.open-popup' ).click(function(e){
    e.preventDefault();
    if( typeof $(this).data('popup-width') === 'undefined' ){ $(this).data('popup-width', 900 ); }
    if( typeof $(this).data('popup-height') === 'undefined' ){ $(this).data('popup-height', 550 ); }
    if( typeof $(this).data('popup-top') === 'undefined' ){ $(this).data('popup-top', 50 ); }
    if( typeof $(this).data('popup-left') === 'undefined' ){ $(this).data('popup-left', 100 ); }
    window.open( $(this).attr('href'), '', 'width='+$(this).data('popup-width')+',height='+$(this).data('popup-height')+',top='+$(this).data('popup-top')+',left='+$(this).data('popup-left')+'' );
  });
}
function addCloseToPopup( ){
  if( window.opener !== null ){
    $( '#foot nav ul' ).append( '<li class="close"><a href="#" onclick="window.close()">'+aQuick['sClose']+'</a></li>' );
    if( $( '#body h2.msg' ).length > 0 ){ $( '#foot .close' ).css( 'font-weight', 'bold' ); };
  }
}
function addCloseToMsg( ){
  if( $( '#body h2.msg' ).length > 0 ){
    $( '#body h2.msg' ).append( '<a href="#" class="close" aria-label="close">'+aQuick['sClose']+'</a>' );
    $( '#body h2.msg .close' ).click( function(){
      $( this ).closest( 'h2' ).slideUp( 'normal', function(){ $(this).remove(); } );
    } );
  }
}

// ONLOAD
$(function(){
  newPopupWindowOpen( );
  addCloseToPopup( );
  addCloseToMsg( );
});

/* PLUGINS */
