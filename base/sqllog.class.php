<?php

/**
 * 
 */
class SqlLog
{
	protected $filename = null;
	protected $action = null;
	protected $replaceArr = [];
	protected $page = 1;
	protected $pagesize = 200;
	
	function __construct($filename, $action)
	{
		$this->filename = $filename;
		$this->action = $action;

		$this->replaceArr = require APP_PATH . 'base/replace.php';

	}

	public function index($page=1, $pagesize = 200)
	{
		$error = '';
		$this->page = $page;
		$this->pagesize = $pagesize;

		//目录存在
		if (!empty($this->filename) && is_file($this->filename)) {

			$this->filename = str_replace('\\', '/', $this->filename);

			if ($this->action == 'del') {
				//可写文件判断
				$fp = @fopen($this->filename, 'w+'); //打开文件指针，创建文件
				if ( !is_writable($this->filename) ){
					$error .= 'log文件:' .$this->filename. '不可写，请检查！ 或尝试删除log文件 , 重启mysql';
				} else {
				   	fwrite($fp, '');
				}	
				@fclose($fp);  //关闭指针
			} else {
				//可读文件判断
				if (!is_readable($this->filename)) { 
					$error = 'log文件:' .$this->filename. '不可读，请检查！ 或尝试删除log文件 , 重启mysql';
				}
			}

			//错误码非空
			if (empty($error)) {

				$fp = @fopen($this->filename, 'r');

				$offset = ($page - 1) * $pagesize;

				$htmlArr = [];
				$tempArr = [];

				$isTitle = false;

				$start = 0;

				//逐行读取文件
				while(!feof($fp))
				{	
					if ($start >= $offset && $start <= ($offset + $pagesize)) {
						$str = fgets($fp);
						//是链接数据库的语句 特殊处理
						if(preg_match('/Connect/', $str)){
							if (!empty($tempArr)) {
								$htmlArr[] = $tempArr;
								$tempArr = [];
							}

							$isTitle = true;
						} else {
							$isTitle = false;
						}
						$str = trim($str);
						if (!empty($str))
							$tempArr[$isTitle ? 'title' : 'list'][] = $str;
					} else {
						fgets($fp);
					}
					
					$start ++ ;
				}

				if (!empty($tempArr)) {
					$htmlArr[] = $tempArr;
					$tempArr = [];
				}
				
				@fclose($fp);  //关闭指针
			}
		} else {
			$error .= 'log文件:' .$this->filename. '不存在, 请正确配置 .env 文件';
		}

		return $this->getLogList(!empty($htmlArr) ? $htmlArr : $error);
	}

	public function getTotal()
	{
		$total = 0;
		if (!empty($this->filename) && is_file($this->filename)) {
			$fp = @fopen($this->filename, 'r');

			while(!feof($fp))
			{
				fgets($fp);
				$total ++ ;
			}

			if ($total > 0) {
				$total ++;
			}

			fseek($fp, 0);

			@fclose($fp);  //关闭指针
		}

		return $total;
	}

	/*
	 * 特殊处理字符
	 */
	protected function getLogList($strArr = [])
	{
		if (empty($strArr))
			return [];

		if (!is_array($strArr))
			return [$this->specialHtml($strArr, '')];

		$list = [];
		$count = ($this->page - 1) * $this->pagesize;
		foreach ($strArr as $key => $value) {
			foreach ($value as $kk => $vv) {
				foreach ($vv as $k => $v) {
					$v = trim($v);
					if (!empty($v)) {
						$list[] = $this->specialHtml($v, $kk=='title' ? 'class="connect"' : 'class="query"', $count++);
					}
				}
			}
		}

		return $list;
	}

	protected function specialHtml($content, $css = '', $sort = 0)
	{
		$content = trim($content);
		//匹配查询主体
		if (false !== strrpos($content, 'Query') || false !== strrpos($content, 'SELECT') || false !== strrpos($content, 'UPDATE') || false !== strrpos($content, 'INSERT') || false !== strrpos($content, 'DELETE')) {

			if (false !== strrpos($content, 'Query'))
				$tmpstr = substr($content, strrpos($content, 'Query') + 6);
			else
				$tmpstr = $content;

			$content = substr($content, 0, strrpos($content, 'Query')) . '<span class="query-content" attr="copy-'.$sort.'" id="copy-'.$sort.'">'.$tmpstr.'</span>';
		}
		//替换通用查询字符
		$content = preg_replace('/([0-9]{1,} Query)/', '', $content);

		// 关键字匹配突出
		$content = str_replace($this->replaceArr, array_map(function($value){
			if (false !== strpos($value, '(')) {
				$str = '<span class="special">'.trim($value, '(').'</span>(';
			} else {
				$str = '<span class="special">'.$value.'</span>';
			}
			return $str;
		}, $this->replaceArr), $content);

		return "<div {$css} title='COPY'>{$content}</div>";
	}	
}