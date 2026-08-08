<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'lowStockThreshold' => config('settings.low_stock_threshold', 10),
            'siteName' => config('settings.site_name', 'Online Shopping Management System'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'low_stock_threshold' => ['required', 'integer', 'min:1', 'max:1000'],
            'site_name' => ['required', 'string', 'max:255'],
        ]);

        // Persist settings in the config cache file
        $path = config_path('settings.php');

        $lowStockThreshold = (int) $request->low_stock_threshold;
        $siteName = $request->site_name;

        $content = "<?php\n\nreturn [\n"
            . "    'low_stock_threshold' => {$lowStockThreshold},\n"
            . "    'site_name' => '" . str_replace("'", "\\'", $siteName) . "',\n"
            . "];\n";

        file_put_contents($path, $content);

        AuditService::log('Settings updated', 'Settings', 'Low-stock threshold set to ' . $lowStockThreshold . '.');

        return back()->with('success', 'Settings saved successfully.');
    }
}
