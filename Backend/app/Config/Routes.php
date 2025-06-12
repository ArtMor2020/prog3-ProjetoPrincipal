<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);



$routes->group('users', function (RouteCollection $routes) {
    $routes->get('', 'UserController::index');
    $routes->get('(:segment)', 'UserController::show/$1');
    $routes->post('', 'UserController::create');
    $routes->put('(:segment)', 'UserController::update/$1');
    $routes->delete('(:segment)', 'UserController::delete/$1');
    $routes->put('(:num)/ban', 'UserController::ban/$1');
    $routes->put('(:num)/unban', 'UserController::unban/$1');
    $routes->put('(:num)/restore', 'UserController::restore/$1');
    $routes->post('auth', 'UserController::auth');
});

$routes->group('communities', function (RouteCollection $routes) {
    $routes->get('', 'CommunityController::index');
    $routes->get('(:segment)', 'CommunityController::show/$1');
    $routes->post('', 'CommunityController::create');
    $routes->put('(:segment)', 'CommunityController::update/$1');
    $routes->delete('(:segment)', 'CommunityController::delete/$1');
    $routes->put('(:num)/ban', 'CommunityController::ban/$1');
    $routes->put('(:num)/unban', 'CommunityController::unban/$1');
    $routes->put('(:num)/restore', 'CommunityController::restore/$1');
    $routes->get('owner/(:num)', 'CommunityController::byOwner/$1');
    $routes->get('search/(:segment)', 'CommunityController::search/$1');
});

$routes->group('posts', function (RouteCollection $routes) {
    $routes->get('', 'PostController::index');
    $routes->get('community/(:segment)', 'PostController::index/$1');
    $routes->get('(:segment)', 'PostController::show/$1');
    $routes->post('', 'PostController::create');
    $routes->put('(:segment)', 'PostController::update/$1');
    $routes->delete('(:segment)', 'PostController::delete/$1');
});

$routes->group('comments', function (RouteCollection $routes) {
    $routes->get('', 'CommentController::index');
    $routes->get('post/(:num)', 'CommentController::index/$1');
    $routes->get('(:num)', 'CommentController::show/$1');
    $routes->post('', 'CommentController::create');
    $routes->put('(:num)', 'CommentController::update/$1');
    $routes->delete('(:num)', 'CommentController::delete/$1');
    $routes->get('comment/(:num)', 'CommentController::byParent/$1');
    $routes->post('(:num)/reply', 'CommentController::reply/$1');
    $routes->delete('(:num)', 'CommentController::delete/$1');
});

$routes->group('blocked-users', function ($routes) {
    $routes->post('block', 'BlockedUserController::block');
    $routes->put('unblock', 'BlockedUserController::unblock');
    $routes->get('(:num)', 'BlockedUserController::index/$1');
});

$routes->group('direct-messages', function ($routes) {
    $routes->post('', 'DirectMessageController::create');
    $routes->get('conversation/(:num)/(:num)', 'DirectMessageController::conversation/$1/$2');
    $routes->put('(:num)/seen', 'DirectMessageController::markSeen/$1');
});

$routes->group('user-communities', function ($routes) {
    $routes->post('add', 'UserInCommunityController::add');
    $routes->put('role', 'UserInCommunityController::setRole');
    $routes->delete('remove', 'UserInCommunityController::remove');
    $routes->get('community/(:num)', 'UserInCommunityController::byCommunity/$1');
    $routes->get('user/(:num)', 'UserInCommunityController::byUser/$1');
    $routes->post('', 'UserInCommunityController::create');
    $routes->delete('(:num)/(:num)', 'UserInCommunityController::delete/$1/$2');
    $routes->put('(:num)/(:num)/role', 'UserInCommunityController::role/$1/$2');
    $routes->put('(:num)/(:num)/ban', 'UserInCommunityController::ban/$1/$2');
    $routes->put('(:num)/(:num)/unban', 'UserInCommunityController::unban/$1/$2');
});

$routes->group('community-views', function ($routes) {
    $routes->post('log/(:num)/(:num)', 'CommunityViewController::log/$1/$2');
    $routes->get('(:num)', 'CommunityViewController::index/$1');
    $routes->post('', 'CommunityViewController::create');
    $routes->get('community/(:num)', 'CommunityViewController::byCommunity/$1');
    $routes->get('user/(:num)', 'CommunityViewController::byUser/$1');
});

$routes->group('community-join-requests', function ($routes) {
    $routes->get('', 'CommunityJoinRequestController::index');
    $routes->get('community/(:num)', 'CommunityJoinRequestController::byCommunity/$1');
    $routes->get('user/(:num)', 'CommunityJoinRequestController::byUser/$1');
    $routes->post('', 'CommunityJoinRequestController::create');
    $routes->put('(:num)/(:num)/approve', 'CommunityJoinRequestController::approve/$1/$2');
    $routes->put('(:num)/(:num)/reject', 'CommunityJoinRequestController::reject/$1/$2');
});

$routes->group('ratings-in-posts', function ($routes) {
    $routes->post('(:num)/votes', 'RatingInPostController::toggle/$1');
    $routes->get('(:num)/votes', 'RatingInPostController::list/$1');
    $routes->get('(:num)/score', 'RatingInPostController::score/$1');
});

$routes->group('post-views', function ($routes) {
    $routes->post('', 'PostViewController::create');
    $routes->get('(:num)/views/count', 'PostViewController::count/$1');
    $routes->get('(:num)/views', 'PostViewController::list/$1');
});

$routes->group('attachments', function ($routes) {
    $routes->get('', 'AttachmentController::index');
    $routes->get('(:num)', 'AttachmentController::show/$1');
    $routes->post('', 'AttachmentController::create');
    $routes->put('(:num)', 'AttachmentController::update/$1');
    $routes->delete('(:num)', 'AttachmentController::delete/$1');
    $routes->put('(:num)/restore', 'AttachmentController::restore/$1');
});

$routes->group('attachment-in-posts', function ($routes) {
    $routes->get('', 'AttachmentInPostController::index');
    $routes->get('(:num)/(:num)', 'AttachmentInPostController::show/$1/$2');
    $routes->post('', 'AttachmentInPostController::create');
    $routes->delete('(:num)/(:num)', 'AttachmentInPostController::delete/$1/$2');
});

$routes->group('attachment-in-comments', function ($routes) {
    $routes->get('', 'AttachmentInCommentController::index');
    $routes->get('(:num)/(:num)', 'AttachmentInCommentController::show/$1/$2');
    $routes->post('', 'AttachmentInCommentController::create');
    $routes->delete('(:num)/(:num)', 'AttachmentInCommentController::delete/$1/$2');
});

$routes->group('ratings-in-comments', function ($routes) {
    $routes->post('(:num)/votes', 'RatingInCommentController::toggle/$1');
    $routes->get('(:num)/votes', 'RatingInCommentController::score/$1');
    $routes->get('(:num)/votes/list', 'RatingInCommentController::listVotes/$1');
});

$routes->group('search', function($routes) {
    $routes->get('(:segment)', 'SearchController::query/$1');
});

if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
