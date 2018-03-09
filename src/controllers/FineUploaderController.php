<?php

namespace Baijunyao\LaravelFineUploader\Controllers;

use File;
use Ramsey\Uuid\Uuid;
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

    /**
     * 上传文件
     *
     * @param Request          $request
     * @param FineUploaderFile $fineUploaderFileModel
     *
     * @return mixed
     */
    public function upload(Request $request, FineUploaderFile $fineUploaderFileModel)
    {
        // 判断请求中是否包含name=file的上传文件
        if (! $request->hasFile('file')) {
            return response_json(401, '没有要上传的文件');
        }
        $path = $request->input('path', '');
        $path = empty($path) ? config('fineUploader.default_path') : 'uploads/'.$path;
        // 上传文件
        $result = upload('file', $path);
        if ($result['status_code'] !== 200) {
            return response_json(500, '文件上传失败');
        }
        // 选中多个文件会分开顺序上传 每次一次 所以可以直接取第一个即可
        $fileUploaderFileData = current($result['data']);
        $fileUploaderFileData ['id'] = Uuid::uuid4();
        $id = $fineUploaderFileModel->create($fileUploaderFileData)->id->toString();
        $data = [
            'success' => true,
            'uuid' => $id
        ];
        return response_json(200, $data);
    }

    /**
     * 删除文件   暂时先不删除上传到服务器上的文件
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $data = [
            'success' => true,
            'uuid' => $request->input('id')
        ];
        return response_json(200, $data);
    }

    /**
     * 下载文件
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(Request $request)
    {
        $id = $request->input('id');
        $data = FineUploaderFile::find($id);
        return response()->download(public_path($data->path), $data->name);
    }
}
