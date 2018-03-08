<?php


// 路由
Route::prefix('fineUploader')->namespace('Baijunyao\LaravelFineUploader\Controllers')->group(function () {
    // 文件详情
    Route::get('detail', 'FineUploaderController@detail');
    // 上传
    Route::post('upload', 'FineUploaderController@upload');
});
