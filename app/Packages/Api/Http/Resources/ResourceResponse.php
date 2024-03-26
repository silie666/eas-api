<?php

namespace Package\Api\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceResponse as BaseResourceResponse;

class ResourceResponse extends BaseResourceResponse
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     * @param array                    $headers
     * @param int                      $encodingOptions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request, array $headers = [], $encodingOptions = 0)
    {
        return tap(response()->json(
            $this->wrap(
                $this->resource->resolve($request),
                $this->resource->with($request),
                $this->resource->additional
            ),
            $this->calculateStatus(),
            $headers,
            $encodingOptions
        ), function ($response) use ($request) {
            $response->original = $this->resource->resource;

            $this->resource->withResponse($request, $response);
        });
    }
}
