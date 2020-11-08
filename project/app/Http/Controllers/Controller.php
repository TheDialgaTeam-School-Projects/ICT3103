<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Closure;
use Countable;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Redirector;
use Illuminate\Session\Store;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var ConfigRepository
     */
    private $configRepository;

    /**
     * @var Redirector
     */
    private $redirector;

    /**
     * @var Store
     */
    private $session;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var ViewFactory
     */
    private $viewFactory;

    public function __construct(
        ConfigRepository $configRepository,
        Redirector $redirector,
        Session $session,
        Translator $translator,
        ViewFactory $viewFactory
    ) {
        $this->viewFactory = $viewFactory;
        $this->session = $session;
        $this->configRepository = $configRepository;
        $this->translator = $translator;
        $this->redirector = $redirector;
    }

    #region Helper Function

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param string|null $view
     * @param Arrayable|array $data
     * @param array $mergeData
     * @return View|ViewFactory
     */
    protected function view(string $view, array $data = [], $mergeData = []): View
    {
        return $this->viewFactory->make($view, $data, array_merge([
            'alertType' => $this->session->pull('alertType'),
            'alertMessage' => $this->session->pull('alertMessage'),
        ], $mergeData));
    }

    /**
     * Create a new redirect response to a named route.
     *
     * @param string $route
     * @param mixed $parameters
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    protected function route(string $route, $parameters = [], $status = 302, $headers = []): RedirectResponse
    {
        return $this->redirector->route($route, $parameters, $status, $headers);
    }

    /**
     * Translate the given message.
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return mixed
     */
    protected function __(string $key, array $replace = [], string $locale = null)
    {
        return $this->translator->get($key, $replace, $locale);
    }

    /**
     * Translates the given message based on a count.
     *
     * @param string $key
     * @param Countable|int|array $number
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    protected function trans_choice(string $key, $number, array $replace = [], string $locale = null): string
    {
        return $this->translator->choice($key, $number, $replace, $locale);
    }

    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param array|string|null $key
     * @param mixed $default
     * @return mixed|Repository
     */
    protected function config($key, $default = null)
    {
        if (is_null($key)) {
            return $this->configRepository;
        }

        if (is_array($key)) {
            $this->configRepository->set($key);
        }

        return $this->configRepository->get($key, $default);
    }

    #endregion Helper Function

    /**
     * Flash an alert message to the session.
     *
     * @param string $alertType
     * @param string $alertMessage
     */
    protected function flashAlertMessage(string $alertType, string $alertMessage): void
    {
        $this->session->flash('alertType', $alertType);
        $this->session->flash('alertMessage', $alertMessage);
    }

    /**
     * Get global lockout view or continue to return from continuation.
     *
     * @param string $view
     * @param Closure $continuation
     * @return View|mixed
     */
    protected function getGlobalLockoutViewOrContinue(string $view, Closure $continuation)
    {
        if ($this->isGlobalLockoutActive($view, $duration)) {
            // Global Lockout is active.
            $this->flashAlertMessage('error', $this->trans_choice('common.lockout.global', $duration));
            return $this->view($view);
        }

        return $continuation();
    }

    /**
     * Increment global lockout failed count by 1.
     *
     * @param string $key
     */
    protected function incrementGlobalLockoutFailedCount(string $key): void
    {
        $failedCount = $this->session->get($this->getGlobalLockoutFailedCountKey($key), 0);
        $failedCount++;

        if ($failedCount >= $this->getGlobalLockoutMaxAttempt()) {
            $failedCount = 0;
            $this->session->put(
                $this->getGlobalLockoutResetTimestampKey($key),
                Carbon::now()->addMinutes($this->getGlobalLockoutDuration())->getTimestamp()
            );
        }

        $this->session->put($this->getGlobalLockoutFailedCountKey($key), $failedCount);
    }

    /**
     * Reset global lockout failed count.
     *
     * @param string $key
     */
    protected function resetGlobalLockoutFailedCount(string $key): void
    {
        $this->session->put($this->getGlobalLockoutFailedCountKey($key), 0);
    }

    /**
     * Check if global lockout is active.
     *
     * @param string $key
     * @param int|null $duration
     * @return bool true if the global lockout is active or false.
     */
    protected function isGlobalLockoutActive(string $key, int &$duration = null): bool
    {
        $resetTimestamp = $this->session->get($this->getGlobalLockoutResetTimestampKey($key));
        if (!isset($resetTimestamp)) return false;

        $currentTimeStamp = Carbon::now()->getTimestamp();
        if ($currentTimeStamp >= $resetTimestamp) return false;

        $duration = $resetTimestamp - $currentTimeStamp;
        return true;
    }

    /**
     * Get the current session object.
     *
     * @return Store session object.
     */
    protected function getSession(): Store
    {
        return $this->session;
    }

    private function getGlobalLockoutFailedCountKey(string $key): string
    {
        return $key . $this->config('lockout.global.session.failed_count');
    }

    private function getGlobalLockoutResetTimestampKey(string $key): string
    {
        return $key . $this->config('lockout.global.session.reset_timestamp');
    }

    private function getGlobalLockoutMaxAttempt(): int
    {
        return $this->config('lockout.global.max_attempt');
    }

    private function getGlobalLockoutDuration(): int
    {
        return $this->config('lockout.global.lockout_duration');
    }
}
