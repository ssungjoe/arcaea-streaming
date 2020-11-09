<?php
	require_once __DIR__ . '/musicInfo.php';
	header("Content-Type: application/json");
	$query = $_GET['term'];
	$list = array();
	for($i = 0; $i < count($more); $i++) {
		array_push($list, $more[$i]['Song']);
	}
	usort($list, function ($a, $b) use ($query) { //검색어와 유사도순 배열
		similar_text(strtolower($query), strtolower($a), $percentA);
		similar_text(strtolower($query), strtolower($b), $percentB);
		return $percentA === $percentB ? 0 : ($percentA > $percentB ? -1 : 1);
	});
	$result = []; //검색어가 포함된 단어 배열 
	foreach($list as $res) {
		if(stripos($res, $query) !== false) {
			$result[] = array("label" => $res, "value" => $res);
		}
	}
	if(count($result) > 9) { //상위 10개만 가져옴
		$result = array_splice($result, 0, 10);
	}
	print_r(json_encode($result));
?>