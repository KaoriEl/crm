
{{-- File drop --}}
<form action="/api/posts/{{ $post->id }}/files" class="mt-3 post-file-dropzone dropzone"></form>

{{-- Список файлов --}}
<ul class="list-group post-files-container dropzone-previews my-3">
    <li class="list-group-item dz-file-preview">

        <div class="media align-items-center dz-details">
            
            <div class="media-body d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <ul class="avatars">
                        <li>
                            <div class="avatar bg-primary dz-file-representation">
                                <span class="mdi mdi-paperclip mdi-18px"></span>
                            </div>
                        </li>
                    </ul>
                                
                    <div class="ml-3 dz-file-details">
                        <a href="#" class="dz-file-link" target="_blank" title="Открыть в новой вкладке"><span data-dz-name class="dz-name"></span></a>
                        <br>
                        <span class="text-muted dz-size"><span data-dz-size></span></span>
                    </div>

                    <img src="{{ asset('img/loader.svg') }}" alt="Loader" class="dz-loading">
                </div>

                <div>
                    <a href="#" class="dz-remove btn btn-danger" data-dz-remove>Отмена</a>

                    <div class="dropdown">
                        <button class="text-muted btn-options" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mdi mdi-dots-vertical mdi-24px"></span>
                        </button>
                        
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="#" class="dropdown-item" data-dz-remove>Удалить</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="progress dz-progress">
            <div class="progress-bar dz-upload" data-dz-uploadprogress></div>
        </div>
    </li>
</ul>
