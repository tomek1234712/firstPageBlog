<?php
/*
* Dane konfiguracyjne strony zależne od języka
* Więcej: http://opensolution.org/docs/?p=pl-settings
*/
setlocale( LC_CTYPE, 'pl_PL' );

/*
* Id strony startowej (głównej)
* Więcej: http://opensolution.org/docs/?p=pl-settings#start_page
*/
$config['start_page'] = "1";

/*
* Id strony z wynikami wyszukiwania
* Więcej: http://opensolution.org/docs/?p=pl-settings#page_search
*/
$config['page_search'] = "3";

/*
* Id strony z mapą strony
* Więcej: http://opensolution.org/docs/?p=pl-settings#page_sitemap
*/
$config['page_sitemap'] = "2";

/*
* Logo strony. Możesz wstawić tu także obrazek stosując kod HTML
* Więcej: http://opensolution.org/docs/?p=pl-settings#logo
*/
$config['logo'] = '<img src="templates/default/img/logo.png" alt="Tomasz Szczepanik Blog Lifestylowy">';

/*
* Tytuł wyświetlający się w znaczniku TITLE obok nazwy aktualnie przeglądanej podstrony
* Więcej: http://opensolution.org/docs/?p=pl-settings#title
*/
$config['title'] = 'Tomasz Szczepaniak - blog lifestylowy';

/*
* Opis strony wyświetlający się w znaczniku META DESCRIPTION, jeśli strona nie posiada wpisanego opisu meta
* Więcej: http://opensolution.org/docs/?p=pl-settings#description
*/
$config['description'] = 'Bezpieczny, szybki, intuicyjny w obsłudze i prężnie rozwijany od ponad 9 lat system zarządzania treścią, który zadowoli nawet wymagających użytkowników.';

/*
* Slogan, który wyświetla się domyślnie pod logo. Można tu zastosować kod HTML
* Więcej: http://opensolution.org/docs/?p=pl-settings#slogan
*/
$config['slogan'] = 'Wszechstronny i prosty w obsłudze system zarządzania treścią';

/*
* Treść stopki strony, która domyślnie znajduje się po lewej stronie
* Więcej: http://opensolution.org/docs/?p=pl-settings#foot_info
*/
$config['foot_info'] = 'Wszelkie prawa zastrzeżone';
/*
* Id strony kontaktowej
*/
$config['page_contact'] = "";





// dodano



$config['copyright'] = '<span>Copyright &copy; '.date("Y").' Tomasz Szczepaniak. <br>Wszelkie prawa zastrzeżone.</span>';

$config['cms'] = '<span>CMS by </span><a href="http://opensolution.org/" target="blank">Quick.CMS</a>';

$config['realizacja'] = '<span>Realizacja: </span><a href="http://czyzkowski.net" target="blank">czyzkowski.net</a>';

$config['szukajka'] = 'Wpisz słowo kluczowe.';



$config['dalej'] = 'czytaj więcej';

/*
* Id strony wyświetlającej strony z tagami
*/
$config['page_tags'] = "4";

?>