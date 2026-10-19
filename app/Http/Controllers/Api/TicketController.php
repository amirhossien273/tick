<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexTicketRequest;
use App\Http\Requests\ShowTicketRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    public function index(IndexTicketRequest $request, TicketService $ticketService): AnonymousResourceCollection
    {
        $tickets = $ticketService->paginateForSystem(
            $request->sourceSystem(),
            $request->safe()->only([
                'search',
                'external_user_id',
                'status_code',
                'priority_code',
                'per_page',
            ]),
        );

        return TicketResource::collection($tickets);
    }

    public function store(StoreTicketRequest $request, TicketService $ticketService): JsonResponse
    {
        $ticket = $ticketService->create(
            $request->sourceSystem(),
            $request->safe()->only([
                'external_user_id',
                'subject',
                'description',
                'priority_code',
                'category_code',
            ]),
        );

        return (new TicketResource($ticket))
            ->additional(['message' => 'تیکت با موفقیت ثبت شد.'])
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function show(
        ShowTicketRequest $request,
        string $ticket,
        TicketService $ticketService,
    ): TicketResource {
        return new TicketResource(
            $ticketService->findForSystemOrFail($request->sourceSystem(), $ticket),
        );
    }
}
