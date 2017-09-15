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
Route::put('/user/{user}/image', 'HomeController@updateImage')->name('user.updateImage');
Route::put('/user/pass/{user}', 'HomeController@updatePassUser')->name('user.updatePassUser');
Route::get('/user/image/destroy/{image}', 'HomeController@destroyImageUser')->name('user.image.destroy');
Route::get('/user/ask/review/{id}', 'HomeController@ask_review')->name('user.ask.review');
Route::get('/details/request/{id}', 'HomeController@details_request')->name('details.request');
Route::resource('/user', 'HomeController',['except' => ['index', 'create', 'store', 'show', 'edit', 'destroy']]);

//Verification
Route::get('/verify', 'HomeController@unconfirmed')->name('user.unconfirmed');
Route::get('register/verify/{confirmationCode}', 'HomeController@confirm')->name('confirmation_path');
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
Route::get('/admin/general/requests/update/{id}', 'AdminController@general_requests_update')->name('admin.general.request.update');

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
Route::put('/ensemble/{user}/image', 'EnsembleController@updateImage')->name('ensemble.updateImage');
Route::get('/ensemble/dashboard', 'EnsembleController@index')->name('ensemble.dashboard');
Route::resource('/ensemble', 'EnsembleController',['except' => ['index', 'create', 'store', 'show', 'edit', 'destroy']]);

Route::post('/ensemble/add/instrument', 'EnsembleController@storeInstruments')->name('ensemble.instrument');
Route::post('/ensemble/add/tag', 'EnsembleController@storeTags')->name('ensemble.tag');
Route::post('/ensemble/add/style', 'EnsembleController@storeStyles')->name('ensemble.style');
Route::post('/ensemble/add/image', 'EnsembleController@storeImages')->name('ensemble.images');
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
//Ensembles

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
//PUBLIC

/**
 * Here we manipulate all the data for calendar of users
 */
//CALENDAR 
Route::get('/events/info', 'CalendarController@index')->name('index.calendar');
Route::get('/events/data', 'CalendarController@get_calendar')->name('get_data.calendar');
Route::get('/add/calendar/options/{option}', 'CalendarController@calendarOptions')->name('user.calendar.options');
Route::post('/block/day', 'CalendarController@block_day')->name('user.block.day');
//CALENDAR