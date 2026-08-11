@php $_title = 'Rekap'; @endphp
@extends('layouts.app')

@section('content')
  <div class="box">
    <div class="box-header">
      <h3 class="text-blue-dark fw-semibold mb-2">Daftar Rekap</h3>

      <fieldset id="search-filter" class="border rounded-3 p-3">
        <form id="filter-form" class="row mt-2 search">
          {{-- No LP --}}
          <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
            <label class="form-label">Nomor LP</label>
            <input type="text" id="no_LP" class="form-control mt-1" name="no_lp" placeholder="Nomor LP">
          </div>

          {{-- Tanggal (dd-mm-yyyy) --}}
          <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
            <label class="form-label">Dari Tanggal</label>
            <input class="form-control" type="text" id="date_from" name="date_from" placeholder="Dari Tanggal (Tanggal Kejadian)" autocomplete="off">
          </div>
          <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
            <label class="form-label">Hingga Tanggal</label>
            <input class="form-control" type="text" id="date_to" name="date_to" placeholder="Hingga Tanggal (Tanggal Kejadian)" autocomplete="off">
          </div>

          {{-- Status --}}
          <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
            <label class="form-label">Status Selra</label>
            <select id="status" name="status" class="form-select">
              <option value="">Status</option>
              <option value="S0107">Dalam Proses</option>
              <option value="S0101">P21</option>
              <option value="S0102">SP3</option>
              <option value="S0103">Diversi</option>
              <option value="S0108">SP2LID</option>
              <option value="S0104">POM/TNI</option>
            </select>
          </div>

          {{-- Polda / Polres (dikunci sesuai akun) --}}
          @php
            $poldaLocked  = $locked['is_lock_polda']  ?? false;
            $polresLocked = $locked['is_lock_polres'] ?? false;
          @endphp

          <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
            <label class="form-label">Polda</label>
            @if($poldaLocked || $polresLocked)
              <input type="text" class="form-control" value="{{ $poldas->first()->name ?? '-' }}" disabled>
              <input type="hidden" id="polda" name="polda" value="{{ $poldas->first()->id ?? '' }}">
            @else
              <select id="polda" name="polda" class="form-select">
                <option value="">Semua Polda</option>
                @foreach ($poldas as $p)
                  <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
              </select>
            @endif
          </div>

          <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
            <label class="form-label">Polres</label>
            @if($polresLocked)
              <input type="text" class="form-control" value="{{ $polress->first()->name ?? '-' }}" disabled>
              <input type="hidden" id="polres" name="polres" value="{{ $polress->first()->id ?? '' }}">
            @elseif($poldaLocked)
              <select id="polres" name="polres" class="form-select">
                <option value="">--Pilih Polres--</option>
                @foreach ($polress as $pr)
                  <option value="{{ $pr->id }}">{{ $pr->name }}</option>
                @endforeach
              </select>
            @else
              <select id="polres" name="polres" class="form-select">
                <option value="">Pilih Polres</option>
                @foreach ($polress as $pr)
                  <option value="{{ $pr->id }}">{{ $pr->name }}</option>
                @endforeach
              </select>
            @endif
          </div>

          <div class="m-2 text-center">
            <button type="submit" id="btn-search" class="btn btn-dark-blue">Search</button>
            <button type="button" id="btn-reset" class="btn btn-secondary">Reset</button>
          </div>
        </form>
      </fieldset>

      {{-- Alert info khusus role 1 (pusat) saat belum ada filter cukup --}}
      @if(($roleId ?? 0) === 1)
        <div id="role1-info" class="alert alert-info mt-3" role="alert" style="display:block;">
          Untuk akun pusat, silakan isi minimal satu filter: <strong>Nomor LP (≥ 3 karakter)</strong>, atau <strong>Status</strong>, atau <strong>Polda/Polres</strong>, atau <strong>rentang tanggal lengkap</strong>, lalu klik <strong>Search</strong>.
        </div>
      @endif

      <div class="box-body">
        <div class="table-responsive mt-3">
          <table class="table table-bordered table-officer" width="100%" id="rekapTable">
            <thead>
              <tr>
                <th class="text-center" style="width:70px">No</th>
                <th class="text-center">No LP</th>
                <th class="text-center">Tanggal Kejadian</th>
                <th class="text-center">Tanggal Tindak Lanjut</th>
                <th class="text-center">Proses Selama</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

        {{-- Loading Overlay --}}
        <div id="loadingOverlay" class="loading-overlay" style="display:none;">
          <div class="loading-box">
            <div class="spinner-border" role="status" aria-hidden="true"></div>
            <div class="mt-2 fw-semibold">Memuat data…</div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('style')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"/>
  <style>
    .loading-overlay{ position:fixed; inset:0; background:rgba(255,255,255,.6);
      z-index:1050; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(1px);}
    .loading-box{ display:flex; flex-direction:column; align-items:center; background:#fff; border-radius:12px;
      padding:18px 22px; box-shadow:0 8px 24px rgba(0,0,0,.12); min-width:160px; }
    .spinner-border{ width:2.5rem; height:2.5rem; }
  </style>
@endpush

@push('script')
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

  <script>
    // Flags dari Blade
    const ROLE_ID          = {{ (int)($roleId ?? 0) }};
    const BLADE_LOCK_POLDA = {{ isset($locked['is_lock_polda'])  && $locked['is_lock_polda']  ? 'true' : 'false' }};
    const BLADE_LOCK_POLRES= {{ isset($locked['is_lock_polres']) && $locked['is_lock_polres'] ? 'true' : 'false' }};

    // Loading overlay
    function showLoading(){ document.getElementById('loadingOverlay').style.display = 'flex'; }
    function hideLoading(){ document.getElementById('loadingOverlay').style.display = 'none'; }

    // Datepicker
    function initDatepicker(){
      $('#date_from, #date_to').datepicker({
        format: 'dd-mm-yyyy', todayHighlight: true, autoclose: true, orientation: 'bottom auto'
      });
    }

    // dd-mm-yyyy -> yyyy-mm-dd
    function toYMD(v){ if(!v) return ''; const s=v.split('-'); return (s.length===3)? `${s[2]}-${s[1]}-${s[0]}` : ''; }

    function getFilters(){
      const poldaEl  = document.querySelector('#polda')  ?? document.querySelector('input[name="polda"]');
      const polresEl = document.querySelector('#polres') ?? document.querySelector('input[name="polres"]');
      return {
        no_lp:     $('#no_LP').val() || '',
        date_from: toYMD($('#date_from').val()),
        date_to:   toYMD($('#date_to').val()),
        status:    $('#status').val() || '',
        polda:     poldaEl  ? (poldaEl.value  || '') : '',
        polres:    polresEl ? (polresEl.value || '') : ''
      };
    }

    // Khusus Role 1: validasi filter minimal
    function role1HasSufficientFilters(){
      const f = getFilters();
      const hasNoLP   = f.no_lp && f.no_lp.trim().length >= 3;
      const hasStatus = !!f.status;
      const hasPolda  = !!f.polda;
      const hasPolres = !!f.polres;
      const hasDates  = !!f.date_from && !!f.date_to;
      return hasNoLP || hasStatus || hasPolda || hasPolres || hasDates;
    }

    let table;

    function buildUrl(){
      const qs = new URLSearchParams(Object.entries(getFilters()).filter(([_,v]) => v !== '')).toString();
      return "{{ route('rekap.api.list') }}" + (qs ? ('?' + qs) : '');
    }

    function initDataTable(urlAwal){
      table = $('#rekapTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: { url: urlAwal || buildUrl(), dataSrc: '' },
        deferRender: true,
        pageLength: 25,
        lengthMenu: [[25,50,100,-1],[25,50,100,'All']],
        columns: [
          { data: null, className:'text-center', render: (d,t,r,m) => m.row + 1 },
          { data: 'no_lp', className:'text-wrap' },
          { data: 'accident_date', className:'text-center' },
          { data: 'accident_tindak_lanjut', className:'text-center' },
          { data: 'accident_proses', className:'text-center' },
          { data: 'selra_flag', className:'text-center',
            render: function(val, type, row){
              const bg = ({
                S0101:'#00FF00', S0102:'#00FFFF', S0103:'#0000FF',
                S0104:'#FF00FF', S0106:'#FFFF00', S0107:'#EE354F', S0108:'#FF9900'
              })[row.selra] || '#FF9900';
              return `<span class="px-3 rounded font-weight-600 d-inline-block fw-bold" style="background-color:${bg};">${val || '-'}</span>`;
            }
          }
        ],
        dom: 'Bfrtip',
        buttons: [
          { extend: 'copyHtml5',  title: 'Daftar Rekap' },
          { extend: 'excelHtml5', title: 'Daftar Rekap', exportOptions: { columns: [1,2,3,4,5] } },
          { extend: 'pdfHtml5',   title: 'Daftar Rekap', exportOptions: { columns: [1,2,3,4,5] }, orientation:'landscape', pageSize:'A4' }
        ]
      });

      // Hook overlay
      $('#rekapTable')
        .on('preXhr.dt', function(){ showLoading(); })
        .on('xhr.dt',    function(){ hideLoading(); })
        .on('error.dt',  function(){ hideLoading(); });
    }

    function destroyTableIfAny(){
      if ($.fn.DataTable.isDataTable('#rekapTable')) {
        table.destroy();
        $('#rekapTable tbody').empty();
      }
    }

    function reloadTable(){
      // Khusus role 1: jangan load kalau filter belum cukup
      if (ROLE_ID === 1 && !role1HasSufficientFilters()){
        destroyTableIfAny();
        $('#role1-info').show();
        return;
      }
      $('#role1-info').hide();

      const url = buildUrl();
      showLoading();
      if ($.fn.DataTable.isDataTable('#rekapTable')) {
        table.ajax.url(url).load();
      } else {
        initDataTable(url);
      }
    }

    $(function(){
      initDatepicker();

      // Submit & Search
      $('#filter-form').on('submit', function(e){ e.preventDefault(); reloadTable(); });
      $('#btn-search').on('click', function(e){ e.preventDefault(); reloadTable(); });

      // Reset
      $('#btn-reset').on('click', function(e){
        e.preventDefault();
        $('#filter-form')[0].reset();
        if (!BLADE_LOCK_POLDA && !BLADE_LOCK_POLRES){
          $('#polres').empty().append('<option value="">Pilih Polres</option>');
        }
        $('#date_from, #date_to').datepicker('update','');
        reloadTable();
      });

      // Pusat saja: populate Polres saat Polda berubah
      if (!BLADE_LOCK_POLDA && !BLADE_LOCK_POLRES){
        $('#polda').on('change', function(){
          const poldaId = this.value;
          $('#polres').empty().append('<option value="">Pilih Polres</option>');
          if (!poldaId) return;
          showLoading();
          $.get('{{ url('pengguna/polres_list') }}/' + poldaId, function(data){
            data.forEach(it => $('#polres').append(`<option value="${it.id}">${it.name}</option>`));
          }).always(function(){ hideLoading(); });
        });
      }

      // Load awal:
      // - Role 1: JANGAN auto-load (biarkan kosong & tampilkan info)
      // - Role selain 1: auto-load seperti biasa
      if (ROLE_ID !== 1) {
        reloadTable();
      } else {
        destroyTableIfAny();
        $('#role1-info').show();
      }
    });
  </script>
@endpush
