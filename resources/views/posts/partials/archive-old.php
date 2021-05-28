<div class="card card-table">
    <div class="card-body">
        <table class="table table-bordered" id="posts-table-archive">
            <thead>
            <tr>
                <th>#</th>
                <th>Дата</th>
                <th width="100%">Тема</th>
                @hasanyrole('admin|editor|journalist')
                    <th>Проект</th>
                @endhasanyrole
                <th>Срок</th>

                <th class="nosort">САЙТ</th>
                <th class="text-nowrap nosort">Соц. сети</th>

                @hasanyrole('editor|seeder')
                    <th class="nosort">Посев</th>
                @endhasanyrole

                @hasanyrole('admin|targeter')
                    <th class="nosort">Таргет</th>
                @endhasanyrole

                @hasanyrole('admin|commenter')
                    <th class="nosort">Комм.</th>
                @endhasanyrole
            </tr>
            </thead>

            <tbody>

            @foreach ($posts as $post)
                @if(($post->project)/*&&($user->hasProject($post->project))*/)
                    <tr class="{{ $post->done() ? ' table-success': '' }}">

                        <td class="text-muted">{{ $post->id }}</td>
                        <td class="text-muted"> {{$post->archived_at }}</td>
                        <td><a href="{{ route('posts.show', $post->id) }}">{{ $post->title }}</a>
                        <p>{{ $post->journalist['name'] }}</p></td>
                        @hasanyrole('admin|editor|journalist')
                            <td>{{ $post->project->name }}</td>
                        @endhasanyrole
                        <td class="text-nowrap {{ $post->expired() ? 'text-danger' : 'text-success' }}" data-order="{{ $post->expires_at->timestamp }}">
                            @if ($post->expired())
                                <span class="mdi mdi-minus"></span>
                            @else
                                <span class="mdi mdi-plus"></span>
                            @endif

                            {{ $post->date_offset }}
                        </td>

                        <td class="text-nowrap">
                            @if($post->status_task === null)
                                <span class="badge badge-primary">ожидает</span>
                            @elseif ($post->publication_url)
                                <a href="{{ $post->publication_url }}"><span class="badge badge-success">опубликовано</span></a>
                            @elseif ($post->approved)
                                <span class="badge badge-warning">ждет публикации</span>
                            @elseif ($post->draft_url && $post->approved === null)
                                <span class="badge badge-warning">нужна проверка</span>
                            @elseif ($post->draft_url && !$post->approved)
                                <small class="text-muted">на доработке</small>
                            @elseif ($post->hasJournalist())
                                <small class="badge badge-secondary">в работе</small>
                            @else
                                <small class="text-muted">без назначения</small>
                            @endif
                        </td>

                        <td class="text-nowrap">
                            {!! $post->vk_post_url ? "<a href=".$post->vk_post_url." target='blank'>":'' !!}<small class="label{{ $post->vk_post_url ? ' label-done' : '' }}{{ $post->posting && $post->posting_to_vk ? '' : ' invisible'  }}">ВК</small>{!! $post->vk_post_url ? "</a>":'' !!}
                            {!! $post->vk_post_url ? "<a href=".$post->ok_post_url." target='blank'>":'' !!}<small class="label{{ $post->ok_post_url ? ' label-done' : '' }}{{ $post->posting && $post->posting_to_ok ? '' : ' invisible'  }}">ОК</small>{!! $post->ok_post_url ? "</a>":'' !!}
                            {!! $post->vk_post_url ? "<a href=".$post->fb_post_url." target='blank'>":'' !!}<small class="label{{ $post->fb_post_url ? ' label-done' : '' }}{{ $post->posting && $post->posting_to_fb ? '' : ' invisible'  }}">FB</small>{!! $post->fb_post_url ? "</a>":'' !!}
                            {!! $post->vk_post_url ? "<a href=".$post->ig_post_url." target='blank'>":'' !!}<small class="label{{ $post->ig_post_url ? ' label-done' : '' }}{{ $post->posting && $post->posting_to_ig ? '' : ' invisible'  }}">Insta</small>{!! $post->ig_post_url ? "</a>":'' !!}
                        </td>

                        @hasanyrole('editor|seeder')
                            <td class="text-nowrap">
                                {!! $post->seed_list_url ? "<a href=".$post->seed_list_url." target='blank'>":'' !!}<small class="label{{ $post->seeding && $post->seeding_to_vk ? '' : ' invisible' }}{{ $post->seed_list_url ? ' label-done' : '' }}">ВК</small>{!! $post->seed_list_url ? "</a>":'' !!}
                                {!! $post->seed_list_url ? "<a href=".$post->seed_list_url." target='blank'>":'' !!}<small class="label{{ $post->seeding && $post->seeding_to_ok ? '' : ' invisible' }}{{ $post->seed_list_url ? ' label-done' : '' }}">ОК</small>{!! $post->seed_list_url ? "</a>":'' !!}
                            </td>
                        @endhasanyrole

                        @hasanyrole('editor|targeter')
                            <td class="text-nowrap">
                                <small class="label{{ $post->targeting && $post->targeting_to_vk ? '' : ' invisible' }}{{ $post->targeted_to_vk ? ' label-wait' : '' }}{{ $post->target_launched_in_vk ? ' label-done' : '' }}">ВК</small>
                                <small class="label{{ $post->targeting && $post->targeting_to_ok ? '' : ' invisible' }}{{ $post->targeted_to_ok ? ' label-wait' : '' }}{{ $post->target_launched_in_ok ? ' label-done' : '' }}">ОК</small>
                                <small class="label{{ $post->targeting && $post->targeting_to_fb ? '' : ' invisible' }}{{ $post->targeted_to_fb ? ' label-wait' : '' }}{{ $post->target_launched_in_fb ? ' label-done' : '' }}">FB</small>
                                <small class="label{{ $post->targeting && $post->targeting_to_ig ? '' : ' invisible' }}{{ $post->targeted_to_ig ? ' label-wait' : '' }}{{ $post->target_launched_in_ig ? ' label-done' : '' }}">Insta</small>
                            </td>
                        @endhasanyrole

                        @hasanyrole('admin|commenter')
                            <td>
                                @if ($post->commenting)
                                    @if ($post->hasCommentScreenshot())
                                        <small class="label label-done">Да</small>
                                    @else
                                        <small class="label">Нет</small>
                                    @endif
                                @endif
                            </td>
                        @endhasanyrole
                    </tr>
                @endif
            @endforeach

            </tbody>
        </table>
    </div>
</div>
