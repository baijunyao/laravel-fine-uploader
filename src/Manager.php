<?php

namespace Baijunyao\LaravelFineUploader;

use Baijunyao\LaravelPluginManager\Contracts\PluginManager;

class Manager extends PluginManager
{
    protected $element = 'laravel-fine-uploader-tag';

    protected function load()
    {
        $content = $this->content;

        // 正则匹配出数据
        preg_match_all("/<span class=\"laravel-fine-uploader-tag bjy-\w+\" style=\"display: none\">(.+?)<\/span>/", $content, $tag);

        // 没有没有数据则直接返回
        if (empty($tag[1])) {
            return false;
        }

        $fineUploaderDiv = [];
        $fineUploaderScript = '';
        $fineUploaderTemplate = file_get_contents(resource_path('views/vendor/laravel-fine-uploader/default.blade.php'));
        $template = [];
        foreach ($tag[1] as $k => $v) {
            $script = file_get_contents(resource_path('views/vendor/laravel-fine-uploader/script.blade.php'));
            $array = json_decode($v, true);
            $templateId = 'qq-template-manual-trigger';
            // 获取所有用到的模板
            if (!empty($array['template']) && $array['template'] != 'default') {
                $template = file_get_contents(resource_path('views/vendor/laravel-fine-uploader/'. $array['template'] .'.blade.php'));
                preg_match_all("/<script.*?id=\"(.*)?\"/", $template, $templateIdArray);
                $templateId = $templateIdArray[1][0];
                $fineUploaderTemplate .= $template;
            }
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
                '{{ $template }}',
                '{{ $path }}',
                '{{ $id }}',
                '{{ $inputName }}',
                "{{ csrf_token() }}",
                "{{ url('fineUploader/upload') }}",
                "{{ url('fineUploader/download') }}",
                "{{ url('fineUploader/destroy') }}",
                "{{ url('fineUploader/detail') }}",
                "{{ asset('statics/laravel-fine-uploader/placeholders/not_available-generic.png') }}",
                "{{ asset('statics/laravel-fine-uploader/placeholders/waiting-generic.png') }}",
            ];
            $scriptReplace = [
                $camelElement,
                $element,
                $templateId,
                $path,
                $id,
                $inputName,
                csrf_token(),
                url('fineUploader/upload'),
                url('fineUploader/download'),
                url('fineUploader/destroy'),
                url('fineUploader/detail'),
                asset('statics/laravel-fine-uploader/placeholders/not_available-generic.png'),
                asset('statics/laravel-fine-uploader/placeholders/waiting-generic.png'),
            ];
            $fineUploaderScript .= "\r\n" . str_replace($scriptSearch, $scriptReplace, $script);
        }

        // css 标签
        $style = file_get_contents(resource_path('views/vendor/laravel-fine-uploader/css.blade.php'));

        $this->cssFile('statics/laravel-fine-uploader/fine-uploader-new.css')
            ->cssFile('statics/laravel-fine-uploader/fine-uploader-gallery.css')
            ->cssContent($style)
            ->jquery()
            ->jsFile('statics/laravel-fine-uploader/fine-uploader.js')
            ->jsContent($fineUploaderTemplate)
            ->jsContent($fineUploaderScript);
        foreach ($fineUploaderDiv as $k => $v) {
            $data = [
                'search' => $tag[0][$k],
                'replace' => $v
            ];
            $this->setReplace($data);
        }

    }


}