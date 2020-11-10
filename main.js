$(function () {
    $("#id").autocomplete({
        minLength: 1, //최소 길이
        source: function (request, response) {
            $.getJSON("/arcaea/keyword.php", {
                term: extractLast(request.term)
            }, response);
        },
        focus: function () {
            // prevent value inserted on focus
            return false;
        },
        select: function (event, ui) {
            var terms = split(this.value);
            terms.pop();
            terms.push(ui.item.value);
            terms.push("");
            this.value = terms.join(", ");
            return false;
        }
    }).data("ui-autocomplete")._renderItem = function (ul, item) { //일치대상 하이라이팅
        let txt = String(item.value).replace(new RegExp(extractLast(this.term), "gi"), "<font color=tomato><b>$&</b></font>");
        return $("<div></div>")
        .data("ui-autocomplete-item", item)
        .append('<a>' + txt + "</a>")
        .appendTo(ul);
    };
    function split(val) {
        return val.split(/,\s*/);
    }
    function extractLast(term) {
        return split(term).pop();
    }
    
});

//검색 결과 없음
if (data.join('') == '') {
    alert('No Match.');
}

let m = false;
if (typeof dual[0] == 'string') m = true;

//세부정보
if (!m) {
    data = data.map(x=> { if (typeof x == 'string') return x.replace(/\\/g, ''); else return x;});
    if (dual) document.getElementById('slider').innerHTML = '<a href="#" class="control_next">></a>\n<a href="#" class="control_prev"><</a>\n<ul>\n	<li>\n		<img class="img-center img-fluid shadow shadow-lg--hover" style="width: 350px;" data-src="' + data[4] + '" src="' + data[4] + '" lazy="loaded">\n	</li>\n	<li>\n		<img class="img-center img-fluid shadow shadow-lg--hover" style="width: 350px;" data-src="' + data[4].replace('.jpg', '_1.jpg') + '" src="' + data[4].replace('.jpg', '_1.jpg') + '" lazy="loaded">\n	</li>\n</ul>';
    else document.getElementById('slider').innerHTML = '<li>\n	<img class="img-center img-fluid shadow shadow-lg--hover" style="width: 350px;" data-src="' + data[4] + '" src="' + data[4] + '" lazy="loaded">\n</li>';

    document.getElementById('title').innerText = data[0];
    document.getElementById('artist').innerText = data[1];
    document.getElementById('bpm').innerText = data[2];

    for (let i = 0; i < data[3].length; i++)
        document.getElementById('diff_' + i).innerText = data[3][i];
    if (data[3][3] == '') {
        document.getElementById('diff_3').innerText = '0';
        document.getElementById('diff_3').style.opacity = 0;
        document.getElementById('diff_3').style.width = 0;
    }
    if (data[3][2] == '?') {
        for (let i = 0; i < 3; i++) {
            if (i == 2) break;
            document.getElementById('diff_' + i).innerText = '0';
            document.getElementById('diff_' + i).style.opacity = 0;
            document.getElementById('diff_' + i).style.width = 0;
        }
    
    }
}
else {
    data = data.map(x=>x.map(y=>y.replace(/\\/g, '')));
    let container = document.getElementById('a');
    container.innerHTML = '';
    for (let j = 0; j < data.length; j++) {
        data[j][3] = eval(data[j][3]);
        let card = document.createElement('div');
        card.className = 'card-body';
        Element.prototype.getElementById = function (id) {
            return document.getElementById(id);
        };
        let slider; //커버
        if (dual[j] == 'true') slider = '<a href="#" class="control_next" id="con_n_' + j + '">></a>\n<a href="#" class="control_prev" id="con_p_' + j + '"><</a>\n<ul>\n	<li>\n		<img class="img-center img-fluid shadow shadow-lg--hover" style="width: 350px;" data-src="' + data[j][4] + '" src="' + data[j][4] + '" lazy="loaded">\n	</li>\n	<li>\n		<img class="img-center img-fluid shadow shadow-lg--hover" style="width: 350px;" data-src="' + data[j][4].replace('.jpg', '_1.jpg') + '" src="' + data[j][4].replace('.jpg', '_1.jpg') + '" lazy="loaded">\n	</li>\n</ul>';
        else slider = '<li>\n	<img class="img-center img-fluid shadow shadow-lg--hover" style="width: 350px;" data-src="' + data[j][4] + '" src="' + data[j][4] + '" lazy="loaded">\n</li>';

        let title = data[j][0];
        let artist = data[j][1];
        let bpm = data[j][2];

        let diff = [];
        for (let i = 0; i < data[j][3].length; i++) {
            let level = ['PST', 'PRS', 'FTR', 'BYD'];
            if (data[j][3][3] == '' && i == 3) break; 
            if (data[j][3][2] == '?') {
                diff.push('<div class="diff" id="diff_2" title="FTR">?</div>');
                break;
            }
            diff.push('<div class="diff" id="diff_' + i + '" title="' + level[i] + '">' + data[j][3][i] + '</div>');
        }
        diff = diff.join('\n');

        card.innerHTML = `<div class="row justify-content-center">
						<div class="col-lg-5 col-md-12 mb-3 mb-lg-0" id="image">
							<div class ="hidden" id= "slider_` + j + `" >${slider}</div>
						</div>
					<div class="col-lg-7 col-md-12 my-auto">
						<h2 class="display-3" id="title">${title}</h2> 
						<span class="text-muted display-4" id="artist">${artist}</span>
						<hr class="my-3"> 
							<div class="row">
								<div class="col-3">
									<h2 class="display-4">BPM</h2>
								</div> 
								<div class="col-9">
									<h2 class="display-4">
										<div id="bpm">${bpm}</div>
									</h2>
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<h2 class="display-4">난이도</h2>
								</div> 
								<div class="col-9">
									<h2 class="display-4">
										${diff}
									</h2>
								</div>
							</div>
						</div>
					</div> 
                 `;
        container.appendChild(card);
    }
}
//슬라이드바
if (!m) {
    jQuery(document).ready(function () {
        var slideCount = $('#slider ul li').length;
        var slideWidth = $('#slider ul li').width();
        var slideHeight = $('#slider ul li').height();
        var sliderUlWidth = slideCount * slideWidth;
        $('#slider').css({ width: slideWidth, height: slideHeight });
        $('#slider ul').css({ width: sliderUlWidth, marginLeft: -slideWidth });
        $('#slider ul li:last-child').prependTo('#slider ul');

        function moveLeft() {
            $('#slider ul').animate({
                left: +slideWidth
            }, 200, function () {
                $('#slider ul li:last-child').prependTo('#slider ul');
                $('#slider ul').css('left', '');
            });
        };
        function moveRight() {
            $('#slider ul').animate({
                left: -slideWidth
            }, 200, function () {
                $('#slider ul li:first-child').appendTo('#slider ul');
                $('#slider ul').css('left', '');
            });
        };
        $('a.control_prev').click(function () {
            moveLeft();
            return false;
        });
        $('a.control_next').click(function () {
            moveRight();
            return false;
        });
    });
}
else {
    for (let j = 0; j < data.length; j++) {
        jQuery(document).ready(function () {
            var slideCount = $('#slider_' + j + ' ul li').length;
            var slideWidth = $('#slider_' + j + ' ul li').width();
            var slideHeight = $('#slider_' + j + ' ul li').height();
            var sliderUlWidth = slideCount * slideWidth;
            $('#slider_' + j).css({ width: slideWidth, height: slideHeight });
            $('#slider_' + j + ' ul').css({ width: sliderUlWidth, marginLeft: -slideWidth });
            $('#slider_' + j + ' ul li:last-child').prependTo('#slider_' + j + ' ul');
            function moveLeft() {
                $('#slider_' + j + ' ul').animate({
                    left: +slideWidth
                }, 200, function () {
                    $('#slider_' + j + ' ul li:last-child').prependTo('#slider_' + j + ' ul');
                    $('#slider_' + j + ' ul').css('left', '');
                });
            };
            function moveRight() {
                $('#slider_' + j + ' ul').animate({
                    left: -slideWidth
                }, 200, function () {
                    $('#slider_' + j + ' ul li:first-child').appendTo('#slider_' + j + ' ul');
                    $('#slider_' + j + ' ul').css('left', '');
                });
            };
            $('a#con_p_' + j + '.control_prev').click(function () {
                moveLeft();
                return false;
            });
            $('a#con_n_' + j + '.control_next').click(function () {
                moveRight();
                return false;
            });
        });
    }
}

//플레이어
const json = {
    element: document.getElementById('myplayer'),	// Element
    narrow: false,	// Narrow
    autoplay: true,	// 자동재생(모바일 X)
    showlrc: 2,	// 가사 형태(0,1,2)
    mutex: true,	// 플레이 중일 때 다른 플레이어 정지
    theme: '#46718b',	// 색(기본 #b7daff)
    mode: 'random',	// 플레이 모드(random, single, circulation(반복), order) (기본 circulation)
    preload: 'metadata',	// 음악 로드 방식(none, metadata, auto) (기본 auto)
    listmaxheight: '513px',
    audio: []
};
if (!m) {
    json.audio.push(
        {
            title: data[0], //제목
            author: data[1], //아티스트
            pic: data[4], //커버
            url: data[5], //음악
            lrc: '[00:00.00]No Lyrics'
        }
    );
}
else {
    for (let i in data) {
        console.log(JSON.stringify(data[i]));
        json.audio.push(
            {
                title: data[i][0], //제목
                author: data[i][1], //아티스트
                pic: data[i][4], //커버
                url: data[i][5], //음악
                lrc: '[00:00.00]No Lyrics'
            }
        );
    }
}
const ap4 = new APlayer(json);

const colorThief = new ColorThief();
const image = new Image();
const xhr = new XMLHttpRequest();
const setTheme = (index) => {
    if (!ap4.list.audios[index].theme) {
        xhr.onload = function () {
            let coverUrl = URL.createObjectURL(this.response);
            image.onload = function () {
                let color = colorThief.getColor(image);
                ap4.theme(`rgb(${color[0]}, ${color[1]}, ${color[2]})`, index);
                URL.revokeObjectURL(coverUrl)
            };
            image.src = coverUrl;
        }
        xhr.open('GET', ap4.list.audios[index].cover, true);
        xhr.responseType = 'blob';
        xhr.send();
    }
};
setTheme(ap4.list.index);
ap4.on('listswitch', (data) => {
    setTheme(data.index);
});