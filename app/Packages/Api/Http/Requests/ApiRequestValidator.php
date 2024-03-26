<?php

namespace Package\Api\Http\Requests;

use Illuminate\Support\Facades\Date;
use Package\Exceptions\Client\BadRequestException;

class ApiRequestValidator
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The cache instance.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var array 支持的签名方法列表
     */
    protected $supportSignMethods = [
        'hmac-md5',
        'hmac-sha256',
    ];

    /**
     * @var int 合法时间范围
     */
    protected $validTimeDiffInMinutes = 5;

    /**
     * @var string 客户端时间
     */
    protected $timeHeader = 'T-Api-Time';

    /**
     * @var string 客户端访问key
     */
    protected $keyHeader = 'T-Api-Key';

    /**
     * @var string 用于计算签名的header key
     */
    protected $signedHeader = 'T-Api-SignedHeaders';

    /**
     * @var string 签名计算所用算法
     */
    protected $signatureAlgorithmHeader = 'T-Api-SignatureAlgorithm';

    /**
     * @var string 签名
     */
    protected $signatureHeader = 'T-Api-Signature';

    /**
     * Create a new signature instance.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    public function __construct($app)
    {
        $this->app     = $app;
        $this->cache   = $app['cache'];
        $this->request = $app['request'];
    }

    /**
     * 验证request是否有效
     *
     * @param \Closure $userResolver
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     *
     * @throws \Package\Exceptions\Client\BadRequestException
     */
    public function validate(\Closure $userResolver)
    {
        // 验证必填的头部是否存在
        foreach ($this->requiredHeaders() as $header) {
            if (!$this->request->hasHeader($header)) {
                throw new BadRequestException('以下头部必传：' . implode(', ', $this->requiredHeaders()));
            }
        }

        // 验证签名头部是否包含必填项
        $signedHeaders = $this->parsedSignedHeaders();
        foreach ($this->requiredSignHeaders() as $header) {
            if (!in_array(strtolower($header), $signedHeaders, true)) {
                throw new BadRequestException('以下头部必须签名：' . implode(', ', $this->requiredSignHeaders()));
            }
        }

        // 验证客户端时间
        try {
            $clientTime = Date::createFromFormat(\DateTime::RFC3339, $this->request->header($this->timeHeader));
        } catch (\Throwable $e) {
            $clientTime = null;
        }
        // 客户端时间不能超过x分钟
        if (!$clientTime || $clientTime->diffInMinutes(null, true) >= $this->validTimeDiffInMinutes) {
            throw new BadRequestException('接口时间误差不能超过' . $this->validTimeDiffInMinutes . '分钟');
        }

        // 验证非幂等请求是否已经处理过
        $signatureCacheKey = null;
        if (!$this->request->isMethodIdempotent()) {
            $signature         = $this->request->header($this->signatureHeader);
            $signatureCacheKey = 'api:signature:' . $signature;
            if ($this->cache->has($signatureCacheKey)) {
                throw new BadRequestException('请勿重复提交请求');
            }
        }

        // 获取用户
        $user = $userResolver($this->request->header($this->keyHeader));
        if (empty($user)) {
            throw new BadRequestException('无效的api key');
        }

        // 签名判断
        if (!$this->verifySignature($user->api_secret)) {
            throw new BadRequestException('签名校验失败');
        }

        // 写入签名缓存
        if ($signatureCacheKey) {
            $this->cache->put($signatureCacheKey, 1, Date::now()->addMinutes($this->validTimeDiffInMinutes * 2));
        }

        return $user;
    }

    /**
     * 签名生成
     *
     * @param string $secret
     *
     * @return string
     *
     * @throws \Package\Exceptions\Client\BadRequestException
     */
    public function sign($secret)
    {
        $signatureAlgorithm = $this->request->header($this->signatureAlgorithmHeader);
        // 判断签名方法是否支持
        if (!in_array($signatureAlgorithm, $this->supportSignMethods, true)) {
            throw new BadRequestException('不支持的签名算法 ' . $signatureAlgorithm);
        }

        $hmacAlgorithm = substr($signatureAlgorithm, 5);
        $signString    = $this->getSignString($hmacAlgorithm, $secret);

        return hash_hmac($hmacAlgorithm, $signString, $secret);
    }

    /**
     * 签名校验
     *
     * @param string $secret
     *
     * @return bool
     */
    public function verifySignature($secret)
    {
        return $this->getSignature() === $this->sign($secret);
    }

    /**
     * 获取签名值
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->request->header($this->signatureHeader);
    }

    /**
     * 根据Request生成签名用数据
     *
     * @param string $hmacAlgorithm
     * @param string $secret
     *
     * @return string
     */
    public function getSignString($hmacAlgorithm, $secret)
    {
        $request = $this->request;

        // 查询字符串排序
        $queries = $request->query();
        ksort($queries, SORT_STRING);
        $queriesString = http_build_query($queries, null, '&', PHP_QUERY_RFC3986);

        // header排序
        $signedHeaders = $this->parsedSignedHeaders();
        sort($signedHeaders, SORT_STRING);
        $signedHeadersString = implode(';', $signedHeaders);
        $headersString       = array_reduce($signedHeaders, function ($carry, $header) {
            return $carry . $header . ':' . trim($this->request->header($header)) . "\n";
        });

        try {
            $content = $request->getContent();
        } catch (\Throwable $e) {
            $content = null;
        }

        $signValues = [
            $request->method(), // verb
            $request->getPathInfo(), // path
            $queriesString, // all queries as string
            $headersString, // all headers as string
            $signedHeadersString, // signed headers as string
            hash_hmac($hmacAlgorithm, $content, $secret), // request content hashed
        ];

        return implode("\n", $signValues);
    }

    /**
     * 获取签名时的调试信息
     *
     * @return array
     */
    public function getSignDebugOutput()
    {
        $output = [];

        // 打印key
        $apiKey   = $this->request->header($this->keyHeader);
        $output[] = 'apiKey:' . $apiKey;

        // 打印用户相关属性
        $user = User::where('api_key', $apiKey)->first();
        if ($user) {
            $apiSecret = $user->api_secret;
            // 隐藏apiSecret几位字符，以防出现泄漏
            $position    = (int)ceil(strlen($apiSecret) / 3);
            $length      = max($position, 6);
            $replacement = str_repeat('*', $length);

            $output[] = 'apiSecret:' . substr_replace($apiSecret, $replacement, $position, $length);
            $output[] = "对应业务系统:{$user->nickname}[{$user->description}]";
        } else {
            $output[] = '此apiKey没有对应用户。';
            return $output;
        }

        // 打印签名所用算法
        $signatureAlgorithm = $this->request->header($this->signatureAlgorithmHeader);
        $hmacAlgorithm      = substr($signatureAlgorithm, 5);
        $output[]           = '签名所用算法:' . $signatureAlgorithm;

        // 打印body签名
        try {
            $requestContent = $this->request->getContent();
        } catch (\Throwable $e) {
            $requestContent = null;
        }
        $output[] = '请求body:' . $requestContent;
        $output[] = '请求body的签名:' . hash_hmac($hmacAlgorithm, $requestContent, $apiSecret);

        // 打印待签名字符串
        $signString = $this->getSignString($hmacAlgorithm, $apiSecret);
        $output[]   = "==================== 准备签名的字符串 Begin ====================\n{$signString}\n==================== 准备签名的字符串 End ====================";

        // 打印生成签名
        $output[] = '最终生成签名:' . hash_hmac($hmacAlgorithm, $signString, $apiSecret);
        $output[] = '请求附带签名:' . $this->request->header($this->signatureHeader);

        return $output;
    }

    /**
     * 设置签名用request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * 获取已签名的头部数组
     *
     * @return array
     */
    public function parsedSignedHeaders()
    {
        return explode(';', strtolower($this->request->header($this->signedHeader)));
    }

    /**
     * 客户端必须存在的header
     *
     * @return array
     */
    protected function requiredHeaders()
    {
        return [
            $this->timeHeader,
            $this->keyHeader,
            $this->signatureAlgorithmHeader,
            $this->signatureHeader,
            $this->signedHeader,
        ];
    }

    /**
     * 签名必须的header
     *
     * @return array
     */
    protected function requiredSignHeaders()
    {
        return [
            'Host',
            $this->timeHeader,
            $this->keyHeader,
            $this->signatureAlgorithmHeader,
        ];
    }
}
