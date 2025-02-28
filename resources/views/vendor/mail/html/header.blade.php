<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
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