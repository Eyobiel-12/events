<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Welkom, {{ auth()->user()->name }}!</h1>
                        <p class="text-gray-600">Beheer je events en tickets</p>
                    </div>

                    <!-- User Info -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Jouw Account</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Naam</p>
                                <p class="text-lg text-gray-900">{{ auth()->user()->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Email</p>
                                <p class="text-lg text-gray-900">{{ auth()->user()->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Rol</p>
                                <p class="text-lg text-gray-900">
                                    @if(auth()->user()->hasRole('admin'))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Admin
                                        </span>
                                    @elseif(auth()->user()->hasRole('organizer'))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Organisator
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Gebruiker
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Lid sinds</p>
                                <p class="text-lg text-gray-900">{{ auth()->user()->created_at->format('d-m-Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Events</h3>
                                    <p class="text-gray-600">Bekijk alle beschikbare events</p>
                                    <a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Bekijk events →
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Mijn Tickets</h3>
                                    <p class="text-gray-600">Beheer je gekochte tickets</p>
                                    <a href="{{ route('tickets.my-tickets') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                        Bekijk tickets →
                                    </a>
                                </div>
                            </div>
                        </div>

                        @if(auth()->user()->hasRole('organizer'))
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Organisator Dashboard</h3>
                                    <p class="text-gray-600">Beheer je events en statistieken</p>
                                    <a href="{{ route('dashboard.index') }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                        Ga naar dashboard →
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(auth()->user()->hasRole('admin'))
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-red-100 text-red-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Admin Panel</h3>
                                    <p class="text-gray-600">Beheer het hele systeem</p>
                                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        Ga naar admin →
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Recente Activiteiten</h2>
                        <div class="space-y-4">
                            @php
                                $recentTickets = auth()->user()->tickets()->with('ticketType.event')->latest()->take(5)->get();
                            @endphp
                            
                            @forelse($recentTickets as $ticket)
                                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                    <div class="p-2 rounded-full bg-green-100 text-green-600 mr-4">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Ticket gekocht voor {{ $ticket->ticketType->event->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $ticket->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="text-sm text-gray-500">€{{ number_format($ticket->amount_paid, 2) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-8">Nog geen activiteiten</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
