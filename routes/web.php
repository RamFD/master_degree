<?php

Auth::routes();

Route::get('/success', 'HomeController@index')->name('home');

Route::get('/', function() {
    return view('index');
});

Route::get('/editor', 'EditorController@index')->middleware('auth');
Route::post('/editor', 'EditorController@saveChanges')->middleware('auth');
Route::post('/editor/getProjectTreeview', 'EditorController@getProjectTreeview')->middleware('auth');
Route::post('/editor/getFile', 'EditorController@getFileContents')->middleware('auth');
Route::post('/editor/createProject', 'EditorController@createProject')->middleware('auth');
Route::post('/editor/saveFileContents', 'EditorController@saveFileContents')->middleware('auth');
Route::post('/editor/createFile', 'EditorController@createFile')->middleware('auth');
Route::post('/editor/runProject', 'EditorController@runProject')->middleware('auth');
Route::post('/editor/trans', 'EditorController@runTrans')->middleware('auth');
Route::post('/editor/cgen', 'EditorController@runCgen')->middleware('auth');
Route::post('/editor/inter', 'EditorController@runInter')->middleware('auth');
Route::post('/editor/cgtopng', 'EditorController@cgToPng')->middleware('auth');
Route::post('/editor/rigtopng', 'EditorController@rigToPng')->middleware('auth');
Route::post('/editor/getPngFile', 'EditorController@getPngFile')->middleware('auth');
Route::post('/editor/importProject', 'EditorController@importProject')->middleware('auth');
