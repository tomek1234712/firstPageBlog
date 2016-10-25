/* 
Quick.Slider v1.1
License:
  Code in this file (or any part of it) can be used only as part of Quick.Cms.Ext v6.1 or later. All rights reserved by OpenSolution.
*/

(function($){
$.fn.quickslider = function( options ){
  return this.each(function() {
    var aDefault = {
      // You will find possible values for variables in documentation: http://opensolution.org/docs/?p=en-design#menu6
      iPause: 4000,        // pause time between the slides
      iAnimateSpeed: 500,  // animation length
      mSliderHeight: null, // slider's height, accepted values: null (first slide), 'auto' (each separately), 150 (manual height in px))
      sPrevious: '',       // Previous button text
      sNext:     '',       // Next button text
      bAutoPlay: true,     // automatically changing slides
      bPauseOnHover: true, // pause after hover over slide
      bNavArrows: true,    // navigation arrows
      bNavDots: true,      // navigation dots
      sAnimation: 'fade',  // slides scrolling visual effect, accepted values: null, 'fade', 'scroll', 'vertical'
      bKeyboard: false,    // keyboard handling
    };
    
    // podstawowe zmienne
    var oQuickSlider = this,
      oConfig = {},
      iFix = 3,                                 // fix for WIN8 slider wrapper size counting problem
      oSlider = {
        oSliderWrapper: $(this),                // container object containing a slider with id used to call the method .quickslider ()
        oSlides: $(this).children().children(), // object of all elements in the list
        iNextSlide: 0,                          // active/next slide
        iPrevSlide: 0,                          // previous slide
        iTimer: 0,                              // references to timer
        bHoldPause: false,                      // stoping the slider pause
      };

    function checkSliderNotBusy( ){
      return ( $( oSlider.oSlides[oSlider.iPrevSlide] ).is(':animated') ? false : true );
    }

    // function scrolling slider back
    function prev( ){
      if( checkSliderNotBusy() === true ){
        oSlider.iPrevSlide = oSlider.iNextSlide--;
        if(oSlider.iNextSlide < 0) 
          oSlider.iNextSlide = oSlider.oSlides.length - 1;
        show( 0 );
      }
    }
    
    // function scrolling slider forward
    function next( ){
      if( checkSliderNotBusy() === true ){
        oSlider.iPrevSlide = oSlider.iNextSlide++;
        if(oSlider.iNextSlide >= oSlider.oSlides.length)
          oSlider.iNextSlide = 0;
        show( 1 );      
      }
    }

    // function showing active slider
    function show( iDirection, i ){
      // when the function is called from the initialization function
      if( checkSliderNotBusy() === true ){
        if( typeof i !== 'undefined' ){
          oSlider.iPrevSlide = oSlider.iNextSlide;
          oSlider.iNextSlide = i;
          if( oSlider.iNextSlide == oSlider.iPrevSlide )
            return false;
        }

        // depending on the configuration (changing slides style)
        if( oConfig.sAnimation == 'scroll' ){
          scrollHorizontal( iDirection );
        }
        else if( oConfig.sAnimation == 'vertical' ){
          scrollVertical( iDirection );
        }
        else if( oConfig.sAnimation == 'fade' ){
          fading();
        }
        else{
          changeSlide();
        }

        if( oConfig.mSliderHeight == 'auto' ){
          setAutoHeight( );
        }
        if( oConfig.bNavDots ){
          updateNavigation();
        }
        if( oConfig.bAutoPlay == true && oSlider.bHoldPause === false ){
          slideTimer();
        }
      }
    }
    // keyboard handling
    function keyUpHandler( e ){
      if( e.keyCode == 37 ) prev( ); 
      if( e.keyCode == 39 ) next( );
    }

    // function handling slider's timer
    function slideTimer( ){
      if(oConfig.iPause && oConfig.iPause > 0 ){
        clearTimeout(oSlider.iTimer);
        oSlider.iTimer = setTimeout(function(){ next( ); }, oConfig.iPause);
      }
    }
    
    // fading function
    function fading( ){
      oSlider.oSlides.fadeOut( oConfig.iAnimateSpeed );
      $( oSlider.oSlides[oSlider.iNextSlide] ).fadeIn( oConfig.iAnimateSpeed );
    }
    
    // standard slide change function
    function changeSlide( ){
      oSlider.oSlides.hide( );
      $( oSlider.oSlides[oSlider.iNextSlide] ).show( );
    }

    // horizontal scroll function
    function scrollHorizontal( iDirection ){
      var sMinus = ( !iDirection ) ? '-' : '',
          sMinus2 = ( !iDirection ) ? '' : '-';

      // preparing the next slide to show
      $( oSlider.oSlides[oSlider.iNextSlide] ).css( 'left', sMinus+(oSlider.oSliderWrapper.width()+iFix)+'px' );
      // sliding out of the slide from the container and flipping to 'stack'
      $( oSlider.oSlides[oSlider.iPrevSlide] ).animate({ left: sMinus2+oSlider.oSliderWrapper.width()+'px' }, oConfig.iAnimateSpeed, function(){ $(this).css( 'left', sMinus+(oSlider.oSliderWrapper.width()+iFix)+'px' ); } );
      // sliding in of the next slide to the container
      $( oSlider.oSlides[oSlider.iNextSlide] ).animate({ left: "0px" }, oConfig.iAnimateSpeed );
    }

    // vertical scroll function
    function scrollVertical( iDirection ){
      var sMinus = ( !iDirection ) ? '-' : '',
          sMinus2 = ( !iDirection ) ? '' : '-';

      // preparing the next slide to show
      $( oSlider.oSlides[oSlider.iNextSlide] ).css( 'top', sMinus+(oSlider.oSliderWrapper.height()+iFix)+'px' );
      // sliding out of the slide from the container and flipping to 'stack'
      $( oSlider.oSlides[oSlider.iPrevSlide] ).animate({ top: sMinus2+oSlider.oSliderWrapper.height()+'px' }, oConfig.iAnimateSpeed, function(){ $(this).css( 'top', sMinus+(oSlider.oSliderWrapper.height()+iFix)+'px' ); } );
      // sliding in of the next slide to the container
      $( oSlider.oSlides[oSlider.iNextSlide] ).animate({ top: "0px" }, oConfig.iAnimateSpeed );
    }

    // function updating active slide dot in the slides dots list
    function updateNavigation(){
      oSlider.oSliderWrapper.find('.quick-slider-nav-dots').removeClass('active');
      $(oSlider.oSliderWrapper.find('.quick-slider-nav-dots').get(oSlider.iNextSlide)).addClass('active');
    }
    
    function setSliderHeight( ){
      if( oConfig.mSliderHeight === null ){
        oSlider.oSliderWrapper.height( oSlider.oSlides.eq(0).height() );
        oSlider.oSlides.height( oSlider.oSlides.eq(0).height() );
      }
      else if( oConfig.mSliderHeight == 'auto' ){
        setAutoHeight( );
      }
      else if( $.isNumeric( oConfig.mSliderHeight ) ){
        oSlider.oSliderWrapper.height( oConfig.mSliderHeight );
      }
    }

    function setAutoHeight( ){
      oSlider.oSliderWrapper.height( oSlider.oSlides.eq(oSlider.iNextSlide).height() );
    }

    // function calculating slides positions
    function initScrollHorizontalStyles( ){
      var aStyles = { left : (oSlider.oSliderWrapper.width()+iFix)+'px', display : 'block', opacity: '1' };
      oSlider.oSlides.css( aStyles );
      oSlider.oSlides.eq(oSlider.iNextSlide).css( 'left', '0px' );
    }

    // function calculating slides positions
    function initScrollVerticalStyles( ){
      var aStyles = { top : (oSlider.oSliderWrapper.height()+iFix)+'px', display : 'block', opacity: '1' };
      oSlider.oSlides.css( aStyles );
      oSlider.oSlides.eq(oSlider.iNextSlide).css( 'top', '0px' );
    }

    function initNavDots( ){
      var oDots = $( oSlider.oSliderWrapper ).append( '<ol class="quick-slider-nav-dots-wrapper"></ol>' );

      oSlider.oSlides.each(function(i){
        // generating of the unique class for each slide
        $(this).addClass( 'slide'+(i+1) );
        // generowanie listy kontrolek
        var oControlNavigation = $('<li><a href="#" class="quick-slider-nav-dots">'+ (i + 1) +'</a></li>');

        // at the event 'click' function show will be called
        oControlNavigation.on('click', function(e){
          e.preventDefault();
          oSlider.bHoldPause = true;
          show( 1, i );
        });

        // adding the control to html code
        oDots.find('.quick-slider-nav-dots-wrapper' ).append( oControlNavigation );
      });
    }

    function initKeyboard( ){
      oSlider.oSliderWrapper.focusin(function(){
        $(document).keyup( function( e ){
          keyUpHandler( e );
        });
      }).focusout(function(){
        $(document).unbind('keyup');
      });
    }

    function initSwipeGestures( ){
      // REQUIRES JQUERY MOBILE: http://opensolution.org/docs/?p=en-design#mobile
      oSlider.oSliderWrapper.on( "swipeleft", function(){
        next( );
        clearTimeout(oSlider.iTimer);
      } );
      oSlider.oSliderWrapper.on( "swiperight", function(){
        prev();
        clearTimeout(oSlider.iTimer);
      } );
    };

    function initPauseOnHover( ){
      oSlider.oSliderWrapper.hover(
        function(){ clearTimeout(oSlider.iTimer); },
        function(){ oSlider.bHoldPause = false; if( oConfig.bAutoPlay == true ) slideTimer(); }
      );
    }

    function initNextPrevButtons( ){
      var oPreviousButton = $('<a href="#" class="quick-slider-nav-arrows quick-slider-nav-arrows-prev">'+ oConfig.sPrevious +'</a>'),
        oNextButton = $('<a href="#" class="quick-slider-nav-arrows quick-slider-nav-arrows-next">'+ oConfig.sNext +'</a>');
      
      // at the event 'click' function prev will be called
      oPreviousButton.on('click', function(e){
        e.preventDefault();
        oSlider.bHoldPause = true;
        prev( );
      });

      // at the event 'click' function next will be called
      oNextButton.on('click', function(e){
        e.preventDefault();
        oSlider.bHoldPause = true;
        next( );
      });

      // adding buttons to html code
      $( oSlider.oSliderWrapper ).append( oPreviousButton, oNextButton ); 
    }

    // function initializing the slider
    function quickSliderInitialize(){
      oSlider.oSliderWrapper.show( );
      // setting the correct styles for the slides scrolling
      oConfig = $.extend({}, aDefault, options);
      // assigning a class to the slider
      oSlider.oSliderWrapper.addClass('quick-slider');
      // assigning a class to each slide
      oSlider.oSlides.addClass('quick-slider-slide');
      // adjusting slide's height
      setSliderHeight();
      
      if( oSlider.oSlides.length > 1 ){
        if( oConfig.sAnimation == 'scroll' ){
          initScrollHorizontalStyles();
        }
        else if( oConfig.sAnimation == 'vertical' ){
          initScrollVerticalStyles();
        }
        if( oConfig.bNavArrows ){
          initNextPrevButtons( );
        }
        if( oConfig.bNavDots ){
          initNavDots( );
        }
        // when the optional keyboard handling is switched on, events are assigned
        if( oConfig.bKeyboard ){
          initKeyboard( );
        }
        initSwipeGestures( );
        // stop autoPlay on hover event
        if(oConfig.bPauseOnHover && oConfig.iPause && oConfig.iPause > 0){
          initPauseOnHover( )
        }
        // update the active slide in the slides list
        if( oConfig.bNavDots )
          updateNavigation();
        if( oConfig.bAutoPlay == true )
          slideTimer();
        $(window).resize(function(){setSliderHeight();});
      }
        
      return oQuickSlider;
    }

    /* PLUGINS */
    
    $(window).load(function(){return quickSliderInitialize(  );});
  });
};
})(jQuery);