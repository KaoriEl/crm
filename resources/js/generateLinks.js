require('./bootstrap');

$('.more').click(function () {
    $arr=[]
    $input = $('.d-flex.align-items-center');
    $id = ($(this)).parent()[0].id;
    $parent = $(this).parent(".form-group.has-icon")
    $past = $parent.find($input).first();
    let button = $parent.find('.more');
    $span = $past.find('span');
    try {
        $span = '<span class="' + $span[0].classList.value + '">' + $span[0].innerHTML + '</span>';
        $name = 'smm_links[' + $id + '][][link]';
        $result = button.first().after('<div class="d-flex align-items-center">\n' + $span +
            '\n' +
            '                        <input type="url" name="' + $name +'" id="vk_post_url" class="form-control" value=""></div></div><br>');
    }catch (e) {
        $svg = '     <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="20px" height="20px" style="margin-right: 10px; -ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path d="M2 2h20v20H2V2m9.25 15.5h1.5v-4.44L16 7h-1.5L12 11.66L9.5 7H8l3.25 6.06v4.44z" fill="#626262"/></svg>';
        $name = 'smm_links[' + $id + '][][link]';
        $result = button.first().after('<div class="d-flex align-items-center">\n' + $svg +
            '\n' +
            '                        <input type="url" name="' + $name +'" id="vk_post_url" class="form-control" value=""></div></div><br>');
    }


    // console.log($result.get(0).childNodes);
    // $div.clone().appendTo($(this));
    // $(this).$div.appendTo($div);
});


$('.moreSeed').click(function () {
    $arr=[]
    $input = $('.d-flex.align-items-center');
    $id = ($(this)).parent()[0].id;
    $parent = $(this).parent(".form-group.has-icon")
    $past = $parent.find($input).first();
    let button = $parent.find('.moreSeed');
    $span = $past.find('span');
    try {
        $span = '<span class="' + $span[0].classList.value + '">' + $span[0].innerHTML + '</span>';
        $name = 'seed_links[' + $id + '][][link]';
        $result = button.first().after('<div class="d-flex align-items-center">\n' + $span +
            '\n' +
            '                        <input type="url" name="' + $name +'" id="vk_post_url" class="form-control" value=""></div></div><br>');
    }catch (e) {
        $svg = '     <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="20px" height="20px" style="margin-right: 10px; -ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path d="M2 2h20v20H2V2m9.25 15.5h1.5v-4.44L16 7h-1.5L12 11.66L9.5 7H8l3.25 6.06v4.44z" fill="#626262"/></svg>';
        $name = 'seed_links[' + $id + '][][link]';
        $result = button.first().after('<div class="d-flex align-items-center">\n' + $svg +
            '\n' +
            '                        <input type="url" name="' + $name +'" id="vk_post_url" class="form-control" value=""></div></div><br>');
    }

});
