/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// window.Vue = require('vue');

import toast from './toast.js';

window.toast = toast;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// const app = new Vue({
//     el: '#app',
// });

Dropzone.autoDiscover = false;

$(function () {
    $('.target-input').on('change', function () {
        let socialId = $(this).data('social-id');
        if ($(this).val() > 0) {
            $('input[type=checkbox][data-social-id='+ socialId +']').prop('checked', true);
        } else {
            $('input[type=checkbox][data-social-id='+ socialId +']').prop('checked', false);
        }
    });

    let username_auth = 'redaktor';
    let password_auth = '0fAIGwVlTN';
    //var Inputmask = require('inputmask');
    //Inputmask({"mask": "(999) 999-9999"}).mask('#phone');
    $('#btn-delete-idea').click(function (e) {
        if (!confirm('Удалить?')) {
            e.preventDefault();

            return false;
        }
    });

    $('#btn-accept-post').click(function (event) {
        event.preventDefault();

        var $form = $(this).closest('form');
        var comment = $form.find('textarea[name="comment"]').val($('#comment').val());

        $form.submit();
    });

    let checkboxGroups = ['posting', 'targeting', 'seeding', 'commercial_seed'];

    checkboxGroups.forEach(function (checkboxGroup) {
        $('input:checkbox#' + checkboxGroup).change(function (e) {
            let checked = $(this).prop('checked');

            $('input:checkbox[id^="' + checkboxGroup + '_"]').each(function () {
                $(this).parent().toggle(checked);
            });
        }).change();
    });

    $('.delete-comment-form').submit(function (e) {
        if (!confirm('Удалить комментарий?')) {
            e.preventDefault();
        }
    });


    $('#posting_checked_all').click(function() {

        if($('#posting_checked_all').prop("checked")){
            $('[name ^= "posting_to"]').prop("checked", true);
        } else {
            $('[name ^= "posting_to"]').prop("checked", false);
        }


    });

    $('#commercial_seed_checked_all').click(function() {

        if($('#commercial_seed_checked_all').prop("checked")){
            $('[name ^= "commercial_seed_to"]').prop("checked", true);
        } else {
            $('[name ^= "commercial_seed_to"]').prop("checked", false);
        }


    });

    $('#targeting_checked_all').click(function() {

        if($('#targeting_checked_all').prop("checked")){
            $('[name ^= "targeting_to"]').prop("checked", true);
        } else {
            $('[name ^= "targeting_to"]').prop("checked", false);
        }



    });


    $('#seeding_checked_all').click(function() {


        if($('#seeding_checked_all').prop("checked")){
            $('[name ^= "seeding_to"]').prop("checked", true);
        } else {
            $('[name ^= "seeding_to"]').prop("checked", false);
        }


    });

    $('#targeting').click(function () {
        if($('#targeting').prop("checked")){

        } else {
            $('[name ^= "targeting_to"]').prop("value", 0);
        }
    });

    $('#posting').click(function () {
        if($('#posting').prop("checked")){

        } else {
            $('[name ^= "posting_to"]').prop("value", 0);
        }
    });

    $('#commercial_seed').click(function () {
        if($('#commercial_seed').prop("checked")){

        } else {
            $('[name ^= "commercial_seed_to"]').prop("value", 0);
        }
    });

    $('#seeding').click(function () {
        if($('#seeding').prop("checked")){

        } else {
            $('[name ^= "seeding_to"]').prop("value", 0);
        }
    });

    let displayCount = 0;
    $('#archive_comment_block').fadeOut(0);

    $('[name ^= "display_comment"]').click(function() {
        if(displayCount == 0) {
            $('#archive_comment_block').fadeIn(500);
            displayCount = 1;
        } else {
            $('#archive_comment_block').fadeOut(500);
            displayCount = 0;
        }
    });


    $('[data-toggle="tooltip"]').tooltip();

    $('.btn-take-article').click(function (e) {
        e.preventDefault();

        var $form = $(this).closest('form');

        if (!$form) {
            return;
        }

        if (confirm('Взять задачу в работу?')) {
            $form.submit();
        }
    });

    $('#display-archived-checkbox').change(function () {
        $(this).closest('form').submit();
    });

    $('#post-expire-date-selector').datetimepicker({
        format: 'd.m.Y H:i',
        mask: '39.19.9999 29:59',
    });

    var $dropzoneElement = $('.dropzone-uploader');

    window.myDropzone = $dropzoneElement.dropzone({
        dictDefaultMessage: 'Перетащите сюда файлы для загрузки',
        url: $dropzoneElement.data('url'),

        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            "Authorization": "Basic " + btoa(username_auth + ":" + password_auth)
        },

        previewTemplate: $('.dropzone-uploader-files-container .list-group-item').remove().wrap('<p/>').parent().html(),
        previewsContainer: '.dropzone-uploader-files-container',

        init() {
            this.on('success', (file, response) => {
                file.id = response.id;
                file.full_url = response.full_url;
                file.filename = response.filename;
                console.log(file);
            });

            this.on('error', (file, response) => {
                this.removeFile(file);
            });

            var files = $dropzoneElement.data('files');

            for (const file of files) {
                var mock = { id: file.id, name: file.filename, size: file.size, full_url: file.full_url, status: 'success' };

                this.emit('addedfile', mock);
                this.emit('complete', mock);
            }
        },

        complete(file) {
            $(file.previewElement).removeClass('dz-processing').addClass('dz-complete').find('.dz-file-link').prop('href', file.full_url);

            if (file.status === 'success') {
                $dropzoneElement.parent().append($('<input type="hidden" name="files[]" value="' + file.id + '" />'));
                $(file.previewElement).find('[data-dz-name]').text(file.filename);
            }
        },

        removedfile(file) {
            if (file.status === 'error' || file.status === 'canceled') {
                $(file.previewElement).remove();
                return;
            }

            $dropzoneElement.parent().find('input[name="files[]"][value="' + file.id + '"]').remove();

            $.ajax({
                url: this.options.url,
                data: { id: file.id },
                type: 'delete',
                headers: {
                    "Authorization": "Basic " + btoa(username_auth + ":" + password_auth)
                },
                success: function () {
                    $(file.previewElement).remove();
                },
            });
        },
    });

    $('#posts-table').dataTable({
        info: false,
        paging: false,
        searching: false,

        columnDefs: [
            { targets: ['nosort'], orderable: false },
        ],
        order: [
            [0, 'desc'],
        ],
    });

    $('#posts-table-archive').dataTable({
        info: false,
        paging: false,
        searching: false,

        columnDefs: [
            { targets: ['nosort'], orderable: false },
        ],
        order: [
            [1, 'desc'],
        ],
    });

    $('.list-group-item-checkbox').click(function () {
        var $checkbox = $(this).find('input:checkbox');

        $checkbox.prop('checked', !$checkbox.prop('checked'));
    });

    $('#delete-comment-screenshot-link').click(function (e) {
        e.preventDefault();

        $.ajax({
            url: $(this).data('href'),
            method: 'delete',
            headers: {
                "Authorization": "Basic " + btoa(username_auth + ":" + password_auth)
            },
            success() {
                location.reload();
            },
        });
    });

    function postCreated(payload) {
        toast(payload.title, 'Новая задача');
    }

    function ideaCreated(payload) {
        toast(payload.text, 'Новая идея');
    }

    if (window.App && App.User) {
        Echo.private('journalists')
            .listen('PostCreated', postCreated);

        Echo.private('App.User.' + App.User.id)
            .listen('PostCreated', postCreated)
            .listen('IdeaCreated', ideaCreated);

        $(window).on('beforeunload', function () {
            Echo.leave('broadcast');
        });
    }
    $('#project').on('change', function() {
        let project_id = this.value;
        $.ajax({
            type:'POST',
            url:'/getmsg',
            data: "project_id="+project_id,
            headers: {
                "Authorization": "Basic " + btoa(username_auth + ":" + password_auth)
            },
            success:function(data){
                if(data)
                {
                    $('#journalist').empty();
                    $("#journalist").append('<option value selected>Без назначения</option>');
                    $.each(data,function(key,value){
                        $('#journalist').append($("<option/>", {
                            value: value.id,
                            text: value.name
                        }));
                    });
                }
            }
        });
    });

    $('#project').on('change', function() {
        let takeProject = $('#project').val();
        $.ajax({
            method: "POST",
            url: "/get/social-network/"+takeProject,
            dataType: 'json',
            headers: {
                "Authorization": "Basic " + btoa(username_auth + ":" + password_auth)
            },
            beforeSend: function() {
                $('[name ^= "posting_to"]').prop("checked", false);
            }
        })
        .done(function( data ) {
            data.forEach(function(element) {
                $('#'+element).prop("checked", true);

            });
        });
    });

    $(document).ready(function() {
        let post_id = $('.text-muted');
        $.each(post_id, function (id) {
            let takePost = this.id;
            $.ajax({
                method: "POST",
                url: "/get/getStatisticSocialNetwork/"+takePost,
                dataType: 'json',
                headers: {
                    "Authorization": "Basic " + btoa(username_auth + ":" + password_auth)
                },
                success: function (data) {

                    GenerateDashboardIcons(data,takePost)
                }
            })
        })


    $('.statistic').on('click',function(){
        $("#StatisticTable th").remove();
        $("#StatisticTable td").remove();
        let post_id = this.id;
        let takePost = post_id;

        // console.log(this.id);
        $.ajax({
            method: "POST",
            url: "/get/getStatisticSocialNetwork/"+takePost,
            dataType: 'json',
            headers: {
                "Authorization": "Basic " + btoa(username_auth + ":" + password_auth)
            },
            success: function (data) {
                var arr = []
                $.each(data, function (index) {
                    let statistic = this
                    if (index === "ig"){
                        var SocialNetworks = "Инстаграмм";
                        var SocialNetworksSlug = "inst";
                        GenerateSumStatistic(statistic,SocialNetworks, SocialNetworksSlug)
                        $.each(statistic, function () {
                            var sum = this.like + this.count_comments + this.reposts;
                            addtable(this.like,this.reposts,this.views, this.count_comments, this.commercial, this.acc_name, this.followers, this.linkstat, this.post_snippet, sum, SocialNetworksSlug)

                        })
                    }else if (index === "vk"){
                        var SocialNetworks = "Вконтакте";
                        var SocialNetworksSlug = "vk";
                        GenerateSumStatistic(statistic,SocialNetworks, SocialNetworksSlug)
                        $.each(statistic, function () {
                            var sum = this.like + this.count_comments + this.reposts;
                            addtable(this.like,this.reposts,this.views, this.count_comments, this.commercial, this.acc_name, this.followers, this.linkstat, this.post_snippet, sum, SocialNetworksSlug)

                        })
                    }else if (index === "ok"){
                        var SocialNetworks = "Одноклассники";
                        var SocialNetworksSlug = "ok";
                        GenerateSumStatistic(statistic,SocialNetworks, SocialNetworksSlug)
                        $.each(statistic, function () {
                            var sum = this.like + this.count_comments + this.reposts;
                            addtable(this.like,this.reposts,this.views, this.count_comments, this.commercial, this.acc_name, this.followers, this.linkstat, this.post_snippet, sum, SocialNetworksSlug)

                        })
                    }else if (index === "fb"){
                        var SocialNetworks = "facebook";
                        var SocialNetworksSlug = "fb";
                        GenerateSumStatistic(statistic,SocialNetworks, SocialNetworksSlug)
                        $.each(statistic, function () {
                            var sum = this.like + this.count_comments + this.reposts;
                            addtable(this.like,this.reposts,this.views, this.count_comments, this.commercial, this.acc_name, this.followers, this.linkstat, this.post_snippet, sum)
                        })
                    }



                })
            }


        })


    });

//Подсчет суммы + создание аккордеона для вывода..
    function GenerateSumStatistic(statistic, SocialNetworks, SocialNetworksSlug) {
        let acc_followers = [];
        let sortarray = [];
        var sumFollowers = 0;
        var sumLike = 0;
        var sumViews = 0;
        var sumReposts = 0;
        var sumComments = 0;
        var sumER = 0;
        var sumAll = 0;
        var sumViewsFeature = 0;
        for (var i = 0; i < statistic.length; i++) {
            sumLike += statistic[i].like;
            sumViews += statistic[i].views;
            sumComments += statistic[i].count_comments;
            sumReposts += statistic[i].reposts;
            sumAll = sumLike + sumComments + sumReposts;
            if (sumViews == 0){
                sumER = 0 ;
            }else{
                sumER = sumAll / sumViews * 100 ;
            }
        }
        //я тут удаляю значение подписчиков с одинаковых акков,
        // так как в бд приходит это к каждому посту
        //100% можно сделать умнее
        $.each(statistic, function () {
            acc_followers.push(this.followers)

        })

        sortarray = acc_followers.filter(function(item, pos) {
            return acc_followers.indexOf(item) == pos;
        })

        for (var i = 0; i < sortarray.length; i++) {
            sumFollowers += sortarray[i];
        }
        sumViewsFeature = sumFollowers/10;
        $( "#StatisticTable" ).append(" <tr data-toggle=\"collapse\" data-target='#" + SocialNetworksSlug + " ' data-parent=\"#StatisticTableHeader\">" +
            "<th scope=\"row\" style=' max-width: 10px; cursor: pointer; border: 1px solid #dee2e6 !important;' > <span class=\"mdi mdi-arrow-down-drop-circle mdi-14px mr-2\"></span>" + SocialNetworks +
            "</th> <td style='cursor: pointer; border: 1px solid #dee2e6 !important;'> <span>" + sumViews + "</span></td>" +
            "<td style='cursor: pointer; border: 1px solid #dee2e6 !important;'> <span>" + Math.floor(sumViewsFeature) + "</span></td>" +
            "<td style='cursor: pointer; border: 1px solid #dee2e6 !important;'><span>" + sumLike + "</span></td>" +
            "<td style='cursor: pointer; border: 1px solid #dee2e6 !important;'><span>" + sumComments + "</span></td>" +
            "<td style='cursor: pointer; border: 1px solid #dee2e6 !important;'><span>" + sumReposts + "</span></td>" +
            "<td style='cursor: pointer; border: 1px solid #dee2e6 !important;'><span>" + sumFollowers + "</span></td>" +
            "<td style='cursor: pointer; border: 1px solid #dee2e6 !important;'><span>" + sumER.toFixed(2) + "</span></td>"+
            "</tr>");

    }

//Создаю таблицу для вставки в аккордеон
    function addtable(like, repost, views, comments, commercial, acc_name, followers, link, text, sum,SocialNetworksSlug) {
        console.log(SocialNetworksSlug)
        var ViewsFeature = followers/10;
        if (views == 0 ){
            var ER = 0;
        }else {
            var ER = sum/views * 100;
        }
        $( "#StatisticTable" ).append("<div class=\"collapse smoth_fade\" >");
        if (commercial === "Y") {
            $( "#StatisticTable" ).append("<tr id='" + SocialNetworksSlug + "' class=\"collapse smoth_fade\" style='background: #dffff9;'>" +
                "<td  class=\"hiddenRow smoth_fade\" style='width: 50%; border: 1px solid #dee2e6 !important;' ><a href='" + link +"' target='_blank'>" + text  + " <span>[Коммерческий выход]</span></a></td>" +
                "<td  class=\"hiddenRow smoth_fade\" style='border: 1px solid #dee2e6 !important;'><span>" + views + "</span></td>"+
                "<td  class=\"hiddenRow smoth_fade\" style='border: 1px solid #dee2e6 !important;'><span>" + Math.floor(ViewsFeature) + "</span></td>"+
                "<td  class=\"hiddenRow smoth_fade\" style='border: 1px solid #dee2e6 !important;'><span>" + like + "</span></td>"+
                "<td  class=\"hiddenRow smoth_fade\" style='border: 1px solid #dee2e6 !important;'><span>" + comments + "</span></td>"+
                "<td  class=\"hiddenRow smoth_fade\" style='border: 1px solid #dee2e6 !important;'><span>" + repost + "</span></td>" +
                "<td  class=\"hiddenRow smoth_fade\" style='border: 1px solid #dee2e6 !important;'><span>" + acc_name + " <span class='mdi mdi-arrow-right-bold-outline mdi-14px mr-2' style='display: contents;'></span> " + followers + "</span></td>" +
                "<td  class=\"hiddenRow smoth_fade\" style='border: 1px solid #dee2e6 !important;'><span>" + ER.toFixed(2) + "</span></td>"+
                "</tr>");
        }else {
            $( "#StatisticTable" ).append("<tr id='" + SocialNetworksSlug + "' class=\"collapse smoth_fade\">" +
                "<td  class=\"hiddenRow smoth_fade\" style='width: 50%; border: 1px solid #dee2e6 !important;' ><a href='" + link +"' target='_blank'>" + text  + "</a></td>" +
                "<td  class=\"hiddenRow smoth_fade\" style='border: 1px solid #dee2e6 !important;'><span>" + views + "</span></td>"+
                "<td  class=\"hiddenRow smoth_fade\" style='border: 1px solid #dee2e6 !important;'><span>" + Math.floor(ViewsFeature) + "</span></td>"+
                "<td  class=\"hiddenRow smoth_fade\" style='border: 1px solid #dee2e6 !important;'><span>" + like + "</span></td>"+
                "<td  class=\"hiddenRow smoth_fade\" style='border: 1px solid #dee2e6 !important;'><span>" + comments + "</span></td>"+
                "<td  class=\"hiddenRow smoth_fade\" style='border: 1px solid #dee2e6 !important;'><span>" + repost + "</span></td>" +
                "<td  class=\"hiddenRow smoth_fade\" style='border: 1px solid #dee2e6 !important;'><span>" + acc_name + " <span class='mdi mdi-arrow-right-bold-outline mdi-14px mr-2' style='display: contents;'></span> " + followers + "</span></td>" +
                "<td  class=\"hiddenRow smoth_fade\" style='border: 1px solid #dee2e6 !important;'><span>" + ER.toFixed(2) + "</span></td>"+
                "</tr>");
        }
        $( "#StatisticTable" ).append("</div>");
    }

    function GenerateDashboardIcons(data,takePost) {
        //Собираю все в кучу массивов чтоб нормально сложить, + уникализрую подписчиков. Ненавижу js
            var Like = [];
            var Comments = [];
            var Reposts = [];
            var Views = [];
            var Followers = [];
            // var ER = [];
        $.each(data, function (index) {
            var statistic = this
            for (var i = 0; i < statistic.length; i++) {
                Like.push(statistic[i]["like"])
                Comments.push(statistic[i]["count_comments"])
                Reposts.push(statistic[i]["reposts"])
                Views.push(statistic[i]["views"])
                Followers.push(statistic[i]["followers"])
            }
        })
        console.log(Like)
        let sortarray = [];
        sortarray = Followers.filter(function(item, pos) {
            return Followers.indexOf(item) == pos;
        })
        var sumFollowers = 0;
        for (var i = 0; i < sortarray.length; i++) {
            sumFollowers += sortarray[i];
        }
        var sumLike = 0;
        var sumViews = 0;
        var sumReposts = 0;
        var sumComments = 0;
        var sumER = 0;
        var sumAll = 0;
        for (var i = 0; i < Like.length; i++) {
            sumLike += Like[i];
            sumViews += Views[i];
            sumReposts += Reposts[i];
            sumComments += Comments[i];
            sumAll = sumLike + sumComments + sumReposts;
            if (sumViews == 0){
                sumER = 0 ;
            }else{
                sumER = sumAll / sumViews * 100 ;
            }
        }
            $( "#StatisticPreview"+ takePost  ).append(
                "<span class=\"mdi mdi-eye-outline mdi-18px mr-2\">" + " " + sumViews + "</span>" +
                "<span class=\"mdi mdi-thumb-up-outline mdi-18px mr-2\">" + " " + sumLike + "</span>" +
                "<span class=\"mdi mdi-comment-multiple-outline mdi-18px mr-2\">" + " " + sumComments + "</span>" +
                "<br>" +
                "<span class=\"mdi mdi-share-all-outline mdi-18px mr-2\">"+ " "  + sumReposts + "</span>"+
                "<span class=\"mdi mdi-account-plus-outline mdi-18px mr-2\">"+ " "  + sumFollowers + "</span>"+
                "<span class=\"mdi mdi-percent mdi-18px mr-2\">" + " " + sumER.toFixed(2) + "</span>");
        }
    });


    $(document).ready(function() {
        if(window.location.pathname === "/profile/vk") {
            if (document.location.hash != ""){
                let token = document.location.hash;
                token = token.substr(1);
                console.log(token);
                console.log('token');
                $.ajax({
                    method: "POST",
                    url: "/authUserVK/"+token,
                    data: {token: token},
                    headers: {
                        "Authorization": "Basic " + btoa(username_auth + ":" + password_auth)
                    },
                    success: function (data) {
                         console.log("заебок")
                    },
                    error:function (data) {
                         console.log("не заебок")
                    },
                })
            }

        }
        if(window.location.pathname === "/profile/ok") {
            if (window.location != ""){
                let token = window.location;
                token = token.search;
                token = token.substr(1);
                console.log(token);
                console.log('token');
                $.ajax({
                    method: "POST",
                    url: "/authUserOK/"+token,
                    data: {token: token},
                    headers: {
                        "Authorization": "Basic " + btoa(username_auth + ":" + password_auth)
                    },
                    success: function (data) {
                        console.log("заебок")
                    },
                    error:function (data) {
                        console.log("не заебок")
                    },
                })
            }

        }

    });

    $('#auth_user').on('click', function() {
        let username = $('[name ^= "username_insta"]').val();
        let password = $('[name ^= "password_insta"]').val();
        let url = '';

        if(window.location.pathname === '/profile/instagram') {
            url = '/authUser';
        } else {
            url = '/authMainUser';
        }

        console.log(password);
        $.ajax({
            type: 'POST',
            url: url,
            data: {username: username, password: password},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                "Authorization": "Basic " + btoa(username_auth + ":" + password_auth)
            },
            beforeSend: function() {
                $('.alert-danger').fadeOut(0);
                $('.alert-success').fadeOut(0);
                $('.spinner-border').fadeIn(0);
                $('.user-info').fadeOut(0);
                $('.output-message').fadeOut(0);
                $('#auth_user').fadeOut(0);
            },
            success: function (data) {
                $('.spinner-border').fadeOut(0);
                if(data.error !== undefined && data.error == 'need_auth') {
                    $('.sms_auth').css('display', 'flex');
                    $('[name ^= "api_path"]').val(data.api_path);
                    return 0;
                } else if(data.error !== undefined && data.error == 'bad_pass') {
                    $('.user-info').fadeIn(0);
                    $('#auth_user').fadeIn(0);
                    $('.spinner-border').fadeOut(0);
                    $('.alert-danger').fadeIn(0);
                    $('[name ^= "password_insta"]').val("");
                    $('[name ^= "api_path"]').val("");
                    $('[name ^= "username_insta"]').val("");
                    $('.output-message').text('Неверный логин или пароль');
                    return 0;
                }else if(data.error !== undefined) {
                    console.log(data.error);
                    $('.user-info').fadeIn(0);
                    $('#auth_user').fadeIn(0);
                    $('.spinner-border').fadeOut(0);
                    $('.alert-danger').fadeIn(0);
                    $('[name ^= "password_insta"]').val("");
                    $('[name ^= "api_path"]').val("");
                    $('[name ^= "username_insta"]').val("");
                    $('.output-message').text('Неверный логин или пароль');
                    return 0;
                }
                $('.alert-success').fadeIn(0);
                $('.text-instagram').fadeIn(0);
                $('.user-info').fadeIn(0);
                $('#auth_user').fadeIn(0);

            },
            error: function() {
                $('.output-message').text('Неверный логин или пароль');
                $('.user-info').fadeIn(0);
                $('#auth_user').fadeIn(0);
                $('.spinner-border').fadeOut(0);
                $('.alert-danger').fadeIn(0);
                $('[name ^= "password_insta"]').val("");
                $('[name ^= "api_path"]').val("");
                $('[name ^= "username_insta"]').val("");
            }

        });

    });

    $('#btn_post_sms_to_auth').on('click', function() {
        let username = $('[name ^= "username_insta"]').val();
        let password = $('[name ^= "password_insta"]').val();
        let sms_code = $('#sms_code').val();
        let api_path = $('[name ^= "api_path"]').val();

        let url = '';

        if(window.location.pathname === '/profile/instagram') {
            url = '/authUser';
        } else {
            url = '/authMainUser';
        }

        $.ajax({
            type: 'POST',
            url: url,
            data: {username: username, password: password, sms_code:sms_code, api_path:api_path },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                "Authorization": "Basic " + btoa(username_auth + ":" + password_auth)
            },
            beforeSend: function() {
                $('.alert-danger').fadeOut(0);
                $('.alert-success').fadeOut(0);
                $('.spinner-border').fadeIn(0);
                $('.sms_auth').fadeOut(0);
                $('.output-message').fadeOut(0);
            },
            success: function (data) {
                $('.spinner-border').fadeOut(0);
                if(data.error !== undefined) {
                    $('.alert-danger').fadeIn(0);
                    $('.output-message').text('Произошла ошибка! Попробуйте снова!');
                    console.log(data.error);
                    return 0;
                }
                $('.alert-success').fadeIn(0);
                $('[name ^= "password_insta"]').val("");
                $('[name ^= "api_path"]').val("");
                $('[name ^= "username_insta"]').val("");
                $('.text-instagram').text('Инстаграм аккаунт: активирован!');
                $('.text-instagram').fadeIn(0);
                $('.user-info').fadeIn(0);
                $('#auth_user').fadeIn(0);

            },
            error: function() {
                $('.output-message').text('Неверный логин или пароль');
                $('.user-info').fadeIn(0);
                $('#auth_user').fadeIn(0);
                $('.spinner-border').fadeOut(0);
                $('.alert-danger').fadeIn(0);
                $('[name ^= "password_insta"]').val("");
                $('[name ^= "api_path"]').val("");
                $('[name ^= "username_insta"]').val("");
            }

        });

    });

    $('#dtHorizontalExample').DataTable({
        "scrollX": true
    });
    $('.dataTables_length').addClass('bs-select');

    // requires jquery library
    jQuery(document).ready(function() {
        jQuery(".main-table").clone(true).appendTo('#table-scroll').addClass('clone');
    });

});












//Это такой ахуенный сокращения ссылок в публичку для соц сетей типа 1..7
$('.fb').click(function () {
    var clickId = $(this).attr('id');
    let fbcount = $('#' + clickId);
    let fb = $('#link'+ clickId);
    fb.css('display', 'inline')
    fbcount.css('display', 'none')
});
$('.inst').click(function () {
    var clickId = $(this).attr('id');
    let instcount = $('#' + clickId);
    let inst = $('#link'+ clickId);
    inst.css('display', 'inline')
    instcount.css('display', 'none')
});
$('.vk').click(function () {
    var clickId = $(this).attr('id');
    let vkcount = $('#' + clickId);
    let vk = $('#link'+ clickId);
    vk.css('display', 'inline')
    vkcount.css('display', 'none')
});
$('.ok').click(function () {
    var clickId = $(this).attr('id');
    let okcount = $('#' + clickId);
    let ok = $('#link'+ clickId);
    ok.css('display', 'inline')
    okcount.css('display', 'none')
});
$('.tt').click(function () {
    var clickId = $(this).attr('id');
    let ttcount = $('#' + clickId);
    let tt = $('#link'+ clickId);
    tt.css('display', 'inline')
    ttcount.css('display', 'none')
});
$('.yt').click(function () {
    var clickId = $(this).attr('id');
    let ytcount = $('#' + clickId);
    let yt = $('#link'+ clickId);
    yt.css('display', 'inline')
    ytcount.css('display', 'none')
});
$('.tg').click(function () {
    var clickId = $(this).attr('id');
    let tgcount = $('#' + clickId);
    let tg = $('#link'+ clickId);
    tg.css('display', 'inline')
    tgcount.css('display', 'none')
});
$('.yd').click(function () {
    var clickId = $(this).attr('id');
    let ydcount = $('#' + clickId);
    let yd = $('#link'+ clickId);
    yd.css('display', 'inline')
    ydcount.css('display', 'none')
});
$('.yr').click(function () {
    var clickId = $(this).attr('id');
    let yrcount = $('#' + clickId);
    let yr = $('#link'+ clickId);
    yr.css('display', 'inline')
    yrcount.css('display', 'none')
});

//а вот это комм выходы
$('.vkcomm').click(function () {
    var clickId = $(this).attr('id');
    let vkcommcount = $('#' + clickId);
    let vkcomm = $('#link'+ clickId);
    vkcomm.css('display', 'inline')
    vkcommcount.css('display', 'none')
});
$('.fbcomm').click(function () {
    var clickId = $(this).attr('id');
    let fbcount = $('#' + clickId);
    let fb = $('#link'+ clickId);
    fb.css('display', 'inline')
    fbcount.css('display', 'none')
});
$('.instcomm').click(function () {
    var clickId = $(this).attr('id');
    let instcount = $('#' + clickId);
    let inst = $('#link'+ clickId);
    inst.css('display', 'inline')
    instcount.css('display', 'none')
});
$('.okcomm').click(function () {
    var clickId = $(this).attr('id');
    let okcount = $('#' + clickId);
    let ok = $('#link'+ clickId);
    ok.css('display', 'inline')
    okcount.css('display', 'none')
});
$('.ttcomm').click(function () {
    var clickId = $(this).attr('id');
    let ttcount = $('#' + clickId);
    let tt = $('#link'+ clickId);
    tt.css('display', 'inline')
    ttcount.css('display', 'none')
});
$('.ytcomm').click(function () {
    var clickId = $(this).attr('id');
    let ytcount = $('#' + clickId);
    let yt = $('#link'+ clickId);
    yt.css('display', 'inline')
    ytcount.css('display', 'none')
});
$('.tgcomm').click(function () {
    var clickId = $(this).attr('id');
    let tgcount = $('#' + clickId);
    let tg = $('#link'+ clickId);
    tg.css('display', 'inline')
    tgcount.css('display', 'none')
});
$('.ydcomm').click(function () {
    var clickId = $(this).attr('id');
    let ydcount = $('#' + clickId);
    let yd = $('#link'+ clickId);
    yd.css('display', 'inline')
    ydcount.css('display', 'none')
});
$('.yrcomm').click(function () {
    var clickId = $(this).attr('id');
    let yrcount = $('#' + clickId);
    let yr = $('#link'+ clickId);
    yr.css('display', 'inline')
    yrcount.css('display', 'none')
});







// $('#auth_user_telegram').on('click',function () {
//     let phone = $('[name ^= "telegram_phone"]').val();
//
//     $.ajax({
//         type: 'POST',
//         url: '/madeline/auth-madeline',
//         data: { phone: phone },
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
//         },
//         beforeSend: function() {
//
//         },
//         success: function (data) {
//
//         },
//         error: function() {
//
//         }
//
//     });
//
// });
