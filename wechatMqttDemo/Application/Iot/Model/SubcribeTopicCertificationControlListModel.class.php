<?php
namespace Iot\Model;
use Think\Model;
class SubcribeTopicCertificationControlListModel extends Model {
	public function SubcribecIsExist($map){//用户是否已存在
        $Subcribec = M()->table('SubcribeTopicCertificationControlList'); // 实例化User对象
		// 把查询条件传入查询方法
		$result = $Subcribec->where($map)->select();
		return $result;
    }
	public function AddSubcribec($subcribec){//添加用户
		$Subcribec = M()->table('SubcribeTopicCertificationControlList');
		$result=$Subcribec->add($subcribec);
		return $result;
    }
	public function DelSubcribec($subcribec){//删除用户
	$Subcribec = M()->table('SubcribeTopicCertificationControlList');
	$result=$Subcribec->where($subcribec)->delete();
	return $result;
    }
}