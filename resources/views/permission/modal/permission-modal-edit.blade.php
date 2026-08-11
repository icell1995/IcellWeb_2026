<div id="edit-data" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content light-blue">
            @if ($errors->any())
                <div class="text-center alert alert-danger" role="alert">
                    <ul class="m-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="modal-header border-0">
                <h4 class="m-0 fw-semibold text-blue-dark">Ubah Hak Akses</h4>
            </div>

            <form method="POST" action="{{ route('permission_edit') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group" hidden>
                        <input id="id_edit" type="text" class="form-control input-form @error('id_edit') is-invalid @enderror"
                            name="id_edit" required autocomplete="id_edit" placeholder="ID*">
                    </div>
                    <div class="form-group">
                        <input id="name_edit" type="text"
                            class="form-control input-form @error('name_edit') is-invalid @enderror" name="name_edit"
                            value="{{ old('name_edit') }}" required autocomplete="name_edit" autofocus
                            placeholder="Nama Permisiion">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-dark-blue">
                        {{ __('Simpan') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
