<?php

/**
 * 
 */
class SqlLog
{
	protected $filename = null;
	protected $action = null;
	
	function __construct($filename, $action)
	{
		$this->filename = $filename;
		$this->action = $action;
	}

	public function index()
	{
		$error = '';

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

				$htmlArr = [];
				$tempArr = [];

				$replaceArr = require APP_PATH . 'base/replace.php';

				$isTitle = false;

				//逐行读取文件
				while(!feof($fp))
				{	
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

	/*
	 * 特殊处理字符
	 */
	protected function getLogList($strArr = [])
	{
		if (empty($strArr))
			return [$this->specialHtml('内容为空', '')];

		if (!is_array($strArr))
			return [$this->specialHtml($strArr, '')];

		$list = [];
		foreach ($strArr as $key => $value) {
			foreach ($value as $kk => $vv) {
				foreach ($vv as $k => $v) {
					if (!empty($v)) {
						$list[] = $this->specialHtml($v, '');
					}
				}
			}
		}

		return $list;
	}

	protected function specialHtml($content, $css = '')
	{
		$content = trim($content);
		return "<div {$css}>{$content}</div>";
	}	
}