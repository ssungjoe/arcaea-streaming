<?php
	require_once __DIR__ . '/musicInfo.php';
	if(isset($_GET['songName']) && $_GET['songName']) 
		$name = urldecode($_GET['songName']);
	if(isset($_GET['type']) && $_GET['type']) 
		$type = $_GET['type'];
	if($name != 'null' && $type == 'input') {
		$index = -1; //song index ($info)
		for($i = 0; $i < count($info); $i++) {
			$arr = $info[$i]['song'];
			$key = in_array($name, $arr);
			if($key == true) {
				$index = $i;
				$name = $arr[count($arr) - 1];
			}
		}
		if($index == -1) {
			print_r(json_encode(array('error'=>true, 'message'=>'no match')));
			die;
		}
		$url = $info[$index];
		$index = -1; 
		for($i = 0; $i < count($more); $i++) {
			$song = $more[$i]['Song'];
			$key = $name == $song;
			if($key == true) $index = $i;
		}
		$d = $more[$index];
		$d['url'] = $url['url'];
	
		header('Content-type: application/json');
		print_r(json_encode(array('error'=>false, 'content'=>$d)));
		die;
	}
	else {
		$rand = mt_rand(0, count($more) - 1);
		$title = $more[$rand]['Song'];
		$index = -1;
		for($i = 0; $i < count($info); $i++) {
			$arr = $info[$i]['song'];
			$key = in_array($title, $arr);
			if($key == true) {
				$index = $i;
			}
		}
		$url = $info[$index]['url'];
		$d = $more[$rand];
		$d['url'] = $url;

		header('Content-type: application/json');
		print_r(json_encode(array('error'=>false, 'content'=>$d)));
		die;
	}