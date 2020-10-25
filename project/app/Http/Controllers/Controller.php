<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Flash alert message into the next request.
     *
     * @param Request $request
     * @param string $alertType
     * @param string $alertMessage
     */
    protected function flashAlertMessage(Request $request, string $alertType, string $alertMessage)
    {
        $session = $request->session();
        $session->flash('alertType', $alertType);
        $session->flash('alertMessage', $alertMessage);
    }

    /**
     * Include alert message from the session data.
     *
     * @param Request $request
     * @param $data
     */
    protected function includeAlertMessage(Request $request, &$data)
    {
        $session = $request->session();

        if ($session->exists('alertType')) {
            $data['alertType'] = $session->get('alertType');
        }

        if ($session->exists('alertMessage')) {
            $data['alertMessage'] = $session->get('alertMessage');
        }
    }

    /**
     * Increment failed request count.
     *
     * @param Request $request
     * @param string $type
     */
    protected function incrementGlobalFailedCount(Request $request, string $type): void
    {
        $session = $request->session();
        $failedCount = $session->get($type . '_failed_count', 0);
        $failedCount++;

        if ($failedCount >= 3) {
            $failedCount = 0;
            $session->put($type . '_reset_datetime', Carbon::now()->addMinutes(5)->getTimestamp());
        }

        $session->put($type . '_failed_count', $failedCount);
    }

    /**
     * Reset failed request count.
     *
     * @param Request $request
     * @param string $type
     */
    protected function resetGlobalFailedCount(Request $request, string $type)
    {
        $session = $request->session();
        $session->put($type . '_failed_count', 0);
    }

    /**
     * Get if the user is currently serving a timeout.
     *
     * @param Request $request
     * @param string $type
     * @return bool true if the user is currently serving a timeout, else false.
     */
    protected function isServingGlobalTimeout(Request $request, string $type): bool
    {
        $session = $request->session();
        return $session->exists($type . '_reset_datetime') && Carbon::now()->lessThan(Carbon::createFromTimestamp($session->get($type . '_reset_datetime')));
    }
}
