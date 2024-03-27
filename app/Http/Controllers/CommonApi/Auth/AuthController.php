<?php

namespace App\Http\Controllers\CommonApi\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommonApi\Auth\AuthRequest;
use App\Http\Requests\CommonApi\EmptyRequest;
use App\Http\Resources\CommonApi\Auth\AuthResource;
use App\Http\Resources\CommonApi\EmptyResource;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\TokenRepository;
use Package\Exceptions\Client\BadRequestException;

class AuthController extends Controller
{
    /**
     * 登录
     *
     * @param \App\Http\Requests\CommonApi\Auth\AuthRequest $request
     *
     * @return \App\Http\Resources\CommonApi\Auth\AuthResource
     */
    public function login(AuthRequest $request)
    {
        $validated = $request->validated();
        $grantType = cons()->key('system.guard_type', \Arr::get($validated, 'guard_type'));
        $url       = config('auth.oauth_url') . '/oauth/token';

        try {
            $response = Http::asForm()->post($url, [
                'grant_type'    => 'password',
                'client_id'     => config('auth.passport.' . $grantType . '_client_id'),
                'client_secret' => config('auth.passport.' . $grantType . '_client_secret'),
                'username'      => \Arr::get($validated, 'username'),
                'password'      => \Arr::get($validated, 'password'),
                'guard'         => \Arr::get($validated, 'guard_type'),
            ]);
            $response->throw();
        } catch (\Throwable $exception) {
            \Log::error('登录失败', [
                'exception' => $exception,
                'request'   => $request->all(),
            ]);
            throw new BadRequestException('登录失败');
        }

        return new AuthResource($response->collect());
    }

    /**
     * 退出登录
     *
     * @param \App\Http\Requests\CommonApi\EmptyRequest $request
     *
     * @return \App\Http\Resources\CommonApi\EmptyResource
     */
    public function logout(EmptyRequest $request)
    {
        if (\Auth::guard('student')->check()) {
            $tokenId = \Auth::guard('student')->user()->currentAccessToken()->id;
        } elseif (\Auth::guard('teacher')->check()) {
            $tokenId = \Auth::guard('teacher')->user()->currentAccessToken()->id;
        }
        $tokenRepository = app(TokenRepository::class);
        $tokenRepository->revokeAccessToken($tokenId);
        return new EmptyResource();
    }
}