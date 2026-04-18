<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessTypeController extends Controller
{
    public function index(): View
    {
        $types = BusinessType::query()->withCount('businesses')->orderBy('name')->get();

        return view('pages.business-types-index', compact('types'));
    }

    public function create(): View
    {
        return view('pages.business-types-create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'return' => ['nullable', 'string', 'in:setup-business'],
        ]);

        BusinessType::query()->create(['name' => $data['name']]);

        if ($this->wantsReturnToSetupBusiness($request)) {
            return redirect()
                ->route('setup.business')
                ->with('status', 'Типът бизнес е записан. Изберете го в падащото меню „Тип бизнес“.');
        }

        return redirect()
            ->route('business-types.index')
            ->with('status', 'Типът бизнес е записан.');
    }

    private function wantsReturnToSetupBusiness(Request $request): bool
    {
        $v = $request->input('return') ?? $request->query('return');

        return $v === 'setup-business';
    }

    public function edit(BusinessType $business_type): View
    {
        return view('pages.business-types-edit', ['type' => $business_type]);
    }

    public function update(Request $request, BusinessType $business_type): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $business_type->update($data);

        return redirect()
            ->route('business-types.index')
            ->with('status', 'Типът е обновен.');
    }

    public function destroy(BusinessType $business_type): RedirectResponse
    {
        if ($business_type->businesses()->exists()) {
            return redirect()
                ->route('business-types.index')
                ->with('error', 'Не може да изтриете тип с присвоени бизнеси. Първо променете типа на тези бизнеси.');
        }

        $business_type->delete();

        return redirect()
            ->route('business-types.index')
            ->with('status', 'Типът е изтрит.');
    }
}
