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

Route::group(['middleware' => 'web'], function () {
	Route::get('/',[
		'uses' => 'SiteController@showIndex',
		'as' => 'site.show-index.get'
		]);
	Route::get('category/{id}',[
		'uses' => 'SiteController@showCategory',
		'as' => 'site.show-category.get'
		]);
	Route::get('categories',[
		'uses' => 'SiteController@showCategories',
		'as' => 'site.show-categories.get'
		]);
	Route::get('partner/{id}',[
		'uses' => 'SiteController@showPartner',
		'as' => 'site.show-partner.get'
		]);
	Route::get('profile/{id}',[
		'uses' => 'SiteController@showProfilePage',
		'as' => 'site.show-user-profile-page.get'
		]);
	Route::get('partner/{id}/reviews',[
		'uses' => 'SiteController@showPartnerReviewsPage',
		'as' => 'site.show-partner-reviews-page.get'
		]);
});
Auth::routes();
Route::group(['middleware' => 'auth'], function () {
	/**
	 * ПОКАЗЫВАТЬ ПАНЕЛЬ УПРАВЛЕНИЯ АДМИНА
	 */
	Route::get('/dashboard',[
		'uses' => 'AdminController@showDashboard',
		'as' => 'dashboard.show-dashboard.get'
		])->middleware('can:show-dashboard-admin,App\User');

	Route::get('/dashboard/create-partner',[
		'uses' => 'AdminController@showCreatePartnerPage',
		'as' => 'dashboard.create-partner.get'	
		])->middleware('can:show-dashboard-admin,App\User');

	Route::post('/dashboard/create-partner',[
		'uses' => 'AdminController@postCreatePartner',
		'as' => 'dashboard.create-partner.post'	
		])->middleware('can:show-dashboard-admin,App\User');



	Route::get('/dashboard/operations',[
		'uses' => 'AdminController@showOperationsPage',
		'as' => 'dashboard.show-operations.get'	
		])->middleware('can:show-dashboard-admin,App\User');

	Route::get('/dashboard/cards/list',[
		'uses' => 'AdminController@showCardListPage',
		'as' => 'dashboard.show-card-list.get'	
		])->middleware('can:show-dashboard-admin,App\User');

	Route::get('/dashboard/agents/list',[
		'uses' => 'AdminController@showAgentListPage',
		'as' => 'dashboard.show-agent-list.get'	
		])->middleware('can:show-dashboard-admin,App\User');



	Route::get('/dashboard/partners/list',[
		'uses' => 'AdminController@getPartnerList',
		'as' => 'dashboard.show-partner-list.get'	
		])->middleware('can:show-dashboard-admin,App\User');

	Route::get('/dashboard/users/list',[
		'uses' => 'AdminController@getUserList',
		'as' => 'dashboard.show-user-list.get'	
		])->middleware('can:show-dashboard-admin,App\User');

	Route::get('/dashboard/visits/list',[
		'uses' => 'AdminController@getVisitsList',
		'as' => 'dashboard.show-visits-list.get'	
		])->middleware('can:show-dashboard-admin,App\User');

	Route::get('/dashboard/visits/{sort_param}/list',[
		'uses' => 'AdminController@getVisitsListByParam',
		'as' => 'dashboard.show-visits-list-by-param.get'	
		])->middleware('can:show-dashboard-admin,App\User');

	Route::get('/dashboard/partner/{partner_id}/show',[
		'uses' => 'AdminController@getPartnerPage',
		'as' => 'dashboard.partner-page.get'	
		])->middleware('can:show-dashboard-admin,App\User');

	Route::post('/dashboard/partner/delete',[
		'uses' => 'AdminController@postDeletePartner',
		'as' => 'dashboard.delete_partner.post'
		])->middleware('can:show-dashboard-admin,App\User');

	Route::post('/dashboard/partner/edit',[
		'uses' => 'AdminController@postEditPartner',
		'as' => 'dashboard.edit_partner.post'
		])->middleware('can:show-dashboard-admin,App\User');

	Route::post('/dashboard/partner/edit-logos',[
		'uses' => 'AdminController@postEditPartnerLogos',
		'as' => 'dashboard.edit_partner_logos.post'
		])->middleware('can:show-dashboard-admin,App\User');

	Route::post('/dashboard/partner/load-gallery',[
		'uses' => 'AdminController@postLoadGallery',
		'as' => 'dashboard.load-gallery.post'
		])->middleware('can:show-dashboard-admin,App\User');

	Route::post('/dashboard/partner/edit-gallery-item',[
		'uses' => 'AdminController@postEditGalleryItem',
		'as' => 'dashboard.edit-gallery-item.post'
		])->middleware('can:show-dashboard-admin,App\User');

	Route::post('/dashboard/partner/delete-gallery-item',[
		'uses' => 'AdminController@postDeleteGalleryItem',
		'as' => 'dashboard.delete-gallery-item.post'
		])->middleware('can:show-dashboard-admin,App\User');

	Route::post('/dashboard/partner/delete-partner-address',[
		'uses' => 'AdminController@postDeletePartnerAddress',
		'as' => 'dashboard.delete-partner-address.post'
		])->middleware('can:show-dashboard-admin,App\User');

	Route::post('/dashboard/partner/add-partner-address',[
		'uses' => 'AdminController@postAddPartnerAddress',
		'as' => 'dashboard.add-partner-address.post'
		])->middleware('can:show-dashboard-admin,App\User');

	Route::post('/dashboard/partner/delete-partner-discount',[
		'uses' => 'AdminController@postDeletePartnerDiscount',
		'as' => 'dashboard.delete-partner-discount.post'
		])->middleware('can:show-dashboard-admin,App\User');

	Route::post('/dashboard/partner/add-partner-discount',[
		'uses' => 'AdminController@postAddPartnerDiscount',
		'as' => 'dashboard.add-partner-discount.post'
		])->middleware('can:show-dashboard-admin,App\User');

	Route::post('/dashboard/partner/delete-partner-bonus',[
		'uses' => 'AdminController@postDeletePartnerBonus',
		'as' => 'dashboard.delete-partner-bonus.post'
		])->middleware('can:show-dashboard-admin,App\User');

	Route::post('/dashboard/partner/add-partner-bonus',[
		'uses' => 'AdminController@postAddPartnerBonus',
		'as' => 'dashboard.add-partner-bonus.post'
		])->middleware('can:show-dashboard-admin,App\User');

	Route::post('/dashboard/agent/add',[
		'uses' => 'AdminController@postAddAgent',
		'as' => 'dashboard.add-agent.post'
		])->middleware('can:show-dashboard-admin,App\User');
	/**
	 * ПОКАЗЫВАТЬ ПАНЕЛЬ УПРАВЛЕНИЯ АГЕНТА
	 */	
		Route::get('/dashboard/agent',[
		'uses' => 'AgentController@showDashboard',
		'as' => 'dashboard.agent.show-dashboard.get'
		])->middleware('can:show-dashboard-agent,App\User');
	/**
	 * ПОКАЗЫВАТЬ ПАНЕЛЬ УПРАВЛЕНИЯ БУХГАЛТЕРА
	 */	
		Route::get('/dashboard/accountant',[
		'uses' => 'AccountantController@showDashboard',
		'as' => 'dashboard.accountant.show-dashboard.get'
		])->middleware('can:show-dashboard-accountant,App\User');
	/**
	 * ПОКАЗЫВАТЬ ПАНЕЛЬ УПРАВЛЕНИЯ ПАРТНЕРА
	 */	
	Route::get('/control-panel',[
		'uses' => 'PartnerController@showDashboard',
		'as' => 'dashboard.partner.show-dashboard.get'
		])->middleware('can:show-dashboard-partner,App\User');

	Route::get('/control-panel/create-operation',[
		'uses' => 'PartnerController@getCreateOperation',
		'as' => 'dashboard.partner.create-operation.get'	
		])->middleware('can:show-dashboard-partner,App\User');

	Route::get('/control-panel/show-operations',[
		'uses' => 'PartnerController@getShowOperations',
		'as' => 'dashboard.partner.show-operations.get'	
		])->middleware('can:show-dashboard-partner,App\User');

	Route::post('/control-panel/create-operation',[
		'uses' => 'PartnerController@postCreateOperation',
		'as' => 'dashboard.partner.create-operation.post'
	])->middleware('can:show-dashboard-partner,App\User');

	Route::get('/control-panel/show-operators',[
		'uses' => 'PartnerController@getShowOperatorsList',
		'as' => 'dashboard.partner.show-operators-list.get'	
		])->middleware('can:show-dashboard-partner-admin,App\User');

	Route::post('/control-panel/create-operator',[
		'uses' => 'PartnerController@postCreateOperator',
		'as' => 'dashboard.partner.create-operator.post'
	])->middleware('can:show-dashboard-partner-admin,App\User');

	Route::post('/control-panel/delete-operator',[
		'uses' => 'PartnerController@postDeleteOperator',
		'as' => 'dashboard.partner.delete-operator.post'
	])->middleware('can:show-dashboard-partner-admin,App\User');

	Route::post('/control-panel/edit-operator',[
		'uses' => 'PartnerController@postEditOperator',
		'as' => 'dashboard.partner.edit-operator.post'
	])->middleware('can:show-dashboard-partner-admin,App\User');

	Route::post('/control-panel/edit-operator-password',[
		'uses' => 'PartnerController@postEditOperatorPassword',
		'as' => 'dashboard.partner.edit-operator-password.post'
	])->middleware('can:show-dashboard-partner-admin,App\User');

	Route::get('/control-panel/show-reviews',[
		'uses' => 'PartnerController@getShowReviews',
		'as' => 'dashboard.partner.show-reviews.get'	
		])->middleware('can:show-dashboard-partner,App\User');

	Route::get('/control-panel/billing',[
		'uses' => 'PartnerController@getBillingPage',
		'as' => 'dashboard.partner.billing.get'	
		])->middleware('can:show-dashboard-partner,App\User');

	Route::get('/pdf', 'PartnerController@createTestPDF');
	/**
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 * 
	 */
	/**
	 * ПОКАЗЫВАТЬ ЛИЧНЫЙ КАБИНЕТ
	 */
	Route::get('profile',[
		'uses' => 'UserController@showProfilePage',
		'as' => 'profile.show-profile-page.get'
		]);
	Route::post('profile/leave-review',[
		'uses' =>'UserController@leaveReview',
		'as' => 'profile.leave-review.post'
		]);

	/**
	 * AJAX ЗАПРОСЫ
	 */
	Route::post('/ajax/check_card_and_operations', [ 
		'uses' => 'PartnerController@ajaxCheckCardAndOperations',
   		'as' => 'ajax.check_card_and_operations.post'
    ])->middleware('can:show-dashboard-partner,App\User');
	Route::post('/ajax/search-partner-list', [ 
		'uses' => 'AdminController@ajaxSearchPartnerList',
   		'as' => 'ajax.search-partner-list.post'
    ])->middleware('can:show-dashboard-admin,App\User');
});
Route::get('/logout', 'Auth\LoginController@logout');

Route::get('/home', 'HomeController@index')->name('home');
