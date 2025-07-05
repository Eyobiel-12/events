<x-filament-panels::page>
    <div class="space-y-12">
        <!-- Toggle platform/organisator -->
        <div class="flex justify-end mb-4">
            <form method="GET" class="flex gap-2">
                <button type="submit" name="scope" value="platform" class="px-4 py-1 rounded-lg text-sm font-semibold transition {{ $scope === 'platform' ? 'bg-blue-700 text-white' : 'bg-gray-800 text-gray-300' }}">Platform</button>
                <button type="submit" name="scope" value="organisator" class="px-4 py-1 rounded-lg text-sm font-semibold transition {{ $scope === 'organisator' ? 'bg-blue-700 text-white' : 'bg-gray-800 text-gray-300' }}">Organisator</button>
            </form>
        </div>

        <!-- Snel Actie Panel -->
        <div class="flex flex-wrap gap-4 justify-end mb-4">
            <a href="/admin/organisations/create" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-800/80 text-white font-semibold shadow-lg hover:shadow-blue-700/30 hover:scale-105 transition-all duration-150">
                <x-heroicon-o-building-office class="w-5 h-5" /> Nieuwe Organisatie
            </a>
            <a href="/admin/events/create" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-green-800/80 text-white font-semibold shadow-lg hover:shadow-green-700/30 hover:scale-105 transition-all duration-150">
                <x-heroicon-o-calendar class="w-5 h-5" /> Nieuw Event
            </a>
            <a href="/admin/users/create" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-purple-800/80 text-white font-semibold shadow-lg hover:shadow-purple-700/30 hover:scale-105 transition-all duration-150">
                <x-heroicon-o-user-plus class="w-5 h-5" /> Nieuwe Gebruiker
            </a>
        </div>

        <!-- Statistieken grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Organisaties -->
            <div class="bg-gray-900/70 backdrop-blur-md ring-1 ring-white/10 rounded-2xl p-6 flex flex-col hover:shadow-xl hover:shadow-blue-700/30 hover:scale-[1.02] hover:ring-2 hover:ring-blue-500/40 transition-all duration-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xl">🏢</span>
                    <span class="text-sm text-muted-foreground">Organisaties</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-extrabold">{{ $stats['organisations'] ?? '-' }}</span>
                </div>
            </div>
            <!-- Events -->
            <div class="bg-gray-900/70 backdrop-blur-md ring-1 ring-white/10 rounded-2xl p-6 flex flex-col hover:shadow-xl hover:shadow-blue-700/30 hover:scale-[1.02] hover:ring-2 hover:ring-blue-500/40 transition-all duration-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xl">📅</span>
                    <span class="text-sm text-muted-foreground">Events</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-extrabold">{{ $stats['events'] ?? '-' }}</span>
                </div>
            </div>
            <!-- Verkochte Tickets -->
            <div class="bg-gray-900/70 backdrop-blur-md ring-1 ring-white/10 rounded-2xl p-6 flex flex-col hover:shadow-xl hover:shadow-blue-700/30 hover:scale-[1.02] hover:ring-2 hover:ring-blue-500/40 transition-all duration-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xl">🎟️</span>
                    <span class="text-sm text-muted-foreground">Verkochte Tickets</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-extrabold">{{ $stats['tickets'] ?? '-' }}</span>
                </div>
            </div>
            <!-- Totale Omzet -->
            <div class="bg-gray-900/70 backdrop-blur-md ring-1 ring-white/10 rounded-2xl p-6 flex flex-col hover:shadow-xl hover:shadow-blue-700/30 hover:scale-[1.02] hover:ring-2 hover:ring-blue-500/40 transition-all duration-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xl">💶</span>
                    <span class="text-sm text-muted-foreground">Totale Omzet</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-extrabold">€ {{ number_format($stats['revenue'] ?? 0, 2) }}</span>
                    @if(!is_null($trends['revenue']))
                        @if($trends['revenue'] > 0)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-900/80 text-green-300 animate-pulse"><x-heroicon-m-arrow-trending-up class="w-4 h-4" />{{ $trends['revenue'] }}%</span>
                        @elseif($trends['revenue'] < 0)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-900/80 text-red-300 animate-pulse"><x-heroicon-m-arrow-trending-down class="w-4 h-4" />{{ $trends['revenue'] }}%</span>
                        @else
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-800/80 text-gray-300">0%</span>
                        @endif
                    @endif
                </div>
            </div>
            <!-- Feedbackscore -->
            <div class="bg-gray-900/70 backdrop-blur-md ring-1 ring-white/10 rounded-2xl p-6 flex flex-col hover:shadow-xl hover:shadow-blue-700/30 hover:scale-[1.02] hover:ring-2 hover:ring-blue-500/40 transition-all duration-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xl">⭐</span>
                    <span class="text-sm text-muted-foreground">Feedbackscore</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-extrabold">{{ number_format($stats['feedback_score'] ?? 0, 1) }}/5</span>
                </div>
            </div>
            <!-- Actieve Gebruikers -->
            <div class="bg-gray-900/70 backdrop-blur-md ring-1 ring-white/10 rounded-2xl p-6 flex flex-col hover:shadow-xl hover:shadow-blue-700/30 hover:scale-[1.02] hover:ring-2 hover:ring-blue-500/40 transition-all duration-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xl">👤</span>
                    <span class="text-sm text-muted-foreground">Actieve Gebruikers</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-extrabold">{{ $stats['users'] ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Omzetgrafiek -->
        <div class="bg-gray-900/70 backdrop-blur-md ring-1 ring-white/10 rounded-2xl p-8 flex flex-col col-span-3 mt-8 hover:shadow-xl hover:shadow-blue-700/30 hover:scale-[1.01] hover:ring-2 hover:ring-blue-500/40 transition-all duration-200">
            <h3 class="flex items-center gap-2 text-base text-muted-foreground mb-4">
                <span class="text-xl">📊</span>
                Omzet (Laatste 12 maanden)
            </h3>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Top Events -->
        <div class="bg-gray-900/70 backdrop-blur-md ring-1 ring-white/10 rounded-2xl p-8 flex flex-col col-span-3 mt-8 hover:shadow-xl hover:shadow-blue-700/30 hover:scale-[1.01] hover:ring-2 hover:ring-blue-500/40 transition-all duration-200">
            <h3 class="flex items-center gap-2 text-base text-muted-foreground mb-4">
                <span class="text-xl">📈</span>
                Top Events
            </h3>
            <ul class="divide-y divide-gray-800">
                @foreach($topEvents as $i => $event)
                    <li class="py-3 flex items-center justify-between">
                        <span class="truncate font-semibold">{{ $i+1 }}. {{ $event->title }} {{ $i === 0 ? '✨' : ($i === 1 ? '🔥' : '') }}</span>
                        <span class="font-bold text-lg">{{ $event->tickets_count }} tickets</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartData = @json($chartData);
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: Object.keys(chartData.monthly_revenue),
                datasets: [{
                    label: 'Omzet (€)',
                    data: Object.values(chartData.monthly_revenue),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '€ ' + context.parsed.y.toLocaleString('nl-NL', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '€ ' + value;
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-filament-panels::page> 