<?php

namespace Imdgr886\Sms;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Imdgr886\Sms\Events\SmsSendEvent;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class Sms
{
    /**
     * 发送短信
     * @param string       $scenes 场景，不同场景的可用渠道和模板可能不一样
     * @param array|string $mobile 手机号
     * @param array        $data  模板数据，用于替换
     * @return bool
     */
    protected function send(string $scenes, $mobile, array $data)
    {
        $gateways = $this->choiceGateways($scenes, $mobile);
        $success = false;

        try {
            $result = app()->get('easysms')->send($mobile, [
                'content'  => function($gateway) use ($scenes) {
                    return config("modules.sms.templates.{$scenes}.{$gateway->getName()}.content");
                },
                'template' => function($gateway) use ($scenes) {
                    return config("modules.sms.templates.{$scenes}.{$gateway->getName()}.template_id");
                },
                'data' => $data
            ], $gateways);
            // 发送成功
            $success = true;
        } catch (NoGatewayAvailableException $e) {
            // 发送失败
        } finally {
            // 触发事件
            // dispatch(new SmsSendEvent($scenes, $mobile, $name, null, $success));

            // cache
//            $usedGateways = Cache::get('sms-used-gateway:' . $mobile . ':' . $scenes, []);
//            $usedGateways[] = $name;
//            Cache::put('sms-used-gateway:' . $mobile . ':' . $scenes, array_unique($usedGateways));
            dump($result);
        }

        return $success;
    }

    /**
     * 选择发送网关，同一个场景下尽量用没用过的网关
     * @param $scenes string 发送场景
     * @param $mobile array|string 手机号
     * @param $filterUsed bool 过滤已使用过的网关
     * @return array
     */
    protected function choiceGateways(string $scenes, $mobile, bool $filterUsed = true)
    {
        $gateways = config('modules.sms.default.gateways.' . $scenes);
        if (!$gateways) {
            $gateways = config('modules.sms.default.gateways.default');
        }

        if ($filterUsed) {
            // 发送失败的网关，再次发送最好不用
            $usedGateways = Cache::get('sms-used-gateway:' . $mobile . ':' . $scenes);

            // 还有没试过的网关， 才需要排除
            if(count($usedGateways) < count($gateways)){
                Arr::forget($gateways, $usedGateways);
            }
        }
        return $gateways;
    }
}
