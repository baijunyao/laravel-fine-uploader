<script>
    var {{ camel_case($element) }}Obj = new qq.FineUploader({
        element: document.getElementById("{{ $element }}"),
        template: 'qq-template-manual-trigger',
        request: {
            customHeaders: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            endpoint: '{{ url('fineUploader/upload') }}',
            inputName: 'file',
            params: {
                'path': '{{ $path }}'
            }
        },
        callbacks: {
            // 文件上传成功后的回调函数
            onComplete: function(id, name, responseJSON) {
                // 文件上传成功后添加一个附带文件id的隐藏域
                var inputStr = '<input type="hidden" name="{{ $inputName }}['+responseJSON.uuid+']" value="'+responseJSON.uuid+'" />'
                var href = $('.js-download').attr('href');
                var new_href = href+'id='+responseJSON.uuid;
                $('.js-download').attr('href',new_href);
                $('#{{ $element }}').append(inputStr);

                // 设置删除时候的url和参数
                {{ camel_case($element) }}Obj.setDeleteFileEndpoint('{{ url('fineUploader/destroy') }}');
                var deleteParame = {
                    'id' : responseJSON.uuid
                }
                {{ camel_case($element) }}Obj.setDeleteFileParams(deleteParame, id);
            },

            // 删除文件后的回调函数
            onDeleteComplete: function (id, response) {
                var responseJSON = $.parseJSON(response.response);
                console.log("input[name='{{ $inputName }}["+responseJSON.uuid+"]']");
                // 删除文件后把隐藏域删掉
                $("input[name='{{ $inputName }}["+responseJSON.uuid+"]']").remove();
            },

            // 获取文件列表后的回调函数
            onSessionRequestComplete: function (file) {
                var downloadUrl = '{{ url('fineUploader/download') }}';
                $.each(file, function (index, val) {
                    var inputStr = '<input type="hidden" name="{{ $inputName }}['+val.uuid+']" value="'+val.uuid+'">';
                    console.log(val);
                    $('.qq-file-info').eq(index).find('.js-download').attr('href', downloadUrl+'?id='+val.uuid);
                    $('#{{ $element }}').append(inputStr);
                    {{ camel_case($element) }}Obj.setDeleteFileEndpoint('{{ url('fineUploader/destroy') }}');
                    var deleteParame = {
                        'id' : val.uuid
                    }
                    {{ camel_case($element) }}Obj.setDeleteFileParams(deleteParame, index);
                })
            },
            addInitialFiles: function (file) {
                console.log(file);
            }
        },
        deleteFile: {
            endpoint: '{{ url('fineUploader/destroy') }}',
            customHeaders: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            enabled: true,
            method: 'POST',
        },
        thumbnails: {
            // 缩略图
            placeholders: {
                notAvailablePath: "{{ asset('statics/fine-uploader/placeholders/not_available-generic.png') }}",
                waitingPath: "{{ asset('statics/fine-uploader/placeholders/waiting-generic.png') }}"
            }
        },
        session: {
            endpoint: '{{ url('fineUploader/detail') }}',
            params: {
                'id': "{{ $id }}"
            }
        }
    })
</script>