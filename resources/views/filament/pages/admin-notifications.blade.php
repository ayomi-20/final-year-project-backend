<x-filament-panels::page>
<style>
    .notif-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:24px; }
    .notif-stat-card { background:white; border-radius:12px; border:1px solid #e5e7eb; padding:20px; text-align:center; }
    .notif-stat-card.dark { background:#0F3B2E; border-color:#0F3B2E; }
    .notif-stat-num { font-size:28px; font-weight:800; margin:0; color:#0F3B2E; }
    .notif-stat-card.dark .notif-stat-num { color:white; }
    .notif-stat-label { font-size:11px; text-transform:uppercase; letter-spacing:.08em; font-weight:600; color:#9ca3af; margin-top:4px; }
    .notif-stat-card.dark .notif-stat-label { color:#E3EFE5; }
    .notif-filters { display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap; }
    .notif-filter-btn { padding:6px 18px; border-radius:999px; font-size:13px; font-weight:600; border:1.5px solid #e5e7eb; background:white; color:#6b7280; cursor:pointer; transition:all .15s; display:flex; align-items:center; gap:6px; }
    .notif-filter-btn.active { background:#0F3B2E; color:white; border-color:#0F3B2E; }
    .notif-badge { background:red; color:white; border-radius:999px; font-size:10px; padding:1px 6px; font-weight:700; }
    .notif-list { display:flex; flex-direction:column; gap:10px; }
    .notif-item { background:white; border-radius:12px; border:1px solid #e5e7eb; padding:16px 18px; display:flex; align-items:flex-start; gap:14px; transition:box-shadow .15s; }
    .notif-item:hover { box-shadow:0 2px 12px rgba(15,59,46,.08); }
    .notif-item.unread { border-left:4px solid #0F3B2E; }
    .notif-icon { width:42px; height:42px; border-radius:10px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:20px; background:#f3f4f6; }
    .notif-item.unread .notif-icon { background:#E3EFE5; }
    .notif-body { flex:1; min-width:0; }
    .notif-header { display:flex; justify-content:space-between; align-items:flex-start; gap:8px; }
    .notif-title { font-size:14px; font-weight:500; color:#0F3B2E; margin:0; }
    .notif-item.unread .notif-title { font-weight:700; }
    .notif-dot { width:9px; height:9px; border-radius:50%; background:#0F3B2E; flex-shrink:0; margin-top:4px; }
    .notif-body-text { font-size:13px; color:#6b7280; margin:3px 0 0; }
    .notif-footer { display:flex; justify-content:space-between; align-items:center; margin-top:10px; }
    .notif-meta { font-size:11px; color:#9ca3af; }
    .notif-actions { display:flex; gap:14px; align-items:center; }
    .notif-btn { font-size:12px; font-weight:600; background:none; border:none; cursor:pointer; text-decoration:underline; padding:0; }
    .notif-btn.mark { color:#0F3B2E; }
    .notif-btn.del { color:#ef4444; }
    .notif-read-label { font-size:12px; color:#d1d5db; }
    .notif-empty { text-align:center; padding:60px 20px; background:white; border-radius:12px; border:1px solid #e5e7eb; }
    .notif-empty-icon { font-size:48px; margin-bottom:12px; }
    .notif-empty-title { color:#6b7280; font-weight:600; margin:0; font-size:15px; }
    .notif-empty-sub { color:#9ca3af; font-size:13px; margin-top:4px; }
    .notif-pagination { margin-top:20px; }
</style>

<div>
    {{-- Stats --}}
    <div class="notif-stats">
        <div class="notif-stat-card">
            <p class="notif-stat-num">{{ \App\Models\AppNotification::where('is_admin', true)->count() }}</p>
            <p class="notif-stat-label">Total</p>
        </div>
        <div class="notif-stat-card dark">
            <p class="notif-stat-num">{{ \App\Models\AppNotification::where('is_admin', true)->where('is_read', false)->count() }}</p>
            <p class="notif-stat-label">Unread</p>
        </div>
        <div class="notif-stat-card">
            <p class="notif-stat-num">{{ \App\Models\AppNotification::where('is_admin', true)->where('is_read', true)->count() }}</p>
            <p class="notif-stat-label">Read</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="notif-filters">
        @php $unreadCount = \App\Models\AppNotification::where('is_admin', true)->where('is_read', false)->count(); @endphp
        @foreach(['all' => 'All', 'unread' => 'Unread', 'read' => 'Read'] as $value => $label)
            <button
                wire:click="setFilter('{{ $value }}')"
                class="notif-filter-btn {{ $this->filter === $value ? 'active' : '' }}"
            >
                {{ $label }}
                @if($value === 'unread' && $unreadCount > 0)
                    <span class="notif-badge">{{ $unreadCount }}</span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- List --}}
    <div class="notif-list">
        @forelse($this->getNotifications() as $notification)
            <div class="notif-item {{ !$notification->is_read ? 'unread' : '' }}">
                <div class="notif-icon">
                    {{ match($notification->type) {
                        'welcome'  => '🎉',
                        'login'    => '🔐',
                        'new_user' => '👤',
                        'booking'  => '📅',
                        'review'   => '⭐',
                        'provider' => '🏪',
                        'service'  => '🗺️',
                        default    => '🔔',
                    } }}
                </div>
                <div class="notif-body">
                    <div class="notif-header">
                        <p class="notif-title">{{ $notification->title }}</p>
                        @if(!$notification->is_read)
                            <span class="notif-dot"></span>
                        @endif
                    </div>
                    <p class="notif-body-text">{{ $notification->body }}</p>
                    <div class="notif-footer">
                        <span class="notif-meta">
                            {{ $notification->created_at->diffForHumans() }} &middot; {{ ucfirst($notification->type) }}
                        </span>
                        <div class="notif-actions">
                            @if(!$notification->is_read)
                                <button wire:click="markRead({{ $notification->id }})" class="notif-btn mark">Mark read</button>
                            @else
                                <span class="notif-read-label">✓ Read</span>
                            @endif
                            <button
                                wire:click="deleteNotification({{ $notification->id }})"
                                wire:confirm="Delete this notification?"
                                class="notif-btn del"
                            >Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="notif-empty">
                <div class="notif-empty-icon">🔔</div>
                <p class="notif-empty-title">No notifications yet</p>
                <p class="notif-empty-sub">Admin notifications will appear here</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($this->getNotifications()->hasPages())
        <div class="notif-pagination">
            {{ $this->getNotifications()->links() }}
        </div>
    @endif
</div>
</x-filament-panels::page>