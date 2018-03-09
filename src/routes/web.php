<?php


// 路由
Route::prefix('fineUploader')
    ->namespace('Baijunyao\LaravelFineUploader\Controllers')
    ->middleware(['web', 'auth'])
    ->group(function () {
    // 文件详情
    Route::get('detail', 'FineUploaderController@detail');
    // 上传
    Route::post('upload', 'FineUploaderController@upload');
    // 下载
    Route::get('download', 'FineUploaderController@download');
    // 删除
    Route::match(['post', 'delete'], 'destroy', 'FineUploaderController@destroy');
});
