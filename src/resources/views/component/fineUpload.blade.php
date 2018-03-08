@php
    $element = empty($element) ? 'bjy'.uniqid() : $element;
@endphp
<div id="{{ $element }}"></div>

@push('css')
    <link href="{{ asset('statics/fine-uploader/fine-uploader-gallery.css') }}" rel="stylesheet">
@endpush

@push('js')
    <script src="{{ asset('statics/fine-uploader/fine-uploader.js') }}"></script>
    <script type="text/template" id="qq-template">
        <div class="qq-uploader-selector qq-uploader qq-gallery" qq-drop-area-text="可以直接把多个文件拖进来">
            <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
            </div>
            <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                <span class="qq-upload-drop-area-text-selector"></span>
            </div>
            <div class="qq-upload-button-selector qq-upload-button">
                <div>选择文件</div>
            </div>
            <span class="qq-drop-processing-selector qq-drop-processing">
                <span>可以直接把多个文件拖进来</span>
                <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
            </span>
            <ul class="qq-upload-list-selector qq-upload-list" role="region" aria-live="polite" aria-relevant="additions removals">
                <li>
                    <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                    <div class="qq-progress-bar-container-selector qq-progress-bar-container">
                        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                    </div>
                    <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                    <div class="qq-thumbnail-wrapper">
                        <img class="qq-thumbnail-selector" qq-max-size="120" qq-server-scale>
                    </div>
                    <button type="button" class="qq-upload-cancel-selector qq-upload-cancel">X</button>
                    <button type="button" class="qq-upload-retry-selector qq-upload-retry">
                        <span class="qq-btn qq-retry-icon" aria-label="Retry"></span>
                        Retry
                    </button>

                    <div class="qq-file-info">
                        <div class="qq-file-name">
                            <span class="qq-upload-file-selector qq-upload-file"></span>
                            <span class="qq-edit-filename-icon-selector qq-btn qq-edit-filename-icon" aria-label="Edit filename"></span>
                        </div>
                        <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                        <span class="qq-upload-size-selector qq-upload-size"></span>
                        <button type="button" class="qq-btn bjy-down">
                            <a class="fa fa-download" aria-label="download" href=""></a>
                        </button>
                        <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">
                            <span class="qq-btn qq-delete-icon" aria-label="Delete"></span>
                        </button>
                        <button type="button" class="qq-btn qq-upload-pause-selector qq-upload-pause">
                            <span class="qq-btn qq-pause-icon" aria-label="Pause"></span>
                        </button>
                        <button type="button" class="qq-btn qq-upload-continue-selector qq-upload-continue">
                            <span class="qq-btn qq-continue-icon" aria-label="Continue"></span>
                        </button>
                    </div>
                </li>
            </ul>

            <dialog class="qq-alert-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">Close</button>
                </div>
            </dialog>

            <dialog class="qq-confirm-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">No</button>
                    <button type="button" class="qq-ok-button-selector">Yes</button>
                </div>
            </dialog>

            <dialog class="qq-prompt-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <input type="text">
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">Cancel</button>
                    <button type="button" class="qq-ok-button-selector">Ok</button>
                </div>
            </dialog>
        </div>
    </script>
    <script>
        var {{camel_case($element)}}Obj = new qq.FineUploader({
            element: document.getElementById("{{ $element }}"),
            template: 'qq-template',
            request: {
                customHeaders: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                endpoint: '{{ url('fineUploader/upload') }}',
                inputName: 'file',
            },
            callbacks: {
                // 文件上传成功后的回调函数
                onComplete: function(id, name, responseJSON) {
                    // 文件上传成功后添加一个附带文件id的隐藏域
                    var inputStr = '<input type="hidden" name="{{ $inputName }}['+responseJSON.uuid+']" value="'+responseJSON.uuid+'" />'
                    var href = $('.fa-download').attr('href');
                    var new_href = href+'id='+responseJSON.uuid;
                    $('.fa-download').attr('href',new_href);
                    $('#{{ $element }}').append(inputStr);

                    // 设置删除时候的url和参数
                    {{camel_case($element)}}Obj.setDeleteFileEndpoint('{{ url('fineUploader/destroy') }}');
                    var deleteParame = {
                        'id' : responseJSON.uuid
                    }
                    {{camel_case($element)}}Obj.setDeleteFileParams(deleteParame, id);
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
                    $.each(file, function (index, val) {
                        var inputStr = '<input type="hidden" name="{{ $inputName }}['+val.uuid+']" value="'+val.uuid+'">';
                        console.log(val);
                        $('.qq-file-info').eq(index).find('.fa-download').attr('href', val.thumbnailUrl);
                        $('#{{ $element }}').append(inputStr);
                        {{camel_case($element)}}Obj.setDeleteFileEndpoint('{{ url('fineUploader/destroy') }}');
                        var deleteParame = {
                            'id' : val.uuid
                        }
                        {{camel_case($element)}}Obj.setDeleteFileParams(deleteParame, index);
                    })
                },
                addInitialFiles: function (file) {
                    console.log(file);
                }
            },
            deleteFile: {
                endpoint: '{{ url('fineUploader/destroy') }}',
                customHeaders: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                    'id': "@if(is_array($id)){{ implode(',', $id) }}@else{{ $id }}@endif"
                }
            }
        })
    </script>
@endpush