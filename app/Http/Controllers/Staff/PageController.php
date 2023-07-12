<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display the staff index page.
     *
     * @param string $locale
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(string $locale, Request $request): \Illuminate\View\View
    {
        $dashboardBlocks = [];

        $dashboardBlocks[] = [
            'link' => route('staff.data.list', ['name' => 'account']),
            'icon' => 'user-circle',
            'title' => trans('common.account_settings'),
            'desc' => trans('common.memberDashboardBlocks.account_settings')
        ];

        $dashboardBlocks[] = [
            'link' => route('staff.data.list', ['name' => 'members']),
            'icon' => 'user-group',
            'title' => trans('common.members'),
            'desc' => trans('common.staffDashboardBlocks.members')
        ];

        return view('staff.index', compact('dashboardBlocks'));
    }
}
