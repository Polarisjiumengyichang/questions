<?php

declare (strict_types = 1);

namespace App\Service;

use App\Model\User;
use App\Model\UserDynamic;
use App\Event\UserSignuped;
use Hyperf\DbConnection\Db;
use App\Constants\ErrorCode;
use Hyperf\Cache\CacheManager;
use App\Constants\KeyConstants;
use Hyperf\Di\Annotation\Inject;
use Psr\SimpleCache\CacheInterface;
use App\Exception\BusinessException;
use App\Service\Instance\JwtInstance;
use Hyperf\Context\ApplicationContext;
use Psr\EventDispatcher\EventDispatcherInterface;

class UserService extends Service
{
    #[Inject]
    private EventDispatcherInterface $eventDispatcher;

    public function signup(array $params)
    {
        // 入库
        Db::beginTransaction();
        try {
            $model = new User();
            $model->email = $params['email'];
            $model->password = password_hash($params['password'], PASSWORD_DEFAULT);
            // 图片路径取决于各自保存在 cdn 的路径
            $model->pic = 'images/avatar/' . rand(1, 382) . '.jpg';
            $model->nickname = 'api_' . rand(1, 99) . date('Hi');
            $model->save();

            // 同步
            $dynamicModel = new UserDynamic();
            $dynamicModel->uid = $model->id;
            $dynamicModel->save();

            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            $this->logger->error($ex->getMessage());
            throw new BusinessException(ErrorCode::SERVER_ERROR);
        }

        // 获取token
        $token = JwtInstance::instance()->encode($model);
        $userInfo = JwtInstance::instance()->getUser();

        // 事件触发
        $this->eventDispatcher->dispatch(new UserSignuped($userInfo));

        return [$userInfo, $token];
    }
    public function login($email, $password)
    {
        // 校验用户是否存在
        $user = User::query()->where(['email' => $email])->first();
        if (empty($user)) {
            throw new BusinessException(ErrorCode::USER_NOT_EXISTS);
        }

        // 校验密码是否正确
        if (!password_verify($password, $user['password'])) {
            throw new BusinessException(ErrorCode::PASSWORD_ERROR);
        }

        // jwt 编码获取 token 和用户信息
        $token = JwtInstance::instance()->encode($user);
        $userInfo = JwtInstance::instance()->getUser();

        return [$userInfo, $token];
    }

    public function getUserInfo($id)
    {
       

        // $container = ApplicationContext::getContainer();
        // $cache = $container->get(\Psr\SimpleCache\CacheInterface::class);
        $cache = make(CacheInterface::class, ['driver' => 'other']);
        // $cache = make(CacheInterface::class, ['driver' => 'default']);
        $userKey = sprintf("userInfo:%d", $id);
        $userInfo = $cache->get($userKey);
        if (is_null($userInfo)) {
            $userInfo = User::query()->where('id', $id)->select('id', 'email', 'pic', 'create_time', 'nickname')->first();
            if (empty($userInfo)) {
                 throw new BusinessException((ErrorCode::USER_NOT_EXISTS));
            }
            $cache->set($userKey, $userInfo, 60);
        }

       

        return $userInfo;
    }

    #[Inject]
    protected CacheManager $cacheManager;

    /**
     * 不通过注解也可以实现缓存.
     * @param $id
     * @return array
     */
    public function getUserInfoFromCache($id)
    {
        $userModel = new User();

        return $this->cacheManager->call(function () use ($userModel, $id) {
            return $userModel->getUserInfo($id);
        }, sprintf(KeyConstants::USER_INFO, $id), env('APP_ENV') == 'dev' ? 10 : 3600, 'other');
    }


    #[Inject]
    protected \App\Components\CacheManager $cacheManager1;

    /**
     * 批量操作.
     *
     * @param $ids
     * @return array
     */
    public function getMultiUserInfosFromCache($ids)
    {
        $userModel = new User();

        return $this->cacheManager1->multiCall(function ($targetIds) use ($userModel) {
            return $userModel->getUserInfos($targetIds);
        }, $ids, 'user', env('APP_ENV') == 'dev' ? 60 : 600, 'id', 'other');
    }

}
