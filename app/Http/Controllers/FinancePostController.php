<?php

namespace App\Http\Controllers;

use App\Models\FinancePost;
use App\Http\Requests\StoreFinancePostRequest;
use App\Http\Requests\UpdateFinancePostRequest;
use Illuminate\Http\Request;

class FinancePostController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $posts = FinancePost::when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('type', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('finance-posts.index', compact('posts', 'search'));
    }

    public function create()
    {
        return view('finance-posts.create');
    }

    public function store(StoreFinancePostRequest $request)
    {
        FinancePost::create($request->validated());

        return redirect()->route('finance-posts.index')
            ->with('success', 'Pos Keuangan berhasil ditambahkan.');
    }

    public function edit(FinancePost $finance_post)
    {
        return view('finance-posts.edit', compact('finance_post'));
    }

    public function update(UpdateFinancePostRequest $request, FinancePost $finance_post)
    {
        $finance_post->update($request->validated());

        return redirect()->route('finance-posts.index')
            ->with('success', 'Pos Keuangan berhasil diperbarui.');
    }

    public function destroy(FinancePost $finance_post)
    {
        $finance_post->delete();

        return redirect()->route('finance-posts.index')
            ->with('success', 'Pos Keuangan berhasil dihapus.');
    }
}
