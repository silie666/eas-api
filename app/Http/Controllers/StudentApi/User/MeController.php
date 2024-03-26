<?php

namespace App\Http\Controllers\StudentApi\User;

use App\Http\Controllers\Common\User\MeController as ParentController;
use App\Http\Requests\StudentApi\EmptyRequest;
use App\Http\Requests\StudentApi\User\MeCardCreateOrUpdateRequest;
use App\Http\Requests\StudentApi\User\MeCardIndexRequest;
use App\Http\Resources\Common\User\UserCardResource;
use App\Http\Resources\StudentApi\EmptyResource;
use App\Services\Card\CardService;

class MeController extends ParentController
{

    /**
     * 信用卡列表
     *
     * @param \App\Http\Requests\StudentApi\User\MeCardIndexRequest $request
     *
     * @return \App\Http\Resources\Common\User\UserCardResource[]
     */
    public function cards(MeCardIndexRequest $request)
    {
        $validated = $request->validated();
        $user      = \Auth::user();
        $withAll   = (bool)\Arr::pull($validated, 'with_all');
        $cards     = CardService::query(array_merge($validated,
            ['student_id' => $user->id]));
        if ($withAll) {
            $cards = $cards->get();
        } else {
            $cards = $cards->paginate($request->getPerPage());
        }
        return UserCardResource::collection($cards);
    }

    /**
     * 创建信用卡
     *
     * @param \App\Http\Requests\StudentApi\User\MeCardCreateOrUpdateRequest $request
     *
     * @return \App\Http\Resources\StudentApi\EmptyResource
     */
    public function storeCard(MeCardCreateOrUpdateRequest $request)
    {
        $validated = $request->validated();
        $user      = \Auth::user();
        CardService::create($validated, $user);
        return new EmptyResource();
    }


    /**
     * 更新信用卡
     *
     * @param \App\Http\Requests\StudentApi\User\MeCardCreateOrUpdateRequest $request
     * @param int                                                            $cardId
     *
     * @return \App\Http\Resources\StudentApi\EmptyResource
     *
     * @var \App\Models\User\Student                                         $user
     * @var \App\Models\Card\Card                                            $card
     */
    public function updateCard(MeCardCreateOrUpdateRequest $request, int $cardId)
    {
        $validated = $request->validated();
        $user      = \Auth::user();
        $card      = $user->cards()->findOrFail($cardId);
        CardService::update($validated, $card);
        return new EmptyResource();
    }

    /**
     * 删除信用卡
     *
     * @param \App\Http\Requests\StudentApi\EmptyRequest $request
     * @param int                                        $cardId
     *
     * @return \App\Http\Resources\StudentApi\EmptyResource
     */
    public function destroyCard(EmptyRequest $request, int $cardId)
    {
        $user = \Auth::user();
        $card = $user->cards()->findOrFail($cardId);
        CardService::delete($card);
        return new EmptyResource();
    }
}