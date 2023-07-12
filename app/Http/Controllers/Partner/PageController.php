<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display the partner index page.
     *
     * @param string $locale
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(string $locale, Request $request): \Illuminate\View\View
    {
        $dashboardBlocks = [];

        /*
        $dashboardBlocks[] = [
            'link' => route('partner.data.list', ['name' => 'account']),
            'icon' => 'user-circle',
            'title' => trans('common.account_settings'),
            'desc' => trans('common.memberDashboardBlocks.account_settings')
        ];
        */

        $dashboardBlocks[] = [
            'link' => route('partner.data.list', ['name' => 'clubs']),
            'icon' => 'funnel',
            'title' => trans('common.clubs'),
            'desc' => trans('common.partnerDashboardBlocks.clubs')
        ];

        $dashboardBlocks[] = [
            'link' => route('partner.data.list', ['name' => 'cards']),
            'icon' => 'qr-code',
            'title' => trans('common.loyalty_cards'),
            'desc' => trans('common.partnerDashboardBlocks.loyalty_cards')
        ];

        $dashboardBlocks[] = [
            'link' => route('partner.data.list', ['name' => 'rewards']),
            'icon' => 'gift',
            'title' => trans('common.rewards'),
            'desc' => trans('common.partnerDashboardBlocks.rewards')
        ];

        $dashboardBlocks[] = [
            'link' => route('partner.data.list', ['name' => 'staff']),
            'icon' => 'briefcase',
            'title' => trans('common.staff'),
            'desc' => trans('common.partnerDashboardBlocks.staff', ['localeSlug' => '<span class="underline">/' . app()->make('i18n')->language->current->localeSlug . '/staff/</span>'])
        ];

        $dashboardBlocks[] = [
            'link' => route('partner.data.list', ['name' => 'members']),
            'icon' => 'user-group',
            'title' => trans('common.members'),
            'desc' => trans('common.partnerDashboardBlocks.members')
        ];

        $dashboardBlocks[] = [
            'link' => route('partner.analytics'),
            'icon' => 'presentation-chart-line',
            'title' => trans('common.analytics'),
            'desc' => trans('common.partnerDashboardBlocks.analytics')
        ];

        return view('partner.index', compact('dashboardBlocks'));
    }
}
