<!-- Modal Add PoliceSpecialEducation-->
<div class="modal fade" id="addPoliceSpecialEducationModal" tabindex="-1" role="dialog" aria-labelledby="addPoliceSpecialEducationModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="modalContent">
      <!-- Header Modal -->
      <div class="modal-header">
        <h5 class="modal-title" id="addPoliceSpecialEducationModalLabel">PENDIDIKAN LANTAS/DIKJUR</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Body Modal -->
      <div class="modal-body">
        <div class="alert alert-primary mt-3 mb-3" role="alert">
            Jika tidak terdapat opsi yang sesuai, silahkan menghubungi Helpdesk ICELL untuk koordinasi.
        </div>
        <form id="addLawForm">
            <div class="form-group form-validate">
                <label for="policeSpecialEducationPlace">Tempat Pendidikan Lantas</label>
                <select class="form-control" id="policeSpecialEducationPlace">
                    <option value="">--Pilihan Tempat Pendidikan Lantas--</option>

                </select>
            </div>
             <div class="form-group">
                <label for="policeSpecialEducationGraduateYear">Tahun Lulus Pendidikan Lantas</label>
                <input type="text" class="form-control" id="policeSpecialEducationGraduateYear" placeholder="YYYY" value="">
            </div>
            <div class="form-group form-validate">
                <label for="policeSpecialEducationMaterial">Materi Pendidikan Lantas</label>
                <select class="form-control" id="policeSpecialEducationMaterial">
                    <option value="">--Pilih Materi Pendidikan Lantas--</option>

                </select>
            </div>
        </form>
      </div>

      <!-- Footer Modal -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="bi bi-x-circle"></i> Batal</button>
        <button type="button" class="btn btn-primary" id="saveAddPoliceSpecialEducationButton"><i class="bi bi-save"></i> Simpan</button>
      </div>
    </div>
  </div>
</div>
