<?php
namespace Iot\Model;
use Think\Model;
class PublishTopicCertificationControlListModel extends Model {
	public function PublishcIsExist($map){//用户是否已存在
        $Publishc = M()->table('PublishTopicCertificationControlList'); // 实例化User对象
		// 把查询条件传入查询方法
		$result = $Publishc->where($map)->select();
		return $result;
    }
	public function AddPublishc($publishc){//添加用户
		$Publishc = M()->table('PublishTopicCertificationControlList');
		$result=$Publishc->add($publishc);
		return $result;
    }
	public function DelPublishc($publishc){//删除用户
	$Publishc = M()->table('PublishTopicCertificationControlList');
	$result=$Publishc->where($publishc)->delete();
	return $result;
    }
}