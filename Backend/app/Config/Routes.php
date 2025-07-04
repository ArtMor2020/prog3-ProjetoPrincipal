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

// ----------------- USERS -----------------
$routes->group('users', function (RouteCollection $routes) {
    $routes->get('',                'UserController::index');
    $routes->get('(:segment)',      'UserController::show/$1');
    $routes->get('name/(:any)',     'UserController::showByName/$1');
    $routes->post('',               'UserController::create');
    $routes->post('auth',           'UserController::auth');
    $routes->put('(:segment)',      'UserController::update/$1');
    $routes->put('(:num)/ban',      'UserController::ban/$1');
    $routes->put('(:num)/unban',    'UserController::unban/$1');
    $routes->put('(:num)/restore',  'UserController::restore/$1');
    $routes->delete('(:segment)',   'UserController::delete/$1');
});

// --------------- COMMUNITIES ---------------
$routes->group('communities', ['filter' => 'auth'], function (RouteCollection $routes) {
    $routes->get('user-status/(:num)/(:num)', 'CommunityController::userStatus/$1/$2');
    $routes->get('',                   'CommunityController::index');
    $routes->get('(:segment)',         'CommunityController::show/$1');
    $routes->get('owner/(:num)',       'CommunityController::byOwner/$1');
    $routes->get('search/(:segment)',  'CommunityController::search/$1');
    $routes->post('',                  'CommunityController::create');
    $routes->put('(:segment)',         'CommunityController::update/$1');
    $routes->put('(:num)/ban',         'CommunityController::ban/$1');
    $routes->put('(:num)/unban',       'CommunityController::unban/$1');
    $routes->put('(:num)/restore',     'CommunityController::restore/$1');
    $routes->delete('(:segment)',      'CommunityController::delete/$1');
});

// ----------------- POSTS -----------------
$routes->group('posts', ['filter' => 'auth'], function (RouteCollection $routes) {
    $routes->get('',                           'PostController::index');
    $routes->get('community/(:segment)',       'PostController::index/$1');
    $routes->get('popular',                    'PostController::getPopular');
    $routes->get('community/(:num)/popular',   'PostController::getPopularInCommunity/$1');
    $routes->get('recommended/(:num)',         'PostController::getRecommended/$1');
    $routes->get('title/(:any)',               'PostController::getByTitle/$1');
    $routes->get('(:segment)',                 'PostController::show/$1');
    $routes->post('',                          'PostController::create');
    $routes->post('submit',                    'PostController::submit');
    $routes->post('(:num)/report', 'PostController::report/$1');
    $routes->put('(:segment)',                 'PostController::update/$1');
    $routes->delete('(:segment)',              'PostController::delete/$1');
});

// ----------------- COMMENTS -----------------
$routes->group('comments', ['filter' => 'auth'], function (RouteCollection $routes) {
    $routes->get('',                    'CommentController::index');
    $routes->get('post/(:num)',         'CommentController::index/$1');
    $routes->get('comment/(:num)',      'CommentController::byParent/$1');
    $routes->get('(:num)',              'CommentController::show/$1');
    $routes->post('',                   'CommentController::create');
    $routes->post('submit',             'CommentController::submit');
    $routes->post('(:num)/reply',       'CommentController::reply/$1');
    $routes->put('(:num)',              'CommentController::update/$1');
    $routes->delete('(:num)',           'CommentController::delete/$1');
    $routes->post('(:num)/report', 'CommentController::report/$1');
});

// ------------- BLOCKED USERS -------------
$routes->group('blocked-users', ['filter' => 'auth'], function ($routes) {
    $routes->post('block',    'BlockedUserController::block');
    $routes->put('unblock',   'BlockedUserController::unblock');
    $routes->get('(:num)',    'BlockedUserController::index/$1');
});

// --------- DIRECT MESSAGES ---------
$routes->group('direct-messages', ['filter' => 'auth'], function ($routes) {
    $routes->post('',                           'DirectMessageController::create');
    $routes->get('conversation/(:num)/(:num)',  'DirectMessageController::conversation/$1/$2');
    $routes->put('(:num)/seen',                 'DirectMessageController::markSeen/$1');
    $routes->get('messages/unseen/(:num)',      'DirectMessageController::getUnseen/$1');
    $routes->get('unread-summary/(:num)', 'DirectMessageController::unreadSummary/$1');
    $routes->put('mark-conversation-seen/(:num)/(:num)', 'DirectMessageController::markConversationSeen/$1/$2');
});

// ------- USER–COMMUNITIES -------
$routes->group('user-communities', ['filter' => 'auth'], function ($routes) {
    $routes->post('',                       'UserInCommunityController::create');
    $routes->post('add',                    'UserInCommunityController::add');
    $routes->put('role',                    'UserInCommunityController::setRole');
    $routes->delete('remove',               'UserInCommunityController::remove');
    $routes->get('community/(:num)',        'UserInCommunityController::byCommunity/$1');
    $routes->get('user/(:num)',             'UserInCommunityController::byUser/$1');
    $routes->delete('(:num)/(:num)',        'UserInCommunityController::delete/$1/$2');
    $routes->put('(:num)/(:num)/role',      'UserInCommunityController::role/$1/$2');
    $routes->put('(:num)/(:num)/ban',       'UserInCommunityController::ban/$1/$2');
    $routes->put('(:num)/(:num)/unban',     'UserInCommunityController::unban/$1/$2');
});

// -------- COMMUNITY VIEWS --------
$routes->group('community-views', ['filter' => 'auth'], function ($routes) {
    $routes->post('',                       'CommunityViewController::create');
    $routes->post('log/(:num)/(:num)',      'CommunityViewController::log/$1/$2');
    $routes->get('(:num)',                  'CommunityViewController::index/$1');
    $routes->get('community/(:num)',        'CommunityViewController::byCommunity/$1');
    $routes->get('user/(:num)',             'CommunityViewController::byUser/$1');
});

// ---- COMMUNITY JOIN REQUESTS ----
$routes->group('community-join-requests', ['filter' => 'auth'], function ($routes) {
    $routes->get('',                    'CommunityJoinRequestController::index');
    $routes->get('community/(:num)',    'CommunityJoinRequestController::byCommunity/$1');
    $routes->get('user/(:num)',         'CommunityJoinRequestController::byUser/$1');
    $routes->post('',                   'CommunityJoinRequestController::create');
    $routes->put('(:num)/approve',      'CommunityJoinRequestController::approve/$1');
    $routes->put('(:num)/reject',       'CommunityJoinRequestController::reject/$1');
});

// ------- RATINGS IN POSTS -------
$routes->group('ratings-in-posts', ['filter' => 'auth'], function ($routes) {
    $routes->post('(:num)/votes',    'RatingInPostController::toggle/$1');
    $routes->get('(:num)/votes',     'RatingInPostController::list/$1');
    $routes->get('(:num)/score',     'RatingInPostController::score/$1');
    $routes->delete('(:num)/vote/delete', 'RatingInPostController::remove/$1');
});

// -------- POST VIEWS --------
$routes->group('post-views', ['filter' => 'auth'], function ($routes) {
    $routes->post('',               'PostViewController::create');
    $routes->get('(:num)/views',    'PostViewController::list/$1');
    $routes->get('(:num)/views/count', 'PostViewController::count/$1');
});

// ---------- ATTACHMENTS ----------
$routes->group('attachments',  function ($routes) {
    $routes->get('',               'AttachmentController::index');
    $routes->get('(:num)',         'AttachmentController::show/$1');
    $routes->post('',              'AttachmentController::create');
    $routes->put('(:num)',         'AttachmentController::update/$1');
    $routes->delete('(:num)',      'AttachmentController::delete/$1');
    $routes->put('(:num)/restore', 'AttachmentController::restore/$1');
    $routes->get('serve/(:num)', 'AttachmentController::serve/$1');
});

// --- ATTACHMENT IN POSTS ---
$routes->group('attachment-in-posts', ['filter' => 'auth'], function ($routes) {
    $routes->get('',               'AttachmentInPostController::index');
    $routes->get('(:num)/(:num)',  'AttachmentInPostController::show/$1/$2');
    $routes->post('',              'AttachmentInPostController::create');
    $routes->delete('(:num)/(:num)','AttachmentInPostController::delete/$1/$2');
});

// -- ATTACHMENT IN COMMENTS --
$routes->group('attachment-in-comments', ['filter' => 'auth'], function ($routes) {
    $routes->get('',               'AttachmentInCommentController::index');
    $routes->get('(:num)/(:num)',  'AttachmentInCommentController::show/$1/$2');
    $routes->post('',              'AttachmentInCommentController::create');
    $routes->delete('(:num)/(:num)','AttachmentInCommentController::delete/$1/$2');
});

// ---- RATINGS IN COMMENTS ----
$routes->group('ratings-in-comments', ['filter' => 'auth'], function ($routes) {
    $routes->post('(:num)/votes',       'RatingInCommentController::toggle/$1');
    $routes->get('(:num)/votes',        'RatingInCommentController::score/$1');
    $routes->get('(:num)/votes/list',   'RatingInCommentController::listVotes/$1');
    $routes->delete('(:num)/vote/remove','RatingInCommentController::remove/$1');
});

// -------- SEARCH --------
$routes->group('search', ['filter' => 'auth'], function($routes) {
    $routes->get('', 'SearchController::query');
    $routes->get('users', 'SearchController::users');
});

// ------- FRIENDSHIP --------
$routes->group('friendship', ['filter' => 'auth'], function($routes) {
    $routes->get('status/(:num)/(:num)', 'FriendshipController::getStatus/$1/$2');
    $routes->post('send-request',     'FriendshipController::sendRequest');
    $routes->get('requests/(:num)',   'FriendshipController::getRequests/$1');
    $routes->put('accept/(:num)',     'FriendshipController::acceptRequest/$1');
    $routes->delete('refuse/(:num)',  'FriendshipController::refuseRequest/$1');
    $routes->get('friends/(:num)',    'FriendshipController::getFriends/$1');
});

// ------- NOTIFICATION --------
$routes->group('notification', ['filter' => 'auth'], function($routes) {
    $routes->get('formatted/(:num)',  'NotificationController::formattedNotifications/$1');
    $routes->put('clear/(:num)',      'NotificationController::clearNotification/$1');
    $routes->put('clear_all/(:num)',  'NotificationController::clearAllNotifications/$1');
    $routes->delete('delete/(:num)',  'NotificationController::delete/$1');
});

if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
