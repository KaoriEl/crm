<style>
    *, *::before, *::after {
        box-sizing: content-box;
    }
</style>
<div class="card card-table">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="posts-table-archive">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Дата</th>
                    <th>Тема</th>
                    {{-- <th width="100%">Тема</th> --}}
                    @hasanyrole('admin|editor|journalist')
                    <th>Проект</th>
                    @endhasanyrole
                    <th>Срок</th>

                    <th class="nosort">Статус</th>
                    <th class=" nosort">Соц. сети</th>
                    <th class=" nosort">Комм. выходы</th>
                    {{--<th class="text-nowrap nosort">Соц. сети</th> --}}
                    @hasanyrole('editor|seeder')
                    <th class="nosort">Посев</th>
                    @endhasanyrole

                    @hasanyrole('admin|targeter')
                    <th class="nosort">Таргет</th>
                    @endhasanyrole

                    @hasanyrole('admin|commenter')
                    <th class="nosort">Комм.</th>
                    @endhasanyrole

                    @hasanyrole('editor')
                    <th class="nosort">Действия</th>
                    @endhasanyrole

                </tr>
                </thead>
                <tbody>

                @foreach ($posts as $post)
                    @if(($post->project)/*&&($user->hasProject($post->project))*/)
                        <tr class="{{ $post->done() ? ' table-success': '' }}">

                            <td class="text-muted" id="{{ $post->id }}">{{ $post->id }}</td>
                            <td class="text-muted"> {{$post->archived_at->timezone('Asia/Novosibirsk') }}</td>

                            <td id="{{ $post->id }}"><a href="{{ route('posts.show', $post->id) }}" >{{ $post->title }}</a>

                                @if($post->journalist !== null)
                                    <p>{{ $post->journalist['name'] }}</p>


                                    <div id="StatisticPreview{{ $post->id }}" style="width: max-content;">

                                    </div>



                                    <button type="button" class="statistic btn btn-link" data-toggle="modal" data-target="#exampleModal" id="{{ $post->id }}" style="display: contents;">
                                        Статистика
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Статистика по социальным сетям</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table table-hover" id="StatisticTableHeader">
                                                        <thead class="thead-dark">
                                                        <tr>
                                                            <th scope="col">Социальная сеть</th>
                                                            <th title="Просмотры" scope="col" class="mdi mdi-eye mdi-18px mr-2"></th>
                                                            <th title="Прогнозные просмотры" scope="col" class="mdi mdi-chart-areaspline mdi-18px mr-2"></th>
                                                            <th title="Лайки" scope="col" class="mdi mdi-thumb-up mdi-18px mr-2"></th>
                                                            <th title="Комментарии" scope="col" class="mdi mdi-comment-multiple mdi-18px mr-2"></th>
                                                            <th title="Репосты" scope="col" class="mdi mdi-share-all mdi-18px mr-2"></th>
                                                            <th title="Кол-во подписчиков" scope="col" class="mdi mdi-account-plus mdi-18px mr-2"></th>
                                                            <th title="ER (вовлеченность)" scope="col" class="mdi mdi-percent mdi-18px mr-2" ></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="StatisticTable">
                                                        </tbody>
                                                    </table>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                            </td>
                            @endif
                            @hasanyrole('admin|editor|journalist')
                            <td>{{ $post->project->name }}</td>
                            @endhasanyrole
                            <td class="text-nowrap {{ $post->expired() ? 'text-danger' : 'text-success' }}"
                                data-order="{{ $post->expires_at->timestamp }}">
                                @if ($post->expired())
                                    <span class="mdi mdi-minus"></span>
                                @else
                                    <span class="mdi mdi-plus"></span>
                                @endif

                                {{ $post->date_offset }}
                            </td>

                            <td class="text-nowrap">
                                @if($post->status_task === null && $post->journalist_id === null)
                                    <span class="badge badge-no_journalist">без назначения</span>
                                @elseif($post->status_task === null && $post->journalist_id != null)
                                    <span class="badge badge-danger">не в работе</span>
                                @elseif ($post->publication_url)
                                    <a href="{{ $post->publication_url }}"><span class="badge badge-success">опубликовано</span></a>
                                @elseif ($post->approved)
                                    <span class="badge badge-warning">ждет публикации</span>
                                @elseif ($post->draft_url && $post->approved === null)
                                    <span class="badge badge-warning">нужна проверка</span>
                                @elseif ($post->draft_url && !$post->approved)
                                    <small class="badge badge-info">на доработке</small>
                                @elseif ($post->hasJournalist())
                                    <small class="badge badge-secondary">в работе</small>
                                @else
                                    <span class="badge badge-primary">не в работе</span>
                                @endif
                            </td>

                            <td class="" style="width: 200px">
                                {{--               Я понимаю - это пиздец.                 --}}
                                @php
                                    $vk = 1;
                                    $inst = 1;
                                    $ok = 1;
                                    $fb = 1;
                                    $y_dzen = 1;
                                    $y_street = 1;
                                    $yt = 1;
                                    $tg = 1;
                                    $tt = 1;
                                @endphp

                                @if($post["posting_to_vk"] == true)<small> ВК <span id="linksvk{{ $post->id }}" class="vkhide{{ $post->id }}" >@foreach($post->getSMMLinks() as $link) @if($link->socialNetwork()->first()->name == "ВК") @if($post->getSMMLinks()->count() > 1 )<a target="_blank" href="{{$link->link}}"> {{$vk++}} </a>@endif @endif @endforeach</span>@if($vk > 3) <style> .vkhide{{ $post->id }}{ display: none}</style><input type="button" id="svk{{ $post->id }}" class="vk btn btn-link" value="1..{{$count = $vk-1}}"style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["posting_to_ig"] == true)<small> INST <span id="linksinsta{{ $post->id }}" class="insthide{{ $post->id }}">@foreach($post->getSMMLinks() as $link) @if($link->socialNetwork()->first()->name == "Insta") <a target="_blank" href="{{$link->link}}"> {{$inst++}} </a> @endif @endforeach</span>@if($inst > 3)<style> .insthide{{ $post->id }}{ display: none}</style><input type="button" id="sinsta{{ $post->id }}" class="inst btn btn-link" value="1..{{$count = $inst-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["posting_to_ok"] == true)<small> OK <span id="linksok{{ $post->id }}" class="okhide{{ $post->id }}">@foreach($post->getSMMLinks() as $link) @if($link->socialNetwork()->first()->name == "ОК") <a target="_blank" href="{{$link->link}}"> {{$ok++}} </a> @endif @endforeach</span>@if($ok > 3)<style> .okhide{{ $post->id }}{ display: none}</style><input type="button" id="sok{{ $post->id }}" class="ok btn btn-link" value="1..{{$count = $ok-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["posting_to_fb"] == true)<small> FB <span id="linksfb{{ $post->id }}" class="fbhide{{ $post->id }}">@foreach($post->getSMMLinks() as $link) @if($link->socialNetwork()->first()->name == "FB") <a target="_blank" href="{{$link->link}}"> {{$fb++}} </a> @endif @endforeach</span>@if($fb > 3)<style> .fbhide{{ $post->id }}{ display: none}</style><input type="button" id="sfb{{ $post->id }}" class="fb btn btn-link" value="1..{{$count = $fb-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["posting_to_y_dzen"] == true)<small> Я.Д <span id="linksyd{{ $post->id }}" class="ydhide{{ $post->id }}">@foreach($post->getSMMLinks() as $link) @if($link->socialNetwork()->first()->name == "Я.Д") <a target="_blank" href="{{$link->link}}"> {{$y_dzen++}}</a> @endif @endforeach</span>@if($y_dzen > 3)<style> .ydhide{{ $post->id }}{ display: none}</style><input type="button" id="syd{{ $post->id }}" class="y_dzen btn btn-link" value="1..{{$count = $y_dzen-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["posting_to_y_street"] == true)<small> Я.Р <span id="linksyr{{ $post->id }}" class="yrhide{{ $post->id }}">@foreach($post->getSMMLinks() as $link) @if($link->socialNetwork()->first()->name == "Я.Р") <a target="_blank" href="{{$link->link}}"> {{$y_street++}} </a>@endif @endforeach</span>@if($y_street > 3)<style> .yrhide{{ $post->id }}{ display: none}</style><input type="button" id="syr{{ $post->id }}" class="y_street btn btn-link" value="1..{{$count = $y_street-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["posting_to_yt"] == true)<small> ЮТ <span id="linksyt{{ $post->id }}" class="ythide{{ $post->id }}">@foreach($post->getSMMLinks() as $link) @if($link->socialNetwork()->first()->name == "YT") <a target="_blank" href="{{$link->link}}"> {{$yt++}} </a>@endif @endforeach</span>@if($yt > 3)<style> .vthide{{ $post->id }}{ display: none}</style><input type="button" id="syt{{ $post->id }}" class="yt btn btn-link" value="1..{{$count = $yt-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["posting_to_tg"] == true)<small> ТГ <span id="linkstg{{ $post->id }}" class="tghide{{ $post->id }}">@foreach($post->getSMMLinks() as $link) @if($link->socialNetwork()->first()->name == "TG") <a target="_blank" href="{{$link->link}}"> {{$tg++}} </a>@endif @endforeach</span>@if($tg > 3)<style> .tghide{{ $post->id }}{ display: none}</style><input type="button" id="stg{{ $post->id }}" class="tg btn btn-link" value="1..{{$count = $tg-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["posting_to_tt"] == true)<small> ТТ <span id="linkstt{{ $post->id }}" class="tthide{{ $post->id }}">@foreach($post->getSMMLinks() as $link) @if($link->socialNetwork()->first()->name == "TT") <a target="_blank" href="{{$link->link}}"> {{$tt++}}</a> @endif @endforeach</span>@if($tt > 3)<style> .tthide{{ $post->id }}{ display: none}</style><input type="button" id="stt{{ $post->id }}" class="tt btn btn-link" value="1..{{$count = $tt-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif



                            </td>

                            <td class="" style="width: 150px">
                                {{--               Я понимаю - это пиздец.                 --}}
                                @php
                                    $vk = 1;
                                    $inst = 1;
                                    $ok = 1;
                                    $fb = 1;
                                    $y_dzen = 1;
                                    $y_street = 1;
                                    $yt = 1;
                                    $tg = 1;
                                    $tt = 1;
                                @endphp

                                @if($post["commercial_seed_to_vk"] == true)<small> ВК <span id="linksvkcomm{{ $post->id }}" class="vkhidecomm{{ $post->id }}" >@foreach($post->getSeedLinks() as $link) @if($link->socialNetwork()->first()->name == "ВК") <a target="_blank" href="{{$link->link}}"> {{$vk++}} </a> @endif @endforeach</span>@if($vk > 3) <style> .vkhidecomm{{ $post->id }}{ display: none}</style><input type="button" id="svkcomm{{ $post->id }}" class="vkcomm btn btn-link" value="1..{{$count = $vk-1}}"style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["commercial_seed_to_ig"] == true)<small> INST <span id="linksinstacomm{{ $post->id }}" class="insthidecomm{{ $post->id }}">@foreach($post->getSeedLinks() as $link) @if($link->socialNetwork()->first()->name == "Insta") <a target="_blank" href="{{$link->link}}"> {{$inst++}} </a> @endif @endforeach</span>@if($inst > 3)<style> .insthidecomm{{ $post->id }}{ display: none}</style><input type="button" id="sinstacomm{{ $post->id }}" class="instcomm btn btn-link" value="1..{{$count = $inst-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["commercial_seed_to_ok"] == true)<small> OK <span id="linksokcomm{{ $post->id }}" class="okhidecomm{{ $post->id }}">@foreach($post->getSeedLinks() as $link) @if($link->socialNetwork()->first()->name == "ОК") <a target="_blank" href="{{$link->link}}"> {{$ok++}} </a> @endif @endforeach</span>@if($ok > 3)<style> .okhidecomm{{ $post->id }}{ display: none}</style><input type="button" id="sokcomm{{ $post->id }}" class="okcomm btn btn-link" value="1..{{$count = $ok-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["commercial_seed_to_fb"] == true)<small> FB <span id="linksfbcomm{{ $post->id }}" class="fbhidecomm{{ $post->id }}">@foreach($post->getSeedLinks() as $link) @if($link->socialNetwork()->first()->name == "FB") <a target="_blank" href="{{$link->link}}"> {{$fb++}} </a> @endif @endforeach</span>@if($fb > 3)<style> .fbhidecomm{{ $post->id }}{ display: none}</style><input type="button" id="sfbcomm{{ $post->id }}" class="fbcomm btn btn-link" value="1..{{$count = $fb-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["commercial_seed_to_y_dzen"] == true)<small> Я.Д <span id="linksydcomm{{ $post->id }}" class="ydhidecomm{{ $post->id }}">@foreach($post->getSeedLinks() as $link) @if($link->socialNetwork()->first()->name == "Я.Д") <a target="_blank" href="{{$link->link}}"> {{$y_dzen++}}</a> @endif @endforeach</span>@if($y_dzen > 3)<style> .ydhidecomm{{ $post->id }}{ display: none}</style><input type="button" id="sydcomm{{ $post->id }}" class="y_dzencomm btn btn-link" value="1..{{$count = $y_dzen-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["commercial_seed_to_y_street"] == true)<small> Я.Р <span id="linksyrcomm{{ $post->id }}" class="yrhide{{ $post->id }}">@foreach($post->getSeedLinks() as $link) @if($link->socialNetwork()->first()->name == "Я.Р") <a target="_blank" href="{{$link->link}}"> {{$y_street++}} </a>@endif @endforeach</span>@if($y_street > 3)<style> .yrhidecomm{{ $post->id }}{ display: none}</style><input type="button" id="syrcomm{{ $post->id }}" class="y_streetcomm btn btn-link" value="1..{{$count = $y_street-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["commercial_seed_to_yt"] == true)<small> ЮТ <span id="linksytcomm{{ $post->id }}" class="ythidecomm{{ $post->id }}">@foreach($post->getSeedLinks() as $link) @if($link->socialNetwork()->first()->name == "YT") <a target="_blank" href="{{$link->link}}"> {{$yt++}} </a>@endif @endforeach</span>@if($yt > 3)<style> .vthidecomm{{ $post->id }}{ display: none}</style><input type="button" id="sytcomm{{ $post->id }}" class="ytcomm btn btn-link" value="1..{{$count = $yt-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["commercial_seed_to_tg"] == true)<small> ТГ <span id="linkstgcomm{{ $post->id }}" class="tghidecomm{{ $post->id }}">@foreach($post->getSeedLinks() as $link) @if($link->socialNetwork()->first()->name == "TG") <a target="_blank" href="{{$link->link}}"> {{$tg++}} </a>@endif @endforeach</span>@if($tg > 3)<style> .tghidecomm{{ $post->id }}{ display: none}</style><input type="button" id="stgcomm{{ $post->id }}" class="tgcomm btn btn-link" value="1..{{$count = $tg-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif
                                @if($post["commercial_seed_to_tt"] == true)<small> ТТ <span id="linksttcomm{{ $post->id }}" class="tthidecomm{{ $post->id }}">@foreach($post->getSeedLinks() as $link) @if($link->socialNetwork()->first()->name == "TT") <a target="_blank" href="{{$link->link}}"> {{$tt++}}</a> @endif @endforeach</span>@if($tt > 3)<style> .tthidecomm{{ $post->id }}{ display: none}</style><input type="button" id="sttcomm{{ $post->id }}" class="ttcomm btn btn-link" value="1..{{$count = $tt-1}}" style="font-size: 11.52px;margin-top: -1px;font-weight: 400;margin-left: -10px;">@endif</small><br> @endif

                            </td>


                            @hasanyrole('editor|seeder')
                            <td class="">
                                {!! $post->seed_list_url ? "<a href=".$post->seed_list_url." target='blank'>":'' !!}
                                <small
                                    class="label{{ $post->seeding && $post->seeding_to_vk ? '' : ' label-invisible' }}{{ $post->seed_list_url ? ' label-done' : '' }}">ВК</small>{!! $post->seed_list_url ? "</a>":'' !!}{!! $post->seeding_to_vk ? "<br>":'' !!}
                                {!! $post->seed_list_url ? "<a href=".$post->seed_list_url." target='blank'>":'' !!}
                                <small
                                    class="label{{ $post->seeding && $post->seeding_to_ok ? '' : ' label-invisible' }}{{ $post->seed_list_url ? ' label-done' : '' }}">ОК</small>{!! $post->seed_list_url ? "</a>":'' !!}{!! $post->seeding_to_ok ? "<br>":'' !!}
                                {!! $post->seed_list_url ? "<a href=".$post->seed_list_url." target='blank'>":'' !!}
                                <small
                                    class="label{{ $post->seeding && $post->seeding_to_y_dzen ? '' : ' label-invisible' }}{{ $post->seed_list_url ? ' label-done' : '' }}">Я.Д</small>{!! $post->seed_list_url ? "</a>":'' !!}{!! $post->seeding_to_y_dzen ? "<br>":'' !!}
                                {!! $post->seed_list_url ? "<a href=".$post->seed_list_url." target='blank'>":'' !!}
                                <small
                                    class="label{{ $post->seeding && $post->seeding_to_y_street ? '' : ' label-invisible' }}{{ $post->seed_list_url ? ' label-done' : '' }}">Я.У</small>{!! $post->seed_list_url ? "</a>":'' !!}{!! $post->seeding_to_y_street ? "<br>":'' !!}
                                {!! $post->seed_list_url ? "<a href=".$post->seed_list_url." target='blank'>":'' !!}
                                <small
                                    class="label{{ $post->seeding && $post->seeding_to_yt ? '' : ' label-invisible' }}{{ $post->seed_list_url ? ' label-done' : '' }}">YT</small>{!! $post->seed_list_url ? "</a>":'' !!}{!! $post->seeding_to_yt ? "<br>":'' !!}
                                {!! $post->seed_list_url ? "<a href=".$post->seed_list_url." target='blank'>":'' !!}
                                <small
                                    class="label{{ $post->seeding && $post->seeding_to_tg ? '' : ' label-invisible' }}{{ $post->seed_list_url ? ' label-done' : '' }}">TG</small>{!! $post->seed_list_url ? "</a>":'' !!}{!! $post->seeding_to_tg ? "<br>":'' !!}

                            </td>
                            @endhasanyrole

                            @hasanyrole('editor|targeter')
                            <td class="">
                                @foreach($post->socialNetworks as $socialNetwork)
                                    @php $socialNetworkPostUrl = $socialNetwork->slug . '_post_url' @endphp
                                    {!! $post->$socialNetworkPostUrl ? "<a href=".$post->$socialNetworkPostUrl." target='blank'>":'' !!}
                                    <small
                                        class="label label-{{ \App\Enums\PostTargetStatusesEnum::getStatusBadgeClass($socialNetwork->pivot->status) ?? 'white'}}">
                                        {{ $socialNetwork->name }}&nbsp;|&nbsp;{{ $socialNetwork->pivot->price ?? '' }}
                                    </small>
                                    {!! $post->$socialNetworkPostUrl ? "</a>":'' !!}
                                    <br>
                                @endforeach
                            </td>
                            @endhasanyrole

                            @hasanyrole('admin|commenter')
                            <td>
                                @if ($post->commenting)
                                    <a href="{{ is_null($post->default_screenshot_url) ? '#' : $post->default_screenshot_url }}"
                                       target="_blank"><small
                                            class="label{{ $post->default_screenshot_url ? ' label-done' : '' }}{{ $post->commenting && $post->default_screenshot_url ? '' : ' label-invisible'  }}">S</small></a>{!! $post->default_screenshot_url ? "<br>":'' !!}
                                    <a href="{{ is_null($post->vk_screenshot) ? '#' : $post->vk_screenshot }}"
                                       target="_blank"><small
                                            class="label{{ $post->vk_screenshot ? ' label-done' : '' }}{{ $post->commenting && $post->vk_screenshot ? '' : ' label-invisible'  }}">ВК</small></a>{!! $post->vk_screenshot ? "<br>":'' !!}
                                    <a href="{{ is_null($post->ok_screenshot) ? '#' : $post->ok_screenshot }}"
                                       target="_blank"> <small
                                            class="label{{ $post->ok_screenshot ? ' label-done' : '' }}{{ $post->commenting && $post->ok_screenshot ? '' : ' label-invisible'  }}">ОК</small></a>{!! $post->ok_screenshot ? "<br>":'' !!}
                                    <a href="{{ is_null($post->fb_screenshot) ? '#' : $post->fb_screenshot }}"
                                       target="_blank"> <small
                                            class="label{{ $post->fb_screenshot ? ' label-done' : '' }}{{ $post->commenting && $post->fb_screenshot ? '' : ' label-invisible'  }}">FB</small></a>{!! $post->fb_screenshot ? "<br>":'' !!}
                                    <a href="{{ is_null($post->ig_screenshot) ? '#' : $post->ig_screenshot }}"
                                       target="_blank"> <small
                                            class="label{{ $post->ig_screenshot ? ' label-done' : '' }}{{ $post->commenting && $post->ig_screenshot ? '' : ' label-invisible'  }}">Insta</small></a>{!! $post->ig_screenshot ? "<br>":'' !!}
                                    <a href="{{ is_null($post->y_dzen_screenshot) ? '#' : $post->y_dzen_screenshot }}"
                                       target="_blank"> <small
                                            class="label{{ $post->y_dzen_screenshot ? ' label-done' : '' }}{{ $post->commenting && $post->y_dzen_screenshot ? '' : ' label-invisible'  }}">Я.Д</small></a>{!! $post->y_dzen_screenshot ? "<br>":'' !!}
                                    <a href="{{ is_null($post->y_street_screenshot) ? '#' : $post->y_street_screenshot }}"
                                       target="_blank"><small
                                            class="label{{ $post->y_street_screenshot ? ' label-done' : '' }}{{ $post->commenting && $post->y_street_screenshot ? '' : ' label-invisible'  }}">Я.Р</small></a>{!! $post->y_street_screenshot ? "<br>":'' !!}
                                    <a href="{{ is_null($post->yt_screenshot) ? '#' : $post->yt_screenshot }}"
                                       target="_blank"><small
                                            class="label{{ $post->yt_screenshot ? ' label-done' : '' }}{{ $post->commenting && $post->yt_screenshot ? '' : ' label-invisible'  }}">YT</small></a>{!! $post->yt_screenshot ? "<br>":'' !!}
                                    <a href="{{ is_null($post->tg_screenshot) ? '#' : $post->tg_screenshot }}"
                                       target="_blank"><small
                                            class="label{{ $post->tg_screenshot ? ' label-done' : '' }}{{ $post->commenting && $post->tg_screenshot ? '' : ' label-invisible'  }}">TG</small></a>{!! $post->tg_screenshot ? "<br>":'' !!}
                                @endif
                            </td>
                            @endhasanyrole


                            @hasanyrole('editor')
                            <td class="">
                                <a href="{{ route('posts.archived.copy',$post->id) }}" class="btn btn-success">Копировать</a>
                            </td>
                            @endhasanyrole

                        </tr>
                    @endif
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
