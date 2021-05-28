@php /** @var $post \App\Models\Post */ @endphp
@if ($post->targeting && Auth::user()->hasAnyRole(['editor', 'targeter']))
    <div class="row">
        <div class="col-12">

            <h5 class="mb-3">Таргетированная реклама</h5>
            @if ($post->targeting_text)
                <label>Комментарий постановщика:</label>
                <textarea class="form-control" disabled>{{ $post->targeting_text }}</textarea>
                <p></p>
            @endif

            @foreach($post->socialNetworks as $socialNetwork)
                <li class="list-group-item d-flex align-items-center">
                    <img src="{{ asset($socialNetwork->icon) }}" alt="{{ $socialNetwork->name }}" width="64" height="64">

                    <div class="ml-2">
                        <div class="custom-control custom-checkbox">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="" name="targeted_to[{{ $socialNetwork->id }}][status]" id="targeted_to_{{ $socialNetwork->id }}_0" {{ old("targeted_to.{$socialNetwork->id}.status", $socialNetwork->pivot->status) === null ? ' checked' : '' }}>
                                <label class="form-check-label" for="targeted_to_{{ $socialNetwork->id }}_0">
                                    Работы еще не проводились
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="{{ \App\Enums\PostTargetStatusesEnum::SENT_FOR_MODERATION_STATUS }}" name="targeted_to[{{ $socialNetwork->id }}][status]" id="targeted_to_{{ $socialNetwork->id }}_1" {{ old("targeted_to.{$socialNetwork->id}.status", $socialNetwork->pivot->status) === \App\Enums\PostTargetStatusesEnum::SENT_FOR_MODERATION_STATUS ? ' checked' : '' }}>
                                <label class="form-check-label" for="targeted_to_{{ $socialNetwork->id }}_1">
                                    {{ \App\Enums\PostTargetStatusesEnum::getStatusName(\App\Enums\PostTargetStatusesEnum::SENT_FOR_MODERATION_STATUS) }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="{{ \App\Enums\PostTargetStatusesEnum::SUCCESSFUL_MODERATED_STATUS }}" name="targeted_to[{{ $socialNetwork->id }}][status]" id="targeted_to_{{ $socialNetwork->id }}_2" {{ old("targeted_to.{$socialNetwork->id}.status", $socialNetwork->pivot->status) === \App\Enums\PostTargetStatusesEnum::SUCCESSFUL_MODERATED_STATUS ? ' checked' : '' }}>
                                <label class="form-check-label" for="targeted_to_{{ $socialNetwork->id }}_2">
                                    {{ \App\Enums\PostTargetStatusesEnum::getStatusName(\App\Enums\PostTargetStatusesEnum::SUCCESSFUL_MODERATED_STATUS) }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="{{ \App\Enums\PostTargetStatusesEnum::NOT_SUCCESSFUL_MODERATED_STATUS }}" name="targeted_to[{{ $socialNetwork->id }}][status]" id="targeted_to_{{ $socialNetwork->id }}_3" {{ old("targeted_to.{$socialNetwork->id}.status", $socialNetwork->pivot->status) === \App\Enums\PostTargetStatusesEnum::NOT_SUCCESSFUL_MODERATED_STATUS ? ' checked' : '' }}>
                                <label class="form-check-label" for="targeted_to_{{ $socialNetwork->id }}_3">
                                    {{ \App\Enums\PostTargetStatusesEnum::getStatusName(\App\Enums\PostTargetStatusesEnum::NOT_SUCCESSFUL_MODERATED_STATUS) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
            <ul class="list-group">

                @if ($post->targeting_to_vk)
                    <li class="list-group-item d-flex align-items-center">
                        <img src="{{ asset('img/vk-medium.png') }}" alt="ВКонтакте" width="64" height="64">

                        <div class="ml-2">
                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="targeted_to_vk" value="0">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       name="targeted_to_vk"
                                       id="targeted_to_vk"
                                       value="1"{{ old('targeted_to_vk', $post->targeted_to_vk) ? ' checked' : '' }}>
                                <label class="custom-control-label" for="targeted_to_vk">Отправлено на модерацию</label>
                            </div>
                            @if ($post->targeted_to_vk)
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="target_not_pass_moderation_in_vk" value="0">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="target_not_pass_moderation_in_vk"
                                           id="target_not_pass_moderation_in_vk"
                                           value="1"{{ old('target_not_pass_moderation_in_vk', $post->target_not_pass_moderation_in_vk) ? ' checked' : '' }}>
                                    <label class="custom-control-label" for="target_not_pass_moderation_in_vk">Не прошло модерацию</label>
                                </div>
                            @endif
                            @if ($post->targeted_to_vk)
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="target_launched_in_vk" value="0">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="target_launched_in_vk"
                                           id="target_launched_in_vk"
                                           value="1"{{ old('target_launched_in_vk', $post->target_launched_in_vk) ? ' checked' : '' }}>
                                    <label class="custom-control-label" for="target_launched_in_vk">Запущено</label>
                                </div>
                            @endif
                        </div>
                    </li>
                @endif

                @if ($post->targeting_to_ok)
                    <li class="list-group-item d-flex align-items-center">
                        <img src="{{ asset('img/ok-medium.png') }}" alt="Одноклассники" width="64" height="64">

                        <div class="ml-2">
                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="targeted_to_ok" value="0">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       name="targeted_to_ok"
                                       id="targeted_to_ok"
                                       value="1"{{ old('targeted_to_ok', $post->targeted_to_ok) ? ' checked' : '' }}>
                                <label class="custom-control-label" for="targeted_to_ok">Отправлено на модерацию</label>
                            </div>
                            @if ($post->targeted_to_ok)
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="target_not_pass_moderation_in_ok" value="0">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="target_not_pass_moderation_in_ok"
                                           id="target_not_pass_moderation_in_ok"
                                           value="1"{{ old('target_not_pass_moderation_in_vk', $post->target_not_pass_moderation_in_ok) ? ' checked' : '' }}>
                                    <label class="custom-control-label" for="target_not_pass_moderation_in_ok">Не прошло модерацию</label>
                                </div>
                            @endif
                            @if ($post->targeted_to_ok)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="target_launched_in_ok"
                                           id="target_launched_in_ok"
                                           value="1"{{ old('target_launched_in_ok', $post->target_launched_in_ok) ? ' checked' : '' }}>
                                    <label class="custom-control-label" for="target_launched_in_ok">Запущено</label>
                                </div>
                            @endif
                        </div>
                    </li>
                @endif

                @if ($post->targeting_to_fb)
                    <li class="list-group-item d-flex align-items-center">
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="64" height="64" viewBox="0 0 408.788 408.788" style="enable-background:new 0 0 408.788 408.788;" xml:space="preserve">
                            <path style="fill:#475993;" d="M353.701,0H55.087C24.665,0,0.002,24.662,0.002,55.085v298.616c0,30.423,24.662,55.085,55.085,55.085 h147.275l0.251-146.078h-37.951c-4.932,0-8.935-3.988-8.954-8.92l-0.182-47.087c-0.019-4.959,3.996-8.989,8.955-8.989h37.882 v-45.498c0-52.8,32.247-81.55,79.348-81.55h38.65c4.945,0,8.955,4.009,8.955,8.955v39.704c0,4.944-4.007,8.952-8.95,8.955 l-23.719,0.011c-25.615,0-30.575,12.172-30.575,30.035v39.389h56.285c5.363,0,9.524,4.683,8.892,10.009l-5.581,47.087 c-0.534,4.506-4.355,7.901-8.892,7.901h-50.453l-0.251,146.078h87.631c30.422,0,55.084-24.662,55.084-55.084V55.085 C408.786,24.662,384.124,0,353.701,0z"/>
                        </svg>

                        <div class="ml-2">
                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="targeted_to_fb" value="0">
                                <input type="checkbox" class="custom-control-input"
                                       name="targeted_to_fb"
                                       id="targeted_to_fb"
                                       value="1"{{ old('targeted_to_fb', $post->targeted_to_fb) ? ' checked' : '' }}>
                                <label for="targeted_to_fb"
                                       class="custom-control-label">Отправлено на модерацию</label>
                            </div>
                            @if ($post->targeted_to_fb)
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="target_not_pass_moderation_in_fb" value="0">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="target_not_pass_moderation_in_fb"
                                           id="target_not_pass_moderation_in_fb"
                                           value="1"{{ old('target_not_pass_moderation_in_fb', $post->target_not_pass_moderation_in_fb) ? ' checked' : '' }}>
                                    <label class="custom-control-label" for="target_not_pass_moderation_in_fb">Не прошло модерацию</label>
                                </div>
                            @endif
                            @if ($post->targeted_to_fb)
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="target_launched_in_fb" value="0">
                                    <input type="checkbox" class="custom-control-input"
                                           name="target_launched_in_fb"
                                           id="target_launched_in_fb"
                                           value="1"{{ old('target_launched_in_fb', $post->target_launched_in_fb) ? ' checked' : '' }}>
                                    <label for="target_launched_in_fb"
                                           class="custom-control-label">Запущено</label>
                                </div>
                            @endif
                        </div>
                    </li>
                @endif

                @if ($post->targeting_to_ig)
                    <li class="list-group-item d-flex align-items-center">
                        <svg enable-background="new 0 0 24 24" height="64" viewBox="0 0 24 24" width="64" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><linearGradient id="SVGID_1_" gradientTransform="matrix(0 -1.982 -1.844 0 -132.522 -51.077)" gradientUnits="userSpaceOnUse" x1="-37.106" x2="-26.555" y1="-72.705" y2="-84.047"><stop offset="0" stop-color="#fd5"/><stop offset=".5" stop-color="#ff543e"/><stop offset="1" stop-color="#c837ab"/></linearGradient><path d="m1.5 1.633c-1.886 1.959-1.5 4.04-1.5 10.362 0 5.25-.916 10.513 3.878 11.752 1.497.385 14.761.385 16.256-.002 1.996-.515 3.62-2.134 3.842-4.957.031-.394.031-13.185-.001-13.587-.236-3.007-2.087-4.74-4.526-5.091-.559-.081-.671-.105-3.539-.11-10.173.005-12.403-.448-14.41 1.633z" fill="url(#SVGID_1_)"/><path d="m11.998 3.139c-3.631 0-7.079-.323-8.396 3.057-.544 1.396-.465 3.209-.465 5.805 0 2.278-.073 4.419.465 5.804 1.314 3.382 4.79 3.058 8.394 3.058 3.477 0 7.062.362 8.395-3.058.545-1.41.465-3.196.465-5.804 0-3.462.191-5.697-1.488-7.375-1.7-1.7-3.999-1.487-7.374-1.487zm-.794 1.597c7.574-.012 8.538-.854 8.006 10.843-.189 4.137-3.339 3.683-7.211 3.683-7.06 0-7.263-.202-7.263-7.265 0-7.145.56-7.257 6.468-7.263zm5.524 1.471c-.587 0-1.063.476-1.063 1.063s.476 1.063 1.063 1.063 1.063-.476 1.063-1.063-.476-1.063-1.063-1.063zm-4.73 1.243c-2.513 0-4.55 2.038-4.55 4.551s2.037 4.55 4.55 4.55 4.549-2.037 4.549-4.55-2.036-4.551-4.549-4.551zm0 1.597c3.905 0 3.91 5.908 0 5.908-3.904 0-3.91-5.908 0-5.908z" fill="#fff"/></svg>

                        <div class="ml-2">
                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="targeted_to_ig" value="0">
                                <input type="checkbox" class="custom-control-input"
                                       name="targeted_to_ig"
                                       id="targeted_to_ig"
                                       value="1"{{ old('targeted_to_ig', $post->targeted_to_ig) ? ' checked' : '' }}>
                                <label for="targeted_to_ig"
                                       class="custom-control-label">Отправлено на модерацию</label>
                            </div>
                            @if ($post->targeted_to_ig)
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="target_not_pass_moderation_in_ig" value="0">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="target_not_pass_moderation_in_ig"
                                           id="target_not_pass_moderation_in_ig"
                                           value="1"{{ old('target_not_pass_moderation_in_ig', $post->target_not_pass_moderation_in_ig) ? ' checked' : '' }}>
                                    <label class="custom-control-label" for="target_not_pass_moderation_in_ig">Не прошло модерацию</label>
                                </div>
                            @endif
                            @if ($post->targeted_to_ig)
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="target_launched_in_ig" value="0">
                                    <input type="checkbox" class="custom-control-input"
                                           name="target_launched_in_ig"
                                           id="target_launched_in_ig"
                                           value="1"{{ old('target_launched_in_ig', $post->target_launched_in_ig) ? ' checked' : '' }}>
                                    <label for="target_launched_in_ig"
                                           class="custom-control-label">Запущено</label>
                                </div>
                            @endif

                        </div>
                    </li>
                @endif


                    @if ($post->targeting_to_y_dzen)
                        <li class="list-group-item d-flex align-items-center">
                            <img src="{{ asset('img/dzen-medium.png') }}" alt="ВКонтакте" width="64" height="64">

                            <div class="ml-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="targeted_to_y_dzen" value="0">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="targeted_to_y_dzen"
                                           id="targeted_to_y_dzen"
                                           value="1"{{ old('targeted_to_y_dzen', $post->targeted_to_y_dzen) ? ' checked' : '' }}>
                                    <label class="custom-control-label" for="targeted_to_y_dzen">Отправлено на модерацию</label>
                                </div>
                                @if ($post->targeted_to_y_dzen)
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="target_not_pass_moderation_in_y_dzen" value="0">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="target_not_pass_moderation_in_y_dzen"
                                               id="target_not_pass_moderation_in_y_dzen"
                                               value="1"{{ old('target_not_pass_moderation_in_y_dzen', $post->target_not_pass_moderation_in_y_dzen) ? ' checked' : '' }}>
                                        <label class="custom-control-label" for="target_not_pass_moderation_in_y_dzen">Не прошло модерацию</label>
                                    </div>
                                @endif
                                @if ($post->targeted_to_y_dzen)
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="target_launched_in_y_dzen" value="0">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="target_launched_in_y_dzen"
                                               id="target_launched_in_y_dzen"
                                               value="1"{{ old('target_launched_in_y_dzen', $post->target_launched_in_y_dzen) ? ' checked' : '' }}>
                                        <label class="custom-control-label" for="target_launched_in_y_dzen">Запущено</label>
                                    </div>
                                @endif
                            </div>
                        </li>
                    @endif


                    @if ($post->targeting_to_y_street)
                        <li class="list-group-item d-flex align-items-center">
                            <img src="{{ asset('img/ya-medium.png') }}" alt="ВКонтакте" width="64" height="64">

                            <div class="ml-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="targeted_to_y_street" value="0">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="targeted_to_y_street"
                                           id="targeted_to_y_street"
                                           value="1"{{ old('targeted_to_y_street', $post->targeted_to_y_street) ? ' checked' : '' }}>
                                    <label class="custom-control-label" for="targeted_to_y_street">Отправлено на модерацию</label>
                                </div>
                                @if ($post->targeted_to_y_street)
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="target_not_pass_moderation_in_y_street" value="0">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="target_not_pass_moderation_in_y_street"
                                               id="target_not_pass_moderation_in_y_street"
                                               value="1"{{ old('target_not_pass_moderation_in_y_street', $post->target_not_pass_moderation_in_y_street) ? ' checked' : '' }}>
                                        <label class="custom-control-label" for="target_not_pass_moderation_in_y_street">Не прошло модерацию</label>
                                    </div>
                                @endif
                                @if ($post->targeted_to_y_street)
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="target_launched_in_y_street" value="0">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="target_launched_in_y_street"
                                               id="target_launched_in_y_street"
                                               value="1"{{ old('target_launched_in_y_street', $post->target_launched_in_y_street) ? ' checked' : '' }}>
                                        <label class="custom-control-label" for="target_launched_in_y_street">Запущено</label>
                                    </div>
                                @endif
                            </div>
                        </li>
                    @endif


                    @if ($post->targeting_to_yt)
                        <li class="list-group-item d-flex align-items-center">
                            <img src="{{ asset('img/yt-medium.png') }}" alt="ВКонтакте" width="64" height="64">

                            <div class="ml-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="targeted_to_yt" value="0">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="targeted_to_yt"
                                           id="targeted_to_yt"
                                           value="1"{{ old('targeted_to_yt', $post->targeted_to_yt) ? ' checked' : '' }}>
                                    <label class="custom-control-label" for="targeted_to_yt">Отправлено на модерацию</label>
                                </div>
                                @if ($post->targeted_to_yt)
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="target_not_pass_moderation_in_yt" value="0">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="target_not_pass_moderation_in_yt"
                                               id="target_not_pass_moderation_in_yt"
                                               value="1"{{ old('target_not_pass_moderation_in_yt', $post->target_not_pass_moderation_in_yt) ? ' checked' : '' }}>
                                        <label class="custom-control-label" for="target_not_pass_moderation_in_yt">Не прошло модерацию</label>
                                    </div>
                                @endif
                                @if ($post->targeted_to_yt)
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="target_launched_in_yt" value="0">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="target_launched_in_yt"
                                               id="target_launched_in_yt"
                                               value="1"{{ old('target_launched_in_yt', $post->target_launched_in_yt) ? ' checked' : '' }}>
                                        <label class="custom-control-label" for="target_launched_in_yt">Запущено</label>
                                    </div>
                                @endif
                            </div>
                        </li>
                    @endif
                    @if ($post->targeting_to_tg)
                        <li class="list-group-item d-flex align-items-center">
                            <img src="{{ asset('img/tg-medium.png') }}" alt="ВКонтакте" width="64" height="64">

                            <div class="ml-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="targeted_to_tg" value="0">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="targeted_to_tg"
                                           id="targeted_to_tg"
                                           value="1"{{ old('targeted_to_tg', $post->targeted_to_tg) ? ' checked' : '' }}>
                                    <label class="custom-control-label" for="targeted_to_tg">Отправлено на модерацию</label>
                                </div>
                                @if ($post->targeted_to_tg)
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="target_not_pass_moderation_in_tg" value="0">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="target_not_pass_moderation_in_tg"
                                               id="target_not_pass_moderation_in_tg"
                                               value="1"{{ old('target_not_pass_moderation_in_tg', $post->target_not_pass_moderation_in_tg) ? ' checked' : '' }}>
                                        <label class="custom-control-label" for="target_not_pass_moderation_in_tg">Не прошло модерацию</label>
                                    </div>
                                @endif
                                @if ($post->targeted_to_tg)
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="target_launched_in_tg" value="0">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="target_launched_in_tg"
                                               id="target_launched_in_tg"
                                               value="1"{{ old('target_launched_in_tg', $post->target_launched_in_tg) ? ' checked' : '' }}>
                                        <label class="custom-control-label" for="target_launched_in_tg">Запущено</label>
                                    </div>
                                @endif
                            </div>
                        </li>
                    @endif


            </ul>
        </div>

        <hr class="w-100">
    </div>
@endif
