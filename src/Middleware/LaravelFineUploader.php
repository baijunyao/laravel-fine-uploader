<?php

namespace Baijunyao\LaravelFineUploader\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class LaravelFineUploader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        // 获取 response 内容
        $content = $response->getContent();

        // 如果没有 body 标签 则直接返回
        if (false === strripos($content, '</body>')) {
            return $response;
        }

        // 如果没有使用上传插件 则直接返回
        if (false === strripos($content, 'laravel-fine-uploader-tag')) {
            return $response;
        }

        // 正则匹配出数据
        preg_match_all("/<span class=\"laravel-fine-uploader-tag bjy-\w+\" style=\"display: none\">(.+?)<\/span>/", $content, $tag);
        // 没有没有数据则直接返回
        if (empty($tag[1])) {
            return $response;
        }

        $fineUploaderDiv = [];
        $fineUploaderScript = '';

        foreach ($tag[1] as $k => $v) {
            $script = file_get_contents(resource_path('views/vendor/fineUploader/script.blade.php'));
            $array = json_decode($v, true);
            // 定义各项默认值
            $element = empty($array['element']) ? 'bjy'.uniqid() : $array['element'];
            $path = empty($array['path']) ? '' : $array['path'];
            $id = isset($array['id']) ? implode(',', (array)$array['id']) : '';
            $inputName = isset($array['inputName']) ? implode(',', (array)$array['inputName']) : '';

            // div的id 转成 驼峰命名
            $camelElement = camel_case($element);

            // 组合 上传标签的 div
            $fineUploaderDiv[] = "<div id='$element'></div>";
            // 替换 实例化 fineUploader 时的 js
            $scriptSearch = [
                '{{ camel_case($element) }}',
                '{{ $element }}',
                '{{ $path }}',
                '{{ $id }}',
                '{{ $inputName }}',
                "{{ csrf_token() }}",
                "{{ url('fineUploader/upload') }}",
                "{{ url('fineUploader/download') }}",
                "{{ url('fineUploader/destroy') }}",
                "{{ url('fineUploader/detail') }}",
                "{{ asset('statics/fine-uploader/placeholders/not_available-generic.png') }}",
                "{{ asset('statics/fine-uploader/placeholders/waiting-generic.png') }}",
            ];
            $scriptReplace = [
                $camelElement,
                $element,
                $path,
                $id,
                $inputName,
                csrf_token(),
                url('fineUploader/upload'),
                url('fineUploader/download'),
                url('fineUploader/destroy'),
                url('fineUploader/detail'),
                asset('statics/fine-uploader/placeholders/not_available-generic.png'),
                asset('statics/fine-uploader/placeholders/waiting-generic.png'),
            ];
            $fineUploaderScript .= "\r\n" . str_replace($scriptSearch, $scriptReplace, $script);
        }

        // 插入css标签
        $fineUploaderCssPath = asset('statics/fine-uploader/fine-uploader-new.css');
        $fineUploaderCss = <<<php
<link rel="stylesheet" href="$fineUploaderCssPath">
<style>
    .qq-file-info a{
        text-decoration: none;
    }
    .bjy-fine-uploader .qq-file-info {
        overflow: hidden;
        height: 80px;
    }
    .bjy-fine-uploader .bjy-fu-thumbnail{
        width: 20%;
        float: left;
    }
    .bjy-fine-uploader .bjy-fu-thumbnail img{
        width: 80px;
        height: 60px;
    }
    .bjy-fine-uploader .bjy-fu-handle{
        padding-left: 10px;
        width: 80%;
        height: 60px;
        float: left;
    }
    .bjy-fine-uploader .bjy-fu-handle .bjy-fuh-name{
        height: 30px;
    }
    .bjy-fine-uploader .bjy-fu-handle .bjy-fuh-btn{
        height: 30px;
    }
    .bjy-fine-uploader .bjy-fu-handle .bjy-fuh-name span{
        width: 100%;
    }
</style>
</head>
php;


        // 插入js标签
        $fineUploaderJsPath = asset('statics/fine-uploader/fine-uploader.js');
        $fineUploaderTemplate = file_get_contents(resource_path('views/vendor/fineUploader/template.blade.php'));
        $jqueryJsPath = asset('statics/jquery-2.2.4/jquery.min.js');
        $fineUploaderJs = <<<php
<script>
    (function(){
        window.jQuery || document.write('<script src="$jqueryJsPath"><\/script>');
    })();
</script>
<script src="$fineUploaderJsPath"></script>
$fineUploaderTemplate
$fineUploaderScript
</body>
php;

        $seach = array_merge($tag[0], [
            '</head>',
            '</body>'
        ]);

        $subject = array_merge($fineUploaderDiv, [
            $fineUploaderCss,
            $fineUploaderJs
        ]);
        // p($seach);die;

        $content = str_replace($seach, $subject, $content);
        // 更新内容并重置Content-Length
        $response->setContent($content);
        $response->headers->remove('Content-Length');
        return $response;
    }
}
