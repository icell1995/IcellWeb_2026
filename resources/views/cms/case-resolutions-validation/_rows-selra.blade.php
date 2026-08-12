@forelse($resolutions as $r)
  @php
    $isApproved = !empty($r->approved_at);
    $statusText = $isApproved ? 'Approved' : 'Pending';
    $badge = $isApproved ? 'success' : 'warning';
  @endphp
  <tr>
    <td class="text-center">{{ optional($r->accident)->no_lp ?? '-' }}</td>
    <td class="text-center">{{ $r->type_name ?? '-' }}</td>
    <td class="text-center">
      @if($r->file_url)
        <a href="{{ $r->file_url }}" target="_blank" class="btn btn-outline-secondary btn-sm">Lihat</a>
      @else
        <span class="text-muted">-</span>
      @endif
    </td>
    <td class="text-center">{{ optional($r->uploaded_at)->format('d-m-Y H:i') ?? '-' }}</td>
    <td class="text-center">{{ optional($r->date)->format('d-m-Y') ?? '-' }}</td>
    <td class="text-center">{{ $r->number ?? '-' }}</td>
    <td class="text-center"><span class="badge bg-{{ $badge }}">{{ $statusText }}</span></td>
    <td class="text-center">
      <a href="{{ route('cms.case-resolutions-validations.show', $r->id) }}" class="btn btn-primary btn-sm">
        <i class="bi bi-eye"></i> View
      </a>
    </td>
  </tr>
@empty
  <tr><td colspan="8" class="text-center text-muted">Tidak ada SELRA.</td></tr>
@endforelse
