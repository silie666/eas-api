<?php

namespace Package\Api\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceResponse;

class PaginatedResourceResponse extends ResourceResponse
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return tap(response()->json(
            $this->wrap($this->resource->resolve($request)),
            200,
            $this->paginationInformation($request)
        ), function ($response) use ($request) {
            $response->original = $this->resource->resource->pluck('resource');

            $this->resource->withResponse($request, $response);
        });
    }

    /**
     * Add the pagination information to the response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function paginationInformation($request)
    {
        $paginator = $this->resource->resource;

        $totalCount  = $paginator->count();
        $currentPage = $paginator->currentPage();
        $lastPage    = max((int)ceil($totalCount / $paginator->perPage()), 1);
        $hasPages    = $paginator->hasMorePages();

        // 追加url query
        $paginator->appends($request->query());

        $links = [
            'first' => $hasPages ? $paginator->url(1) : null,
            'last'  => $hasPages ? $paginator->url($lastPage) : null,
            'prev'  => $currentPage > 1 ? $paginator->url($currentPage - 1) : null,
            'next'  => $currentPage < $lastPage ? $paginator->url($currentPage + 1) : null,
        ];

        $urlGenerator = url();
        $linkHeader   = array_reduce(array_keys($links), function ($carry, $key) use ($urlGenerator, $links) {
            if (empty($links[$key])) {
                return $carry;
            }

            $separator   = $carry ? ', ' : '';
            $absoluteUrl = $urlGenerator->to($links[$key]);
            return "{$carry}{$separator}<{$absoluteUrl}>; rel=\"{$key}\"";
        });

        return [
            'S-Page-TotalCount'  => $paginator->total(),
            'S-Page-CurrentPage' => $currentPage,
            'S-Page-PerPage'     => $paginator->perPage(),
            'Link'               => $linkHeader,
        ];
    }
}
