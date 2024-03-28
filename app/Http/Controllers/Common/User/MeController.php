<?php

namespace App\Http\Controllers\Common\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\EmptyRequest;
use App\Http\Requests\Common\Request;
use App\Http\Requests\Common\User\MeUpdateRequest;
use App\Http\Resources\Common\User\UserNodeResource;
use App\Http\Resources\Common\User\UserResource;
use App\Models\User\Student;
use App\Models\User\Teacher;
use Illuminate\Support\Facades\Auth;

class MeController extends Controller
{
    /**
     * 我的资料
     *
     * @param \App\Http\Requests\Common\Request $request
     *
     * @return \App\Http\Resources\Common\User\UserResource
     */
    public function index(Request $request): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    /**
     * 更新我的资料
     *
     * @param \App\Http\Requests\Common\User\MeUpdateRequest $request
     *
     * @return \App\Http\Resources\Common\User\UserResource
     */
    public function update(MeUpdateRequest $request): UserResource
    {
        $validated = $request->validated();
        $user      = Auth::user();
        $user->update($validated);
        return new UserResource($user);
    }

    /**
     * 我的节点列表
     *
     * @param \App\Http\Requests\Common\EmptyRequest $request
     *
     * @return \App\Http\Resources\Common\User\UserNodeResource[]
     */
    public function nodes(EmptyRequest $request)
    {
        $user = Auth::user();

        $routes = \Route::getRoutes();

        $userNodes = collect();
        foreach ($routes as $route) {
            if ($user instanceof Student && \Str::is('*student-api*',$route->uri)) {
                $item = [
                    'uri'  => $route->uri,
                    'sign' => $route->methods[0] . ' ' . $route->uri,
                ];
                $userNodes->add($item);
            }
            if ($user instanceof Teacher && \Str::is('*teacher-api*',$route->uri)) {

                $item = [
                    'uri'  => $route->uri,
                    'sign' => $route->methods[0] . ' ' . $route->uri,
                ];
                $userNodes->add($item);
            }
        }
        return UserNodeResource::collection($userNodes);
    }
}