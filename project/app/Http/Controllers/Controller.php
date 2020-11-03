<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Closure;
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
     * @var Session
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

    public function __construct(ConfigRepository $configRepository,
                                Redirector $redirector,
                                Session $session,
                                Translator $translator,
                                ViewFactory $viewFactory)
    {
        $this->viewFactory = $viewFactory;
        $this->session = $session;
        $this->configRepository = $configRepository;
        $this->translator = $translator;
        $this->redirector = $redirector;
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param string $view
     * @param Arrayable|array $data
     * @return View
     */
    protected function view(string $view, array $data = []): View
    {
        return $this->viewFactory->make($view, $data, [
            'alertType' => $this->session->get('alertType'),
            'alertMessage' => $this->session->get('alertMessage'),
        ]);
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
    protected function redirectToRoute(string $route, $parameters = [], $status = 302, $headers = [])
    {
        return $this->redirector->route($route, $parameters, $status, $headers);
    }

    /**
     * Get the translation for a given key.
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return mixed
     */
    protected function __(string $key, $replace = [], $locale = null)
    {
        return $this->translator->get($key, $replace, $locale);
    }

    protected function flashAlertMessage(string $alertType, string $alertMessage): void
    {
        $this->session->flash('alertType', $alertType);
        $this->session->flash('alertMessage', $alertMessage);
    }

    protected function getGlobalLockoutViewOrContinue($view, Closure $continuation)
    {
        if ($this->isGlobalLockoutActive($view, $duration)) {
            // Global Lockout is active.
            $this->flashAlertMessage('error', $this->translator->choice('lockout.message', $duration, ['seconds', $duration]));
            return $this->view($view);
        }

        return $continuation();
    }

    protected function incrementGlobalLockoutFailedCount(string $key): void
    {
        $failedCount = $this->session->get($this->getGlobalLockoutFailedCountKey($key), 0);
        $failedCount++;

        if ($failedCount >= $this->getGlobalLockoutMaxAttempt()) {
            $failedCount = 0;
            $this->session->put($this->getGlobalLockoutResetTimestampKey($key), Carbon::now()->addMinutes($this->getGlobalLockoutDuration())->getTimestamp());
        }

        $this->session->put($this->getGlobalLockoutFailedCountKey($key), $failedCount);
    }

    protected function resetGlobalLockoutFailedCount(string $key): void
    {
        $this->session->put($this->getGlobalLockoutFailedCountKey($key), 0);
    }

    protected function isGlobalLockoutActive(string $key, int &$duration = null): bool
    {
        $resetTimestamp = $this->session->get($this->getGlobalLockoutResetTimestampKey($key));
        if (!isset($resetTimestamp)) return false;

        $currentTimeStamp = Carbon::now()->getTimestamp();
        if ($currentTimeStamp >= $resetTimestamp) return false;

        $duration = $resetTimestamp - $currentTimeStamp;
        return true;
    }

    protected function getSession(): Session
    {
        return $this->session;
    }

    private function getGlobalLockoutFailedCountKey(string $key): string
    {
        return $key . $this->configRepository->get('lockout.global.session.failed_count');
    }

    private function getGlobalLockoutResetTimestampKey(string $key): string
    {
        return $key . $this->configRepository->get('lockout.global.session.reset_timestamp');
    }

    private function getGlobalLockoutMaxAttempt(): int
    {
        return $this->configRepository->get('lockout.global.max_attempt');
    }

    private function getGlobalLockoutDuration(): int
    {
        return $this->configRepository->get('lockout.global.lockout_duration');
    }
}
