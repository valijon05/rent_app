<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use MoonShine\Enums\ToastType;
use MoonShine\Http\Responses\MoonShineJsonResponse;
use MoonShine\Pages\Page;
use MoonShine\Pages\ViewPage;
use MoonShine\Traits\Controller\InteractsWithAuth;
use MoonShine\Traits\Controller\InteractsWithUI;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class MoonShineController extends BaseController
{
    use InteractsWithUI;
    use InteractsWithAuth;

    protected function json(
        string $message = '',
        array $data = [],
        string $redirect = null,
        string|ToastType $messageType = 'success'
    ): JsonResponse {
        return MoonShineJsonResponse::make(data: $data)
            ->toast($message, $messageType)
            ->when(
                $redirect,
                fn (MoonShineJsonResponse $response): MoonShineJsonResponse => $response->redirect($redirect)
            );
    }

    protected function view(string $path, array $data = []): Page
    {
        $page = ViewPage::make();

        $page->beforeRender();

        return $page->setContentView($path, $data);
    }

    /**
     * @throws Throwable
     */
    protected function reportAndResponse(bool $isAjax, Throwable $e, string $redirectRoute): Response
    {
        $message = app()->isProduction() ? __('moonshine::ui.saved_error') : $e->getMessage();
        $type = 'error';

        if ($flash = session()->get('toast')) {
            session()->forget(['toast', '_flash.old', '_flash.new']);

            $message = $flash['message'] ?? $message;
        }

        report_if(app()->isProduction(), $e);

        if ($isAjax) {
            return $this->json(
                message: __($message),
                messageType: $type
            );
        }

        throw_if(! app()->isProduction(), $e);

        $this->toast(
            __($message),
            $type
        );

        return redirect($redirectRoute)->withInput();
    }
}
