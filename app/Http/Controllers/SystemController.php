<?php

namespace App\Http\Controllers;

use App\Actions\GenerateSystemApiKey;
use App\Http\Requests\IndexSystemRequest;
use App\Http\Requests\StoreSystemRequest;
use App\Http\Requests\UpdateSystemRequest;
use App\Models\System;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SystemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexSystemRequest $request): View
    {
        $filters = $request->safe()->only(['search', 'status']);

        $systems = System::query()
            ->with('creator:id,name')
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(($filters['status'] ?? null) === 'active', fn ($query) => $query->where('is_active', true))
            ->when(($filters['status'] ?? null) === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $statistics = [
            'total' => System::query()->count(),
            'active' => System::query()->where('is_active', true)->count(),
            'inactive' => System::query()->where('is_active', false)->count(),
            'recently_used' => System::query()->where('last_used_at', '>=', now()->subDays(30))->count(),
        ];

        return view('systems.index', [
            'filters' => $filters,
            'statistics' => $statistics,
            'systems' => $systems,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', System::class);

        return view('systems.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSystemRequest $request, GenerateSystemApiKey $generateApiKey): RedirectResponse
    {
        $system = new System([
            ...$request->safe()->only(['name', 'description', 'is_active']),
            'created_by' => $request->user()->getKey(),
        ]);
        $system->issueApiKey($generateApiKey->handle())->save();

        return redirect()
            ->route('systems.show', $system)
            ->with('success', 'سامانه و API Key اختصاصی آن با موفقیت ایجاد شدند.')
            ->with('api_key_revealed', true);
    }

    /**
     * Display the specified resource.
     */
    public function show(System $system): View
    {
        Gate::authorize('view', $system);

        $system->load([
            'creator:id,name,email',
            'activities' => fn ($query) => $query->latest()->limit(8),
        ]);

        return view('systems.show', ['system' => $system]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(System $system): View
    {
        Gate::authorize('update', $system);

        return view('systems.edit', ['system' => $system]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSystemRequest $request, System $system): RedirectResponse
    {
        $system->update($request->safe()->only(['name', 'description']));

        return redirect()
            ->route('systems.show', $system)
            ->with('success', 'اطلاعات سامانه با موفقیت به‌روزرسانی شد.');
    }
}
