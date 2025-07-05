<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $event->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Event header -->
                <div class="relative">
                    @if($event->image)
                        <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="w-full h-64 object-cover">
                    @else
                        <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-500">Geen afbeelding</span>
                        </div>
                    @endif
                    
                    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                        <h1 class="text-3xl font-bold mb-2">{{ $event->title }}</h1>
                        <div class="flex items-center space-x-4 text-sm">
                            <span>{{ $event->start_date->format('d M Y H:i') }}</span>
                            <span>{{ $event->location }}, {{ $event->city }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Event details -->
                        <div class="lg:col-span-2">
                            <h3 class="text-xl font-semibold mb-4">Over dit event</h3>
                            <div class="prose max-w-none mb-6">
                                {!! nl2br(e($event->description)) !!}
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold mb-2">Datum & Tijd</h4>
                                    <p>{{ $event->start_date->format('d M Y H:i') }}</p>
                                    @if($event->end_date)
                                        <p class="text-sm text-gray-600">tot {{ $event->end_date->format('d M Y H:i') }}</p>
                                    @endif
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold mb-2">Locatie</h4>
                                    <p>{{ $event->location }}</p>
                                    <p class="text-sm text-gray-600">{{ $event->city }}, {{ $event->country }}</p>
                                </div>
                            </div>

                            @if($event->organisation)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold mb-2">Organisatie</h4>
                                    <p>{{ $event->organisation->name }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Ticketverkoop -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 p-6 rounded-lg sticky top-6">
                                <h3 class="text-xl font-semibold mb-4">Tickets</h3>
                                
                                @if($event->ticketTypes->count() > 0)
                                    <form method="POST" action="{{ route('tickets.store', $event) }}" class="space-y-4">
                                        @csrf
                                        
                                        <!-- Ticket type selectie -->
                                        <div>
                                            <label for="ticket_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                                                Ticket Type
                                            </label>
                                            @foreach($event->ticketTypes as $ticketType)
                                                <div class="border rounded-lg p-4 mb-3 {{ old('ticket_type_id') == $ticketType->id ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                                                    <label class="flex items-start">
                                                        <input type="radio" name="ticket_type_id" value="{{ $ticketType->id }}" 
                                                               {{ old('ticket_type_id') == $ticketType->id ? 'checked' : '' }}
                                                               class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                                        <div class="ml-3 flex-1">
                                                            <div class="flex justify-between items-start">
                                                                <div>
                                                                    <p class="font-medium">{{ $ticketType->name }}</p>
                                                                    <p class="text-sm text-gray-600">{{ $ticketType->description }}</p>
                                                                </div>
                                                                <p class="font-semibold text-lg">€{{ number_format($ticketType->price, 2) }}</p>
                                                            </div>
                                                            <p class="text-xs text-gray-500 mt-1">
                                                                {{ $ticketType->quantity - $ticketType->sold_quantity }} van {{ $ticketType->quantity }} beschikbaar
                                                            </p>
                                                        </div>
                                                    </label>
                                                </div>
                                            @endforeach
                                            @error('ticket_type_id')
                                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Aantal tickets -->
                                        <div>
                                            <label for="quantity" class="block text-sm font-medium text-gray-700">Aantal tickets</label>
                                            <select name="quantity" id="quantity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                @for($i = 1; $i <= 10; $i++)
                                                    <option value="{{ $i }}" {{ old('quantity') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                            @error('quantity')
                                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Bezoeker informatie -->
                                        <div>
                                            <label for="attendee_name" class="block text-sm font-medium text-gray-700">Naam bezoeker</label>
                                            <input type="text" name="attendee_name" id="attendee_name" value="{{ old('attendee_name') }}"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                   required>
                                            @error('attendee_name')
                                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="attendee_email" class="block text-sm font-medium text-gray-700">Email bezoeker</label>
                                            <input type="email" name="attendee_email" id="attendee_email" value="{{ old('attendee_email') }}"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                   required>
                                            @error('attendee_email')
                                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="attendee_phone" class="block text-sm font-medium text-gray-700">Telefoon bezoeker (optioneel)</label>
                                            <input type="tel" name="attendee_phone" id="attendee_phone" value="{{ old('attendee_phone') }}"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            @error('attendee_phone')
                                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        @error('error')
                                            <div class="bg-red-50 border border-red-200 rounded-md p-3">
                                                <p class="text-red-600 text-sm">{{ $message }}</p>
                                            </div>
                                        @enderror

                                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-3 rounded-md hover:bg-indigo-700 transition-colors font-medium">
                                            Tickets Kopen
                                        </button>
                                    </form>
                                @else
                                    <div class="text-center py-8">
                                        <p class="text-gray-500">Geen tickets beschikbaar voor dit event.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 