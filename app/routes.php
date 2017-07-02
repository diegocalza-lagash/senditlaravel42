<?php
error_reporting(E_ALL);
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
 /*Route::get('/', function()
    {
        return View::make('login');
    });*/

Route::resource('data','ReportSeguimientoController');
Route::controller('dataform','ReportSeguimientoController');//para el Getindex




Route::get('trabajos/show', 'ReportSeguimientoController@showTrabajos');

//Route::get('list-works', 'DataSendController@showWorks');
Route::resource('excel','ExcelController');
// Nos mostrará el formulario de login.

//Route::get('/', array('as' => 'home', function () { }));
//Route::get('login', array('as' => 'login', function () { }))->before('guest');

Route::get('login', 'AuthController@showLogin');
// Validamos los datos de inicio de sesión.
Route::post('login', 'AuthController@postLogin');
//Route::post('login', ['uses' => 'AuthController@postLogin', 'before' => 'guest']);
Route::get('logout', 'AuthController@logOut');
//Route::get('/logout', ['uses' => 'AuthController@logOut', 'before' => 'auth']);


Route::get('/dataform', array('before' => 'auth', function(){
    return View::make('ReportSeguimiento.index');
}));
Route::get('/', array('as' => 'home', function(){
    return View::make('ReportSeguimiento.index');
}))->before('auth');
//for download excel
Route::get('/download','HomeController@getDownload');


//**RUTAS REPORT TECHNIQUE para agregar una nueva ruta a la Resource se debe declarar antes del Resource**//
Route::get('/report_tech/excel/{id}', 'ReportTechController@exportarToExcel');
Route::resource('report_tech','ReportTechController');
Route::controller('report_tech','ReportTechController');
Route::get('/report_tech', 'ReportTechController@getIndex');

//Download a PDF
Route::get('/download/pdf/{id_request}','PdfController@exportarToPdf');
//Download a Excel
Route::get('/download/excel/{id_request}','ExcelController@exportarToExcel');
Route::get('/download/excel','ExcelController@exportToExcel');
