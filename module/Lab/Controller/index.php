<?php
class IndexController extends AbstractController {
	public function __construct() {
		
	}
	public function indexAction(){
          // $url = "http://cgi.music.soso.com/fcgi-bin/m.q?w=%CA%AE%C4%EA";
         /*  $url = 'http://douban.fm/j/mine/playlist?type=n&channel=1';
          $ch = curl_init();
          curl_setopt ($ch, CURLOPT_URL, $url);
          curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux i686; zh-CN; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
          curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10);
          $html = curl_exec($ch);
          echo $html;exit();
          // $pat = '/<a(.*?)href="(.*?)"(.*?)>(.*?)<\/a>/i';
          $pat = '/<a(.*)target="downloadHiddenFrame"(.*)<\/a>/i';
          preg_match_all($pat, $html, $arr);
          print_r($arr); exit();
          // "<a\s+href=(?<url>.+?)>(?<content>.+?)</a>" */
		
	}
}