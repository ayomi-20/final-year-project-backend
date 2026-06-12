<div style="padding: 16px;">
    @if ($provider->logo)
        <div style="margin-bottom: 24px;">
            <p style="font-weight: bold; margin-bottom: 8px; color: #0F3B2E;">Business Logo</p>
            <img src="{{ asset('storage/' . $provider->logo) }}"
                 style="max-width: 200px; border-radius: 8px; border: 1px solid #ddd;">
        </div>
    @endif

    @if ($provider->national_id)
        <div style="margin-bottom: 24px;">
            <p style="font-weight: bold; margin-bottom: 8px; color: #0F3B2E;">National ID</p>
            @php $ext = pathinfo($provider->national_id, PATHINFO_EXTENSION); @endphp
            @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
                <img src="{{ asset('storage/' . $provider->national_id) }}"
                     style="max-width: 400px; border-radius: 8px; border: 1px solid #ddd;">
            @else
                <a href="{{ asset('storage/' . $provider->national_id) }}"
                   target="_blank"
                   style="color: #0F3B2E; text-decoration: underline;">
                   📄 View PDF
                </a>
            @endif
        </div>
    @endif

    @if ($provider->trading_license)
        <div style="margin-bottom: 24px;">
            <p style="font-weight: bold; margin-bottom: 8px; color: #0F3B2E;">Trading License</p>
            @php $ext = pathinfo($provider->trading_license, PATHINFO_EXTENSION); @endphp
            @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
                <img src="{{ asset('storage/' . $provider->trading_license) }}"
                     style="max-width: 400px; border-radius: 8px; border: 1px solid #ddd;">
            @else
                <a href="{{ asset('storage/' . $provider->trading_license) }}"
                   target="_blank"
                   style="color: #0F3B2E; text-decoration: underline;">
                   📄 View PDF
                </a>
            @endif
        </div>
    @endif

    @if (!$provider->logo && !$provider->national_id && !$provider->trading_license)
        <p style="color: #999;">No documents uploaded yet.</p>
    @endif
</div>