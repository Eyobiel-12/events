<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket - {{ $ticket->ticketType->event->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .ticket {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .ticket-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-group {
            margin-bottom: 15px;
        }
        .info-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            color: #1f2937;
            font-weight: 500;
        }
        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            border: 2px dashed #d1d5db;
            border-radius: 10px;
        }
        .qr-code {
            width: 150px;
            height: 150px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
        .qr-text {
            font-family: monospace;
            font-size: 12px;
            color: #6b7280;
        }
        .footer {
            background: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        .status-paid {
            background: #dcfce7;
            color: #166534;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <h1>{{ $ticket->ticketType->event->title }}</h1>
            <p>Event Ticket</p>
        </div>
        
        <div class="content">
            <div class="status-paid">✓ BETAALD</div>
            
            <div class="ticket-info">
                <div class="info-group">
                    <div class="info-label">Event</div>
                    <div class="info-value">{{ $ticket->ticketType->event->title }}</div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Ticket Type</div>
                    <div class="info-value">{{ $ticket->ticketType->name }}</div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Datum & Tijd</div>
                    <div class="info-value">{{ $ticket->ticketType->event->start_date->format('d M Y H:i') }}</div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Locatie</div>
                    <div class="info-value">{{ $ticket->ticketType->event->location }}</div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Stad</div>
                    <div class="info-value">{{ $ticket->ticketType->event->city }}, {{ $ticket->ticketType->event->country }}</div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Bezoeker</div>
                    <div class="info-value">{{ $ticket->attendee_name }}</div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $ticket->attendee_email }}</div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Ticket ID</div>
                    <div class="info-value">{{ $ticket->qr_code }}</div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Bedrag</div>
                    <div class="info-value">€{{ number_format($ticket->amount_paid, 2) }}</div>
                </div>
            </div>
            
            <div class="qr-section">
                <div class="qr-code">
                    {!! QrCode::format('svg')->size(150)->margin(5)->generate($ticket->qr_code) !!}
                </div>
                <p>Toon deze QR code bij de ingang</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Dit ticket is geldig voor één persoon op de aangegeven datum en tijd.</p>
            <p>Gegenereerd op {{ now()->format('d M Y H:i') }}</p>
        </div>
    </div>
</body>
</html> 