<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index() { return view('admin.menus.index', ['menus' => Menu::latest()->paginate(15)]); }
    public function create() { return view('admin.menus.create'); }
    public function store(Request $request) { return redirect()->route('admin.menus.index')->with('success', 'Menu created.'); }
    public function edit(Menu $menu) { return view('admin.menus.edit', compact('menu')); }
    public function update(Request $request, Menu $menu) { return redirect()->route('admin.menus.index')->with('success', 'Menu updated.'); }
    public function destroy(Menu $menu) { $menu->delete(); return redirect()->route('admin.menus.index')->with('success', 'Menu deleted.'); }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:menus,id']);
        $count = Menu::whereIn('id', $request->ids)->delete();
        return redirect()->route('admin.menus.index')->with('success', "{$count} menu(s) deleted.");
    }
}
