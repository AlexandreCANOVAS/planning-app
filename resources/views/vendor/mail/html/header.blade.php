<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Planify')
                <table style="margin: 0 auto;">
                    <tr>
                        <td>
                            <div style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; margin-right: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                <span style="color: white; font-size: 24px; font-weight: bold;">P</span>
                            </div>
                        </td>
                        <td>
                            <span style="color: #4f46e5; font-size: 28px; font-weight: bold; vertical-align: middle;">{{ $slot }}</span>
                        </td>
                    </tr>
                </table>
            @elseif (trim($slot) === 'Laravel')
                @if (config('mail.brand.logo'))
                    <img src="{{ config('mail.brand.logo') }}" alt="{{ config('app.name') }}" style="max-height: 75px;">
                @else
                    {{ config('app.name') }}
                @endif
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr> 