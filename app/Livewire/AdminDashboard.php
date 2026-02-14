<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\TaxonomyTerm;
use App\Models\User;
use App\Models\NewsletterSubscriber;
use Illuminate\View\View;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class AdminDashboard extends Component
{
    public function render(): View
    {
        $stats = [
            'total_posts' => Post::count(),
            'published_posts' => Post::published()->count(),
            'draft_posts' => Post::draft()->count(),
            'total_users' => User::count(),
            'total_tags' => TaxonomyTerm::whereHas('taxonomy', fn ($q) => $q->where('type', 'tag'))->count(),
            'total_categories' => TaxonomyTerm::whereHas('taxonomy', fn ($q) => $q->where('type', 'category'))->count(),
            'total_subscribers' => NewsletterSubscriber::count(),
            'active_subscribers' => NewsletterSubscriber::where('status', 'active')->count(),
        ];

        $recentPosts = Post::query()
            ->with(['postable', 'author'])
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = User::query()
            ->latest()
            ->take(5)
            ->get();

        $roles = Role::all();

        return view('livewire.admin-dashboard', [
            'stats' => $stats,
            'recentPosts' => $recentPosts,
            'recentUsers' => $recentUsers,
            'roles' => $roles,
        ]);
    }
}
