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

Auth::routes();
Route::get('/conta-inativa', 'UserController@conta_inativa');
Route::get('/ajax/veiculos/marcas', 'VeiculoController@getMarcas');
Route::get('/ajax/veiculos/modelos', 'VeiculoController@getModelos');
Route::get('/ajax/veiculos/versoes', 'VeiculoController@getVersoes');
Route::get('/anuncios', 'AnuncioController@anuncios')->name('anuncios');
Route::get('/{tipo}/{marca}/{modelo}/{versao}/{titulo}/{ano}/{blindado}/{id}', 'AnuncioController@view')->middleware('anuncio');
Route::get('/fale-conosco', 'ContatoController@index')->name('fale_conosco');
Route::post('/fale-conosco', 'ContatoController@store')->name('fale_conosco_post');
Route::get('/como-comprar-carro', 'UserController@duvida_comprar_carro')->name('duvida_comprar_carro');
Route::get('/como-vender-carro', 'UserController@duvida_vender_carro')->name('duvida_vender_carro');
Route::get('/duvidas-anuncios', 'UserController@duvida_anuncios')->name('duvida_anuncios');
Route::get('/', 'HomeController@index');
Route::get('/cronjob/update/all', 'VeiculoController@importMarcaModelos');
Route::post('/anuncio/contato', 'ContatoAnuncioController@store')->name('contato_anuncio');
Route::get('/telefone/{nome}/{cidade}/{id}', 'RevendaController@homepage');
Route::get('/telefone/{nome}/{cidade}/{id}/videos', 'VideoController@index');
Route::get('/consulta-tabela-fipe', 'FipeController@index')->name('fipe');
Route::get('/encontre-uma-revenda', 'RevendaController@revendas')->name('revendas');
Route::get('/faq', 'UserController@faq')->name('faq');
Route::get('/cron/anuncios', 'RevendaController@importAll');
Route::get('/termos-de-uso', 'UserController@termos_uso')->name('termos_uso');
Route::get('/anuncio-inativo/{id}', 'AnuncioController@inativo')->name('anuncio_inativo');
Route::get('/revenda-inativa/{id}', 'RevendaController@inativo')->name('revenda_inativa');
Route::post('/anuncios/count/visualizacao/', 'VisualizacaoDadosController@store');
Route::post('/newsletter/fipe', 'NewsletterUserController@store');
Route::post('/pagseguro/notification/transaction/', 'TransactionController@transactionNotification')->name('notification_pagseguro');

Route::middleware(['auth', 'confirm_account'])->group(function () {
  Route::get('/telefone/{nome}/{cidade}/{id}/videos/adicionar', 'VideoController@create');
  Route::post('/videos/store', 'VideoController@store')->name('revenda_add_video');
  Route::post('/anuncios/{id}/desabilitar', 'AnuncioController@desabilitar')->name('desabilitar_anuncio');
  Route::get('/minha-conta/configuracoes', 'UserController@configuracoes')->name('configuracoes_conta');
  Route::post('/pagseguro/startSession', 'PagseguroController@startSession')->name('start_session');
  Route::get('/anuncie', 'AnuncioController@anuncie')->name('anuncie')->middleware('documento');
  Route::get('/minha-conta', 'UserController@profile')->name('minhaconta');
  Route::get('/minha-conta/meus-anuncios', 'UserController@meus_anuncios')->name('meusanuncios');
  Route::post('/anuncios/store', 'AnuncioController@anuncieStore')->name('anuncieStore');
  Route::post('/imagens/store', 'ImagemController@imageUpload');
  Route::get('/revenda/{id}/configuracoes', 'RevendaController@config');
  Route::post('/revenda/{id}/update', 'RevendaController@update')->name('update_revenda');
  Route::post('/cadastrar-endereco', 'UserController@cadastrarEndereco');
  Route::get('/revenda/rel/chartjs', 'RevendaController@viewsByMonth')->name('rel_chart_mes');
  Route::post('/atualizar-dados/{id}', 'UserController@update')->name('atualizar_conta');
  Route::get('/anuncios/{id}/editar', 'AnuncioController@edit')->middleware('is_my_anuncio');
  Route::get('/videos/{id}/revenda', 'VideoController@videos');
  Route::post('/videos/delete', 'VideoController@delete');
  Route::post('/anuncios/{id}/update', 'AnuncioController@update')->name('update_anuncio');
});
Route::get('/confirmacao-email/{token}', 'UserController@confirmAccount');
Route::post('/reenviar-confirmacao', 'UserController@reenviarConfirmacao')->middleware('auth');

Route::get('/importxml', 'VeiculoController@updateVeiculos');
Route::middleware(['auth','admin', 'confirm_account'])->group(function(){
  Route::get('/admin/fale-conosco', 'ContatoController@admin')->name('fale_conosco_admin');
  Route::get('/admin/contatos', 'ContatoAnuncioController@index')->name('contatos_anuncios');
  Route::post('/admin/anuncios/desabilitar', 'AnuncioController@changeStatus')->name('anuncio_change_status');
  Route::post('/admin/revendas/desabilitar', 'RevendaController@changeStatus')->name('revenda_change_status');
  Route::get('/admin/anuncios/', 'AnuncioController@admin')->name('anuncios_adm');
  Route::post('/admin/option/update', 'OptionController@update')->name('option_update');
  Route::get('/admin/pagseguro', 'PagseguroController@admin')->name('pagseguro_config');
  Route::get('/contratar-revenda', 'RevendaController@sejarevendedor')->name('contratar_revenda');
  Route::post('/revendas/store', 'RevendaController@store')->name('store_revenda');
  Route::get('/admin/revenda', 'RevendaController@admin');
  Route::post('/admin/revenda/import', 'RevendaController@importRevendas');
  Route::get('/admin', 'UserController@admin')->name('admin');
  Route::get('/admin/tables', 'UserController@tables');
  Route::get('/admin/form', 'UserController@form');
  Route::get('/admin/configuracoes', 'ConfigController@index')->name('configuracoes');
  Route::resource('/admin/marcas', 'MarcaController');
  Route::resource('/admin/modelos', 'ModelosController');
  Route::resource('/admin/versoes', 'VersaoController');
  Route::resource('/admin/anuncioFields', 'AnuncioFieldController');
  Route::resource('/admin/planos', 'PlanoController');
  Route::resource('/admin/users', 'UserController');
  Route::resource('/admin/revendas', 'RevendaController');
  Route::resource('/admin/newsletterUsers', 'NewsletterUserController');
  Route::resource('/admin/transactions', 'TransactionController');
  Route::resource('/admin/transactionItems', 'TransactionItemController');
});

Route::get('auth/{provider}', 'AuthController@redirectToProvider');
Route::get('auth/{provider}/callback', 'AuthController@handleProviderCallback');
