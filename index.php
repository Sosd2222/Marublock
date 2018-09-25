<?php
  /*
  제작: SOSD(KDW)
  제작시작: 2018-09-23
  */
  $opts = [
      "http" => [
          "method" => "GET",
          "header" => "Accept-language: en\r\n".
          "User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)\r\n".
          "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" /* HTTP HEADER */
      ]
  ];
  $link = $_REQUEST['link']; //링크를 요청받습니다.
  $marulink = "http://wasabisyrup.com/archives/"; //만화 있는 링크
  $marulink_path = "http://wasabisyrup.com"; //도메인
  $marulink_img = "http://wasabisyrup.com/storage/gallery/"; //이미지 링크

  /* html form */
  echo "<form action=\"index.php\" method=\"get\">";
  echo "만화 링크: <input type=\"text\" name=\"link\" value=\"$link\">";
  echo "&nbsp;<input type=\"submit\" value=\"GET\">";
  echo "</form>";

  $context = stream_context_create($opts);
  $source = file_get_contents($link, false, $context);

  $title_temp = explode('<title>', $source);
  $title_temp = explode('</title>', $title_temp[1]);
  $title = $title_temp[0]; //타이틀값
  $title_temp_2 = explode(" |", $title);
  $title = $title_temp_2[0];

  /* 저장용 타이틀값  */
  $title_replace = str_replace(' ', '_', $title); //공백을 _ 로 치환
  $title_replace = preg_replace('/\?/', '$$', $title_replace); //? 가 있으면 웹상에서 읽지 못함

  /* 만화 리스트 가져오기 */
  $list = explode('<select class="list-articles select-js-inline select-js-nofocus select-js-inline-right">', $source);
  $list = explode('</select>', $list[1]);
  $list = $list[0];

  echo "<title>$title</title>";
  echo "<select onchange=\"location.href='./index.php?link=$marulink' + this.value\">";
  echo $list;
  echo "</select>";

  /* 다음화 보기 */
  preg_match('/btn btn-go-next[\s\S]+?[^>]*/', $source, $next); //next 글 매칭
  $next[0] = preg_replace('/(btn btn-go-next|\s|href=|")/', '', $next[0]); //삭제

  if ($next[0] != 'disabled') {
    echo "&nbsp;<a href=\"./index.php?link=".$marulink_path.$next[0]."\" style=\"color:#000000; text-decoration:none;\">다음화 보기</a>";
  }
  echo "<br>";
  /* 이미지 매칭 시작 */
  preg_match_all("/data-src=(\"|')[\s\S]+?[^\"]*/", $source, $img); //이미지 매칭
  for ($i=0; $i<count($img[0]); $i++) {
    $img_path[$i] = preg_replace('/(data-src|"|\'|=)/', '', $img[0][$i]);
    $img_path[$i] = $marulink_path.$img_path[$i];
  }

  for ($i=0; $i<count($img[0]); $i++) {
    $img_name[$i] = basename($img_path[$i]);
  }
  /* 이미지 저장 시작 */
  $opt_img = [ //http header for img
    "http" => [
        "method" => "GET",
        "header" => "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8\r\n".
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36\r\n". //user agent
        "Referer: $link" /* HTTP HEADER */
    ]
  ];
  $context_img = stream_context_create($opt_img);

  $count = count($img_name);

  for ($i=0; $i<$count; $i++) {
    $imgpath_r = "./result/img/img_{$i}_".$img_name[$i]; //이미지경로+img[$i]+랜덤_문자열_10자리.확장자
    file_put_contents($imgpath_r, file_get_contents($img_path[$i], false, $context_img)); //get contents
    echo "<br><img src=\"".$imgpath_r."\">";
  }
 ?>
