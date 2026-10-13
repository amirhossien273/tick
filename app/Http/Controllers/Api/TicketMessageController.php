<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexTicketMessageRequest;
use App\Http\Requests\StoreTicketMessageRequest;
use App\Http\Resources\TicketMessageResource;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketMessageController extends Controller
{
    public function index(
        IndexTicketMessageRequest $request,
        string $ticket,
        TicketService $ticketService,
    ): AnonymousResourceCollection {
        $messages = $ticketService->paginateMessagesForSystem(
            $request->sourceSystem(),
            $ticket,
            $request->integer('per_page', 50),
        );

        return TicketMessageResource::collection($messages);
    }

    public function store(
        StoreTicketMessageRequest $request,
        string $ticket,
        TicketService $ticketService,
    ): JsonResponse {
        $message = $ticketService->createCustomerMessage(
            $request->sourceSystem(),
            $ticket,
            $request->safe()->only(['message']),
        );

        return (new TicketMessageResource($message))
            ->additional(['message' => 'پیام با موفقیت ارسال شد.'])
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
