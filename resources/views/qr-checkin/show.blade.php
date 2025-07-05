<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            QR Check-in - {{ $event->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Event Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $event->title }}</h3>
                            <p class="text-gray-600">{{ $event->start_date->format('d M Y H:i') }} - {{ $event->location }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Check-in Status</p>
                            <p class="text-lg font-semibold text-green-600" id="checkin-status">Ready</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- QR Scanner -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
            <div id="results" class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Check-in Resultaat</h3>
                    <div id="result-content"></div>
                </div>
            </div>

            <!-- Recent Check-ins -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Recente Check-ins</h3>
                    <div id="recent-checkins" class="space-y-2">
                        <p class="text-gray-500 text-sm">Geen recente check-ins</p>
                    </div>
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
            
            fetch('{{ route("qr-checkin.scan", $event) }}', {
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
            
            fetch('{{ route("qr-checkin.manual", $event) }}', {
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
</x-app-layout> 