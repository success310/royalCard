<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display the admin index page.
     *
     * This method returns the view for the admin index page.
     *
     * @param string $locale The locale for the current request.
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(string $locale, Request $request): \Illuminate\View\View
    {
        $dashboardBlocks = [];

        if (auth('admin')->user()->role != 1) {
            $dashboardBlocks[] = [
                'link' => route('admin.data.list', ['name' => 'account']),
                'icon' => 'user-circle',
                'title' => trans('common.account_settings'),
                'desc' => trans('common.adminDashboardBlocks.account_settings')
            ];
        }

        if (auth('admin')->user()->role == 1) {
            $dashboardBlocks[] = [
                'link' => route('admin.data.list', ['name' => 'admins']),
                'icon' => 'users',
                'title' => trans('common.administrators'),
                'desc' => trans('common.adminDashboardBlocks.administrators', ['localeSlug' => '<span class="underline">/' . app()->make('i18n')->language->current->localeSlug . '/admin/</span>'])
            ];

            $dashboardBlocks[] = [
                'link' => route('admin.data.list', ['name' => 'networks']),
                'icon' => 'cube-transparent',
                'title' => trans('common.networks'),
                'desc' => trans('common.adminDashboardBlocks.networks')
            ];
        }

        $dashboardBlocks[] = [
            'link' => route('admin.data.list', ['name' => 'partners']),
            'icon' => 'building-storefront',
            'title' => trans('common.partners'),
            'desc' => trans('common.adminDashboardBlocks.partners', ['localeSlug' => '<span class="underline">/' . app()->make('i18n')->language->current->localeSlug . '/partner/</span>'])
        ];

        if (auth('admin')->user()->role == 1) {
            $dashboardBlocks[] = [
                'link' => route('admin.data.list', ['name' => 'members']),
                'icon' => 'user-group',
                'title' => trans('common.members'),
                'desc' => trans('common.adminDashboardBlocks.members')
            ];
    
        }

        return view('admin.index', compact('dashboardBlocks'));
    }
}
