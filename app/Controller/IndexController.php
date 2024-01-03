<?php

declare (strict_types = 1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use Hyperf\Redis\Redis;
use Hyperf\Context\Context;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\Coroutine;
use App\Service\UserService;
use Hyperf\Redis\RedisProxy;
use App\Components\FileAdapter;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\ApplicationContext;
use App\Service\Instance\JwtInstance;
use App\Controller\AbstractController;
use Hyperf\Elasticsearch\ClientBuilderFactory;
use Hyperf\HttpServer\Annotation\AutoController;

#[AutoController]
class IndexController extends AbstractController
{
    #[Inject]
    public UserService $userService;

    public $a;

    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();
        $data = [
            'method' => $method,
            'message' => "Hello {$user}.",
        ];

        return $data;
    }

    public function info()
    {
        $id = (int) $this->request->input('id', 0);
        return $this->response->success($this->indexService->info($id));
    }

    public function test()
    {
        return memory_get_usage(true) . '||' . convert_size(memory_get_usage(true));
    }

    public function test2()
    {
        $a = $this->request->input('a');
        Context::set('a', $a);
        return [
            'co_is' => Coroutine::inCoroutine(), // 判断当前是否在协程内
            'co_id' => Coroutine::id(), // 获取当前协程 id
            'a' => Context::get('a'),
        ];
    }

    public function test3()
    {
        $id = $this->request->input('id');

        $user = [
            'id' => $id,
            'name' => $id . '_name',
        ];
        $instance = JwtInstance::instance();
        $instance->encode($user);

        return [
            'id' => $instance->id,
            'user' => $instance->user,
        ];
    }

    public function test4()
    {
        return Db::select('SELECT * FROM email_code;');
    }

    public function email()
    {
        return $this->response->success($this->indexService->email());
    }

    public function redis()
    {
        $redis = ApplicationContext::getContainer()->get(Redis::class);
        $redis->set('a', 1);

        return $this->response->success([
            'a' => $redis->get('a'),
        ]);
    }
    public function rediscache()
    {
        $redis = make(RedisProxy::class, ['pool' => 'cache']);
        $redis->set('cache', 123);

        return $this->response->success([
            'cache' => $redis->get('cache'),
        ]);
    }
    public function redis_persistence()
    {
        $redis = make(RedisProxy::class, ['pool' => 'persistence']);
        $redis->set('per', 1);

        return $this->response->success([
            'per' => $redis->get('per'),
        ]);
    }

    public function info2()
    {
        $id = $this->request->input('id');
        return $this->userService->getUserInfoFromCache($id);
    }

    public function info3()
    {
        $uids = [1, 2, 3, 4];
        return $this->userService->getMultiUserInfosFromCache($uids);
    }

    public function qiniu(FileAdapter $adapter)
    {
        $path = "contents/" . time() . ".txt";
        $content = 'hello qiniu123';

        // 上传内容到七牛
        $adapter->write($path, $content);

        // 获取私有链接
        $url = $adapter->privateDownloadUrl($path);

        return [
            'url' => $url,
            'content' => $adapter->read($url),
        ];
    }

    public function info4()
    {
        // 如果在协程环境下创建，则会自动使用协程版的 Handler，非协程环境下无改变
        $builder = $this->container->get(ClientBuilderFactory::class)->create();
        $client = $builder->setHosts([env('ELASTICSEARCH_HOST')])->build();
        $info = $client->info();
        $params = [
            'index' => 'question',
            'type' => '_doc',
            'id' => '3',
        ];
        $qInfo = $client->get($params);
        $params = [
            'index' => 'question',
            'type' => '_doc',
        ];
        $params['body']['query']['match']['content'] = 'hello';
        // $params['body']['query']['bool']['filter']['term']['id'] = '2';
        // $params['body']['query']['bool']['filter']['range']['create_time']['gte'] = '1672901482';
        $matchRes = $client->search($params);
        return [
            'info' => $info,
            'qInfo' => $qInfo,
            'matchInfo' => $matchRes,
        ];
    }

    public array $data = [];
    public function leak()
    {
        $this->data[] = str_repeat("'hello world'", 1024);
        return [];
    }

}
