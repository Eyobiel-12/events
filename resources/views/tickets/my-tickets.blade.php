<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mijn Tickets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($tickets->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($tickets as $ticket)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <!-- Status badge -->
                                <div class="mb-4">
                                    @if($ticket->status === 'paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Betaald
                                        </span>
                                    @elseif($ticket->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            In behandeling
                                        </span>
                                    @elseif($ticket->status === 'failed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Mislukt
                                        </span>
                                    @elseif($ticket->status === 'expired')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Verlopen
                                        </span>
                                    @endif
                                </div>

                                <!-- Event informatie -->
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    {{ $ticket->ticketType->event->title }}
                                </h3>
                                
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $ticket->ticketType->event->start_date->format('d M Y H:i') }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $ticket->ticketType->event->location }}, {{ $ticket->ticketType->event->city }}
                                    </div>
                                </div>

                                <!-- Ticket details -->
                                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Ticket Type:</span>
                                            <span class="font-medium">{{ $ticket->ticketType->name }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Bezoeker:</span>
                                            <span class="font-medium">{{ $ticket->attendee_name }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Ticket ID:</span>
                                            <span class="font-mono text-xs">{{ $ticket->qr_code }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Bedrag:</span>
                                            <span class="font-medium">€{{ number_format($ticket->amount_paid, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- QR Code preview -->
                                @if($ticket->status === 'paid')
                                    <div class="mb-4 text-center">
                                        <div class="bg-white p-2 border rounded-lg inline-block">
                                            <div class="w-24 h-24 bg-gray-100 flex items-center justify-center">
                                                <span class="text-gray-500 text-xs text-center">
                                                    QR<br>{{ substr($ticket->qr_code, -8) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Acties -->
                                <div class="flex space-x-2">
                                    @if($ticket->status === 'paid')
                                        <a href="{{ route('tickets.download', $ticket) }}" 
                                           class="flex-1 bg-indigo-600 text-white px-3 py-2 rounded-md hover:bg-indigo-700 transition-colors text-center text-sm">
                                            Download
                                        </a>
                                    @elseif($ticket->status === 'pending')
                                        <a href="{{ route('checkout.show', $ticket) }}" 
                                           class="flex-1 bg-green-600 text-white px-3 py-2 rounded-md hover:bg-green-700 transition-colors text-center text-sm">
                                            Betalen
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('events.show', $ticket->ticketType->event) }}" 
                                       class="flex-1 bg-gray-600 text-white px-3 py-2 rounded-md hover:bg-gray-700 transition-colors text-center text-sm">
                                        Event Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $tickets->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Geen tickets</h3>
                        <p class="mt-1 text-sm text-gray-500">Je hebt nog geen tickets gekocht.</p>
                        <div class="mt-6">
                            <a href="{{ route('events.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Bekijk Events
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout> 