<?php

namespace App\Services\Card;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Analytic;
use App\Models\Card;
use App\Models\Staff;
use App\Models\Member;
use App\Models\Reward;

class AnalyticsService
{
    /**
     * Increment the views for a primary and optional secondary model, once per unique visitor.
     *
     * @param Model $model The primary Eloquent model.
     * @param Model|null $secondaryModel Optional secondary Eloquent model.
     * 
     * @return bool True if the view is incremented (i.e., the session didn't exist yet),
     *              false if the session already exists and the view count is not incremented.
     */
    public function incrementViews(Model $model, ?Model $secondaryModel = null): bool
    {
        // Define the session key based on the class name and the ID of the model
        $session_key = class_basename(get_class($model)) . '_viewed_' . $model->id;

        // Check if a session value exists
        if (!session($session_key)) {
            // Increment the views column and update the last_view column.
            // Increment the views column.
            $model->increment('views');
            $model->where('id', $model->id)->update(['last_view' => Carbon::now()]);

            // Add analytic
            $this->addViewAnalytic($model, $secondaryModel);

            // Set the session value
            session([$session_key => 'true']);

            // Return true as the view count was incremented
            return true;
        }

        // Return false as the view count was not incremented
        return false;
    }

    /**
     * Adds the view analytics entry for a given model and an optional secondary model.
     *
     * @param Model $model The primary Eloquent model.
     * @param Model|null $secondaryModel Optional secondary Eloquent model.
     */
    private function addViewAnalytic(Model $model, ?Model $secondaryModel = null): void
    {
        if (class_basename($model) === 'Card') {
            $eventType = 'card_view';
            $cardId = $model->id;
            $rewardId = null;
        }

        if (class_basename($model) === 'Reward') {
            $eventType = 'reward_view';
            $rewardId = $model->id;
            $cardId = $secondaryModel ? $secondaryModel->id : null;
        }

        Analytic::create([
            'partner_id' => $model->partner->id,
            'member_id' => auth('member')->check() ? auth('member')->user()->id : null,
            'staff_id' => null,
            'card_id' => $cardId,
            'reward_id' => $rewardId,
            'event' => $eventType,
            'locale' => app()->make('i18n')->language->current->locale,
        ]);
    }

    /**
     * Adds issue points analytics entry.
     *
     * @param Card $card The Card model.
     * @param Staff $staff The Staff model.
     * @param Member $member The Member model.
     * @param int $points The amount of points issued.
     * @param string $currency The currency of the purchase.
     * @param int $purchase_amount The amount of the purchase.
     * @param string $created_at Optional date.
     */
    public function addIssueAnalytic(Card $card, Staff $staff, Member $member, int $points, string $currency, int $purchase_amount, string 
    $created_at = null): void
    {
        Analytic::create([
            'partner_id' => $card->partner->id,
            'member_id' => $member->id,
            'staff_id' => $staff->id,
            'card_id' => $card->id,
            'points' => $points,
            'currency' => $currency,
            'purchase_amount' => $purchase_amount,
            'event' => 'issue_points',
            'created_at' => $created_at ?? Carbon::now('UTC'),
            'updated_at' => $created_at ?? Carbon::now('UTC'),
        ]);
    }

    /**
     * Adds claim reward analytics entry.
     *
     * @param Card $card The Card model.
     * @param Staff $staff The Staff model.
     * @param Member $member The Member model.
     * @param Reward $reward The Reward model.
     * @param string $created_at Optional date.
     */
    public function addClaimRewardAnalytic(Card $card, Staff $staff, Member $member, Reward $reward, string 
    $created_at = null): void
    {
        Analytic::create([
            'partner_id' => $card->partner->id,
            'member_id' => $member->id,
            'staff_id' => $staff->id,
            'card_id' => $card->id,
            'reward_id' => $reward->id,
            'points' => $reward->points,
            'event' => 'claim_reward',
            'created_at' => $created_at ?? Carbon::now('UTC'),
            'updated_at' => $created_at ?? Carbon::now('UTC'),
        ]);
    }

    /**
     * Get the user's time zone if they're logged in, otherwise get the default time zone
     *
     * @return DateTimeZone
     */
    private function getUserTimeZone(): \DateTimeZone
    {
        $timeZone = auth('partner')->check()
            ? auth('partner')->user()->time_zone
            : app()->make('i18n')->time_zone;

        return new \DateTimeZone($timeZone);
    }

    /**
     * Get the number of card views for each hour of a given day or the current day if no day is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $day The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the number of views for each hour of the day, the hours of the day, and a label with the current day.
     */
    public function cardViewsDay(int $cardId, string $day = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no day is given, default to today.
        $day = $day ? Carbon::createFromFormat('Y-m-d', $day, $timeZone) : Carbon::now($timeZone);

        // Initialize views for all hours of the day, from 0 to 23.
        $viewsPerHour = array_fill_keys(range(0, 23), 0);

        // Fetch analytics for the card views on the given day.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $dateFunction = $connection == 'sqlite' ? 'strftime("%H", created_at)' : 'DATE_FORMAT(created_at, "%H")';

        //$analytics = Analytic::whereIn('event', ['card_view', 'reward_view'])
        $analytics = Analytic::where('event', 'card_view')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$day->startOfDay()->toDateTimeString(), $day->endOfDay()->toDateTimeString()])
            ->selectRaw($dateFunction.' as hour, COUNT(*) as views')
            ->groupBy('hour')
            ->get()
            ->keyBy('hour');

        // Replace the initialized views with actual values.
        $total = 0;
        foreach ($analytics as $hour => $analytic) {
            $utcTime = Carbon::createFromFormat('H:i', $hour . ':00', 'UTC');
            $userTime = $utcTime->setTimezone($timeZone);
            $userHour = $userTime->format('G');
            $viewsPerHour[$userHour] = $analytic->views;
            $total += $analytic->views;
        }

        // Get the hours of the day and convert them to two digit strings.
        $hours = array_map(function($hour) {
            return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
        }, array_keys($viewsPerHour));

        // Create a label for the current day.
        $label = '<span class="format-date" data-date-format="xl">' . $day->format('Y-m-d') . '</span>';

        return ['units' => $hours, 'views' => array_values($viewsPerHour), 'label' => $label, 'total' => $total];
    }

    /**
     * Get the number of card views for each day of a given week or the current week if no date is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $date The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the number of views for each day of the week, the days of the week, 
     *               and the label for the week and its range.
     */
    public function cardViewsWeek(int $cardId, string $date = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no date is given, default to today.
        $date = $date ? Carbon::createFromFormat('Y-m-d', $date, $timeZone) : Carbon::now($timeZone);

        // Initialize views for all days of the week.
        $viewsPerDay = array_fill_keys(range(0, 6), 0);

        // Fetch analytics for the card views in the given week.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $dateFunction = $connection === 'sqlite' ? 'strftime("%w", created_at)' : 'DAYOFWEEK(created_at)';

        //$analytics = Analytic::whereIn('event', ['card_view', 'reward_view'])
        $analytics = Analytic::where('event', 'card_view')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$date->startOfWeek()->toDateTimeString(), $date->endOfWeek()->toDateTimeString()])
            ->selectRaw($dateFunction.' as day, COUNT(*) as views')
            ->groupBy('day')
            ->get()
            ->keyBy('day');
        
        // Replace the initialized views with actual values.
        $total = 0;
        foreach ($analytics as $day => $analytic) {
            $day = intval($day);
            $day = $day === 0 ? 6 : $day - 1; // remap so 0 (Sunday) -> 6, 1 (Monday) -> 0, ..., 6 (Saturday) -> 5
            $viewsPerDay[$day] = $analytic->views;
            $total += $analytic->views;
        }

        // Define days of the week.
        $days = array_map(function($dayIndex) use ($date) {
            return $date->copy()->startOfWeek()->addDays($dayIndex)->translatedFormat('D');
        }, range(0, 6));

        // Define the label for the week and its range.
        $label = trans('common.week') . ' ' . $date->weekOfYear . ', ' . $date->year . ' <span class="text-sm ml-2"><span class="format-date" data-date-format="md">' . $date->startOfWeek()->modify('+1 day')->format('Y-m-d') . '</span> - <span class="format-date" data-date-format="md">' . $date->endOfWeek()->modify('+1 day')->format('Y-m-d') . '</span></span>';

        return ['units' => $days, 'views' => array_values($viewsPerDay), 'label' => $label, 'total' => $total];
    }

    /**
     * Get the number of card views for each day of a given month or the current month if no month is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $date The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the number of views for each day of the month, the days of the month, 
     *               and the label containing the name of the month and the year.
     */
    public function cardViewsMonth(int $cardId, string $date = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no date is given, default to today.
        $date = $date ? Carbon::createFromFormat('Y-m-d', $date, $timeZone) : Carbon::now($timeZone);

        // Determine the number of days in the month.
        $daysInMonth = $date->daysInMonth;

        // Initialize views for all days of the month.
        $viewsPerDay = array_fill_keys(range(1, $daysInMonth), 0);

        // Fetch analytics for the card views in the given month.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $dateFunction = $connection === 'sqlite' ? 'strftime("%d", created_at)' : 'DAY(created_at)';

        //$analytics = Analytic::whereIn('event', ['card_view', 'reward_view'])
        $analytics = Analytic::where('event', 'card_view')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$date->startOfMonth()->toDateTimeString(), $date->endOfMonth()->toDateTimeString()])
            ->selectRaw($dateFunction.' as day, COUNT(*) as views')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        // Replace the initialized views with actual values.
        $total = 0;
        foreach ($analytics as $day => $analytic) {
            $viewsPerDay[intval($day)] = $analytic->views;
            $total += $analytic->views;
        }

        // Define days of the month.
        $days = array_map(function($day) {
            return str_pad($day, 2, '0', STR_PAD_LEFT);
        }, array_keys($viewsPerDay));

        // Define the label for the month and year.
        $label = ucfirst($date->translatedFormat('F')) . ' ' . $date->translatedFormat('Y');

        return ['units' => $days, 'views' => array_values($viewsPerDay), 'label' => $label, 'total' => $total];
    }

    /**
     * Get the number of card views for each month of a given year or the current year if no date is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $date The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the number of views for each month of the year, the months of the year, 
     *               and the label containing the year.
     */
    public function cardViewsYear(int $cardId, string $date = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no date is given, default to today.
        $date = $date ? Carbon::createFromFormat('Y-m-d', $date, $timeZone) : Carbon::now($timeZone);

        // Initialize views for all months of the year.
        $viewsPerMonth = array_fill_keys(range(1, 12), 0);

        // Fetch analytics for the card views in the given year.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $dateFunction = $connection === 'sqlite' ? 'strftime("%m", created_at)' : 'MONTH(created_at)';

        //$analytics = Analytic::whereIn('event', ['card_view', 'reward_view'])
        $analytics = Analytic::where('event', 'card_view')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$date->startOfYear()->toDateTimeString(), $date->endOfYear()->toDateTimeString()])
            ->selectRaw($dateFunction.' as month, COUNT(*) as views')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Replace the initialized views with actual values.
        $total = 0;
        foreach ($analytics as $month => $analytic) {
            $viewsPerMonth[intval($month)] = $analytic->views;
            $total += $analytic->views;
        }

        // Define months of the year.
        $months = array_map(function($month) use ($date) {
            return $date->copy()->day(1)->month($month)->translatedFormat('F');
        }, array_keys($viewsPerMonth));

        // Define the label for the year.
        $label = $date->format('Y');

        return ['units' => $months, 'views' => array_values($viewsPerMonth), 'label' => $label, 'total' => $total];
    }

    /**
     * Get the number of reward views for each hour of a given day or the current day if no day is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $day The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the number of views for each hour of the day, the hours of the day, and a label with the current day.
     */
    public function rewardViewsDay(int $cardId, string $day = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no day is given, default to today.
        $day = $day ? Carbon::createFromFormat('Y-m-d', $day, $timeZone) : Carbon::now($timeZone);

        // Initialize views for all hours of the day, from 0 to 23.
        $viewsPerHour = array_fill_keys(range(0, 23), 0);

        // Fetch analytics for the card views on the given day.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $dateFunction = $connection == 'sqlite' ? 'strftime("%H", created_at)' : 'DATE_FORMAT(created_at, "%H")';

        //$analytics = Analytic::whereIn('event', ['card_view', 'reward_view'])
        $analytics = Analytic::where('event', 'reward_view')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$day->startOfDay()->toDateTimeString(), $day->endOfDay()->toDateTimeString()])
            ->selectRaw($dateFunction.' as hour, COUNT(*) as views')
            ->groupBy('hour')
            ->get()
            ->keyBy('hour');

        // Replace the initialized views with actual values.
        $total = 0;
        foreach ($analytics as $hour => $analytic) {
            $utcTime = Carbon::createFromFormat('H:i', $hour . ':00', 'UTC');
            $userTime = $utcTime->setTimezone($timeZone);
            $userHour = $userTime->format('G');
            $viewsPerHour[$userHour] = $analytic->views;
            $total += $analytic->views;
        }

        // Get the hours of the day and convert them to two digit strings.
        $hours = array_map(function($hour) {
            return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
        }, array_keys($viewsPerHour));

        // Create a label for the current day.
        $label = '<span class="format-date" data-date-format="xl">' . $day->format('Y-m-d') . '</span>';

        return ['units' => $hours, 'views' => array_values($viewsPerHour), 'label' => $label, 'total' => $total];
    }

    /**
     * Get the number of reward views for each day of a given week or the current week if no date is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $date The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the number of views for each day of the week, the days of the week, 
     *               and the label for the week and its range.
     */
    public function rewardViewsWeek(int $cardId, string $date = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no date is given, default to today.
        $date = $date ? Carbon::createFromFormat('Y-m-d', $date, $timeZone) : Carbon::now($timeZone);

        // Initialize views for all days of the week.
        $viewsPerDay = array_fill_keys(range(0, 6), 0);

        // Fetch analytics for the card views in the given week.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $dateFunction = $connection === 'sqlite' ? 'strftime("%w", created_at)' : 'DAYOFWEEK(created_at)';

        //$analytics = Analytic::whereIn('event', ['card_view', 'reward_view'])
        $analytics = Analytic::where('event', 'reward_view')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$date->startOfWeek()->toDateTimeString(), $date->endOfWeek()->toDateTimeString()])
            ->selectRaw($dateFunction.' as day, COUNT(*) as views')
            ->groupBy('day')
            ->get()
            ->keyBy('day');
        
        // Replace the initialized views with actual values.
        $total = 0;
        foreach ($analytics as $day => $analytic) {
            $day = intval($day);
            $day = $day === 0 ? 6 : $day - 1; // remap so 0 (Sunday) -> 6, 1 (Monday) -> 0, ..., 6 (Saturday) -> 5
            $viewsPerDay[$day] = $analytic->views;
            $total += $analytic->views;
        }

        // Define days of the week.
        $days = array_map(function($dayIndex) use ($date) {
            return $date->copy()->startOfWeek()->addDays($dayIndex)->translatedFormat('D');
        }, range(0, 6));

        // Define the label for the week and its range.
        $label = trans('common.week') . ' ' . $date->weekOfYear . ', ' . $date->year . ' <span class="text-sm ml-2"><span class="format-date" data-date-format="md">' . $date->startOfWeek()->modify('+1 day')->format('Y-m-d') . '</span> - <span class="format-date" data-date-format="md">' . $date->endOfWeek()->modify('+1 day')->format('Y-m-d') . '</span></span>';

        return ['units' => $days, 'views' => array_values($viewsPerDay), 'label' => $label, 'total' => $total];
    }

    /**
     * Get the number of reward views for each day of a given month or the current month if no month is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $date The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the number of views for each day of the month, the days of the month, 
     *               and the label containing the name of the month and the year.
     */
    public function rewardViewsMonth(int $cardId, string $date = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no date is given, default to today.
        $date = $date ? Carbon::createFromFormat('Y-m-d', $date, $timeZone) : Carbon::now($timeZone);

        // Determine the number of days in the month.
        $daysInMonth = $date->daysInMonth;

        // Initialize views for all days of the month.
        $viewsPerDay = array_fill_keys(range(1, $daysInMonth), 0);

        // Fetch analytics for the card views in the given month.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $dateFunction = $connection === 'sqlite' ? 'strftime("%d", created_at)' : 'DAY(created_at)';

        //$analytics = Analytic::whereIn('event', ['card_view', 'reward_view'])
        $analytics = Analytic::where('event', 'reward_view')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$date->startOfMonth()->toDateTimeString(), $date->endOfMonth()->toDateTimeString()])
            ->selectRaw($dateFunction.' as day, COUNT(*) as views')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        // Replace the initialized views with actual values.
        $total = 0;
        foreach ($analytics as $day => $analytic) {
            $viewsPerDay[intval($day)] = $analytic->views;
            $total += $analytic->views;
        }

        // Define days of the month.
        $days = array_map(function($day) {
            return str_pad($day, 2, '0', STR_PAD_LEFT);
        }, array_keys($viewsPerDay));

        // Define the label for the month and year.
        $label = ucfirst($date->translatedFormat('F')) . ' ' . $date->translatedFormat('Y');

        return ['units' => $days, 'views' => array_values($viewsPerDay), 'label' => $label, 'total' => $total];
    }

    /**
     * Get the number of reward views for each month of a given year or the current year if no date is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $date The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the number of views for each month of the year, the months of the year, 
     *               and the label containing the year.
     */
    public function rewardViewsYear(int $cardId, string $date = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no date is given, default to today.
        $date = $date ? Carbon::createFromFormat('Y-m-d', $date, $timeZone) : Carbon::now($timeZone);

        // Initialize views for all months of the year.
        $viewsPerMonth = array_fill_keys(range(1, 12), 0);

        // Fetch analytics for the card views in the given year.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $dateFunction = $connection === 'sqlite' ? 'strftime("%m", created_at)' : 'MONTH(created_at)';

        //$analytics = Analytic::whereIn('event', ['card_view', 'reward_view'])
        $analytics = Analytic::where('event', 'reward_view')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$date->startOfYear()->toDateTimeString(), $date->endOfYear()->toDateTimeString()])
            ->selectRaw($dateFunction.' as month, COUNT(*) as views')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Replace the initialized views with actual values.
        $total = 0;
        foreach ($analytics as $month => $analytic) {
            $viewsPerMonth[intval($month)] = $analytic->views;
            $total += $analytic->views;
        }

        // Define months of the year.
        $months = array_map(function($month) use ($date) {
            return $date->copy()->day(1)->month($month)->translatedFormat('F');
        }, array_keys($viewsPerMonth));

        // Define the label for the year.
        $label = $date->format('Y');

        return ['units' => $months, 'views' => array_values($viewsPerMonth), 'label' => $label, 'total' => $total];
    }

    /**
     * Get the total points issued for each hour of a given day or the current day if no day is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $day The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the total points for each hour of the day, the hours of the day, and a label with the current day.
     */
    public function pointsIssuedDay(int $cardId, string $day = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no day is given, default to today.
        $day = $day ? Carbon::createFromFormat('Y-m-d', $day, $timeZone) : Carbon::now($timeZone);

        // Initialize points for all hours of the day, from 0 to 23.
        $pointsPerHour = array_fill_keys(range(0, 23), 0);

        // Fetch analytics for the points issued on the given day.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $dateFunction = $connection === 'sqlite' ? 'strftime("%H", created_at)' : 'HOUR(created_at)';

        $analytics = Analytic::where('event', 'issue_points')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$day->startOfDay()->toDateTimeString(), $day->endOfDay()->toDateTimeString()])
            ->selectRaw($dateFunction.' as hour, SUM(points) as points')
            ->groupBy('hour')
            ->get()
            ->keyBy('hour');

        // Replace the initialized points with actual values.
        $total = 0;
        foreach ($analytics as $hour => $analytic) {
            $utcTime = Carbon::createFromFormat('H:i', $hour . ':00', 'UTC');
            $userTime = $utcTime->setTimezone($timeZone);
            $userHour = $userTime->format('G');
            $pointsPerHour[$userHour] = $analytic->points;
            $total += $analytic->points;
        }

        // Get the hours of the day and convert them to two digit strings.
        $hours = array_map(function($hour) {
            return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
        }, array_keys($pointsPerHour));

        // Create a label for the current day.
        $label = '<span class="format-date" data-date-format="xl">' . $day->format('Y-m-d') . '</span>';

        return ['units' => $hours, 'points' => array_values($pointsPerHour), 'label' => $label, 'total' => $total];
    }

    /**
     * Get the total points issued for each day of a given week or the current week if no date is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $date The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the total points for each day of the week, the days of the week, 
     *               and the label for the week and its range.
     */
    public function pointsIssuedWeek(int $cardId, string $date = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no date is given, default to today.
        $date = $date ? Carbon::createFromFormat('Y-m-d', $date, $timeZone) : Carbon::now($timeZone);

        // Initialize points for all days of the week.
        $pointsPerDay = array_fill_keys(range(0, 6), 0);

        // Fetch analytics for the points issued in the given week.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $dateFunction = $connection === 'sqlite' ? 'strftime("%w", created_at)' : 'WEEKDAY(created_at)';

        $analytics = Analytic::where('event', 'issue_points')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$date->startOfWeek()->toDateTimeString(), $date->endOfWeek()->toDateTimeString()])
            ->selectRaw($dateFunction.' as day, SUM(points) as points') // SUM(points) - COUNT(*)
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        // Replace the initialized points with actual values.
        $total = 0;
        foreach ($analytics as $day => $analytic) {
            $day = intval($day);
            $day = $day === 0 ? 6 : $day - 1; // remap so 0 (Sunday) -> 6, 1 (Monday) -> 0, ..., 6 (Saturday) -> 5
            $pointsPerDay[$day] = $analytic->points;
            $total += $analytic->points;
        }

        // Define days of the week.
        $days = array_map(function($dayIndex) use ($date) {
            return $date->copy()->startOfWeek()->addDays($dayIndex)->translatedFormat('D');
        }, range(0, 6));

        // Define the label for the week and its range.
        $label = trans('common.week') . ' ' . $date->weekOfYear . ', ' . $date->year . ' <span class="text-sm ml-2"><span class="format-date" data-date-format="md">' . $date->startOfWeek()->modify('+1 day')->format('Y-m-d') . '</span> - <span class="format-date" data-date-format="md">' . $date->endOfWeek()->modify('+1 day')->format('Y-m-d') . '</span></span>';

        return ['units' => $days, 'points' => array_values($pointsPerDay), 'label' => $label, 'total' => $total];
    }

    /**
     * Get the total points issued for each day of a given month or the current month if no month is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $date The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the total points for each day of the month, the days of the month, 
     *               and the label containing the name of the month and the year.
     */
    public function pointsIssuedMonth(int $cardId, string $date = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no date is given, default to today.
        $date = $date ? Carbon::createFromFormat('Y-m-d', $date, $timeZone) : Carbon::now($timeZone);

        // Determine the number of days in the month.
        $daysInMonth = $date->daysInMonth;

        // Initialize points for all days of the month.
        $pointsPerDay = array_fill_keys(range(1, $daysInMonth), 0);

        // Fetch analytics for the points issued in the given month.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $dateFunction = $connection === 'sqlite' ? 'strftime("%d", created_at)' : 'DAY(created_at)';

        $analytics = Analytic::where('event', 'issue_points')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$date->startOfMonth()->toDateTimeString(), $date->endOfMonth()->toDateTimeString()])
            ->selectRaw($dateFunction.' as day, SUM(points) as points')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        // Replace the initialized points with actual values.
        $total = 0;
        foreach ($analytics as $day => $analytic) {
            $pointsPerDay[intval($day)] = $analytic->points;
            $total += $analytic->points;
        }

        // Define days of the month.
        $days = array_map(function($day) {
            return str_pad($day, 2, '0', STR_PAD_LEFT);
        }, array_keys($pointsPerDay));

        // Define the label for the month and year.
        $label = ucfirst($date->translatedFormat('F')) . ' ' . $date->translatedFormat('Y');

        return ['units' => $days, 'points' => array_values($pointsPerDay), 'label' => $label, 'total' => $total];
    }

    /**
     * Get the total points issued for each month of a given year or the current year if no date is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $date The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the total points for each month of the year, the months of the year, 
     *               and the label containing the year.
     */
    public function pointsIssuedYear(int $cardId, string $date = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no date is given, default to today.
        $date = $date ? Carbon::createFromFormat('Y-m-d', $date, $timeZone) : Carbon::now($timeZone);

        // Initialize points for all months of the year.
        $pointsPerMonth = array_fill_keys(range(1, 12), 0);

        // Fetch analytics for the points issued in the given year.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $dateFunction = $connection === 'sqlite' ? 'strftime("%m", created_at)' : 'MONTH(created_at)';

        $analytics = Analytic::where('event', 'issue_points')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$date->startOfYear()->toDateTimeString(), $date->endOfYear()->toDateTimeString()])
            ->selectRaw($dateFunction.' as month, SUM(points) as points')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Replace the initialized points with actual values.
        $total = 0;
        foreach ($analytics as $month => $analytic) {
            $pointsPerMonth[intval($month)] = $analytic->points;
            $total += $analytic->points;
        }

        // Define months of the year.
        $months = array_map(function($month) use ($date) {
            return $date->copy()->day(1)->month($month)->translatedFormat('F');
        }, array_keys($pointsPerMonth));

        // Define the label for the year.
        $label = $date->format('Y');

        return ['units' => $months, 'points' => array_values($pointsPerMonth), 'label' => $label, 'total' => $total];
    }

    /**
     * Get the number of claimed rewards for each hour of a given day or the current day if no day is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $day The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the number of claimed rewards for each hour of the day, the hours of the day, and a label with the current day.
     */
    public function rewardsClaimedDay(int $cardId, string $day = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no day is given, default to today.
        $day = $day ? Carbon::createFromFormat('Y-m-d', $day, $timeZone) : Carbon::now($timeZone);

        // Initialize rewards for all hours of the day, from 0 to 23.
        $rewardsPerHour = array_fill_keys(range(0, 23), 0);

        // Fetch analytics for the claimed rewards on the given day.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $dateFunction = $connection === 'sqlite' ? 'strftime("%H", created_at)' : 'HOUR(created_at)';

        $analytics = Analytic::where('event', 'claim_reward')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$day->startOfDay()->toDateTimeString(), $day->endOfDay()->toDateTimeString()])
            ->selectRaw($dateFunction.' as hour, COUNT(*) as rewards')
            ->groupBy('hour')
            ->get()
            ->keyBy('hour');

        // Replace the initialized rewards with actual values.
        $total = 0;
        foreach ($analytics as $hour => $analytic) {
            $utcTime = Carbon::createFromFormat('H:i', $hour . ':00', 'UTC');
            $userTime = $utcTime->setTimezone($timeZone);
            $userHour = $userTime->format('G');
            $rewardsPerHour[$userHour] = $analytic->rewards;
            $total += $analytic->rewards;
        }

        // Get the hours of the day and convert them to two digit strings.
        $hours = array_map(function($hour) {
            return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
        }, array_keys($rewardsPerHour));

        // Create a label for the current day.
        $label = '<span class="format-date" data-date-format="xl">' . $day->format('Y-m-d') . '</span>';

        return ['units' => $hours, 'rewards' => array_values($rewardsPerHour), 'label' => $label, 'total' => $total];
    }

    /**
     * Get the number of claimed rewards for each day of a given week or the current week if no date is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $date The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the number of claimed rewards for each day of the week, the days of the week, 
     *               and the label for the week and its range.
     */
    public function rewardsClaimedWeek(int $cardId, string $date = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no date is given, default to today.
        $date = $date ? Carbon::createFromFormat('Y-m-d', $date, $timeZone) : Carbon::now($timeZone);

        // Initialize rewards for all days of the week.
        $rewardsPerDay = array_fill_keys(range(0, 6), 0);

        // Fetch analytics for the claimed rewards in the given week.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
         // +1 to make it consistent with SQLite's strftime %w, which treats Sunday as 0.
        $dateFunction = $connection === 'sqlite' ? 'strftime("%w", created_at)' : 'WEEKDAY(created_at) + 1';

        $analytics = Analytic::where('event', 'claim_reward')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$date->startOfWeek()->toDateTimeString(), $date->endOfWeek()->toDateTimeString()])
            ->selectRaw($dateFunction.' as day, COUNT(*) as rewards')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        // Replace the initialized rewards with actual values.
        $total = 0;
        foreach ($analytics as $day => $analytic) {
            $day = intval($day);
            $day = $day === 0 ? 6 : $day - 1; // remap so 0 (Sunday) -> 6, 1 (Monday) -> 0, ..., 6 (Saturday) -> 5
            $rewardsPerDay[$day] = $analytic->rewards;
            $total += $analytic->rewards;
        }

        // Define days of the week.
        $days = array_map(function($dayIndex) use ($date) {
            return $date->copy()->startOfWeek()->addDays($dayIndex)->translatedFormat('D');
        }, range(0, 6));

        // Define the label for the week and its range.
        $label = trans('common.week') . ' ' . $date->weekOfYear . ', ' . $date->year . ' <span class="text-sm ml-2"><span class="format-date" data-date-format="md">' . $date->startOfWeek()->modify('+1 day')->format('Y-m-d') . '</span> - <span class="format-date" data-date-format="md">' . $date->endOfWeek()->modify('+1 day')->format('Y-m-d') . '</span></span>';

        return ['units' => $days, 'rewards' => array_values($rewardsPerDay), 'label' => $label, 'total' => $total];
    }

    /**
     * Get the number of claimed rewards for each day of a given month or the current month if no month is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $date The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the number of rewards for each day of the month, the days of the month, 
     *               and the label containing the name of the month and the year.
     */
    public function rewardsClaimedMonth(int $cardId, string $date = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no date is given, default to today.
        $date = $date ? Carbon::createFromFormat('Y-m-d', $date, $timeZone) : Carbon::now($timeZone);

        // Determine the number of days in the month.
        $daysInMonth = $date->daysInMonth;

        // Initialize rewards for all days of the month.
        $rewardsPerDay = array_fill_keys(range(1, $daysInMonth), 0);

        // Fetch analytics for the claimed rewards in the given month.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $dateFunction = $connection === 'sqlite' ? 'strftime("%d", created_at)' : 'DATE_FORMAT(created_at, "%d")';

        $analytics = Analytic::where('event', 'claim_reward')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$date->startOfMonth()->toDateTimeString(), $date->endOfMonth()->toDateTimeString()])
            ->selectRaw($dateFunction.' as day, COUNT(*) as rewards')
            ->groupBy('day')
            ->get()
            ->keyBy('day');
        
        // Replace the initialized rewards with actual values.
        $total = 0;
        foreach ($analytics as $day => $analytic) {
            $rewardsPerDay[intval($day)] = $analytic->rewards;
            $total += $analytic->rewards;
        }

        // Define days of the month.
        $days = array_map(function($day) {
            return str_pad($day, 2, '0', STR_PAD_LEFT);
        }, array_keys($rewardsPerDay));

        // Define the label for the month and year.
        $label = ucfirst($date->translatedFormat('F')) . ' ' . $date->translatedFormat('Y');

        return ['units' => $days, 'rewards' => array_values($rewardsPerDay), 'label' => $label, 'total' => $total];
    }

    /**
     * Get the number of claimed rewards for each month of a given year or the current year if no date is given.
     *
     * @param  int  $cardId The card id.
     * @param  string|null  $date The date (Y-m-d). If null, defaults to today's date.
     * @return array An array containing the number of rewards for each month of the year, the months of the year, 
     *               and the label containing the year.
     */
    public function rewardsClaimedYear(int $cardId, string $date = null): array
    {
        // Get the user's time zone
        $timeZone = $this->getUserTimeZone();

        // If no date is given, default to today.
        $date = $date ? Carbon::createFromFormat('Y-m-d', $date, $timeZone) : Carbon::now($timeZone);

        // Initialize rewards for all months of the year.
        $rewardsPerMonth = array_fill_keys(range(1, 12), 0);

        // Fetch analytics for the claimed rewards in the given year.
        $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $dateFunction = $connection === 'sqlite' ? 'strftime("%m", created_at)' : 'MONTH(created_at)';

        $analytics = Analytic::where('event', 'claim_reward')
            ->where('card_id', $cardId)
            ->whereBetween('created_at', [$date->startOfYear()->toDateTimeString(), $date->endOfYear()->toDateTimeString()])
            ->selectRaw($dateFunction.' as month, COUNT(*) as rewards')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Replace the initialized rewards with actual values.
        $total = 0;
        foreach ($analytics as $month => $analytic) {
            $rewardsPerMonth[intval($month)] = $analytic->rewards;
            $total += $analytic->rewards;
        }

        // Define months of the year.
        $months = array_map(function($month) use ($date) {
            return $date->copy()->day(1)->month($month)->translatedFormat('F');
        }, array_keys($rewardsPerMonth));

        // Define the label for the year.
        $label = $date->format('Y');

        return ['units' => $months, 'rewards' => array_values($rewardsPerMonth), 'label' => $label, 'total' => $total];
    }
}