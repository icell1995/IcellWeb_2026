<div id="myModalP21Tahap1" name="myModalP21Tahap1" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
    <div class="modal-content ">
            <div class="modal-header">
                <h3 class="modal-title">Surat Pengiriman Berkas Perkara Tahap 1</h3>
            </div>
            <form action="{{route('add_surat_p21_tahap_1')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input id="accident_id_p21_tahap_1" name="accident_id_p21_tahap_1" type="text" value="{{$id}}" hidden>
                <div class="modal-body text-white" >
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="province">Provinsi:</label>
                            <input type="text" class="form-control" id="province" name="province" placeholder="Enter province" required readonly value="{{$surat_p21_province}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="polres">Polres:</label>
                            <input type="text" class="form-control" id="polres" name="polres" placeholder="Enter polres" required readonly value="{{$surat_p21_polres}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="polres_address">Alamat Polres:</label>
                            <input type="text" class="form-control" id="polres_address" name="polres_address" placeholder="Enter polres address" required value="{{$surat_p21_polres_address}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="no_p21">No P21:</label>
                            <input type="text" class="form-control" id="no_p21" name="no_p21" placeholder="Enter P21 number" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="p21_date">Tanggal P21:</label>
                            <input type="date" class="form-control" id="p21_date" name="p21_date" required value="{{date('Y-m-d')}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="p21_location">Tempat P21:</label>
                            <input type="text" class="form-control" id="p21_location" name="p21_location" placeholder="Enter Tempat P21" required value="{{$surat_p21_place}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="classification">Klasifikasi:</label>
                            <input type="text" class="form-control" id="classification" name="classification" placeholder="Enter Klasifikasi" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="attachment">Lampiran:</label>
                            <input type="text" class="form-control" id="attachment" name="attachment" placeholder="Enter Lampiran" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="subject">Perihal:</label>
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter Perihal" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="letter_recipient">Penerima Surat:</label>
                            <input type="text" class="form-control" id="letter_recipient" name="letter_recipient" placeholder="Enter Penerima Surat" required value="{{$surat_p21_letter_recepient}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="recipient_location">Tempat Penerima Surat:</label>
                            <input type="text" class="form-control" id="recipient_location" name="recipient_location" placeholder="Enter Tempat Penerima Surat" required value="{{$surat_p21_letter_recepient_place}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="no_spdp">No SPDP:</label>
                            <input type="text" class="form-control" id="no_spdp" name="no_spdp" required value="{{$surat_p21_no_spdp}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="spdp_date">Tanggal SPDP:</label>
                            <input type="date" class="form-control" id="spdp_date" name="spdp_date" required value="{{$surat_p21_spdp_date}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="no_lp">No LP:</label>
                            <input type="text" class="form-control" id="no_lp" name="no_lp" placeholder="Enter No LP" readonly required value="{{$surat_p21_no_lp}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="accident_date">Tanggal Kejadian:</label>
                            <input type="date" class="form-control" id="accident_date" name="accident_date" readonly required value="{{$surat_p21_accident_date}}">
                        </div>
                    </div>
                    {{--<div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="offense_articles">Pasal Yang Dilanggar:</label>
                            <input type="text" class="form-control" id="offense_articles" name="offense_articles" placeholder="Enter Pasal Yang Dilanggar" required>
                        </div>
                    </div>--}}
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="suspects">Tersangka:</label>
                            <button type="button" id="add-tersangka-p21-tahap-1" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addSuspectModal">+</button>
                            <select class="form-control select2" id="create-suspects-p21-tahap-1" name="suspects[]">
                                <!-- <option value="" disabled>--Pilih Tersangka--</option> -->
                                @foreach($surat_p21_suspects as $row)
                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="incident_description">Deskripsi Kejadian:</label>
                            <textarea class="form-control" id="incident_description" rows="5" name="incident_description" placeholder="Enter Deskripsi Kejadian" required>{{$surat_p21_description}}</textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12" id="cc-container-p21-tahap-1">
                            <label for="cc">Tembusan:</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="cc" name="cc[]" placeholder="Enter Tembusan">
                                <button type="button" class="btn btn-secondary add-cc-p21-tahap-1">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="penyidik_name">Nama Penyidik:</label>
                            <input type="text" class="form-control" id="penyidik_name" name="penyidik_name" placeholder="Enter Nama Penyidik" required value="{{$surat_p21_penyidik_name}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="penyidik_position">Jabatan Penyidik:</label>
                            <input type="text" class="form-control" id="penyidik_position" name="penyidik_position" placeholder="Enter Jabatan Penyidik" required value="{{$surat_p21_penyidik_position}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="penyidik_nrp">NRP Penyidik:</label>
                            <input type="text" class="form-control" id="penyidik_nrp" name="penyidik_nrp" placeholder="Enter NRP Penyidik" required value="{{$surat_p21_penyidik_nrp}}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary">
                            {{ __('Submit') }}
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myEditModalP21Tahap1" name="myEditModalP21Tahap1" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
    <div class="modal-content ">
            <div class="modal-header">
                <h3 class="modal-title">Edit Surat Pengiriman Berkas Perkara Tahap 1</h3>
            </div>
            <form action="{{route('edit_surat_p21_tahap_1')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input id="edit_p21t1_accident_id_p21_tahap_1" name="edit_accident_id_p21_tahap_1" type="text" value="{{$id}}" hidden>
                <div class="modal-body text-white" >
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="province">Provinsi:</label>
                            <input type="text" class="form-control" id="edit_p21t1_province" name="province" placeholder="Enter province" required readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="polres">Polres:</label>
                            <input type="text" class="form-control" id="edit_p21t1_polres" name="polres" placeholder="Enter polres" required readonly>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="polres_address">Alamat Polres:</label>
                            <input type="text" class="form-control" id="edit_p21t1_polres_address" name="polres_address" placeholder="Enter polres address" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="no_p21">No P21:</label>
                            <input type="text" class="form-control" id="edit_p21t1_no_p21" name="no_p21" placeholder="Enter P21 number" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="p21_date">Tanggal P21:</label>
                            <input type="date" class="form-control" id="edit_p21t1_p21_date" name="p21_date" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="p21_location">Tempat P21:</label>
                            <input type="text" class="form-control" id="edit_p21t1_p21_location" name="p21_location" placeholder="Enter Tempat P21" required value="{{$surat_p21_place}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="classification">Klasifikasi:</label>
                            <input type="text" class="form-control" id="edit_p21t1_classification" name="classification" placeholder="Enter Klasifikasi" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="attachment">Lampiran:</label>
                            <input type="text" class="form-control" id="edit_p21t1_attachment" name="attachment" placeholder="Enter Lampiran" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="subject">Perihal:</label>
                            <input type="text" class="form-control" id="edit_p21t1_subject" name="subject" placeholder="Enter Perihal" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="letter_recipient">Penerima Surat:</label>
                            <input type="text" class="form-control" id="edit_p21t1_letter_recipient" name="letter_recipient" placeholder="Enter Penerima Surat" required value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="recipient_location">Tempat Penerima Surat:</label>
                            <input type="text" class="form-control" id="edit_p21t1_recipient_location" name="recipient_location" placeholder="Enter Tempat Penerima Surat" required value="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="no_spdp">No SPDP:</label>
                            <input type="text" class="form-control" id="edit_p21t1_no_spdp" name="no_spdp" required value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="spdp_date">Tanggal SPDP:</label>
                            <input type="date" class="form-control" id="edit_p21t1_spdp_date" name="spdp_date" required value="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="no_lp">No LP:</label>
                            <input type="text" class="form-control" id="edit_p21t1_no_lp" name="no_lp" placeholder="Enter No LP" readonly required value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="accident_date">Tanggal Kejadian:</label>
                            <input type="date" class="form-control" id="edit_p21t1_accident_date" name="accident_date" readonly required value="">
                        </div>
                    </div>
                    {{-- <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="offense_articles">Pasal Yang Dilanggar:</label>
                            <input type="text" class="form-control" id="edit_p21t1_offense_articles" name="offense_articles" placeholder="Enter Pasal Yang Dilanggar" required>
                        </div>
                    </div> --}}
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="suspects">Tersangka:</label>
                            <button type="button" id="edit-add-tersangka-p21-tahap-1" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addSuspectModal">+</button>
                            <select class="form-control select2" id="edit_p21t1_suspects" name="suspects[]" required>
                                <option value="" disabled selected>--Pilih Tersangka--</option>
                                @foreach($surat_p21_suspects as $row)
                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="incident_description">Deskripsi Kejadian:</label>
                            <textarea class="form-control" id="edit_p21t1_incident_description" rows="5" name="incident_description" placeholder="Enter Deskripsi Kejadian" required></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12" id="edit-cc-container-p21-tahap-1">
                            <label for="cc">Tembusan:</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="edit_p21t1_cc" name="cc[]" placeholder="Enter Tembusan">
                                <button type="button" class="btn btn-secondary edit-add-cc-p21-tahap-1">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="penyidik_name">Nama Penyidik:</label>
                            <input type="text" class="form-control" id="edit_p21t1_penyidik_name" name="penyidik_name" placeholder="Enter Nama Penyidik" required value="{{$surat_p21_penyidik_name}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="penyidik_position">Jabatan Penyidik:</label>
                            <input type="text" class="form-control" id="edit_p21t1_penyidik_position" name="penyidik_position" placeholder="Enter Jabatan Penyidik" required value="{{$surat_p21_penyidik_position}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="penyidik_nrp">NRP Penyidik:</label>
                            <input type="text" class="form-control" id="edit_p21t1_penyidik_nrp" name="penyidik_nrp" placeholder="Enter NRP Penyidik" required value="{{$surat_p21_penyidik_nrp}}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary">
                            {{ __('Submit') }}
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalP21Tahap2" name="myModalP21Tahap2" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
    <div class="modal-content ">
            <div class="modal-header">
                <h3 class="modal-title">Surat Pengiriman Berkas Perkara Tahap 2</h3>
            </div>
            <form action="{{route('add_surat_p21_tahap_2')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input id="accident_id_p21_tahap_2" name="accident_id_p21_tahap_2" type="text" value="{{$id}}" hidden>
                <div class="modal-body text-white" >
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="province">Provinsi:</label>
                            <input type="text" class="form-control" id="province" name="province" placeholder="Enter province" required readonly value="{{$surat_p21_province}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="polres">Polres:</label>
                            <input type="text" class="form-control" id="polres" name="polres" placeholder="Enter polres" required readonly value="{{$surat_p21_polres}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="polres_address">Alamat Polres:</label>
                            <input type="text" class="form-control" id="polres_address" name="polres_address" placeholder="Enter polres address" required value="{{$surat_p21_polres_address}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="no_p21">No P21:</label>
                            <input type="text" class="form-control" id="no_p21" name="no_p21" placeholder="Enter P21 number" required readonly value="{{$surat_p21_no}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="p21_date">Tanggal P21:</label>
                            <input type="date" class="form-control" id="p21_date" name="p21_date" required value="{{date('Y-m-d')}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="p21_start_date">Tanggal P21 Awal:</label>
                            <input type="date" class="form-control" id="p21_start_date" name="p21_start_date" required readonly value="{{$surat_p21_start_date}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="p21_location">Tempat P21:</label>
                            <input type="text" class="form-control" id="p21_location" name="p21_location" placeholder="Enter Tempat P21" required value="{{$surat_p21_place}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="classification">Klasifikasi:</label>
                            <input type="text" class="form-control" id="classification" name="classification" placeholder="Enter Klasifikasi" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="attachment">Lampiran:</label>
                            <input type="text" class="form-control" id="attachment" name="attachment" placeholder="Enter Lampiran" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="subject">Perihal:</label>
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter Perihal" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="letter_recipient">Penerima Surat:</label>
                            <input type="text" class="form-control" id="letter_recipient" name="letter_recipient" placeholder="Enter Penerima Surat" required value="{{$surat_p21_letter_recepient}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="recipient_location">Tempat Penerima Surat:</label>
                            <input type="text" class="form-control" id="recipient_location" name="recipient_location" placeholder="Enter Tempat Penerima Surat" required value="{{$surat_p21_letter_recepient_place}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                        <label for="suspects">Barang Bukti:</label>
                            <button type="button" id="add-barang-bukti-p21-tahap-2" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addEvidenceModal">+</button>
                            <select class="form-control select2" id="evidences" name="evidences[]" required>
                                <option value="" disabled selected>--Pilih Barang Bukti--</option>
                                @foreach($surat_p21_evidences as $row)
                                    <option value="{{$row->id}}">{{$row->nama_barang}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="no_lp">No LP:</label>
                            <input type="text" class="form-control" id="no_lp" name="no_lp" placeholder="Enter No LP" readonly required value="{{$surat_p21_no_lp}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="accident_date">Tanggal Kejadian:</label>
                            <input type="date" class="form-control" id="accident_date" name="accident_date" readonly required value="{{$surat_p21_accident_date}}">
                        </div>
                    </div>
                    {{-- <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="offense_articles">Pasal Yang Dilanggar:</label>
                            <input type="text" class="form-control" id="offense_articles" name="offense_articles" placeholder="Enter Pasal Yang Dilanggar" required>
                        </div>
                    </div> --}}
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="suspects">Tersangka:</label>
                            <button type="button" id="add-tersangka-p21-tahap-2" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addSuspectModal">+</button>
                            <select class="form-control select2" id="suspects" name="suspects[]" required>
                                <option value="" disabled selected>--Pilih Tersangka--</option>
                                @foreach($surat_p21_suspects as $row)
                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="incident_description">Deskripsi Kejadian:</label>
                            <textarea class="form-control" id="incident_description" rows="5" name="incident_description" placeholder="Enter Deskripsi Kejadian" required>{{$surat_p21_description}}</textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12" id="cc-container-p21-tahap-2">
                            <label for="cc">Tembusan:</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="cc" name="cc[]" placeholder="Enter Tembusan">
                                <button type="button" class="btn btn-secondary add-cc-p21-tahap-2">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="penyidik_name">Nama Penyidik:</label>
                            <input type="text" class="form-control" id="penyidik_name" name="penyidik_name" placeholder="Enter Nama Penyidik" required value="{{$surat_p21_penyidik_name}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="penyidik_position">Jabatan Penyidik:</label>
                            <input type="text" class="form-control" id="penyidik_position" name="penyidik_position" placeholder="Enter Jabatan Penyidik" required value="{{$surat_p21_penyidik_position}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="penyidik_nrp">NRP Penyidik:</label>
                            <input type="text" class="form-control" id="penyidik_nrp" name="penyidik_nrp" placeholder="Enter NRP Penyidik" required value="{{$surat_p21_penyidik_nrp}}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary">
                            {{ __('Submit') }}
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myEditModalP21Tahap2" name="myEditModalP21Tahap2" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
    <div class="modal-content ">
            <div class="modal-header">
                <h3 class="modal-title">Edit Surat Pengiriman Berkas Perkara Tahap 2</h3>
            </div>
            <form action="{{route('edit_surat_p21_tahap_2')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input id="edit_p21t2_accident_id_p21_tahap_2" name="accident_id_edit_p21_tahap_2" type="text" value="{{$id}}" hidden>
                <div class="modal-body text-white" >
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="province">Provinsi:</label>
                            <input type="text" class="form-control" id="edit_p21t2_province" name="province" placeholder="Enter province" required readonly value="{{$surat_p21_province}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="polres">Polres:</label>
                            <input type="text" class="form-control" id="edit_p21t2_polres" name="polres" placeholder="Enter polres" required readonly value="{{$surat_p21_polres}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="polres_address">Alamat Polres:</label>
                            <input type="text" class="form-control" id="edit_p21t2_polres_address" name="polres_address" placeholder="Enter polres address" required value="{{$surat_p21_polres_address}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="no_p21">No P21:</label>
                            <input type="text" class="form-control" id="edit_p21t2_no_p21" name="no_p21" placeholder="Enter P21 number" required readonly value="{{$surat_p21_no}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="p21_date">Tanggal P21:</label>
                            <input type="date" class="form-control" id="edit_p21t2_p21_date" name="p21_date" required value="{{date('Y-m-d')}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="p21_start_date">Tanggal P21 Awal:</label>
                            <input type="date" class="form-control" id="edit_p21t2_p21_start_date" name="p21_start_date" required readonly value="{{$surat_p21_start_date}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="p21_location">Tempat P21:</label>
                            <input type="text" class="form-control" id="edit_p21t2_p21_location" name="p21_location" placeholder="Enter Tempat P21" required value="{{$surat_p21_place}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="classification">Klasifikasi:</label>
                            <input type="text" class="form-control" id="edit_p21t2_classification" name="classification" placeholder="Enter Klasifikasi" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="attachment">Lampiran:</label>
                            <input type="text" class="form-control" id="edit_p21t2_attachment" name="attachment" placeholder="Enter Lampiran" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="subject">Perihal:</label>
                            <input type="text" class="form-control" id="edit_p21t2_subject" name="subject" placeholder="Enter Perihal" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="letter_recipient">Penerima Surat:</label>
                            <input type="text" class="form-control" id="edit_p21t2_letter_recipient" name="letter_recipient" placeholder="Enter Penerima Surat" required readonly value="{{$surat_p21_letter_recepient}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="recipient_location">Tempat Penerima Surat:</label>
                            <input type="text" class="form-control" id="edit_p21t2_recipient_location" name="recipient_location" placeholder="Enter Tempat Penerima Surat" required readonly value="{{$surat_p21_letter_recepient_place}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                        <label for="suspects">Barang Bukti:</label>
                            <button type="button" id="edit-add-barang-bukti-p21-tahap-2" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addEvidenceModal">+</button>
                            <select class="form-control select2" id="edit_p21t2_evidences" name="evidences[]" required>
                                <option value="" disabled selected>--Pilih Barang Bukti--</option>
                                @foreach($surat_p21_evidences as $row)
                                    <option value="{{$row->id}}">{{$row->nama_barang}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="no_lp">No LP:</label>
                            <input type="text" class="form-control" id="edit_p21t2_no_lp" name="no_lp" placeholder="Enter No LP" readonly required value="{{$surat_p21_no_lp}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="accident_date">Tanggal Kejadian:</label>
                            <input type="date" class="form-control" id="edit_p21t2_accident_date" name="accident_date" readonly required value="{{$surat_p21_accident_date}}">
                        </div>
                    </div>
                    {{-- <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="offense_articles">Pasal Yang Dilanggar:</label>
                            <input type="text" class="form-control" id="edit_p21t2_offense_articles" name="offense_articles" placeholder="Enter Pasal Yang Dilanggar" required>
                        </div>
                    </div> --}}
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="suspects">Tersangka:</label>
                            <button type="button" id="edit-add-tersangka-p21-tahap-2" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addSuspectModal">+</button>
                            <select class="form-control select2" id="edit_p21t2_suspects" name="suspects[]" required>
                                <option value="" disabled selected>--Pilih Tersangka--</option>
                                @foreach($surat_p21_suspects as $row)
                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="incident_description">Deskripsi Kejadian:</label>
                            <textarea class="form-control" id="edit_p21t2_incident_description" rows="5" name="incident_description" placeholder="Enter Deskripsi Kejadian" required>{{$surat_p21_description}}</textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12" id="edit-cc-container-p21-tahap-2">
                            <label for="cc">Tembusan:</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="edit_p21t2_cc" name="cc[]" placeholder="Enter Tembusan">
                                <button type="button" class="btn btn-secondary add-cc-p21-tahap-2">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="penyidik_name">Nama Penyidik:</label>
                            <input type="text" class="form-control" id="edit_p21t2_penyidik_name" name="penyidik_name" placeholder="Enter Nama Penyidik" required value="{{$surat_p21_penyidik_name}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="penyidik_position">Jabatan Penyidik:</label>
                            <input type="text" class="form-control" id="edit_p21t2_penyidik_position" name="penyidik_position" placeholder="Enter Jabatan Penyidik" required value="{{$surat_p21_penyidik_position}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="penyidik_nrp">NRP Penyidik:</label>
                            <input type="text" class="form-control" id="edit_p21t2_penyidik_nrp" name="penyidik_nrp" placeholder="Enter NRP Penyidik" required value="{{$surat_p21_penyidik_nrp}}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary">
                            {{ __('Submit') }}
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
