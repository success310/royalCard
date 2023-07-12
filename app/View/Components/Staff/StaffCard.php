<?php

namespace App\View\Components\Staff;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Staff;
use App\Models\Transaction;
use Laravolt\Avatar\Facade as Avatar;

class StaffCard extends Component
{
    public ?Staff $staff;
    public ?Transaction $transaction;
    public ?string $avatar;

    /**
     * Create a new component instance.
     *
     * @param Staff|null $staff The staff model. Default is null.
     * @param Transaction|null $transaction The transaction model. Default is null.
     */
    public function __construct(?Staff $staff = null, ?Transaction $transaction = null)
    {
        $this->staff = $staff;
        $this->transaction = $transaction;

        if ($staff->avatar) {
            $this->avatar = $staff->avatar;
        } else {
            $name = ($transaction) ? parse_attr($transaction->staff_name) : parse_attr($staff->name);
            $this->avatar = Avatar::create($name)->toBase64();
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.staff.staff-card');
    }
}
