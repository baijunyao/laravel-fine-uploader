<?php

namespace Baijunyao\LaravelFineUploader\Controllers;

use File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Baijunyao\LaravelFineUploader\Models\FineUploaderFile;

class FineUploaderController extends Controller
{
    /**
     * 根据 id 获取文件详情
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request)
    {
        $id = $request->input('id');
        $idArray = explode(',', $id);
        $file = FineUploaderFile::whereIn('id', $idArray)->get();
        if ($file->isEmpty()) {
            return response()->json([]);
        }
        $data = [];
        foreach ($file as $k => $v) {
            $data[] = [
                'uuid' => $v->id,
                'name' => $v->name,
                'size' => File::size(public_path($v->path)),
                'thumbnailUrl' => url($v->path)
            ];
        }
        return response()->json($data);
    }
}
