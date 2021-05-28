@if ($post->commenting && Auth::user()->hasAnyRole(['editor','commenter']))
    <div class="row">
        <div class="col-12">
            <h5>Комментирование</h5>
            @if ($post->commenting_text)
                <label>Комментарий постановщика:</label>
                <textarea class="form-control" disabled>{{ $post->commenting_text }}</textarea>
                <p></p>
            @endif

            <div class="form-group">
                <div class="custom-control custom-checkbox" style="display: none">
                    <input type="hidden" name="commented" value="0">
                    <input type="checkbox" class="custom-control-input" name="commented"
                           id="commented"
                           value="1"{{ $post->commented ? 'checked' : '' }}>
                    <label for="commented"
                           class="custom-control-label">Комментирование начато</label>
                </div>
            </div>

            <div class="form-group">
                <labsel for="comment_screenshot">Cсылка на скриншот комментария:</labsel>
                <input type="url" class="form-control" name="default_screenshot_url" id="default_screenshot_url" value="{{ $post->default_screenshot_url }}">
            </div>

            <div class="form-group">
                <label for="vk_screenshot">Cсылка на скриншот комментария VK:</label>
                <input type="url" class="form-control" name="vk_screenshot" id="vk_screenshot" value="{{ $post->vk_screenshot  }}">
            </div>

            <div class="form-group">
                <label for="ok_screenshot">Cсылка на скриншот комментария OK:</label>
                <input type="url" class="form-control" name="ok_screenshot" id="ok_screenshot" value="{{ $post->ok_screenshot  }}">
            </div>

            <div class="form-group">
                <label for="fb_screenshot">Cсылка на скриншот комментария FB:</label>
                <input type="url" class="form-control" name="fb_screenshot" id="fb_screenshot" value="{{ $post->fb_screenshot  }}">
            </div>

            <div class="form-group">
                <label for="insta_screenshot">Cсылка на скриншот комментария INSTA:</label>
                <input type="url" class="form-control" name="ig_screenshot" id="ig_screenshot" value="{{ $post->ig_screenshot  }}">
            </div>

            <div class="form-group">
                <label for="y_dzen_screenshot">Cсылка на скриншот комментария Я.Дзен:</label>
                <input type="url" class="form-control" name="y_dzen_screenshot" id="y_dzen_screenshot" value="{{ $post->y_dzen_screenshot  }}">
            </div>


            <div class="form-group">
                <label for="y_street_screenshot">Cсылка на скриншот комментария Я.Улица:</label>
                <input type="url" class="form-control" name="y_street_screenshot" id="y_street_screenshot" value="{{ $post->y_street_screenshot  }}">
            </div>

            <div class="form-group">
                <label for="yt_screenshot">Cсылка на скриншот комментария Youtube:</label>
                <input type="url" class="form-control" name="yt_screenshot" id="yt_screenshot" value="{{ $post->yt_screenshot  }}">
            </div>


            <div class="form-group">
                <label for="tg_screenshot">Cсылка на скриншот комментария Telegram:</label>
                <input type="url" class="form-control" name="tg_screenshot" id="tg_screenshot" value="{{ $post->tg_screenshot }}">
            </div>


            <hr class="w-100">
        </div>
    </div>
@endif
