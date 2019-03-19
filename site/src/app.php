<?php
require_once __DIR__ . '/../vendor/autoload.php';
use ttm4135\webapp\Auth;


$templatedir =  __DIR__.'/webapp/templates/';
$app = new \Slim\Slim([
    'debug' => true,
    'templates.path' => $templatedir,
    'view' => new \Slim\Views\Twig($templatedir
  )
]);
$view = $app->view();
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);
$view->parserOptions = array(
    'debug' => true
);



try {
    // Create (connect to) SQLite database in file
    $app->db = new PDO('sqlite:/home/grp4/apache/htdocs/site/app.db');   //TODO update with location of your database
    // Set errormode to exceptions
    $app->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo $e->getMessage();
    exit();
}


$ns ='ttm4135\\webapp\\controllers\\';

Auth::updateSessionExpiration();
if(Auth::isSessionExpired()){
    $app->post('/logout',$ns . 'LoginController:logout');
}


/// app->(GET/POST) (URL, $ns . CONTROLLER);    // description..   <who has access>

$app->get('/',     $ns . 'HomeController:index');             //front page            <all site visitors>



$app->get( '/login', $ns . 'LoginController:index');        //login form            <all site visitors>
$app->post('/login', $ns . 'LoginController:login');       //login action          <all site visitors>

$app->get('/user/edit/:userid',    $ns . 'AdminController:show');       //add user userid          <staff and group members>
$app->post('/user/edit/:userid',   $ns . 'AdminController:edit');       //add user userid          <staff and group members>

$app->post('/logout',$ns . 'LoginController:logout');  //logs out    <all users>
$app->get('/logout', $ns . 'LoginController:logout');  //logs out    <all users>

$app->get('/expired', $ns . 'LoginController:expired'); // session expired

$app->get( '/register', $ns . 'UserController:index');     //registration form     <all visitors with valid personal cert>
$app->post('/register', $ns . 'UserController:create');    //registration action   <all visitors with valid personal cert>

//Admin
$app->get('/admin', $ns . 'AdminController:index');        //admin overview        <staff and group members>
$app->get('/admin/delete/:userid', $ns . 'AdminController:delete');     //delete user userid        <staff and group members>
$app->post('/admin/deleteMultiple', $ns . 'AdminController:deleteMultiple');     //delete user userid        <staff and group members>
$app->get('/admin/edit/:userid',    $ns . 'AdminController:show');       //add user userid          <staff and group members>
$app->post('/admin/edit/:userid',   $ns . 'AdminController:edit');       //add user userid          <staff and group members>
$app->get('/admin/create',    $ns . 'AdminController:create');       //add user userid          <staff and group members>
$app->post('/admin/create',   $ns . 'AdminController:newuser');       //add user userid          <staff and group members>  

return $app;
