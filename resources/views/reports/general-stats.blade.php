<div class="p-8 bg-white font-sans">
    <div class="mb-8 border-b-2 border-gray-200 pb-4 text-center">
        <div class="mb-4">
            @php
                $imagePath = public_path('storage/pdf/cintillo.png');
                $base64Image = null;
                if (file_exists($imagePath)) {
                    $imageData = base64_encode(file_get_contents($imagePath));
                    $base64Image = 'data:image/jpeg;base64,' . $imageData;
                }
            @endphp

            @if($base64Image)
                <img src="{{ $base64Image }}" style="width: 200%; max-height: 200px; object-fit: contain;">
            @endif
        </div>
        
        <h1 style="font-size: 24px; font-weight: bold; text-transform: uppercase;">{{ $data['titulo'] }}</h1>
        
        <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 12px; color: #4b5563;">
            <span><strong>Filtrado:</strong> {{ $data['periodo'] }}</span><br>
            <span><strong>Emisión:</strong> {{ $data['fecha_generacion']->format('d/m/Y | g:i A') }}</span>
        </div>
    </div>

    <div style="background-color: #f3f4f6; padding: 10px; margin-bottom: 20px; border-radius: 8px;">
        <strong>Total registros:</strong> {{ $data['total_registros'] }}
    </div>

    <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
        <thead>
            <tr style="color: black;">
                <th style="padding: 8px; text-align: left;">Cédula</th>
                <th style="padding: 8px; text-align: left;">Personal</th>
                <th style="padding: 8px; text-align: left;">Ubicación</th>
                <th style="padding: 8px; text-align: center;">Fecha</th>
                <th style="padding: 8px; text-align: center;">Hora</th>
                <th style="padding: 8px; text-align: center;">Tipo</th>
                <th style="padding: 8px; text-align: left;">Observación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['registros'] as $attendance)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 8px;">{{ $attendance->personal?->document }}</td>
                    <td style="padding: 8px;">{{ strtoupper($attendance->personal?->name . ' ' . $attendance->personal?->last_name) }}</td>
                    <td style="padding: 8px; font-style: italic;">{{ $attendance->personal?->nominalLocation?->name }}</td>
                    <td style="padding: 8px; text-align: center;">{{ \Carbon\Carbon::parse($attendance->day)->format('d/m/Y') }}</td>
                    <td style="padding: 8px; text-align: center; font-weight: bold;">{{ \Carbon\Carbon::parse($attendance->hour)->format('g:i A') }}</td>
                    <td style="padding: 8px; text-align: center;">{{ $attendance->record_type }}</td>
                    <td style="padding: 8px; color: #6b7280;">{{ $attendance->observation ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>