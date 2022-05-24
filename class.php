<?php
class wGrab{
	protected $_html;
	protected $_url;

	function __construct($_url){
		$this->_url = $_url;
	}


	protected function exp($from, $to){
		$a = explode($from, $this->_html)[1];
		$b = explode($to, $a)[0];
		return $b;
	}
	protected function regex($ex){
		if(!preg_match($ex, $this->_html, $result)) return false;
		return $result;
	}
	protected function regexs($ex){
		if(!preg_match_all($ex, $this->_html, $result, PREG_SET_ORDER, 0)) return false;
		return $result;
	}


	public function grab(){
		$this->_html = file_get_contents($this->_url);
		return $this->_html;
	}
	public function minify(){
	   $search = array(
		    '/(\n|^)(\x20+|\t)/',
		    '/(\n|^)\/\/(.*?)(\n|$)/',
		    '/\n/',
		    '/\<\!--.*?-->/',
		    '/(\x20+|\t)/', # Delete multispace (Without \n)
		    '/\>\s+\</', # strip whitespaces between tags
		    '/(\"|\')\s+\>/', # strip whitespaces between quotation ("') and end tags
		    '/=\s+(\"|\')/',
		    '/\s+\/\s+/',
		    ); # strip whitespaces between = "'

	   $replace = array(
		    "\n",
		    "\n",
		    " ",
		    "",
		    " ",
		    "><",
		    "$1>",
		    "=$1",
		    "/",
		);
	    $this->_html = preg_replace($search,$replace, $this->grab());
	    return $this;
	}
}


class gogoplay5 extends wGrab
{
	private function fix_page_list($page_list){
		return [
			'title' => $page_list[4],
			'url' => $page_list[1],
			// 'url' => '/watch/'.explode('/videos/', $page_list[1])[1],
			'image' => [
				'title' => $page_list[3],
				'url' => "https://i1.wp.com/".explode('//', $page_list[2])[1]
			],
			'time' => $page_list[5],
		];
	}
	private function fix_pagination($pagination){
		return [
			'page' => $pagination[3],
			'url' => $pagination[2],
			// 'url' => preg_replace('/\?page=(\d+)$/', 'page/$1', $pagination[2]),
			'status' => str_replace("'", '', $pagination[1]),
		];
	}
	private function fix_stream_list($stream_list){
		return [
			'title' => $stream_list[6],
			'url' => $stream_list[1],
			# 'url' => str_replace('/videos/', '/watch/', $stream_list[1]),
			'type' => $stream_list[5],
			'image' => [
				'title' => $stream_list[4],
				'url' => "https://i1.wp.com/".explode('//', $stream_list[2])[1],
				'onerror' => "https://i1.wp.com/".explode('//', $stream_list[3])[1],
			],
			'time' => $stream_list[7],
		];
	}
	public function pages(){
		$list = $this->regexs('/<li class="video-block "><a href="(.*?)"><div class="img"><div class="picture"><img src="(.*?)" alt="(.*?)" \/><\/div><div class="hover_watch"><div class="watch"><\/div><\/div><\/div><div class="name"> (.*?) <\/div><div class="meta"><span class="date">(.*?)<\/span><\/div><\/a><\/li>/');
		$pagination = $this->regexs('/<li\s+(?:class=(active|\'next\'|\'previous\')|)><a href=\'(.*?)\' data-page=\'(\d+)\'>/');

		$result['list'] = array_map('self::fix_page_list', $list) ?? false;
		$result['Pagination'] = array_map('self::fix_pagination',$pagination) ?? false;


		return $result;
	}
	public function stream(){
		$main = $this->regex('/<h1>(.*?)<\/h1><div class="watch_play"><div class="play-video"><iframe src="(.*?)" allowfullscreen="true".*?"content-more-js" id="rmjs-1">(.*?)$/m');
		$eps_list = $this->regexs('/<li class="video-block "><a href="(.*?)"><div class="img"><div class="picture"><img onerror="this\.src=\'(.*?)\';" src="(.*?)" alt="(.*?)" \/><\/div><div class="hover_watch"><div class="watch"><\/div><\/div><div class="type .*?"><span>(.*?)<\/span><\/div><\/div><div class="name"> (.*?) <\/div><div class="meta"><span class="date">(.*?)<\/span><\/div><\/a><\/li>/');
		// get latest/side
		$exp_side = $this->exp('>Latest Episodes</h4>','<div class="clearfix"></div>');
		$side = $this->regexs('/<li class="video-block "><a href="(.*?)"><div class="img"><div class="picture"><img src="(.*?)" alt="(.*?)" \/><\/div><div class="hover_watch"><div class="watch"><\/div><\/div><\/div><div class="name"> (.*?) <\/div><div class="meta"><span class="date">(.*?)<\/span><\/div><\/a><\/li>/', $exp_side);


		$result['title'] = $main[1];
		$result['iframe'] = $main[2];
		$result['description'] = explode('</div></div></div><', $main[3])[0];
		$result['eps_list'] = array_map('self::fix_stream_list', $eps_list) ?? false;
		$result['latest_eps'] = array_map('self::fix_page_list', $side) ?? false;
		

		return $result;
		
	}
}
