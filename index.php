<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Music Player</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" media="screen" href="./main.css" />
    <script src="./APlayer.min.js"></script>
	<script src="./color-thief.js"></script>
	<link rel="stylesheet" media="screen" href="./APlayer.min.css" />
<link rel="stylesheet" media="screen" href="/css/cardview.css" />
</head>
<body class="select">
	<br>
    <div class="container">
		<form action="" method="get">
			<center>
				<table width="100%">
					<tr>
						<td>
							<h2>Arcaea!</h2>
						</td>
						<td>
							<span style="float:right">
								<input type="text" id="id" name="id" placeholder="검색어 입력" style = "text-align:center;" autocomplete="off">
							</span>
						</td>
					</tr>
				</table>
			</center>
		</form>
		<div id="myplayer" class="aplayer"></div>
        <details>
			<summary>자세한 정보</summary>
			<div class="card" id= "a">
				<div class="card-body"> 
					<div class="row justify-content-center">
						<div class="col-lg-5 col-md-12 mb-3 mb-lg-0" id="image">
							<div id="slider"></div>
						</div>
					<div class="col-lg-7 col-md-12 my-auto">
						<h2 class="display-3" id="title"></h2> 
						<span class="text-muted display-4" id="artist"></span>
						<hr class="my-3"> 
							<div class="row">
								<div class="col-3">
									<h2 class="display-4">BPM</h2>
								</div> 
								<div class="col-9">
									<h2 class="display-4">
										<small id="bpm"></small>
									</h2>
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<h2 class="display-4">난이도</h2>
								</div> 
								<div class="col-9">
									<h2 class="display-4">
										<div class="diff" id="diff_0" title="PST"></div>
										<div class="diff" id="diff_1" title="PRS"></div>
										<div class="diff" id="diff_2" title="FTR"></div>
										<div class="diff" id="diff_3" title="BYD"></div>
									</h2>
								</div>
							</div>
						</div>
					</div> 
				</div>
			</div>
		</details>
    </div>
    <script>
        if (/mobile/i.test(window.navigator.userAgent)) {
            new VConsole();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jquery"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript">
		<?php
			//PHP START
			include('./lib/Snoopy.class.php');
			$multi = false;
			function endsWith($haystack, $needle) {
				return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
			}
			function get($url) {
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

				$resp = curl_exec($curl);
				curl_close($curl);
				return $resp;
			}
			function contains($str, $text) { 
				return (strpos($text, $str) !== false); 
			}
			//데이터 받아오기
			if(isset($_GET['id']) && $_GET['id']) {
				$msg = trim(urldecode($_GET['id'])); //하나짜리
				if(contains(',', $msg)) { //여러개(재생목록)
					$multi = true;
					$data_arr = array();
					if(endsWith($msg, ',')) { // 끝이 , 로 끝나면 (자동완성받은거 안고치고 그래도 제출)
						$msg = trim(substr($msg, 0, -1));
						$a = explode(', ', $msg); //title array
						for($i = 0; $i < count($a); $i++) {
							$resp = get('http://vectorbot.dothome.co.kr/arcaea/getMusicData.php?songName='.urlencode(trim($a[$i])).'&type=input');
							array_push($data_arr, $resp);
						}
					}
					else { //불편해서 끝에 콤마 지우고 냈으면
						$a = explode(',', $msg); //title array
						for($i = 0; $i < count($a); $i++) {
							$resp = get('http://vectorbot.dothome.co.kr/arcaea/getMusicData.php?songName='.urlencode(trim($a[$i])).'&type=input');
							array_push($data_arr, $resp);
						}
					}
				}
				else  {
					$multi = false;
					$data = get('http://vectorbot.dothome.co.kr/arcaea/getMusicData.php?songName='.$msg.'&type=input');
				}
				//하나짜리면 $data
				//재생목록이면 $data_arr 받으면 됨
			}
			else { //하나짜리 랜덤
				$multi = false;
				$data = get('http://vectorbot.dothome.co.kr/arcaea/getMusicData.php?songName=null&type=random');
			}
			//var_dump($data_arr);
			function getdata($data, $bool) {
				if($bool === false) { //하나짜리
					$data = substr($data, 3);
					//var_dump($data);
					$data = json_decode($data, true)['content'];
					$title = addslashes($data['Song']); //제목
					$artist = addslashes($data['Artist']); //아티스트
					$difficulty = array($data['PST'], $data['PRS'], $data['FTR'], $data['BYD']); //난이도 배열
					$difficulty = json_encode($difficulty, true);
					$bpm = $data['BPM']; //BPM
					$jacket = 'http://vectorbot.dothome.co.kr/arcaea/resize/'.$data['No'].'.jpg'; //자켓
			
					$url = $data['url'];
					$dual = array(2, 3, 10, 20, 22, 96, 113, 134, 161); //자켓이 2개인 번호
					if(in_array($data['No'], $dual))  $dual = 'true';
					else $dual = 'false';
					
					if(contains('soundcloud', $url)) {
						$url = urlencode($url);
						$resp = get("http://vectorbot.dothome.co.kr/sc/index.php?link=$url");
						$temp = json_decode($resp, true);
						$url = $temp['content']['stream_url'];
					}
					else { // (contains('youtube', $url) || contains('youtu.be'))
						$url = urlencode($url);
						$resp = get("http://vectorbot.dothome.co.kr/yt/index.php?link=$url");
						$temp = json_decode($resp, true);
						$url = $temp['content'][0]['url']; 
					}
					return array($title, $artist, $bpm, $difficulty, $jacket, $url, $dual);
				}
				else {
					$arr = array();
					for($i = 0; $i < count($data); $i++) {
						array_push($arr, getdata($data[$i],false));
					}
					return $arr;
				}
			}
			if($multi == false) { //하나짜리
				$data_f = getdata($data, false);
				//var_dump($data_f);
				if($data_f[0] != '') {
					$title = $data_f[0]; $artist = $data_f[1]; $bpm = $data_f[2]; $difficulty = $data_f[3]; 
					$jacket = $data_f[4]; $url = $data_f[5]; $dual = $data_f[6];
					$result = "[\n\t\t\t'$title',\n\t\t\t'$artist',\n\t\t\t$bpm,\n\t\t\t$difficulty,\n\t\t\t'$jacket',\n\t\t\t'$url'\n\t\t];";
					echo ("const data = $result\n\t\tconst dual = $dual;\n");
				}
				else {
					$data = get('http://vectorbot.dothome.co.kr/arcaea/getMusicData.php?songName=null&type=random');
					$data_s = getdata($data, false);
					$title = $data_s[0]; $artist = $data_s[1]; $bpm = $data_s[2]; $difficulty = $data_s[3]; 
					$jacket = $data_s[4]; $url = $data_s[5]; $dual = $data_s[6];
					$result = "[\n\t\t\t'$title',\n\t\t\t'$artist',\n\t\t\t$bpm,\n\t\t\t$difficulty,\n\t\t\t'$jacket',\n\t\t\t'$url'\n\t\t];";
					echo ("alert('일치하는 곡명이 없습니다. 무작위 노래를 재생합니다.');\n\t\tconst data = $result\n\t\tconst dual = $dual;\n");
				}
			}
			else {
				$data_f = getdata($data_arr, true);
				//dual 변수 분리
				$v = array(); //data
				$dual_arr = array(); // bool dual
				for($i = 0; $i < count($data_f); $i++) {
					$v_ = array();
					for($j = 0; $j < 6; $j++) {
						array_push($v_, $data_f[$i][$j]);
					}
					array_push($dual_arr, $data_f[$i][6]);
					array_push($v, $v_);
				}
				$result = json_encode($v, JSON_PRETTY_PRINT);
				$dual = json_encode($dual_arr, true);
				echo "\n\t\tconst data = $result;\n\t\tconst dual = $dual;\n";
			}
			//PHP FINISH
		?>
	</script>
	<script src="./main.js"></script>
</body>
</html>