<?php
/*
* Główne dane konfiguracyjne strony, niezależne od języka
* Więcej: http://opensolution.org/docs/?p=pl-settings
*/
unset( $config, $lang, $aData );

/*
* Login w postaci emaila i hasło do zalogowania się do panelu administracyjnego
* Dbaj o ich bezpieczeństwo. Nie ustawiaj hasła na "admin", "1234", "qwerty" itp.
* Więcej: http://opensolution.org/docs/?p=pl-settings#login_email
*/
$config['login_email'] = "szczepaniak@czyzkowski.net";
$config['login_pass'] = "szczepaniakhaslo"; // jeśli ta zmienna i także $config['developer_login_email'] są zakomentowane, to zablokowana zostanie możliwość uruchamiania panelu admina

/*
* Login w postaci emaila i hasło dla webmastera zaszyfrowane w md5. Wygeneruj login i hasło,
* wstaw wartości do poniższych zmiennych i odkomentuj linie.
* Dla większego bezpieczeństwa możesz także podać swój zaszyfrowany adres IP,
* aby tryb deweloperski był dostępny tylko dla danego IP
* Więcej: http://opensolution.org/docs/?p=pl-settings#developer_login_email
*/
//$config['developer_login_email'] = ''; // wymagana zmienna, aby dać możliwość logowania dla webmastera
//$config['developer_login_pass'] = ''; // wymagana zmienna, aby dać możliwość logowania dla webmastera
//$config['developer_login_ip'] = ''; // niewymagana zmienna, ale zalecana do zastosowania

/*
* Jeśli strona jest w trakcie tworzenia, warto pozostawić włączoną opcję DEVELOPER_MODE
* Następnie przy zdefiniowaniu zmiennych $config['developer_login_email'] i $config['developer_login_pass']
* webmaster będzie mógł uzyskać dostęp do panelu administracyjnego z wszystkimi możliwymi opcjami
* Więcej: http://opensolution.org/docs/?p=pl-settings#DEVELOPER_MODE
*/
define( 'DEVELOPER_MODE', true ); // po uruchomieniu strony zakomentuj tą linię
if( !defined( 'DEVELOPER_MODE' ) && isset( $config['developer_login_email'] ) && isset( $config['developer_login_pass'] ) && isset( $_SESSION[$config['developer_login_email']] ) && isset( $_SESSION[$_SESSION[$config['developer_login_email']]] ) && $_SESSION[$_SESSION[$config['developer_login_email']]] == -1 ){
  define( 'DEVELOPER_MODE', true );
}
if( defined( 'DEVELOPER_MODE' ) ){
  error_reporting( E_ALL | E_STRICT );
}

/*
* Email kontaktowy. Domyślnie taki sam jak adres email do logowania
* Więcej: http://opensolution.org/docs/?p=pl-settings#contact_email
*/
$config['contact_email'] = $config['login_email']; // domyślna wartość: $config['login_email']

/*
* Ustawienie adresu lub adresów IP do logowania do administracji
* Więcej: http://opensolution.org/docs/?p=pl-settings#allowed_ips_admin_panel
*/
$config['allowed_ips_admin_panel'] = null; // domyślna wartość: null

/*
* Zmienna przechowuje nazwę katalogu skórki
* Więcej: http://opensolution.org/docs/?p=pl-settings#skin
*/
$config['skin'] = 'default'; // domyślna wartość: 'default'

/*
* Rozmiary miniaturek i lokalizacje zdjęć. Dodając nową lokalizację, nadaj jej cyfrę nie mniejszą niż 50
* Więcej: http://opensolution.org/docs/?p=pl-settings#images_thumbnails
*/
$config['images_thumbnails'] = Array( 100, 200,1160,400 ); // domyślna wartość: Array( 100, 200 )
$config['images_locations'] = Array( 1 => 'Lewa strona', 2 => 'Prawa strona', 3 => 'Nad tekstem', 4 => 'Pod tekstem', 0 => 'Tylko lista', 7 => 'Galleria z podglądem' ); // domyślna wartość: Array( 1 => 'Lewa strona', 2 => 'Prawa strona', 3 => 'Galeria 1', 4 => 'Galeria 2', 0 => 'Tylko lista' )

/*
* Rodzaje menu przedstawione w postaci tablicy. Klucz 0 w zmiennej jest zarezerwowany dla ukrytego menu!
* Więcej: http://opensolution.org/docs/?p=pl-settings#pages_menus
*/
$config['pages_menus'] = Array( 1 => 'Menu górne', 0 => 'Ukryte' ); // domyślna wartość: Array( 1 => 'Menu górne', 0 => 'Ukryte' )

/*
* Lokalizacje komunikatów
*/
$config['widgets_notices_types'] = Array( 0 => 'Lewy dolny róg', 1 => 'Dół', 2 => 'Góra', 3 => 'W warstwie nad stroną' );

/*
* Rodzaje list wyświetlanych podstron. Dodając nowy rodzaj list, nadaj mu cyfrę nie mniejszą niż 50
* Więcej: http://opensolution.org/docs/?p=pl-settings#subpages_list_types
*/
$config['subpages_list_types'] = Array( 1 => 'Lista tylko z nazwą', 2 => 'Lista bez zdjęcia', 3 => 'Lista ze zdjęciem', 4 => 'Newsy', 5 => 'Galeria', 0 => 'Brak wyświetlania' ); // domyślna wartość: Array( 1 => 'Lista tylko z nazwą', 2 => 'Lista bez zdjęcia', 3 => 'Lista ze zdjęciem', 4 => 'Newsy', 5 => 'Galeria', 0 => 'Brak wyświetlania' )

/*
* Lokalizacje sliderów
* Więcej: http://opensolution.org/docs/?p=pl-settings#sliders_types
*/
$config['sliders_types'] = Array( 1 => 'Nagłówek', 2 => 'Widżet 1', 3 => 'Widżet 2', 4 => 'Widżet 3', 5 => 'Widżet 4' ); // domyślna wartość: Array( 1 => 'Nagłówek', 2 => 'Widżet 1', 3 => 'Widżet 2', 4 => 'Widżet 3', 5 => 'Widżet 4' )

/*
* Ustawienia motywów do wyboru w czasie edycji strony
* Więcej: http://opensolution.org/docs/?p=pl-settings#themes
*/
$config['themes'] = Array(
  1 => Array( 'header.php', 'page.php', 'footer.php', 'Domyślny układ' ),  2 => Array( 'header.php', 'page_glowna.php', 'footer.php', 'Strona_glowna' ),// domyślna wartość: 1 => Array( 'header.php', 'page.php', 'footer.php', 'Domyślny układ' )
);

/*
* Rodzaje komend dla robotów wyszukiwarek np. Google
* Więcej: http://opensolution.org/docs/?p=pl-settings#meta_robots_options
*/
$config['meta_robots_options'] = Array( 0 => Array( 'Brak instrukcji' ), 2 => Array( 'Nie indeksuj strony', 'noindex' ), 3 => Array( 'Nie indeksuj strony i nie wchodź na linki w opisie', 'noindex, nofollow' ), 4 =>  Array( 'Nie archiwizuj strony', 'noarchive' ) ); // domyślna wartość: Array( 0 => Array( 'Brak instrukcji' ), 2 => Array( 'Nie indeksuj strony', 'noindex' ), 3 => Array( 'Nie indeksuj strony i nie wchodź na linki w opisie', 'noindex, nofollow' ), 4 =>  Array( 'Nie archiwizuj strony', 'noarchive' ) )

/*
* Lokalizacje widżetów
* Więcej: http://opensolution.org/docs/?p=pl-settings#widgets_types
*/
$config['widgets_types'] = Array( 0 => 'Niestandardowy', 1 => 'Header prawo ', 2 => 'Header niżej', 3 => 'Footer lewo', 4 => 'Footer prawo', 5 => 'Lewa kolumna', 6 => 'Prawa kolumna' , 7 => 'Slider' ); // domyślna wartość: Array( 0 => 'Niestandardowy', 1 => 'Nagłówek - nad menu', 2 => 'Nagłówek - pod menu i nad treścią strony', 3 => 'Stopka - pod treścią strony', 4 => 'Stopka - pod stopką strony', 5 => 'Inny jak komunikaty, dodatkowe skrypty, itp.' )

/*
* Rodzaje widżetów. Dodając nowy rodzaj widżetów, nadaj mu cyfrę nie mniejszą niż 50
* Więcej: http://opensolution.org/docs/?p=pl-settings#widgets_contents
*/
$config['widgets_contents'] = Array( 1 => 'Treść', 2 => 'Dane strony', 3 => 'Slider', 4 => 'Menu', 5 => 'Podstrony - lista', 6 => 'Podstrony - slider', 20 => 'Powrót na górę strony', 7 => 'Formularz kontaktowy', 10 => 'Komunikat z informacją o np. ciastkach', 23 => 'Tagi', 12 => 'Subskrypcja' ); // domyślna wartość: Array( 1 => 'Treść', 2 => 'Dane strony', 3 => 'Slider', 4 => 'Menu', 5 => 'Podstrony - lista', 6 => 'Podstrony - slider' )

/*
* Opcje kadrowania zdjęć
* Więcej: http://opensolution.org/docs/?p=pl-settings#crop_options
*/
$config['crop_options'] = Array( 0 => Array( 'Nie kadruj' ), 1 => Array( '1:1', '_1x1', 1 ), 2 => Array( '4:3', '_4x3', 0.75 ), 3 => Array( '16:9', '_16x9', 0.5625 ) ); // domyślna wartość: Array( 0 => Array( 'Nie kadruj' ), 1 => Array( '1:1', '_1x1', 1 ), 2 => Array( '4:3', '_4x3', 0.75 ), 3 => Array( '16:9', '_16x9', 0.5625 ) )

/*
* Zmienna przechowuje domyślną wersję języka. Strona będzie się wyświetlać w tej wersji języka dopóki klient nie zmieni tłumaczenia
* Więcej: http://opensolution.org/docs/?p=pl-settings#default_language
*/
$config['default_language'] = 'pl'; // domyślna wartość: 'pl'

/*
* Tłumaczenie opisów pól i komunikatów w panelu administracyjnym
* Więcej: http://opensolution.org/docs/?p=pl-settings#admin_lang
*/
$config['admin_lang'] = 'pl'; // domyślna wartość: 'pl'

/*
* Nazwa pliku administracji
* Więcej: http://opensolution.org/docs/?p=pl-settings#admin_file
*/
$config['admin_file'] = 'zaplecze.php'; // domyślna wartość: 'admin.php'

/*
* Zmienna wyłącza wczytywanie się slidera po stronie klienta
* Więcej: http://opensolution.org/docs/?p=pl-settings#enabled_sliders
*/
$config['enabled_sliders'] = true; // możliwe wartości: true (domyślne), null

/*
* Zmienna wyłącza wczytywanie się widżetów po stronie klienta
* Więcej: http://opensolution.org/docs/?p=pl-settings#enabled_widgets
*/
$config['enabled_widgets'] = true; // możliwe wartości: true (domyślne), null

/*
* Możliwość wysyłania hasła administratora na email
* Więcej: http://opensolution.org/docs/?p=pl-settings#enabled_main_admin_password_remind
*/
$config['enabled_main_admin_password_remind'] = null; // możliwe wartości: true, null (domyślne)

/*
* Opcja włączania edytora WYSIWYG (domyślnie tinyMCE)
* Więcej: http://opensolution.org/docs/?p=pl-settings#wysiwyg
*/
$config['wysiwyg'] = 'ckeditor'; // możliwe wartości: 'tinymce' (domyślne), null

/*
* Zmienna umożliwia przyśpieszenie działania skryptu przez użycie pamięci podręcznej w database/cache/
* Więcej: http://opensolution.org/docs/?p=pl-settings#enable_cache
*/
$config['enable_cache'] = null; // możliwe wartości: true, null (domyślne)

/*
* Ilość podstron wyświetlanych na jedną stronę - paginacja
* Więcej: http://opensolution.org/docs/?p=pl-settings#pages_list_all
*/
// podstrony typu - wszystkie, poniżej tej zmiennej znajdują się inne typy
$config['pages_list_all'] = 10; // domyślna wartość: 10

// podstrony typu - news
$config['pages_list_4'] = 8; // domyślna wartość: 5

/*
* Ilość wyświetlanych elementów w listach w panelu administracyjnym (opcja domyślnie nie używana, ale wykorzystywana przez dodatki)
* Więcej: http://opensolution.org/docs/?p=pl-settings#admin_list
*/
$config['admin_list'] = 25; // domyślna wartość: 25

/*
* Zmienna wyłącza wyświetlanie się linka do podstrony aktualnie przeglądanej w scieżce nawigacji
* Więcej: http://opensolution.org/docs/?p=pl-settings#page_link_in_navigation_path
*/
$config['page_link_in_navigation_path'] = true; // możliwe wartości: true (domyślne), null

/*
* Możliwość przeglądania stron ukrytych jeśli administrator zalogowany jest do panelu administracyjnego
* Więcej: http://opensolution.org/docs/?p=pl-settings#display_hidden_pages
*/
$config['display_hidden_pages'] = null; // możliwe wartości: true, null (domyślne)

/*
* Opcje edycji i usunięcia strony wyświetlające się po stronie klienta
* Więcej: http://opensolution.org/docs/?p=pl-settings#display_editing_options
*/
$config['display_editing_options'] = null; // możliwe wartości: true (domyślne), null

/*
* Jeśli ustawione na true, nazwa głównej strony będzie wyświetlana w TITLE
* Więcej: http://opensolution.org/docs/?p=pl-settings#display_homepage_name_title
*/
$config['display_homepage_name_title'] = null; // możliwe wartości: true, null (domyślne)

/*
* Opcja umożliwia wyświetlanie zdjęć w standardowych lokalizacjach, nawet jak były wyświetlone w opisie
* Więcej: http://opensolution.org/docs/?p=pl-settings#display_images_displayed_in_description
*/
$config['display_images_displayed_in_description'] = null; // możliwe wartości: true, null (domyślne)

/*
* Wyświetlanie komunikatu o akceptacji licencji w panelu admina, nie zmieniaj jeśli nie jesteś pewny, że wszyscy administratorzy znają licencję
*/
$config['display_admin_license_info'] = true; // możliwe wartości: true (domyślne), null

/*
* Format daty wpisywany w trakcie dodawania strony.
* Jeśli dodajesz kilka news'ów dziennie to ustaw tą wartość na true. Stracisz kalendarz, ale zyskasz pełniejszy zakres daty.
* Więcej: http://opensolution.org/docs/?p=pl-settings#datetime_format_in_page_form
*/
$config['datetime_format_in_page_form'] = null; // możliwe wartości: true, null (domyślne)

/*
* Opcje ukrywania i pokazywania niektórych "wrażliwych" elementów w administracji
* Niektóre opcje warto zablokować na pewien czas użytkownikowi.
* Opcje nie działają gdy jest włączony tryb deweloperski
* Więcej: http://opensolution.org/docs/?p=pl-settings#disable_page_theme_selecting
*/
if( !defined( 'DEVELOPER_MODE' ) ){
  // Ukrywanie opcji "Szablony" w czasie edycji strony
  $config['disable_page_theme_selecting'] = null; // możliwe wartości: true, null (domyślne)
  // Ukrywanie opcji "ID strony w linku"
  $config['disable_page_link_id_selecting'] = null; // możliwe wartości: true, null (domyślne)
  // Ukrywanie opcji "Instrukcja dla robotów"
  $config['disable_page_robots_selecting'] = null; // możliwe wartości: true, null (domyślne)
  // Ukrycie pola w "Nazwa w menu"
  $config['disable_page_menu_name'] = null; // możliwe wartości: true, null (domyślne)
  // Ukrywanie opcji "Zaawansowane opcje" w liście plików w czasie edycji strony
  $config['disable_page_files_advanced_options'] = null; // możliwe wartości: true, null (domyślne)
  // Ukrycie pola "Funkcja dla podstron"
  $config['disable_page_list_function_selecting'] = null; // możliwe wartości: true, null (domyślne)
  // Wyłączanie możliwości usuwania języków
  $config['disable_language_delete'] = true; // możliwe wartości: true (domyślne), null
  // Wyłączanie opcji przywracania kopii zapasowej
  $config['disable_backup_restore'] = null; // możliwe wartości: true, null (domyślne)
  // Wyłączanie funkcjonalności przeglądania i zarządzania widżetami
  $config['disable_widgets'] = null; // możliwe wartości: true, null (domyślne)
  // Zablokowanie usuwania widżetów
  $config['disable_widgets_delete'] = null; // możliwe wartości: true, null (domyślne)
  // Wyłączanie funkcjonalności przegladania i instalowania dodatków
  $config['disable_plugins'] = null; // możliwe wartości: true, null (domyślne)
  // Ukrycie opcji instalacji dodatków
  $config['disable_plugins_install'] = true; // możliwe wartości: true (domyślne), null
  // Wyłączanie funkcjonalności przeglądania i zarządzania sliderami
  $config['disable_sliders'] = null; // możliwe wartości: true, null (domyślne)
  // Wyłączanie możliwości usuwania stron nadrzędnych pierwszego rzędu
  $config['disable_main_page_delete'] = null; // możliwe wartości: true, null (domyślne)
  // Zablokowanie możliwości zmiany przypisania stron do odpowiednich funkcji jak strony głównej, strony kontaktowej, itp.
  $config['disable_settings_tab_pages'] = null; // możliwe wartości: true, null (domyślne)
  // Wyłączanie opcji dodawania widżetów do opisu strony
  $config['disable_adding_widgets_to_page_description'] = null; // możliwe wartości: true, null (domyślne)
  // Wyłączanie opcji dodawania zdjęć do opisu strony
  $config['disable_adding_images_to_page_description'] = null; // możliwe wartości: true, null (domyślne)
}

/*
* Odpowiada za generowanie meta description pobieranego z opisu krótkiego lub pełnego strony
* Więcej: http://opensolution.org/docs/?p=pl-settings#dynamic_meta_description
*/
$config['dynamic_meta_description'] = true; // możliwe wartości: true (domyślne), null

/*
* Opcja usuwania nieużywanych plików w czasie usuwania strony
* Więcej: http://opensolution.org/docs/?p=pl-settings#delete_unused_files
*/
$config['delete_unused_files'] = true; // możliwe wartości: true (domyślne), null

/*
* Ograniczenie wyświetlania plików na serwerze w katalogu files/
* Więcej: http://opensolution.org/docs/?p=pl-settings#dir_files_list_limit
*/
$config['dir_files_list_limit'] = 50; // domyślna wartość: 50

/*
* Przechowują możliwe rozszerzenia dla zdjęć i zwykłych plików
* Więcej: http://opensolution.org/docs/?p=pl-settings#allowed_not_image_extensions
*/
// Rozszerzenia dla plików - nie zdjęć
$config['allowed_not_image_extensions'] = 'pdf|swf|doc|txt|xls|ppt|rtf|odt|ods|odp|rar|zip|7z|bz2|tar|gz|tgz|arj|docx'; // domyślna wartość: 'pdf|swf|doc|txt|xls|ppt|rtf|odt|ods|odp|rar|zip|7z|bz2|tar|gz|tgz|arj|docx'
// Rozszerzenia dla zdjęc
$config['allowed_image_extensions'] = 'jpg|jpeg|gif|png'; // domyślna wartość: 'jpg|jpeg|gif|png'

/*
* Ustawienia dla rozmiarów i jakości wgrywanych zdjęć
* Więcej: http://opensolution.org/docs/?p=pl-settings#max_image_size
*/
// Maksymalny rozmiar dłuższego boku zdjęcia dla którego wygeneruje się miniaturka
$config['max_image_size'] = 2000; // domyślna wartość: 2000
// Maksymalna wielkość dłuższego boku zdjęcia. Gdy poniższa wartość zostanie przekroczona, to zostanie pomniejszony do niżej zdefiniowanej.
$config['max_dimension_of_image'] = 1100; // domyślna wartość: 1100
// Jakość zapisywanego i pomniejszanego zdjęcia
$config['image_quality'] = 80; // domyślna wartość: 80

/*
* Zmiana nazwy pliku do nazwy strony, do której jest dodawany
* Więcej: http://opensolution.org/docs/?p=pl-settings#change_files_names
*/
$config['change_files_names'] = null; // możliwe wartości: true, null (domyślne)

/*
* Ustawienia domyślne dla niektórych opcji
* Więcej: http://opensolution.org/docs/?p=pl-settings#default_pages_menu
*/
// Domyślny typ strony. Opcja dla zmiennej: $config['pages_menus']
$config['default_pages_menu'] = 1; // domyślna wartość: 1

// Domyślna lokalizacja zdjęcia dla strony. Opcja dla zmiennej: $config['images_locations']
$config['default_image_location'] = 1; // domyślna wartość: 1

// Domyślny rozmiar miniaturki zdjęcia dla strony. Opcja dla zmiennej: $config['images_thumbnails']
$config['default_image_size'] = 200; // domyślna wartość: 200

// Domyślny rodzaj wyświetlania podstron. Opcja dla zmiennej: $config['subpages_list_types']
$config['default_subpages_list_type'] = 3; // domyślna wartość: 3

// Domyślny rodzaj slidera. Opcja dla zmiennej: $config['sliders_types']
$config['default_sliders_type'] = 1; // domyślna wartość: 1

// Domyślny motyw. Opcja dla zmiennej: $config['themes']
$config['default_theme'] = 1; // domyślna wartość: 1

// Domyślna opcja komendy dla robotów. Opcja dla zmiennej: $config['meta_robots_options']
$config['default_robots_option'] = 0; // domyślna wartość: 0

// Domyślna lokalizacja widżetu. Opcja dla zmiennej: $config['widgets_types']
$config['default_widget_type'] = 0; // domyślna wartość: 0

// Domyślna opcja wyświetlania widżetu. Opcja dla zmiennej: $config['widgets_contents']
$config['default_widget_content'] = 1; // domyślna wartość: 1

// Domyślna opcja typu slidera w widżetach. Opcja dla zmiennej: $config['sliders_types']
$config['default_widget_slider_type'] = 2; // domyślna wartość: 2

// Domyślna opcja kadrowania zdjęć. Opcja dla zmiennej: $config['crop_options']
$config['default_image_crop'] = 0; // domyślna wartość: 0

// Domyślna opcja id strony w linku
$config['default_page_id_in_link'] = null; // możliwe wartości: true, null (domyślne)

// Domyślna opcja ukrywania listy podstron w panelu
$config['default_hide_subpages_list'] = null; // możliwe wartości: true, null (domyślne)

// Domyślne ustawienie dla slidera, więcej możliwych opcji znajdziesz w core/libraries/quick.slider.js
$config['default_slider_config'] = 'sAnimation:"fade",iPause:4000'; // domyślna wartość: 'sAnimation:"fade",iPause:4000'

/*
* Elementy strony wyświetlane w widżecie
* Więcej: http://opensolution.org/docs/?p=pl-settings#default_widgets_page_elements
* Possible options for pages: 'image', 'name', 'description', 'date', 'more'
*/
$config['default_widgets_page_elements'] = Array( 'image', 'name', 'description' ); // domyślna wartość: Array( 'image', 'name', 'description' )

/*
* Ogólne ustawienia dla poszczególnych treści wyświetlanych w widżetach. Lista znajduje się w zmiennej $config['widgets_contents']
* Więcej: http://opensolution.org/docs/?p=pl-settings#widgets_functions_parameteres_content_type_X
*/
// Ustawienie ogólne dla funkcji wyświetlających slidery
$config['widgets_functions_parameteres_content_type_3'] = Array( 'sConfig' => 'sAnimation:"scroll",iPause:1000' );

// Ustawienie ogólne dla funkcji wyświetlających menu
$config['widgets_functions_parameteres_content_type_4'] = Array( 'bExpanded' => true, 'iDepthLimit' => 1 );

// Ustawienie ogólne dla funkcji wyświetlających podstrony
$config['widgets_functions_parameteres_content_type_5'] = Array( 'iLimitPerPage' => 999 );

// Ustawienie ogólne dla funkcji wyświetlających podstrony - slider
$config['widgets_functions_parameteres_content_type_6'] = Array( 'iLimitPerPage' => 7, 'sConfig' => 'bNavArrows:false' );

/*
* Ustawienia dla konkretnych typów widżetów. Poniższe ustawienia nadpisują ogólne ustawienia widżetów, które znajdują się powyżej
* Lista typów znajduje się w zmiennej $config['widgets_types']
* Więcej: http://opensolution.org/docs/?p=pl-settings#widgets_functions_parameteres_type_X
*/
$config['widgets_functions_parameteres_type_2'] = Array( 3 => Array( 'sConfig' => 'sAnimation:"scroll",iPause:3000' ), 4 => Array( 'bExpanded' => null ) );

/*
* Jeśli serwer nie pozwala na wysyłanie emaili z adresu
* nie skofigurowanego na tym serwerze, typy wartości:
* 1 - emaile będą wysłane z adresu email zdefiniowangeo w konfiguracji lub z emaila klienta ( podczas używania formularza kontaktowego )
* 2 - emaile będą wysyłane z domyślnego emaila zkonfigurowanego na serwerze ( adresu email nie ma w skrypcie )
* 3 - wszystkie emaile będą wysyłane z adresu email zdefiniowanego w konfiguracji
* Więcej: http://opensolution.org/docs/?p=pl-settings#emails_from_header_option
*/
$config['emails_from_header_option'] = 1; // możliwe wartości: 1 (domyślne), 2, 3

/*
* Format daty
* Więcej: http://opensolution.org/docs/?p=pl-settings#date_format_admin_default
*/
// Prezentacja daty w panelu administracyjnym
$config['date_format_admin_default'] = 'Y-m-d H:i'; // domyślna wartość: 'Y-m-d H:i'
// Prezentacja daty w liscie newsów po stronie klienta
$config['date_format_customer_news'] = 'Y-m-d'; // domyślna wartość: 'Y-m-d'

/*
* Dodaj różnicę czasu (w minutach) między czasem lokalnym a czasem na serwerze
* Więcej: http://opensolution.org/docs/?p=pl-settings#time_diff
*/
$config['time_diff'] = 0; // domyślna wartość: 0

/*
* Jeśli w adresie URL nazwy strony ma być dodawany znacznik języka, to dodaj do niego separator np. _
* Po wypełnieniu poniższej zmiennej, edytuj jakąkolwiek stronę w administracji i zapisz ją (nie musisz w niej nic zmieniać),
* aby adresy stron zaktualizowały się o nazwę języka i separator.
* Więcej: http://opensolution.org/docs/?p=pl-settings#language_separator
*/
$config['language_separator'] = null; // domyślna wartość: null

/*
* Wybór rodzaju przywrócenia kopii zapasowej (0 - wszystko, 1 - konfiguracja, 2 - baza danych ze sliderami, plikami, stronami, itd.)
* Więcej: http://opensolution.org/docs/?p=pl-settings#restore_backup
*/
$config['restore_backup'] = "0";

/*
* Wpisz adres domeny np. 'domena.pl' (bez http) jeśli chcesz, aby przekierowania
* i pobierane adresy URL przez Google przez sitemap.xml kierowały do konkretnej domeny
* Więcej: http://opensolution.org/docs/?p=pl-settings#domain
*/
$config['domain'] = null; // domyślna wartość: null

/*
* Jeśli zdefiniowałeś domenę w $config['domain'] i chcesz, aby narzucało ten adres
* w przypadku, gdy wywołany adres jest inny niż wpisany w $config['domain']
* Więcej: http://opensolution.org/docs/?p=pl-settings#redirect_to_domain
*/
$config['redirect_to_domain'] = null; // możliwe wartości: true, null (domyślne)

/*
* Podaj wszystkie id stron, które nie mają się wyświetlić w mapie strony
* Więcej: http://opensolution.org/docs/?p=pl-settings#disable_pages_in_sitemap
*/
$config['disable_pages_in_sitemap'] = Array( 2 => true, 3 => true ); // domyślna wartość: Array( 2 => true, 3 => true )

/*
* Podaj wszystkie id stron, które nie chcesz przekazywać do mapy strony dla Google. Adres: http://twoj-adres.pl/sitemap.xml
* Więcej: http://opensolution.org/docs/?p=pl-settings#disable_pages_in_sitemap_xml
*/
$config['disable_pages_in_sitemap_xml'] = Array( 2 => true, 3 => true ); // domyślna wartość: Array( 2 => true, 3 => true )

/*
* Podaj wszystkie id stron, które nie chcesz, aby były wyszukiwane za pomocą wyszukiwarki.
* Uwaga! Id stron wpisywane są inaczej niż w powyższych zmiennych
* Więcej: http://opensolution.org/docs/?p=pl-settings#disable_pages_in_search_results
*/
//$config['disable_pages_in_search_results'] = Array( 2, 3 ); // domyślna wartość: Array( 2, 3 )

/*
* Możliwość ograniczenia wyświetlania widżetów tylko do konkretnych stron.
* Ograniczenie nie działa do widżetów wyświetlanych przy pomocy funkcji displayWidget( ) i w opisie pełnym strony
* Więcej: http://opensolution.org/docs/?p=pl-settings#display_widgets_on_page
*/
/*$config['display_widgets_on_page'] = Array(
  9999 => Array( 1 => true, 3 => true ), // wyświetlanie jedynie widżetów o id 1 i 3 na stronie o id 9999
  8888 => true, // wyświetlanie widżetów na stronie o id 8888
);*/

/*
* Przekierowanie do ostatnio używanej listy po naciśnięciu "zapisz i przejdź do listy"
* Więcej: http://opensolution.org/docs/?p=pl-settings#redirect_to_last_used_list
*/
$config['redirect_to_last_used_list'] = true; // możliwe wartości: true (domyślne), null

/*
* Dopisywanie do listy, czy ma być potwierdzone przez wysłanie emaila
* Uwaga! Jeśli chcesz potwierdzenia, koniecznie wcześniej sprawdź czy Twój serwer
* poprawnie wysyła na różne skrzynki email na różnych serwerach. Czasami bywa z tym problem i wiele
* osób może nie odbierać potwierdzenia
*/
$config['newsletter_confirm_email'] = null; // możliwe wartości: true, null (domyślne)

/*
* Katalog z bazą danych. Istnieje możliwość zmiany jego nazwy i w tym przypadku zapoznaj się koniecznie z dokumentacją
* Więcej: http://opensolution.org/docs/?p=pl-settings#dir_database
*/
$config['dir_database'] = 'database/'; // domyślna wartość: 'database/'
$config['database'] = $config['dir_database'].'database.db'; // domyślna wartość: $config['dir_database'].'database.db'

/*
* Adres URL newsów od webmastera wyświetlających się w okienku "Aktualności"
* Więcej: http://opensolution.org/docs/?p=pl-settings#developer_news_url
*/
//$config['developer_news_url'] = '';

/*
* Prefix identyfikujący adres po którym rozpoznawane będzie wyświetlenie stron posiadających wybrane tagi
*/
$config['tags_url_prefix'] = '';

/*
* Lista rozszerzeń oraz przypisanych do nich klas (styli CSS)
* Więcej: http://opensolution.org/docs/?p=pl-settings#ext_icons
*/
$config['ext_icons'] = Array( 'rar'=>'zip', 'zip'=>'zip', 'bz2'=>'zip', 'gz'=>'zip', 'fla'=>'fla', 'mp3'=>'media', 'mpeg'=>'media', 'mpe'=>'media', 'mov'=>'media', 'mid'=>'media', 'midi'=>'media', 'asf'=>'media', 'avi'=>'media', 'wav'=>'media', 'wma'=>'media', 'msg'=>'eml', 'eml'=>'eml', 'pdf'=>'pdf', 'jpg'=>'pic', 'jpeg'=>'pic', 'jpe'=>'pic', 'gif'=>'pic', 'bmp'=>'pic', 'tif'=>'pic', 'tiff'=>'pic', 'wmf'=>'pic', 'png'=>'png', 'chm'=>'chm', 'hlp'=>'chm', 'psd'=>'psd', 'swf'=>'swf', 'pps'=>'pps', 'ppt'=>'pps', 'sys'=>'sys', 'dll'=>'sys', 'txt'=>'txt', 'doc'=>'txt', 'rtf'=>'txt', 'vcf'=>'vcf', 'xls'=>'xls', 'xml'=>'xml', 'tpl'=>'web', 'html'=>'web', 'htm'=>'web', 'com'=>'exe', 'bat'=>'exe', 'exe'=>'exe' );

/*
* Możliwość wyłączenia języka po stronie klienta.
* Uwaga! Ta opcja zmniejsza wydajność skryptu, wiec korzystaj z niej jedynie kiedy jest potrzebna
* w innym przypadku zakomentuj linie lub usuń zmienną
* Więcej: http://opensolution.org/docs/?p=pl-settings#enabled_languages
*/
//$config['enabled_languages'] = Array( $config['default_language'] => true ); // domyślna wartość: Array( $config['default_language'] => true )

/*
* Uwaga!
* Zmienne i kod znajdujący się poniżej przeznaczony jest jedynie dla zaawansowanych użytkowników i nie zalecamy jego modyfikacji
*/
$config['language_cookie_name'] = defined( 'CUSTOMER_PAGE' ) ? 'sLanguage' : 'sLanguageBackEnd';

if( isset( $_GET['sLanguage'] ) && strlen( $_GET['sLanguage'] ) == 2 && is_file( $config['dir_database'].'config_'.$_GET['sLanguage'].'.php' ) ){
  setCookie( $config['language_cookie_name'], $_GET['sLanguage'], time( ) + 86400 );
  $config['language'] = $_GET['sLanguage'];
  $config['current_page_id'] = true;
}
else{
  if( !empty( $_COOKIE[$config['language_cookie_name']] ) && is_file( $config['dir_database'].'config_'.$_COOKIE[$config['language_cookie_name']].'.php' ) && strlen( $_COOKIE[$config['language_cookie_name']] ) == 2 )
    $config['language'] = $_COOKIE[$config['language_cookie_name']];
  else
    $config['language'] = $config['default_language'];
}

if( !isset( $_GET['p'] ) && !isset( $config['current_page_id'] ) && defined( 'CUSTOMER_PAGE' ) ){
  $config['current_page_id'] = getPageId( );
  if( is_numeric( $config['current_page_id'] ) && isset( $_COOKIE[$config['language_cookie_name']] ) && $config['language'] != $_COOKIE[$config['language_cookie_name']] ){
    setCookie( $config['language_cookie_name'], $config['language'], time( ) + 86400 );
  }
}

require $config['dir_database'].'config_'.$config['language'].'.php';
require defined( 'CUSTOMER_PAGE' ) ? $config['dir_database'].'lang_'.$config['language'].'.php' : ( is_file( $config['dir_database'].'lang_'.$config['admin_lang'].'.php' ) ? $config['dir_database'].'lang_'.$config['admin_lang'].'.php' : $config['dir_database'].'lang_'.$config['language'].'.php' );

if( isset( $config['current_tag_id'] ) && is_numeric( $config['current_tag_id'] ) && !is_numeric( $config['current_page_id'] ) && !empty( $config['page_tags'] ) ){
  $config['current_page_id'] = $config['page_tags'];
}

if( isset( $config['current_page_id'] ) && $config['current_page_id'] === true ){
  $config['current_page_id'] = $config['start_page'];
}

$config['version'] = '6.1';
$config['manual_link'] = 'http://opensolution.org/docs/?p='.( ( $config['admin_lang'] == 'pl' ) ? 'pl' : 'en' ).'-';
$config['bugfixes_link'] = 'http://opensolution.org/bug-info.html';

/*
* Sprawdza ustawienia serwera i konfiguracji skryptu
*/
if( defined( 'DEVELOPER_MODE' ) ){
  $sValue = (float) phpversion( );
  if( $sValue < '5.2' )
    exit( '<h1>Required PHP version is <u>5.2.0</u>, your version is '.phpversion( ).'</h1>' );
  elseif( !extension_loaded( 'pdo_sqlite' ) )
    exit( '<h1>Required <u>PDO</u> library with <u>pdo_sqlite</u> extension is not available</h1>' );
  elseif( !is_file( $config['database'] ) )
    exit( '<h1>Required file <u>'.$config['database'].'</u> is not available</h1>' );
  elseif( defined( 'ADMIN_PAGE' ) && ini_get( 'allow_url_fopen' ) != 1 ){
    exit( '<h1>Turn ON <u>allow_url_fopen</u> in PHP configuration (php.ini)</h1>' );
  }
}
elseif( isset( $_GET['error'] ) && $_GET['error'] == md5( $_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'] ) ){
  exit( '<h1>This page is temporary unavailable</h1>' );
}

if( isset( $config['redirect_to_domain'] ) && !empty( $config['domain'] ) && !strstr( $_SERVER['HTTP_HOST'], $config['domain'] ) ){
  header( 'HTTP/1.1 301 Moved Permanently' );
  header( 'Location: http://'.$config['domain'].$_SERVER['REQUEST_URI'] );
  exit;
}

/**
* Returns page id from the $_GET
* @return array
*/
function getPageId( ){
  global $config;
  if( !is_file( $config['dir_database'].'cache/links' ) )
    exit( '<h1>'.( defined( 'DEVELOPER_MODE' ) ? 'There is no required file: '.$config['dir_database'].'cache/links' : 'This page is temporary unavailable' ).'</h1>' );

  $config['pages_links'] = unserialize( file_get_contents( $config['dir_database'].'cache/links' ) );
  $aTagsUrls = is_file( $config['dir_database'].'cache/tags_links' ) ? unserialize( file_get_contents( $config['dir_database'].'cache/tags_links' ) ) : null;
  if( isset( $_GET ) && is_array( $_GET ) ){
    if( isset( $_GET['p'] ) )
      return false;
    else{
      foreach( $_GET as $mKey => $mValue ){
        if( isset( $config['pages_links'][$mKey] ) ){
          $config['language'] = $config['pages_links'][$mKey][1];
          return $config['pages_links'][$mKey][0];
        }
        elseif( isset( $aTagsUrls[$mKey] ) ){
          $config['language'] = $aTagsUrls[$mKey][1];
          $config['current_tag_id'] = $aTagsUrls[$mKey][0];
          $config['current_tag_url'] = $mKey;
          return false;
        }
        else{
          $aExp = explode( ',', $mKey );
          return ( isset( $aExp[1] ) && is_numeric( $aExp[1] ) ) ? $aExp[1] : false;
        }
      } // end foreach
      return true;
    }
  }
} // end function getPageId
?>