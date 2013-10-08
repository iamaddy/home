<?php
class uploadController extends AbstractController {
	private $attachDir = 'public/upload';
	private $tempPath = "";
	private $localName = "";
	private $dirType = 1; // 1:按天存入目录 2:按月存入目录 3:按扩展名存目录  建议使用按天存
	private $upExt = 'txt,rar,zip,jpg,jpeg,gif,png'; // 上传扩展名
	private $msgType = 1;//返回上传参数的格式：1，只返回url，2，返回参数数组
	private $maxAttachSize = 2097152; // 最大上传大小，默认是2M
	private $immediate;
	private $err = "";
	private $msg = "";
	
	public function __construct() {
		
	}
	public function indexAction(){
		$inputName = 'filedata'; // 表单文件域name
		$this->tempPath = $this->attachDir.'/'.date("YmdHis").mt_rand(10000,99999).'.tmp';
		$this->immediate = isset($_GET['immediate']) ? $_GET['immediate'] : 0;//立即上传模式
		$this->getUploadFile($inputName);
		$this->msg = '/' . $this->msg;
		return new JsonView(array('err' => $this->err, 'msg' => $this->msg));
	}
	private function getUploadFile($inputName){
		// HTML5上传
		if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])&&preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){
			file_put_contents($this->tempPath, file_get_contents("php://input"));
			$this->localName = urldecode($info[2]);
		}
		else{
			// 标准表单式上传
			$upfile = @$_FILES[$inputName];
			if(!isset($upfile)) $this->err = '文件域的name错误';
			elseif(!empty($upfile['error'])){
				switch($upfile['error']) {
					case '1':
						$this->err = '文件大小超过了php.ini定义的upload_max_filesize值';
						break;
					case '2':
						$this->err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
						break;
					case '3':
						$this->err = '文件上传不完全';
						break;
					case '4':
						$this->err = '无文件上传';
						break;
					case '6':
						$this->err = '缺少临时文件夹';
						break;
					case '7':
						$this->err = '写文件失败';
						break;
					case '8':
						$this->err = '上传被其它扩展中断';
						break;
					case '999':
					default:
						$this->err = '无有效错误代码';
				}
			}
			elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none') {
				$this->err = '无文件上传';
			}
			else {
				move_uploaded_file($upfile['tmp_name'], $this->tempPath);
				$localName = $upfile['name'];
			}
		}
		$this->error();
	}
	private function error(){
		if($this->err == ''){
			$fileInfo = pathinfo($this->localName);
			$extension = $fileInfo['extension'];
			if(preg_match('/^(' . str_replace(',', '|', $this->upExt).')$/i', $extension)) {
				$bytes = filesize($this->tempPath);
				if($bytes > $this->maxAttachSize){
					$this->err = '请不要上传大小超过' . $this->formatBytes($this->maxAttachSize).'的文件';
				}
				else {
					switch($this->dirType) {
						case 1: $attachSubDir = 'day_'.date('ymd'); break;
						case 2: $attachSubDir = 'month_'.date('ym'); break;
						case 3: $attachSubDir = 'ext_'.$extension; break;
					}
					$this->attachDir = $this->attachDir.'/'.$attachSubDir;
					if(!is_dir($this->attachDir)) {
						@mkdir($this->attachDir, 0777);
						@fclose(fopen($this->attachDir.'/index.htm', 'w'));
					}
					PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
					$newFilename = date("YmdHis").mt_rand(1000,9999).'.'.$extension;
					$targetPath = $this->attachDir.'/'.$newFilename;
					rename($this->tempPath, $targetPath);
					@chmod($targetPath,0755);
					if($this->immediate == '1') $targetPath ='!'.$targetPath;
					if($this->msgType == 1) $this->msg= "$targetPath";
					else $this->msg = array('url' => $targetPath, 'localname' => $this->localName, 'id' =>1);
					// id参数固定不变，仅供演示，实际项目中可以是数据库ID
				}
			}
			else $this->err = '上传文件扩展名必需为：'.$this->upExt;
			@unlink($this->tempPath);
		}
	}
	private function formatBytes($bytes) {
		if($bytes >= 1073741824) {
			$bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
		} elseif($bytes >= 1048576) {
			$bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
		} elseif($bytes >= 1024) {
			$bytes = round($bytes / 1024 * 100) / 100 . 'KB';
		} else {
			$bytes = $bytes . 'Bytes';
		}
		return $bytes;
	}
}