<?php
//error_reporting(E_ALL);
//ini_set('display_errors',1); 
header('Content-type:text/html; charset=UTF-8');

require '../jonas-php-library/lib/Route.class.php';

$rulelist = array(
    'rule_startpage' => array(
        'rule'=>'/', // combinará com "http://localhost/jonas-php-library/demo/"
        'action'=>'./admin/home.php', // a ação a ser tomada
    ),
    'index' => array(
        'rule'=>'/index', // combinará com "http://localhost/jonas-php-library/demo/"
        'action'=>'./admin/home.php', // a ação a ser tomada
    ),
	'home' => array(
        'rule'=>'/home', // combinará com "http://localhost/jonas-php-library/demo/"
        'action'=>'./admin/home.php', // a ação a ser tomada
    ),
	'cadastro' => array(
        'rule'=>'/cadastro', // combinará com "http://localhost/jonas-php-library/demo/"
        'action'=>'./admin/cadastro.php', // a ação a ser tomada
    ),
	'welcome' => array(
        'rule'=>'/welcome', // combinará com "http://localhost/jonas-php-library/demo/"
        'action'=>'./admin/welcome.php', // a ação a ser tomada
    ),
	'download' => array(
        'rule'=>'/download', // combinará com "http://localhost/jonas-php-library/demo/"
        'action'=>'./admin/download.php', // a ação a ser tomada
    ),
);

$my_protocol = 'http';
$my_domain   = '96.126.115.143';
$my_basedir  = '/motoclube/';

$my_url_prefix = $my_protocol.'://'. $my_domain. $my_basedir;

try {

    $myRoute = Route::getInstance();
    $myRoute
        ->setConfig($rulelist,$my_domain,$my_basedir,$my_protocol)
        ->init($_SERVER['REQUEST_URI'])
        ->check();

	//var_export($myRoute);
    include $myRoute->getMatchedRouteAction();
    
}catch (RouteNotFoundException $e){
    
    include 'route-not-found.php';
    
}


?>
