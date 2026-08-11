<div class="form-group mb-4">
    <div class="form-check">
        @if (isset($document->is_legacy))
        <input class="form-check-input" type="checkbox" id="isLegacy" name="isLegacy"
            value="true" aria-label="..."
            @if ($document->is_legacy == true) {{ 'checked' }} @endif>
        @else
        <input class="form-check-input" type="checkbox" id="isLegacy" name="isLegacy"
            value="true" aria-label="..."
            @if (old('isLegacy') == 1) {{ 'checked' }} @endif>

        @endif
        <label for="isLegacy">
            <b>Tandai Dokumen Ini Sebagai Kasus Sudah Selesai</b> <span class="badge badge-primary">Legacy</span>
            <h5 class="text-danger" id="isLegacyLabel">(<b>Perhatian:</b> Dengan Mencentang Ini Dokumen Tidak Akan Terkirim Ke EMP, Kejaksaan dan SPPT-TI)</h5>
        </label>
    </div>
</div>