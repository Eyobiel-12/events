<x-filament-panels::page>
    @if($this->selectedEvent)
        <div class="space-y-6">
            <!-- Event Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $this->selectedEvent->title }}</h2>
                        <p class="text-gray-600">{{ $this->selectedEvent->start_date->format('d M Y H:i') }} - {{ $this->selectedEvent->location }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Check-in Status</p>
                        <p class="text-lg font-semibold text-green-600" id="checkin-status">Ready</p>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            @php $stats = $this->getCheckinStats(); @endphp
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Tickets</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_tickets'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Checked In</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['checked_in'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Not Checked In</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['not_checked_in'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Check-in Rate</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['percentage'] }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- QR Scanner -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">QR Code Scanner</h3>
                        
                        <div class="mb-4">
                            <video id="qr-video" class="w-full h-64 bg-gray-100 rounded-lg" autoplay></video>
                        </div>

                        <div class="flex space-x-2">
                            <button id="start-camera" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Start Camera
                            </button>
                            <button id="stop-camera" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700" disabled>
                                Stop Camera
                            </button>
                        </div>

                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Of scan QR code hier:</p>
                            <input type="text" id="qr-input" placeholder="Plak QR code data hier..." 
                                   class="mt-2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <button id="scan-qr" class="mt-2 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                Scan QR Code
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Manual Entry -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Manual Entry</h3>
                        
                        <form id="manual-form" class="space-y-4">
                            <div>
                                <label for="ticket-id" class="block text-sm font-medium text-gray-700">Ticket ID / QR Code</label>
                                <input type="text" id="ticket-id" name="ticket_id" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Voer ticket ID of QR code in">
                            </div>
                            
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700">Locatie</label>
                                <select id="location" name="location" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="Main Entrance">Hoofdingang</option>
                                    <option value="Side Entrance">Zijingang</option>
                                    <option value="VIP Entrance">VIP Ingang</option>
                                    <option value="Back Entrance">Achteringang</option>
                                </select>
                            </div>

                            <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Check-in Ticket
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div id="results" class="bg-white rounded-lg shadow hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Check-in Resultaat</h3>
                    <div id="result-content"></div>
                </div>
            </div>

            <!-- Recent Check-ins -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Recente Check-ins</h3>
                    <div id="recent-checkins" class="space-y-2">
                        <p class="text-gray-500 text-sm">Geen recente check-ins</p>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script src="https://unpkg.com/html5-qrcode"></script>
        <script>
            let html5QrcodeScanner = null;
            let isScanning = false;

            // Camera controls
            document.getElementById('start-camera').addEventListener('click', startCamera);
            document.getElementById('stop-camera').addEventListener('click', stopCamera);
            document.getElementById('scan-qr').addEventListener('click', scanQrInput);
            document.getElementById('manual-form').addEventListener('submit', manualCheckin);

            function startCamera() {
                if (isScanning) return;

                const videoElement = document.getElementById('qr-video');
                
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "qr-video",
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    false
                );

                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                isScanning = true;
                
                document.getElementById('start-camera').disabled = true;
                document.getElementById('stop-camera').disabled = false;
                updateStatus('Scanning...', 'text-blue-600');
            }

            function stopCamera() {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear();
                    html5QrcodeScanner = null;
                }
                isScanning = false;
                
                document.getElementById('start-camera').disabled = false;
                document.getElementById('stop-camera').disabled = true;
                updateStatus('Ready', 'text-green-600');
            }

            function onScanSuccess(decodedText, decodedResult) {
                processQrCode(decodedText);
            }

            function onScanFailure(error) {
                // Handle scan failure silently
            }

            function scanQrInput() {
                const qrInput = document.getElementById('qr-input').value;
                if (qrInput.trim()) {
                    processQrCode(qrInput);
                }
            }

            function processQrCode(qrData) {
                updateStatus('Processing...', 'text-yellow-600');
                
                fetch('{{ route("qr-checkin.scan", $this->selectedEvent) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        qr_data: qrData,
                        location: document.getElementById('location').value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    showResult(data);
                    if (data.success) {
                        updateStatus('Success!', 'text-green-600');
                        addRecentCheckin(data.ticket);
                        // Refresh stats
                        window.location.reload();
                    } else {
                        updateStatus('Failed', 'text-red-600');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    updateStatus('Error', 'text-red-600');
                });
            }

            function manualCheckin(e) {
                e.preventDefault();
                
                const formData = new FormData(e.target);
                updateStatus('Processing...', 'text-yellow-600');
                
                fetch('{{ route("qr-checkin.manual", $this->selectedEvent) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    showResult(data);
                    if (data.success) {
                        updateStatus('Success!', 'text-green-600');
                        addRecentCheckin(data.ticket);
                        e.target.reset();
                        // Refresh stats
                        window.location.reload();
                    } else {
                        updateStatus('Failed', 'text-red-600');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    updateStatus('Error', 'text-red-600');
                });
            }

            function showResult(data) {
                const resultsDiv = document.getElementById('results');
                const contentDiv = document.getElementById('result-content');
                
                let html = '';
                
                if (data.success) {
                    html = `
                        <div class="bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">Check-in Succesvol!</h3>
                                    <div class="mt-2 text-sm text-green-700">
                                        <p><strong>Bezoeker:</strong> ${data.ticket.attendee_name}</p>
                                        <p><strong>Email:</strong> ${data.ticket.attendee_email}</p>
                                        <p><strong>Ticket Type:</strong> ${data.ticket.ticket_type}</p>
                                        <p><strong>Check-in Tijd:</strong> ${data.ticket.checked_in_at}</p>
                                        <p><strong>Door:</strong> ${data.ticket.checked_in_by}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    let errorType = '';
                    switch(data.type) {
                        case 'not_found':
                            errorType = 'Ticket niet gevonden';
                            break;
                        case 'not_paid':
                            errorType = 'Ticket niet betaald';
                            break;
                        case 'already_used':
                            errorType = 'Ticket al gebruikt';
                            break;
                        default:
                            errorType = 'Fout opgetreden';
                    }
                    
                    html = `
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Check-in Mislukt</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>${data.message}</p>
                                        ${data.ticket ? `
                                            <div class="mt-2 p-2 bg-red-100 rounded">
                                                <p><strong>Laatste Check-in:</strong> ${data.ticket.checked_in_at}</p>
                                                <p><strong>Door:</strong> ${data.ticket.checked_in_by}</p>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                contentDiv.innerHTML = html;
                resultsDiv.classList.remove('hidden');
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    resultsDiv.classList.add('hidden');
                }, 5000);
            }

            function updateStatus(status, colorClass) {
                const statusElement = document.getElementById('checkin-status');
                statusElement.textContent = status;
                statusElement.className = `text-lg font-semibold ${colorClass}`;
            }

            function addRecentCheckin(ticket) {
                const recentDiv = document.getElementById('recent-checkins');
                const checkinHtml = `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium">${ticket.attendee_name}</p>
                            <p class="text-sm text-gray-600">${ticket.ticket_type}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">${ticket.checked_in_at}</p>
                            <p class="text-xs text-gray-400">${ticket.checked_in_by}</p>
                        </div>
                    </div>
                `;
                
                // Remove "no check-ins" message if present
                const noCheckins = recentDiv.querySelector('p');
                if (noCheckins && noCheckins.textContent.includes('Geen recente')) {
                    noCheckins.remove();
                }
                
                // Add new check-in at the top
                recentDiv.insertAdjacentHTML('afterbegin', checkinHtml);
                
                // Keep only last 5 check-ins
                const checkins = recentDiv.querySelectorAll('div');
                if (checkins.length > 5) {
                    checkins[checkins.length - 1].remove();
                }
            }

            // Auto-start camera when page loads
            window.addEventListener('load', () => {
                setTimeout(startCamera, 1000);
            });
        </script>
        @endpush
    @else
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Geen events beschikbaar</h3>
            <p class="mt-1 text-sm text-gray-500">Er zijn geen gepubliceerde events voor QR check-in.</p>
        </div>
    @endif
</x-filament-panels::page> 