@if ($post->posting && Auth::user()->hasAnyRole(['editor', 'smm']))
    <script src="{{ mix('js/generateLinks.js') }}" defer></script>
    <div class="row">
        <div class="col-12">
            <h5>Размещение в социальных сетях</h5>
            @if ($post->posting_text)
                <label>Комментарий постановщика:</label>
                <textarea class="form-control" disabled>{{ $post->posting_text }}</textarea>
                <p></p>
            @endif

            @if ($post->posting_to_vk)
                <div class="form-group has-icon" id="vk">
                    <label for="vk_post_url">Ссылка на пост в ВКонтакте</label> <input type="button" class="more btn btn-link" value="Добавить ссылку" />
                    @if(isset($arrSMMLinks['vk']))
                    @foreach($arrSMMLinks['vk'] as $link)
                        <div class="d-flex align-items-center">
                            <span class="mdi mdi-vk mdi-18px mr-2"></span>

                            <input type="url" name="smm_links[vk][][link]" id="vk_post_url" class="form-control{{ $errors->has('vk_post_url') ? ' is-invalid' : '' }}" value="{{ $link }}">
                        </div>
                        <br>
                        @error('vk_post_url')
                        <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    @endforeach
                    @else
                        <div class="d-flex align-items-center">
                            <span class="mdi mdi-vk mdi-18px mr-2"></span>

                            <input type="url" name="smm_links[vk][][link]" id="vk_post_url" class="form-control{{ $errors->has('vk_post_url') ? ' is-invalid' : '' }}" value="">
                        </div>
                    @endif
                </div>
            @endif



            @if ($post->posting_to_ok)
                <div class="form-group has-icon" id="ok">
                    <label for="ok_post_url">Ссылка на пост в Одноклассниках</label> <input type="button" class="more btn btn-link" value="Добавить ссылку" />
                    @if(isset($arrSMMLinks['ok']))
                        @foreach($arrSMMLinks['ok'] as $link)
                            <div class="d-flex align-items-center">
                                <span class="mdi mdi-odnoklassniki mdi-18px mr-2"></span>

                                <input type="url" name="smm_links[ok][][link]" id="ok_post_url" class="form-control{{ $errors->has('ok_post_url') ? ' is-invalid' : '' }}" value="{{ $link }}">
                            </div>
                            <br>
                            @error('ok_post_url')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        @endforeach
                    @else
                        <div class="d-flex align-items-center">
                            <span class="mdi mdi-odnoklassniki mdi-18px mr-2"></span>

                            <input type="url" name="smm_links[ok][][link]" id="ok_post_url" class="form-control{{ $errors->has('ok_post_url') ? ' is-invalid' : '' }}" value="{{ old('ok_post_url', $post->ok_post_url) }}">
                        </div>
                        @error('ok_post_url')
                        <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    @endif

                </div>
            @endif

            @if ($post->posting_to_fb)
                <div class="form-group has-icon" id="fb">
                    <label for="fb_post_url">Ссылка на пост в Facebook</label> <input type="button" class="more btn btn-link" value="Добавить ссылку" />
                    @if(isset($arrSMMLinks['fb']))
                        @foreach($arrSMMLinks['fb'] as $link)
                            <div class="d-flex align-items-center">
                                <span class="mdi mdi-facebook mdi-18px mr-2"></span>

                                <input type="url" name="smm_links[fb][][link]" id="fb_post_url" class="form-control{{ $errors->has('fb_post_url') ? ' is-invalid' : '' }}" value="{{ $link }}">
                            </div>
                            <br>
                            @error('fb_post_url')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        @endforeach
                    @else
                        <div class="d-flex align-items-center">
                            <span class="mdi mdi-facebook mdi-18px mr-2"></span>

                            <input type="url" name="smm_links[fb][][link]" id="fb_post_url" class="form-control{{ $errors->has('fb_post_url') ? ' is-invalid' : '' }}" value="{{ old('fb_post_url', $post->fb_post_url) }}">
                        </div>
                        <br>
                        @error('fb_post_url')
                        <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    @endif

                </div>
            @endif

            @if ($post->posting_to_ig)
                <div class="form-group has-icon" id="ig">
                    <label for="ig_post_url">Ссылка на пост в Instagram</label> <input type="button" class="more btn btn-link" value="Добавить ссылку" />
                    @if(isset($arrSMMLinks['ig']))
                        @foreach($arrSMMLinks['ig'] as $link)
                            <div class="d-flex align-items-center">
                                <span class="mdi mdi-instagram mdi-18px mr-2"></span>

                                <input type="url" name="smm_links[ig][][link]" id="ig_post_url" class="form-control{{ $errors->has('ig_post_url') ? ' is-invalid' : '' }}" value="{{ $link }}">
                            </div>
                            <br>
                            @error('ig_post_url')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        @endforeach
                    @else
                        <div class="d-flex align-items-center">
                            <span class="mdi mdi-instagram mdi-18px mr-2"></span>

                            <input type="url" name="smm_links[ig][][link]" id="ig_post_url" class="form-control{{ $errors->has('ig_post_url') ? ' is-invalid' : '' }}" value="{{ old('ig_post_url', $post->ig_post_url) }}">
                        </div>
                        <br>
                        @error('ig_post_url')
                        <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    @endif
                </div>
            @endif

            @if ($post->posting_to_y_dzen)
                <div class="form-group has-icon" id="y_dzen">
                    <label for="y_dzen_post_url">Ссылка на пост в Я.Дзен</label> <input type="button" class="more btn btn-link" value="Добавить ссылку" />
                    @if(isset($arrSMMLinks['y_dzen']))
                        @foreach($arrSMMLinks['y_dzen'] as $link)
                            <div class="d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="20px" height="20px" style="margin-right: 10px; -ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path d="M2 2h20v20H2V2m9.25 15.5h1.5v-4.44L16 7h-1.5L12 11.66L9.5 7H8l3.25 6.06v4.44z" fill="#626262"/></svg>

                                <input type="url" name="smm_links[y_dzen][][link]" id="y_dzen_post_url" class="form-control{{ $errors->has('y_dzen_post_url') ? ' is-invalid' : '' }}" value="{{ $link }}">
                            </div>
                            <br>
                            @error('y_dzen_post_url')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        @endforeach
                    @else
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="20px" height="20px" style="margin-right: 10px; -ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path d="M2 2h20v20H2V2m9.25 15.5h1.5v-4.44L16 7h-1.5L12 11.66L9.5 7H8l3.25 6.06v4.44z" fill="#626262"/></svg>

                            <input type="url" name="smm_links[y_dzen][][link]" id="y_dzen_post_url" class="form-control{{ $errors->has('y_dzen_post_url') ? ' is-invalid' : '' }}" value="{{ old('y_dzen_post_url', $post->y_dzen_post_url) }}">
                        </div>
                        <br>
                        @error('y_dzen_post_url')
                        <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    @endif

                </div>
            @endif
            @if ($post->posting_to_y_street)
                <div class="form-group has-icon" id="y_street">
                    <label for="y_street_post_url">Ссылка на пост в Я.Район</label> <input type="button" class="more btn btn-link" value="Добавить ссылку" />
                    @if(isset($arrSMMLinks['y_street']))
                        @foreach($arrSMMLinks['y_street'] as $link)
                            <div class="d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="20px" height="20px" style="margin-right: 10px; -ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path d="M2 2h20v20H2V2m9.25 15.5h1.5v-4.44L16 7h-1.5L12 11.66L9.5 7H8l3.25 6.06v4.44z" fill="#626262"/></svg>

                                <input type="url" name="smm_links[y_street][][link]" id="y_street_post_url" class="form-control{{ $errors->has('y_street_post_url') ? ' is-invalid' : '' }}" value="{{ $link }}">
                            </div>
                            <br>
                            @error('y_street_post_url')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        @endforeach
                    @else
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="20px" height="20px" style="margin-right: 10px; -ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path d="M2 2h20v20H2V2m9.25 15.5h1.5v-4.44L16 7h-1.5L12 11.66L9.5 7H8l3.25 6.06v4.44z" fill="#626262"/></svg>

                            <input type="url" name="smm_links[y_street][][link]" id="y_street_post_url" class="form-control{{ $errors->has('y_street_post_url') ? ' is-invalid' : '' }}" value="{{ old('y_street_post_url', $post->y_street_post_url) }}">
                        </div>
                        <br>
                        @error('y_street_post_url')
                        <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    @endif

                </div>
            @endif

            @if ($post->posting_to_yt)
                <div class="form-group has-icon" id="yt">
                    <label for="yt_post_url">Ссылка на пост в Youtube</label> <input type="button" class="more btn btn-link" value="Добавить ссылку" />
                    @if(isset($arrSMMLinks['yt']))
                        @foreach($arrSMMLinks['yt'] as $link)
                            <div class="d-flex align-items-center">
                                <span class="mdi mdi-youtube mdi-18px mr-2"></span>

                                <input type="url" name="smm_links[yt][][link]" id="yt_post_url" class="form-control{{ $errors->has('yt_post_url') ? ' is-invalid' : '' }}" value="{{ $link }}">

                            </div>
                            <br>
                            @error('yt_post_url')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        @endforeach
                    @else
                        <div class="d-flex align-items-center">
                            <span class="mdi mdi-youtube mdi-18px mr-2"></span>

                            <input type="url" name="smm_links[yt][][link]" id="yt_post_url" class="form-control{{ $errors->has('yt_post_url') ? ' is-invalid' : '' }}" value="{{ old('yt_post_url', $post->yt_post_url) }}">
                        </div>
                        <br>
                        @error('yt_post_url')
                        <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    @endif

                </div>
            @endif

{{--            @if ($post->posting_to_tg)--}}
{{--                <div class="form-group has-icon">--}}
{{--                    <label for="tg_post_url">Ссылка на пост в Telegram</label>--}}
{{--                    <div class="d-flex align-items-center">--}}
{{--                        <span class="mdi mdi-telegram mdi-18px mr-2"></span>--}}

{{--                        <input type="url" name="smm_links[vk][][link]" id="tg_post_url" class="form-control{{ $errors->has('tg_post_url') ? ' is-invalid' : '' }}" value="{{ old('yt_post_url', $post->tg_post_url) }}">--}}
{{--                    </div>--}}

{{--                    @error('tg_post_url')--}}
{{--                    <small class="invalid-feedback">{{ $message }}</small>--}}
{{--                    @enderror--}}
{{--                </div>--}}
{{--            @endif--}}

            @if ($post->posting_to_tg)
                <div class="form-group has-icon" id="tg">
                    <label for="tg_post_url">Ссылка на пост в Telegram</label> <input type="button" class="more btn btn-link" value="Добавить ссылку" />
                    @if(isset($arrSMMLinks['tg']))
                        @foreach($arrSMMLinks['tg'] as $link)
                            <div class="d-flex align-items-center">
                                <span class="mdi mdi-telegram mdi-18px mr-2"></span>

                                <input type="url" name="smm_links[tg][][link]" id="tg_post_url" class="form-control{{ $errors->has('tg_post_url') ? ' is-invalid' : '' }}" value="{{ $link}}">
                            </div>
                            <br>
                            @error('tg_post_url')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        @endforeach
                    @else
                        <div class="d-flex align-items-center">
                            <span class="mdi mdi-telegram mdi-18px mr-2"></span>

                            <input type="url" name="smm_links[tg][][link]" id="tg_post_url" class="form-control{{ $errors->has('tg_post_url') ? ' is-invalid' : '' }}" value="{{ old('tg_post_url', $post->tg_post_url) }}">
                        </div>
                        <br>
                        @error('tg_post_url')
                        <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    @endif

                </div>
            @endif

            @if ($post->posting_to_tt)
                <div class="form-group has-icon" id="tt">
                    <label for="tt_post_url">Ссылка на пост в TikTok</label> <input type="button" class="more btn btn-link" value="Добавить ссылку" />
                    @if(isset($arrSMMLinks['tt']))
                        @foreach($arrSMMLinks['tt'] as $link)
                            <div class="d-flex align-items-center">
                                {{--            Иконки тиктока нет в мди, пришлось подставить так.            --}}
                                <span class="mdi mdi-tiktok mdi-18px mr-2"><img src="../img/tik-tok-icon.png" style="width: 18px; height: 19px;"></span>

                                <input type="url" name="smm_links[tt][][link]" id="tt_post_url" class="form-control{{ $errors->has('tt_post_url') ? ' is-invalid' : '' }}" value="{{ $link }}">
                            </div>
                            <br>
                            @error('tt_post_url')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        @endforeach
                    @else
                        <div class="d-flex align-items-center">
                            {{--            Иконки тиктока нет в мди, пришлось подставить так.            --}}
                            <span class="mdi mdi-tiktok mdi-18px mr-2"><img src="../img/tik-tok-icon.png" style="width: 18px; height: 19px;"></span>

                            <input type="url" name="smm_links[tt][][link]" id="tt_post_url" class="form-control{{ $errors->has('tt_post_url') ? ' is-invalid' : '' }}" value="{{ old('tt_post_url', $post->tt_post_url) }}">
                        </div>

                        @error('tt_post_url')
                        <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    @endif

                </div>
            @endif

        </div>

        <hr class="w-100">
    </div>
@endif
