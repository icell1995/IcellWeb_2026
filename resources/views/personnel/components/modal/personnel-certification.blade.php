<!-- Modal Add Certification-->
<div class="modal fade" id="addCertificationModal" tabindex="-1" role="dialog" aria-labelledby="addCertificationModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="modalContent">
      <!-- Header Modal -->
      <div class="modal-header">
        <h5 class="modal-title" id="addCertificationModalLabel">Tambah Riwayat Sertifikasi Personel</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Body Modal -->
      <div class="modal-body">
        <form id="addLawForm">
             <div class="form-group">
                <label for="certificationNumber">Nomor Registrasi Sertifikat</label>
                <input type="text" class="form-control" id="certificationNumber" placeholder="" value="">
            </div>
            <div class="form-group">
                <label for="certificationStartDate">Tanggal Mulai Berlaku</label>
                <input class="form-control datepicker" id="certificationStartDate" name="certificationStartDate"
                        placeholder="YYYY-MM-DD" autocomplete="off" value="{{old('certificationStartDate')}}" data-provide="datepicker">
            </div>
            <div class="form-group">
                <label for="certificationEndDate">Tanggal Kadaluwarsa</label>
                <input class="form-control datepicker" id="certificationEndDate" name="certificationEndDate"
                        placeholder="YYYY-MM-DD" autocomplete="off" value="{{old('certificationEndDate')}}" data-provide="datepicker">
            </div>
        </form>
      </div>

      <!-- Footer Modal -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="bi bi-x-circle"></i> Batal</button>
        <button type="button" class="btn btn-primary" id="saveAddCertificationButton"><i class="bi bi-save"></i> Simpan</button>
      </div>
    </div>
  </div>
</div>
