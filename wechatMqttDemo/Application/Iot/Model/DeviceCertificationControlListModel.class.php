<?php
namespace Iot\Model;
use Think\Model;
class DeviceCertificationControlListModel extends Model {
	public function DeviceIsExist($map){//用户是否已存在
        $Device = M()->table('DeviceCertificationControlList'); // 实例化User对象
		// 把查询条件传入查询方法
		$result = $Device->where($map)->select();
		return $result;
    }
	public function AddDevice($device){//添加用户
		$Device = M()->table('DeviceCertificationControlList');
		$result=$Device->add($device);
		return $result;
    }
	public function DelDevice($device){//删除用户
	$Device = M()->table('DeviceCertificationControlList');
	$result=$Device->where($device)->delete();
	return $result;
    }
}