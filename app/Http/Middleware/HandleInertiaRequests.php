<?php

namespace App\Http\Middleware;

use App\Models\Bar;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $shared = [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];

        // Share current bar if we're on a bar route, and remember it in session.
        if ($request->route() && $request->route()->hasParameter('bar')) {
            $bar = $request->route()->parameter('bar');
            if ($bar instanceof Bar) {
                $shared['currentBar'] = [
                    'id' => $bar->id,
                    'name' => $bar->name,
                    'slug' => $bar->slug,
                ];

                if ($request->user()) {
                    $request->session()->put('current_bar_id', $bar->id);
                }
            }
        }

        if (! isset($shared['currentBar']) && $request->user()) {
            $barId = $request->session()->get('current_bar_id');
            if ($barId) {
                $bar = Bar::find($barId);
                if ($bar && $bar->canBeAccessedBy($request->user())) {
                    $shared['currentBar'] = [
                        'id' => $bar->id,
                        'name' => $bar->name,
                        'slug' => $bar->slug,
                    ];
                }
            }
        }

        return $shared;
    }
}
