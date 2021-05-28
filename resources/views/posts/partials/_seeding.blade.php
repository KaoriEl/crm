@if ($post->seeding && Auth::user()->hasAnyRole(['editor', 'seeder']))
    <div class="row">
        <div class="col-12">
            <h5>Посев</h5>
            @if ($post->seeding_text)
                <label>Комментарий постановщика:</label>
                <textarea class="form-control" disabled>{{ $post->seeding_text }}</textarea>
                <p></p>
            @endif

            <div class="form-group">
                @if ($post->seeding_to_vk)
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" checked disabled>
                        <label class="custom-control-label">ВКонтакте</label>
                    </div>
                @endif

                @if ($post->seeding_to_ok)
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" checked disabled>
                        <label class="custom-control-label">Одноклассники</label>
                    </div>
                @endif

                    @if ($post->seeding_to_fb)
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" checked disabled>
                            <label class="custom-control-label">Facebook</label>
                        </div>
                    @endif

                    @if ($post->seeding_to_insta)
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" checked disabled>
                            <label class="custom-control-label">Instagram</label>
                        </div>
                    @endif

                    @if ($post->seeding_to_y_dzen)
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" checked disabled>
                            <label class="custom-control-label">Я.Дзен</label>
                        </div>
                    @endif

                    @if ($post->seeding_to_y_street)
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" checked disabled>
                            <label class="custom-control-label">Я.Улица</label>
                        </div>
                    @endif

                    @if ($post->seeding_to_yt)
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" checked disabled>
                            <label class="custom-control-label">Youtube</label>
                        </div>
                    @endif

                    @if ($post->seeding_to_tg)
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" checked disabled>
                            <label class="custom-control-label">Telegram</label>
                        </div>
                    @endif


            </div>



            <div class="form-group has-icon">
                <label for="seed_list_url">Ссылка на список групп</label>

                <div class="d-flex align-items-center">
                    <span class="mdi mdi-link mdi-18px mr-2"></span>
                    <input type="url" class="form-control{{ $errors->has('seed_list_url') ? ' is-invalid' : '' }}" name="seed_list_url" id="seed_list_url" value="{{ old('seed_list_url', $post->seed_list_url) }}">
                </div>

                @error('seed_list_url')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <hr class="w-100">
    </div>
@endif
