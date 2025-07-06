<?php

namespace App;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TenantFinder
{
    public function findForRequest(Request $request): ?BaseTenant
    {
        if ($slug = $request->query('tenant')) {
            return $this->resolveTenant($slug);
        }

        $host = preg_replace('/^www\./', '', $request->getHost());
        if (!str_contains($host, '.')) {
            return null;
        }

        $subdomain = explode('.', $host)[0];
        return $this->resolveTenant($subdomain);
    }

    protected function resolveTenant(string $slug): ?BaseTenant
    {
        $tenant = Tenant::where('slug', $slug)->first();
logger()->info('[TenantFinder] Tenant resolvido:', ['slug' => $tenant->slug]);
        if (!$tenant) {
            throw new NotFoundHttpException("Tenant '{$slug}' não encontrado.");
        }

        $tenant->makeCurrent();

        return $tenant;
    }
}