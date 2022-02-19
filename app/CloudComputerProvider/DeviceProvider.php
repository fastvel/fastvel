<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace App\CloudComputerProvider;

use App\Models\Device;

abstract class DeviceProvider
{
    /**
     * 设备提供商名称
     * @return string
     */
    public function name(): string
    {
        return class_basename($this);
    }

    /**
     * 创建实例
     * @region 区域id
     * @return mixed
     */
    abstract public function create($region, $type, $period, $options);

    /**
     * 销毁/退订实例
     * @return mixed
     */
    abstract public function terminate(Device $device);

    /**
     * 续费
     * @return mixed
     */
    abstract public function renew();

    /**
     * 启动实例
     * @return mixed
     */
    abstract public function start();
    /**
     * 停止实例
     * @return mixed
     */
    abstract public function stop();
    /**
     * 重启实例
     * @return mixed
     */
    abstract public function restart();
}
