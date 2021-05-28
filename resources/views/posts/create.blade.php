@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col col-lg-10 offset-lg-1">
                <form action="{{ route('posts.store') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    @if (isset($idea))
                        <input type="hidden" name="idea_id" value="{{ $idea->id }}">
                    @endif

                    <div class="row mt-0">
                        <div class="col">
                            <h3 class="mb-4">Новая задача</h3>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-2">
                            <div class="form-group">
                                <label for="title" class="col-form-label">О чем пишем? *</label>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">
                                <input type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"
                                       name="title" value="{{ old('title') }}" required>

                                @error('title')
                                <small class="invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2">
                            <div class="form-group">
                                <label for="text" class="col-form-label">Тезисы *</label>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">
                                <textarea rows="5" name="text" id="summernote"
                                          class="form-control{{ $errors->has('text') ? ' is-invalid' : '' }}">{{ old('text', isset($idea) ? $idea->text : '') }}</textarea>

                                @error('text')
                                <small class="invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2">
                            <div class="form-group">
                                <label for="project" class="col-form-label">Проект</label>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">
                                <select name="project_id" id="project" class="form-control">
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2">
                            <div class="form-group">
                                <label for="journalist" class="col-form-label">Журналист</label>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">
                                <select name="journalist_id" id="journalist" class="form-control">
                                    <option value="" selected>Без назначения</option>

                                    @foreach ($journalists as $journalist)
                                        @if($journalist->hasProject($currentProject))
                                            <option value="{{ $journalist->id }}">{{ $journalist->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2">
                            <div class="form-group">
                                <label for="post-expire-date-selector" class="col-form-label">Дата истечения *</label>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">
                                <input type="text" id="post-expire-date-selector" name="expires_at"
                                       class="form-control{{ $errors->has('expires_at') ? ' is-invalid' : '' }}"
                                       value="{{ old('expires_at', now()->add('4','hours')->setTimezone(Auth::user()->timezone)->format('d.m.Y H:i')) }}"
                                       required>
                                @error('expires_at')
                                <small class="invalid-feedback">{{ $message }}</small>
                                @else
                                    <small class="form-text text-muted">Дата в формате: дд.мм.гггг чч:мм</small>
                                    @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-2">
                            <label>Вложенные файлы: </label>
                        </div>

                        <div class="col">
                            @include('layouts.dropzone', ['url' => '/api/files', 'files' => $files])
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-2">
                            <div class="form-group">
                                <label for="posting">Размещение</label>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="form-group">

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="posting" id="posting"
                                           value="1">
                                    <label for="posting" class="custom-control-label">Разместить в социальных
                                        сетях</label>
                                </div>
                            </div>


                            <div class="form-group">

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="posting_checked_all"
                                           id="posting_checked_all" value="1">
                                    <label for="posting_checked_all" class="custom-control-label">Выбрать все</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="posting_to_vk"
                                           id="posting_to_vk" value="1">
                                    <label for="posting_to_vk" class="custom-control-label">ВКонтакте</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="posting_to_ok"
                                           id="posting_to_ok" value="1">
                                    <label for="posting_to_ok" class="custom-control-label">Одноклассники</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="posting_to_fb"
                                           id="posting_to_fb" value="1">
                                    <label for="posting_to_fb" class="custom-control-label">Facebook</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="posting_to_ig"
                                           id="posting_to_ig" value="1">
                                    <label for="posting_to_ig" class="custom-control-label">Instagram</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="posting_to_y_dzen"
                                           id="posting_to_y_dzen" value="1">
                                    <label for="posting_to_y_dzen" class="custom-control-label">Я.Дзен </label>
                                </div>

{{--                                <div class="custom-control custom-checkbox">--}}
{{--                                    <input type="checkbox" class="custom-control-input" name="posting_to_y_street"--}}
{{--                                           id="posting_to_y_street" value="1">--}}
{{--                                    <label for="posting_to_y_street" class="custom-control-label">Я.Район</label>--}}
{{--                                </div>--}}

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="posting_to_yt"
                                           id="posting_to_yt" value="1">
                                    <label for="posting_to_yt" class="custom-control-label">Youtube</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="posting_to_tg"
                                           id="posting_to_tg" value="1">
                                    <label for="posting_to_tg" class="custom-control-label">Telegram</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="posting_to_tt"
                                           id="posting_to_tt" value="1">
                                    <label for="posting_to_tt" class="custom-control-label">TikTok</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <textarea rows="5" name="posting_text"
                                          class="form-control">{{ old('posting_text', isset($idea) ? $idea->posting_text : '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-2">
                            <div class="form-group">
                                <label for="commercial_seed">Коммерческие выходы</label>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="form-group">

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="commercial_seed" id="commercial_seed"
                                           value="1">
                                    <label for="commercial_seed" class="custom-control-label">Коммерческие выходы</label>
                                </div>
                            </div>


                            <div class="form-group">

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="commercial_seed_checked_all"
                                           id="commercial_seed_checked_all" value="1">
                                    <label for="commercial_seed_checked_all" class="custom-control-label">Выбрать все</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="commercial_seed_to_vk"
                                           id="commercial_seed_to_vk" value="1">
                                    <label for="commercial_seed_to_vk" class="custom-control-label">ВКонтакте</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="commercial_seed_to_ok"
                                           id="commercial_seed_to_ok" value="1">
                                    <label for="commercial_seed_to_ok" class="custom-control-label">Одноклассники</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="commercial_seed_to_fb"
                                           id="commercial_seed_to_fb" value="1">
                                    <label for="commercial_seed_to_fb" class="custom-control-label">Facebook</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="commercial_seed_to_ig"
                                           id="commercial_seed_to_ig" value="1">
                                    <label for="commercial_seed_to_ig" class="custom-control-label">Instagram</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="commercial_seed_to_y_dzen"
                                           id="commercial_seed_to_y_dzen" value="1">
                                    <label for="commercial_seed_to_y_dzen" class="custom-control-label">Я.Дзен </label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="commercial_seed_to_yt"
                                           id="commercial_seed_to_yt" value="1">
                                    <label for="commercial_seed_to_yt" class="custom-control-label">Youtube</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="commercial_seed_to_tg"
                                           id="commercial_seed_to_tg" value="1">
                                    <label for="commercial_seed_to_tg" class="custom-control-label">Telegram</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="commercial_seed_to_tt"
                                           id="commercial_seed_to_tt" value="1">
                                    <label for="commercial_seed_to_tt" class="custom-control-label">TikTok</label>
                                </div>


                            </div>
                        </div>

                        <div class="col-4">
                            <div class="form-group">
                                <textarea rows="5" name="commercial_seed_text"
                                          class="form-control">{{ old('commercial_seed_text', isset($idea) ? $idea->commercial_seed : '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <hr>



{{--                    <div class="row">--}}
{{--                        <div class="col-2">--}}
{{--                            <div class="form-group">--}}
{{--                                <label for="commercial_seed">Коммерческие посевы</label>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="col-4">--}}
{{--                            <div class="form-group">--}}

{{--                                <div class="custom-control custom-checkbox">--}}
{{--                                    <input type="checkbox" class="custom-control-input" name="commercial_seed" id="commercial_seed"--}}
{{--                                           value="1">--}}
{{--                                    <label for="commercial_seed" class="custom-control-label">Коммерческие посевы</label>--}}
{{--                                </div>--}}
{{--                            </div>--}}


{{--                                <div class="form-group">--}}
{{--                                    @foreach($socialNetworksSeed as $socialNetworkSeed)--}}

{{--                                        <div class="custom-control custom-checkbox"--}}
{{--                                             style="display: flex;align-items: center;justify-content: space-between;margin-bottom: 10px;">--}}
{{--                                            <input type="checkbox" class="custom-control-input"--}}
{{--                                                   name="commercial_seed_to[{{ $socialNetworkSeed->id }}]"--}}
{{--                                                   id="commercial_seed_to_{{ $socialNetworkSeed->id }}" value="{{ $socialNetworkSeed->id }}"--}}
{{--                                                   data-social-id="{{ $socialNetworkSeed->id }}">--}}
{{--                                            <label for="commercial_seed_to_{{ $socialNetworkSeed->id }}"--}}
{{--                                                   class="custom-control-label">{{ $socialNetworkSeed->name }}</label>--}}
{{--                                        </div>--}}
{{--                                    @endforeach--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        <div class="col-4">--}}
{{--                            <div class="form-group">--}}
{{--                                <textarea rows="5" name="posting_text"--}}
{{--                                          class="form-control">{{ old('posting_text', isset($idea) ? $idea->posting_text : '') }}</textarea>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <hr>--}}

                    <div class="row">
                        <div class="col-2">
                            <div class="form-group">
                                <label for="targeting">Таргет</label>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="form-group">

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="targeting" id="targeting"
                                           value="1">
                                    <label for="targeting" class="custom-control-label">Таргетированная реклама</label>
                                </div>
                            </div>

{{--                            <div class="form-group">--}}
{{--                                <div class="custom-control custom-checkbox">--}}
{{--                                    <input type="checkbox" class="custom-control-input" name="posting_checked_all"--}}
{{--                                           id="posting_checked_all" value="1">--}}
{{--                                    <label for="posting_checked_all" class="custom-control-label">Выбрать все</label>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="form-group">
                                @foreach($socialNetworks as $socialNetwork)
                                    <div class="custom-control custom-checkbox"
                                         style="display: flex;align-items: center;justify-content: space-between;margin-bottom: 10px;">
                                        <input type="checkbox" class="custom-control-input"
                                               name="targeting_to[{{ $socialNetwork->id }}]"
                                               id="targeting_to_{{ $socialNetwork->id }}" value="1"
                                               data-social-id="{{ $socialNetwork->id }}">
                                        <label for="targeting_to_{{ $socialNetwork->id }}"
                                               class="custom-control-label">{{ $socialNetwork->name }}</label>
                                        <input type="number" class="form-control target-input"
                                               name="targeting_to[{{ $socialNetwork->id }}][price]"
                                               data-social-id="{{ $socialNetwork->id }}"
                                               style="width: 100px;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <textarea rows="5" name="targeting_text"
                                          class="form-control">{{ old('targeting_text', isset($idea) ? $idea->targeting_text : '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-2">
                            <div class="form-group">
                                <label for="seeding">Посев</label>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="seeding" id="seeding"
                                           value="1">
                                    <label for="seeding" class="custom-control-label">Посев</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="seeding_checked_all"
                                           id="seeding_checked_all" value="1">
                                    <label for="seeding_checked_all" class="custom-control-label">Выбрать все</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="seeding_to_vk"
                                           id="seeding_to_vk" value="1">
                                    <label for="seeding_to_vk" class="custom-control-label">ВКонтакте</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="seeding_to_ok"
                                           id="seeding_to_ok" value="1">
                                    <label for="seeding_to_ok" class="custom-control-label">Одноклассники</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="seeding_to_fb"
                                           id="seeding_to_fb" value="1">
                                    <label for="seeding_to_fb" class="custom-control-label">Facebook</label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="seeding_to_insta"
                                           id="seeding_to_insta" value="1">
                                    <label for="seeding_to_insta" class="custom-control-label">Instagram</label>
                                </div>


                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="seeding_to_y_dzen"
                                           id="seeding_to_y_dzen" value="1">
                                    <label for="seeding_to_y_dzen" class="custom-control-label">Я.Дзен</label>
                                </div>


{{--                                <div class="custom-control custom-checkbox">--}}
{{--                                    <input type="checkbox" class="custom-control-input" name="seeding_to_y_street"--}}
{{--                                           id="seeding_to_y_street" value="1">--}}
{{--                                    <label for="seeding_to_y_street" class="custom-control-label">Я.Район</label>--}}
{{--                                </div>--}}


                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="seeding_to_yt"
                                           id="seeding_to_yt" value="1">
                                    <label for="seeding_to_yt" class="custom-control-label">Youtube</label>
                                </div>


                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="seeding_to_tg"
                                           id="seeding_to_tg" value="1">
                                    <label for="seeding_to_tg" class="custom-control-label">Telegram</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <textarea rows="5" name="seeding_text"
                                          class="form-control">{{ old('seeding_text', isset($idea) ? $idea->seeding_text : '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-2">
                            <div class="form-group">
                                <label for="commenting">Комментирование</label>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="commenting"
                                           id="commenting" value="1">
                                    <label for="commenting" class="custom-control-label">Комментирование</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <textarea rows="5" name="commenting_text"
                                          class="form-control">{{ old('commenting_text', isset($idea) ? $idea->commenting_text : '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col offset-2">
                            <button type="submit" class="btn btn-primary">Сохранить</button>


                            <a href="{{ route('posts.index') }}" class="btn btn-link">Отмена</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
