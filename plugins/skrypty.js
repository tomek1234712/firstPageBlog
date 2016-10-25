$(document).ready(function() {

//wyrownaj wysokosc artykolow
// $('#kolumna_srodek .pages-list li').matchHeight({
//     byRow: true

// });

//preloader slidera

		$('#slider-1 li').append('<div class="preloader"><img src="templates/default/img/spinner.gif"></div>');


		$('#slider-1').imagesLoaded( function() {

		$("<style>").text("#slider-1 img { opacity:1; }").appendTo("head");
		$('.preloader').css('opacity', '0');

		});

//animacja menu

$('.menu-2 li').mouseenter(function(){

			$('img', this).clearQueue();

			$('img', this).animate({ 
			        bottom: "-100px",
			      }, {
			    queue: false,
			    duration: 200,
			    complete: function() {
			$(this).css('bottom', '100px');
			$(this).animate({ 
			        bottom: "0px",
			      }, 200 );


			    }
			});


});

//usuwaj szare bordery z ostatnich elementow


var ilosc_w_rzedzie = 4;

var reszta = $('.pages-list li').length % ilosc_w_rzedzie;

if(reszta == 0){
for (i = ilosc_w_rzedzie; i > 0; i--) {
	 $('.pages-list li:nth-last-child('+i+')').css('border', 'none');

}

}else{
for (i = reszta; i > 0; i--) {
	 $('.pages-list li:nth-last-child('+i+')').css('border', 'none');

}
}




//wysun szukajke i wsun szukajke po klikniecu na guzik
var otwarto = false;

$('#searchicon').click(function(){


if(!otwarto){
$('#wysuwana_szukajka').animate({ 
			        top: "0px",
			      }, 200 );

$('#searchicon').addClass('szukajhover');
}else{
$('#wysuwana_szukajka').animate({ 
			        top: "-100px",
			      }, 200 );  

$('#searchicon').removeClass('szukajhover');

}
otwarto = !otwarto;
});



//zamknij szukajkę po klikniecu poza

$(document).on('click', function(event) {

// if(otwarto == 1){

  if ((!$(event.target).parents('#wysuwana_szukajka').length && $(event.target).attr('id') != 'wysuwana_szukajka' && $(event.target).attr('id') != 'searchicon')) {
$('#wysuwana_szukajka').animate({ 
			        top: "-100px",
			      }, 200 );  

$('#searchicon').removeClass('szukajhover');
// otwarto = 0;

// }
}

});


//blokuj duże zdjęcie
// $('.images-3 a, .images-4 a').click(function(event){

// event.preventDefault();
// $(this).unbind();
// });

// ajax form


function preload(arrayOfImages) {
    $(arrayOfImages).each(function(){
        $('<img/>')[0].src = this;

    });
}

// Usage:

preload([
    'templates/default/img/news_spinner.GIF',
    'templates/default/img/ok.png',
    'templates/default/img/error.png',
]);


//likebox

       
$('#fb-likebox').click(function(){


$('#fb-likebox').toggleClass('rozwin');

});



});