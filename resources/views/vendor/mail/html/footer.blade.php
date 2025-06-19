<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center" style="padding: 25px; font-size: 0.9em; color: #6b7280;">
{{ Illuminate\Mail\Markdown::parse($slot) }}
<p style="margin-top: 15px; font-size: 0.85em;">&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
</td>
</tr>
</table>
</td>
</tr>
