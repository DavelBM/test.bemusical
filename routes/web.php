<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/user/logout', 'Auth\LoginController@userLogout')->name('user.logout');
Route::get('/dashboard', 'HomeController@index')->name('user.dashboard');
Route::get('/blocked', 'HomeController@blocked')->name('user.blocked');
Route::post('/user/{user}/image', 'HomeController@updateImage')->name('user.updateImage');
Route::put('/user/pass/{user}', 'HomeController@updatePassUser')->name('user.updatePassUser');
Route::get('/user/image/destroy/{image}', 'HomeController@destroyImageUser')->name('user.image.destroy');
Route::get('/user/ask/review/{id}', 'HomeController@ask_review')->name('user.ask.review');
Route::get('/details/request/{id}', 'HomeController@details_request')->name('details.request');
Route::get('/user/payments', 'HomeController@payments')->name('user.payments');
Route::get('/user/payouts', 'HomeController@payouts')->name('user.payouts');
Route::resource('/user', 'HomeController',['except' => ['index', 'create', 'store', 'show', 'edit', 'destroy']]);

//Verification
Route::get('/verify', 'HomeController@unconfirmed')->name('user.unconfirmed');
Route::get('register/verify/{confirmationCode}', 'HomeController@confirm')->name('confirmation_path');
Route::post('/user/send/phone', 'HomeController@send_phone')->name('user.send.phone');
Route::post('/user/confirm/phone', 'HomeController@confirm_phone')->name('user.confirm.phone');
Route::post('/user/send/code/phone', 'HomeController@send_code_phone')->name('user.send.code.phone');
Route::post('/user/reset/phone', 'HomeController@reset_phone')->name('user.reset.phone');
Route::post('/change/email', 'HomeController@change_email')->name('change.email');
Route::get('/update/email/{token}', 'HomeController@update_email')->name('update.email');
Route::post('/updating/email', 'HomeController@updating_email')->name('updating.email');
//Verification

//Login Logout//
Route::get('/admin/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
Route::post('/admin/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');
Route::get('/admin/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');
//Login Logout//

Route::post('/admin/assign_user', 'AdminController@assign_user')->name('admin.assign_user');
Route::get('/admin/maps/{address}', 'AdminController@display_map')->name('admin.maps');
Route::resource('/admin', 'AdminController');
Route::get('admin/{id}/destroy', 'AdminController@destroy')->name('admin.destroy');
Route::get('/admin', 'AdminController@index')->name('admin.dashboard');
Route::get('/admin/users/manage', 'AdminController@manage_user')->name('admin.manage_user');
Route::get('/admin/{id}/block', 'AdminController@blockuser')->name('admin.blockuser');
Route::get('/admin/{id}/unlock', 'AdminController@unlockuser')->name('admin.unlockuser');
Route::get('/admin/{id}/nonvisible', 'AdminController@nonvisible')->name('admin.nonvisible');
Route::get('/admin/{id}/visible', 'AdminController@visible')->name('admin.visible');
Route::get('/admin/general/requests', 'AdminController@general_requests')->name('admin.general.request');
Route::get('/admin/received/payments', 'AdminController@payments')->name('admin.payments');
Route::get('/admin/general/requests/update/{id}', 'AdminController@general_requests_update')->name('admin.general.request.update');
Route::put('/admin/pass/{admin}', 'AdminController@updatePassAdmin')->name('admin.updatePassAdmin');
Route::post('/admin/change/email', 'AdminController@change_email')->name('admin.change.email');
Route::get('/admin/update/email/{token}', 'AdminController@update_email')->name('admin.update.email');
Route::post('/admin/updating/email', 'AdminController@updating_email')->name('admin.updating.email');

//Reset Passwords//
Route::post('/admin/password/email', 'Auth\AdminForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');

Route::get('/admin/password/reset', 'Auth\AdminForgotPasswordController@showLinkRequestForm')->name('admin.password.request');

Route::post('/admin/password/reset', 'Auth\AdminResetPasswordController@reset');

Route::get('/admin/password/reset/{token}', 'Auth\AdminResetPasswordController@showResetForm')->name('admin.password.reset');
//Reset Passwords//

//Options routes//
Route::post('/add/admin/instrument', 'OptionController@instrument')->name('option.instrument');
Route::post('/add/admin/tag', 'OptionController@tag')->name('option.tag');
Route::post('/add/admin/style', 'OptionController@style')->name('option.style');

Route::get('/remove/{id}/instrument', 'OptionController@destroyInstrument')->name('instrument.destroy');
Route::get('/remove/{id}/tag', 'OptionController@destroyTag')->name('tag.destroy');
Route::get('/remove/{id}/style', 'OptionController@destroyStyle')->name('style.destroy');
//Options routes//

//Options add links
Route::post('/add/user/repertoir', 'HomeController@repertoir')->name('user.repertoir');
Route::get('/user/delete/repertoir/{id}', 'HomeController@destroy_repertoir')->name('user.repertoir.destroy');
Route::get('/user/update/repertoir/{id}', 'HomeController@update_repertoir')->name('user.repertoir.update');
Route::post('/add/user/video', 'HomeController@video')->name('option.video');
Route::get('/user/delete/video/{id}', 'HomeController@delete_video')->name('user.delete.video');
Route::post('/add/user/song', 'HomeController@song')->name('option.song');
Route::get('/user/delete/song/{id}', 'HomeController@delete_song')->name('user.delete.song');
//Options add links

//Associate to users//
Route::post('/add/instrument', 'HomeController@storeInstruments')->name('user.instrument');
Route::post('/add/tag', 'HomeController@storeTags')->name('user.tag');
Route::post('/add/style', 'HomeController@storeStyles')->name('user.style');
Route::post('/add/image', 'HomeController@storeImages')->name('user.images');
//Associate to users//

//Ensembles
Route::post('/ensemble/{user}/image', 'EnsembleController@updateImage')->name('ensemble.updateImage');
Route::get('/ensemble/dashboard', 'EnsembleController@index')->name('ensemble.dashboard');
Route::resource('/ensemble', 'EnsembleController',['except' => ['index', 'create', 'store', 'show', 'edit', 'destroy']]);

Route::post('/ensemble/add/instrument', 'EnsembleController@storeInstruments')->name('ensemble.instrument');
Route::post('/ensemble/add/tag', 'EnsembleController@storeTags')->name('ensemble.tag');
Route::post('/ensemble/add/style', 'EnsembleController@storeStyles')->name('ensemble.style');
Route::post('/add/ensemble/image', 'EnsembleController@storeImages')->name('ensemble.images');
Route::post('/ensemble/add/video', 'EnsembleController@video')->name('ensemble.video');
Route::post('/ensemble/add/song', 'EnsembleController@song')->name('ensemble.song');

Route::get('/ensemble/image/destroy/{image}', 'EnsembleController@destroyImageEnsemble')->name('ensemble.image.destroy');
Route::get('/ensemble/delete/video/{id}', 'EnsembleController@delete_video')->name('ensemble.video.destroy');
Route::get('/ensemble/delete/song/{id}', 'EnsembleController@delete_song')->name('ensemble.song.destroy');

Route::post('/ensemble/add/repertoir', 'EnsembleController@repertoir')->name('ensemble.repertoir');
Route::get('/ensemble/delete/repertoir/{id}', 'EnsembleController@destroy_repertoir')->name('ensemble.repertoir.destroy');
Route::get('/ensemble/update/repertoir/{id}', 'EnsembleController@update_repertoir')->name('ensemble.repertoir.update');

Route::post('/ensemble/add/member', 'EnsembleController@member')->name('ensemble.member');
Route::post('/ensemble/add/not/member', 'EnsembleController@notmember')->name('ensemble.not.member');
Route::get('/ensemble/destroy/member/{id}', 'EnsembleController@destroy_member')->name('ensemble.member.destroy');
Route::post('/ensemble/send/phone', 'EnsembleController@send_phone')->name('ensemble.send.phone');
Route::post('/ensemble/confirm/phone', 'EnsembleController@confirm_phone')->name('ensemble.confirm.phone');
Route::post('/ensemble/send/code/phone', 'EnsembleController@send_code_phone')->name('ensemble.send.code.phone');
Route::post('/ensemble/reset/phone', 'EnsembleController@reset_phone')->name('ensemble.reset.phone');
//Ensembles

/**
 * Here we manipulate all the data for CLIENTS
 */
//Login y logout CLIENT
Route::get('/client/login', 'Auth\ClientLoginController@showLoginForm')->name('client.login');
Route::post('/client/login', 'Auth\ClientLoginController@login')->name('client.login.submit');
Route::get('/client/dashboard', 'ClientController@index')->name('client.dashboard');//UPDATE
Route::get('/client/logout', 'Auth\ClientLoginController@logout')->name('client.logout');

//Password reset routes
Route::post('/client/password/email', 'Auth\ClientForgotPasswordController@sendResetLinkEmail')->name('client.password.email');

Route::get('/client/password/reset', 'Auth\ClientForgotPasswordController@showLinkRequestForm')->name('client.password.request');

Route::post('/client/password/reset', 'Auth\ClientResetPasswordController@reset');

Route::get('/client/password/reset/{token}', 'Auth\ClientResetPasswordController@showResetForm')->name('client.password.reset');

Route::get('/client/register', 'ClientController@register')->name('client.register');
Route::post('/client/store', 'ClientController@store')->name('client.store');
Route::post('/client/update/{id}', 'ClientController@update')->name('client.update');
Route::get('/review/{id}', 'ClientController@review')->name('client.review');
Route::post('/store/review/', 'ClientController@store_review')->name('client.store_review');
//Clients

//PUBLIC **This routes does not need to be log-in**
Route::get('/{slug}', 'PublicController@view')->name('index.public');
Route::get('/review/{slug}', 'PublicController@review_for_slug')->name('review.slug');
Route::get('/ensemble/invitation/{code}', 'PublicController@member_invitation')->name('ensemble.invitation');
Route::post('/member/add/instrument', 'PublicController@add_instrument_to_member')->name('member.add.instrument');
Route::post('/member/new', 'PublicController@member_new')->name('member.new');
Route::post('/specific/request', 'PublicController@specific_request')->name('specific.request');
Route::post('/general/request', 'PublicController@general_request')->name('general.request');
Route::get('/specified/request/invitation/{token}', 'PublicController@asking_request')->name('specific.request.response');
Route::get('/price/{token}', 'PublicController@price')->name('request.price');
Route::post('/send/price', 'PublicController@send_price')->name('general.request.send_price');
Route::get('/return/answer/price/{token}', 'PublicController@return_answer_price')->name('general.return.answer.price');
Route::get('/return/answer/price/cash/{token}', 'PublicController@return_answer_price_cash')->name('general.return.answer.price.cash');
Route::post('/return/answer/reject', 'PublicController@return_reject')->name('general.return.reject');
Route::post('/return/answer/confirmed/{id}', 'PublicController@return_confirmed')->name('general.return.confirmed');
Route::get('/allow/times/{day}', 'PublicController@allowtimes')->name('allow.times');
Route::post('/query/results', 'PublicController@query')->name('query.results');
Route::post('/filter/results', 'PublicController@filter')->name('filter.results');

Route::get('/.well-known/apple-developer-merchantid-domain-association', function () {
	$pathFile = '/var/www/test.bemusical.us/public/apple/apple-developer-merchantid-domain-association';
	return response()->file($pathFile);
});
//PUBLIC

/**
 * Here we manipulate all the data for calendar of users
 */
//CALENDAR 
Route::get('/events/info', 'CalendarController@index')->name('index.calendar');
Route::get('/events/data', 'CalendarController@get_calendar')->name('get_data.calendar');
Route::get('/add/calendar/options/{option}', 'CalendarController@calendarOptions')->name('user.calendar.options');
Route::post('/block/day', 'CalendarController@block_day')->name('user.block.day');
Route::get('/events/dates/{date}', 'CalendarController@get_dates')->name('user.dates');
Route::post('/event/destroy/date', 'CalendarController@destroydate')->name('event.destroy.day');
//CALENDAR

// //PDF
// Route::get('/get/invoice', 'PDFController@index')->name('get.invoice');;
// //PDF

//CHAT CONTROLLER
Route::get('/get/messages', 'ChatController@messages')->name('get.messages')->middleware('auth:web');
Route::post('/post/messages', 'ChatController@post_messages')->name('post.messages')->middleware('auth:web');

Route::get('/admin/get/messages/{id}', 'ChatController@messages_admin')->name('admin.get.messages')->middleware('auth:admin');

Route::get('/chat/log/{id}', 'ChatController@admin_chat')->name('admin.chat')->middleware('auth:admin');

Route::post('/admin/post/messages/{id}', 'ChatController@post_messages_admin')->name('admin.post.messages')->middleware('auth:admin');
//CHAT CONTROLLER