@extends('layouts.app')

@section('title', 'Feedback Geven - ' . $event->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Feedback Geven</h1>
                <p class="text-gray-600">{{ $event->title }}</p>
                <p class="text-sm text-gray-500">{{ $event->start_date->format('d M Y H:i') }}</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('feedback.store', $event) }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jouw Naam *
                    </label>
                    <input type="text" name="attendee_name" value="{{ old('attendee_name') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email Adres *
                    </label>
                    <input type="email" name="attendee_email" value="{{ old('attendee_email') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Algemene Beoordeling *
                    </label>
                    <div class="flex items-center space-x-4">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="flex items-center">
                                <input type="radio" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required
                                    class="mr-2">
                                <span class="text-2xl">{{ $i == 1 ? '😞' : ($i == 2 ? '😐' : ($i == 3 ? '😊' : ($i == 4 ? '😄' : '🤩'))) }}</span>
                                <span class="ml-1">{{ $i }}</span>
                            </label>
                        @endfor
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Categorieën (Meerdere keuzes mogelijk)
                    </label>
                    <div class="space-y-2">
                        @php
                            $categories = [
                                'organization' => 'Organisatie & Communicatie',
                                'venue' => 'Locatie & Faciliteiten',
                                'content' => 'Inhoud & Programma',
                                'value' => 'Prijs-Kwaliteit Verhouding',
                                'overall' => 'Algemene Ervaring'
                            ];
                        @endphp
                        
                        @foreach ($categories as $key => $label)
                            <label class="flex items-center">
                                <input type="checkbox" name="categories[]" value="{{ $key }}" 
                                    {{ in_array($key, old('categories', [])) ? 'checked' : '' }}
                                    class="mr-2">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jouw Feedback (Optioneel)
                    </label>
                    <textarea name="comment" rows="4" placeholder="Deel je ervaring, suggesties of opmerkingen..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('comment') }}</textarea>
                </div>

                <div class="flex justify-between items-center">
                    <a href="{{ route('events.show', $event) }}" 
                        class="text-blue-600 hover:text-blue-800">
                        ← Terug naar Event
                    </a>
                    <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Feedback Versturen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 