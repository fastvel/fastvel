<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace App\DeviceProvider\Providers;

use App\DeviceProvider\DeviceProvider;
use App\DeviceProvider\Exceptions\OperationNotSupportException;
use App\Models\Device;

class SelfHost extends DeviceProvider
{
    public function create()
    {
        // TODO: Implement create() method.
    }

    public function terminate(Device $device)
    {
        return $device->delete();
    }

    public function renew()
    {
        throw new OperationNotSupportException();
    }

    /**
     * @return mixed
     * @throws OperationNotSupportException
     */
    public function start()
    {
        throw new OperationNotSupportException();
    }

    /**
     * @return mixed
     * @throws OperationNotSupportException
     */
    public function stop()
    {
        throw new OperationNotSupportException();
    }

    /**
     * @return mixed
     * @throws OperationNotSupportException
     */
    public function restart()
    {
        throw new OperationNotSupportException();
    }
}
