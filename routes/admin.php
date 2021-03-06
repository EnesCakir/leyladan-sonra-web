<?php

Route::get('/', 'Admin\HomeController@index')->name('home');
Route::post('/telegram/webhook', 'Admin\Miscellaneous\TelegramController@webhook')->name('telegram.webhook');
Route::get('/telegram/set', 'Admin\Miscellaneous\TelegramController@set')->name('telegram.set');

Route::prefix('dashboard')->group(function () {
    Route::get('/', 'Admin\Miscellaneous\DashboardController@index')->name('dashboard');
    Route::get('/data', 'Admin\Miscellaneous\DashboardController@data')->name('dashboard.data');
});

Route::get('sidebar/data', 'Admin\Miscellaneous\SidebarController@data')->name('sidebar.data');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

// TODO: Uncomment on Laravel 5.7
//Route::prefix('email')->as('verification.')->group(function () {
//    Route::get('resend', 'Admin\Auth\VerificationController@resend')->name('resend');
//    Route::get('verify', 'Admin\Auth\VerificationController@show')->name('notice');
//    Route::get('verify/{id}', 'Admin\Auth\VerificationController@verify')->name('verify');
//});
Route::prefix('password')->as('password.')->group(function () {
    Route::post('email', 'Admin\Auth\ForgotPasswordController@sendResetLinkEmail')->name('email');
    Route::get('reset', 'Admin\Auth\ForgotPasswordController@showLinkRequestForm')->name('request');
    Route::post('reset', 'Admin\Auth\ResetPasswordController@reset')->name('update');
    Route::get('reset/{token}', 'Admin\Auth\ResetPasswordController@showResetForm')->name('reset');
});
Route::get('login', 'Admin\Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Admin\Auth\LoginController@login')->name('login.post');
Route::post('logout', 'Admin\Auth\LoginController@logout')->name('logout');
Route::get('register', 'Admin\Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Admin\Auth\RegisterController@register')->name('register.post');

Route::get('/email/activation/{token}', 'Admin\Auth\ActivateEmailController@activate')->name('email.activate');

/*
|--------------------------------------------------------------------------
| Children Routes
|--------------------------------------------------------------------------
*/
Route::prefix('child')->as('child.')->group(function () {
    Route::prefix('{child}')->group(function () {
        Route::get('chat', 'Admin\Volunteer\ChildChatController@index');
        Route::put('chat', 'Admin\Volunteer\ChildChatController@update');
        Route::post('process', 'Admin\Child\ChildProcessController@store')->name('process.store');
        Route::get('verification', 'Admin\Child\ChildVerificationController@show')->name('verification.show');
    });
});
Route::resource('child', 'Admin\Child\ChildController');

Route::post('tmp-media', 'Admin\Child\TemporaryMediaController@store')->name('tempMedia.store');

/*
|--------------------------------------------------------------------------
| Post Routes
|--------------------------------------------------------------------------
*/
Route::prefix('post')->as('post.')->group(function () {
    Route::prefix('{post}')->group(function () {
        Route::put('media/{media}/feature', 'Admin\Child\PostMediaController@feature')->name('media.feature');
        Route::resource('media', 'Admin\Child\PostMediaController')->only(['store', 'destroy'])->parameters([
            'media' => 'media'
        ]);
    });
});

Route::approve('post', 'Admin\Child\PostController');
Route::resource('post', 'Admin\Child\PostController');

/*
|--------------------------------------------------------------------------
| Volunteer Routes
|--------------------------------------------------------------------------
*/
Route::prefix('chat')->as('chat.')->group(function () {
    Route::prefix('{chat}')->group(function () {
        Route::get('message', 'Admin\Volunteer\ChatMessageController@index');
        Route::put('message', 'Admin\Volunteer\ChatMessageController@update');
    });
});
Route::resource('chat', 'Admin\Volunteer\ChatController');
Route::resource('volunteer', 'Admin\Volunteer\VolunteerController');


/*
|--------------------------------------------------------------------------
| Faculty Routes
|--------------------------------------------------------------------------
*/
Route::prefix('faculty')->as('faculty.')->group(function () {
    Route::prefix('{faculty}')->group(function () {
        Route::resource('child', 'Admin\Child\FacultyChildController')->only(['index', 'edit']);
        Route::resource('chat', 'Admin\Volunteer\FacultyChatController')->only(['index']);
        Route::resource('post', 'Admin\Child\FacultyPostController')->only(['index', 'edit']);
        Route::resource('email', 'Admin\Management\FacultyEmailController')->only(['create', 'store']);
        Route::resource('user', 'Admin\Management\FacultyUserController')->parameters([
            'user' => 'any_user'
        ])->only(['index', 'edit']);
    });
});
Route::resource('faculty', 'Admin\Management\FacultyController');

Route::get('/form/create', 'Admin\Management\FacultyFormController@create')->name('form.create');
Route::post('/form', 'Admin\Management\FacultyFormController@store')->name('form.store');


/*
|--------------------------------------------------------------------------
| Blood Routes
|--------------------------------------------------------------------------
*/
Route::prefix('blood')->as('blood.')->group(function () {
    Route::get('/people', 'Admin\Blood\BloodUserController@edit')->name('people.edit');
    Route::post('/people', 'Admin\Blood\BloodUserController@update')->name('people.update');
    Route::get('/sms', 'Admin\Blood\SmsController@index')->name('sms.index');
    Route::get('/sms/send', 'Admin\Blood\SmsController@show')->name('sms.show');
    Route::post('/sms/send', 'Admin\Blood\SmsController@send')->name('sms.send');
    Route::get('/sms/balance', 'Admin\Blood\SmsController@checkBalance')->name('sms.balance');
    Route::post('/sms/preview', 'Admin\Blood\SmsController@preview')->name('sms.preview');
    Route::post('/sms/test', 'Admin\Blood\SmsController@test')->name('sms.test');
});
Route::resource('blood', 'Admin\Blood\BloodController');

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/
Route::prefix('profile')->as('profile.')->group(function () {
    Route::get('/', 'Admin\Management\ProfileController@index')->name('index');
    Route::get('child', 'Admin\Management\ProfileController@show')->name('show');
    Route::get('setting', 'Admin\Management\ProfileController@edit')->name('edit');
    Route::put('setting', 'Admin\Management\ProfileController@update')->name('update');
});


/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
Route::approve('user', 'Admin\Management\UserController');
Route::resource('user', 'Admin\Management\UserController')->parameters([
    'user' => 'any_user'
]);

/*
|--------------------------------------------------------------------------
| General Setting Routes
|--------------------------------------------------------------------------
*/
Route::resource('diagnosis', 'Admin\Child\DiagnosisController');
Route::resource('department', 'Admin\Child\DepartmentController');
Route::resource('wish-category', 'Admin\Child\WishCategoryController');

/*
|--------------------------------------------------------------------------
| Content Routes
|--------------------------------------------------------------------------
*/
Route::middleware('can:website-content')->group(function () {
    Route::resource('new', 'Admin\Content\NewController');
    Route::resource('channel', 'Admin\Content\ChannelController');
    Route::resource('sponsor', 'Admin\Content\SponsorController');
    Route::resource('question', 'Admin\Content\QuestionController');
    Route::approve('testimonial', 'Admin\Content\TestimonialController');
    Route::resource('testimonial', 'Admin\Content\TestimonialController');
});

/*
|--------------------------------------------------------------------------
| Resource Routes
|--------------------------------------------------------------------------
*/
Route::resource('emailsample', 'Admin\Resource\EmailSampleController');
Route::resource('material', 'Admin\Resource\MaterialController');
Route::resource('tutorial', 'Admin\Resource\TutorialController');


/*
|--------------------------------------------------------------------------
| Statistics Routes
|--------------------------------------------------------------------------
*/

Route::prefix('statistic')->as('statistic.')->group(function () {
    Route::get('child', 'Admin\Miscellaneous\StatisticController@child')->name('child');
    Route::get('faculty', 'Admin\Miscellaneous\StatisticController@faculty')->name('faculty');
    Route::get('volunteer', 'Admin\Miscellaneous\StatisticController@volunteer')->name('volunteer');
    Route::get('blood', 'Admin\Miscellaneous\StatisticController@blood')->name('blood');
    Route::get('user', 'Admin\Miscellaneous\StatisticController@user')->name('user');
    Route::get('website', 'Admin\Miscellaneous\StatisticController@website')->name('website');
});
