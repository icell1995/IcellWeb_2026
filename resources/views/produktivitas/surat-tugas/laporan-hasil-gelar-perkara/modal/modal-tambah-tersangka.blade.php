
@push('script')
<script type="text/javascript">

    $(document).ready(function() {
        // Initialize Ajax CSRF Token
        // $.ajaxSetup({
        //     headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //     }
        // });

                // Get Polres Regency Data
        $('#province').on('change', function() {
            var provinceId = $(this).val();
            if (provinceId) {
                $.ajax({
                    url: '/api/lhgp/regency',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'provinceId': provinceId
                    },
                    success: function(data) {
                        $('#regency').empty();
                        $('#regency').append(
                            '<option value="">Pilih Kabupaten/Kota</option>');
                        $.each(data, function(key, value) {
                            $('#regency').append('<option value="' + value.id +
                                '">' + value.name + '</option>');
                        });

                        // Reset District, and Village Dropdown
                        $('#district').empty();
                        $('#district').append(
                            '<option value="">Pilih Kecamatan (Silahkan Pilih Kabupaten/Kota Terlebih Dahulu)</option>'
                            );
                        $('#village').empty();
                        $('#village').append(
                            '<option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>'
                            );
                        // $('#regency').select2({
                        //     theme: 'classic',
                        // });
                    }
                });
            } else {
                $('#regency').empty();
            }
        });

                // Get Polres District Data
        $('#regency').on('change', function() {
            var regencyId = $(this).val();
            if (regencyId) {
                $.ajax({
                    url: '/api/forms/district',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'regencyId': regencyId
                    },
                    success: function(data) {
                        $('#district').empty();
                        $('#district').append('<option value="">Pilih Kecamatan</option>');
                        $.each(data, function(key, value) {
                            $('#district').append('<option value="' + value.id +
                                '">' + value.name + '</option>');
                        });
                        // $('#district').select2({
                        //     theme: 'classic',
                        // });

                        // Reset Village Dropdown
                        $('#village').empty();
                        $('#village').append(
                            '<option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>'
                            );
                    }
                });
            } else {
                $('#district').empty();
            }
        });

                // Get Polres Village Data
        $('#district').on('change', function() {
            var districtId = $(this).val();
            if (districtId) {
                $.ajax({
                    url: '/api/forms/village',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'districtId': districtId
                    },
                    success: function(data) {
                        $('#village').empty();
                        $('#village').append(
                            '<option value="">Pilih Kelurahan/Desa</option>');
                        $.each(data, function(key, value) {
                            $('#village').append('<option value="' + value.id +
                                '">' + value.name + '</option>');
                        });
                        // $('#village').select2({
                        //     theme: 'classic',
                        // });
                    }
                });
            } else {
                $('#village').empty();
            }
        });
    });

    $('#birth_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        container: '#modalTersangka',
        orientation: 'auto bottom'
    });

    function disableInputs(){
        //identification_1 dipilih, semua input kecuali name akan disabled
        if ($('.identification_1').is(':checked')) {
            $('select[name=identity_type],\
                input[name=identity_number],\
                input[name=birth_place],\
                input[name=birth_date],\
                input[name=mother_name],\
                input[name=father_name],\
                select[name=ethnicity],\
                select[name=occupation],\
                select[name=religion],\
                select[name=education],\
                select[name=country],\
                input[name=phone_number],\
                input[name=email_address],\
                select[name=province],\
                select[name=regency],\
                select[name=district],\
                select[name=village],\
                textarea[name=address]').attr('disabled', true).val('');
            $('input[name=name]').attr('disabled', false);
            $('input[name=gender]').attr('disabled', true);
            $('input[name=marital_status]').attr('disabled', true);
        }else{
            $('select[name=identity_type],\
                input[name=identity_number],\
                input[name=name],\
                input[name=gender],\
                input[name=birth_place],\
                input[name=birth_date],\
                input[name=mother_name],\
                input[name=father_name],\
                select[name=ethnicity],\
                select[name=occupation],\
                select[name=religion],\
                select[name=education],\
                select[name=country],\
                input[name=marital_status],\
                input[name=phone_number],\
                input[name=email_address],\
                select[name=province],\
                select[name=regency],\
                select[name=district],\
                select[name=village],\
                textarea[name=address]').attr('disabled', false);
        }
    }

    $(document).ready(function() {
        $('.disabled-inputs').attr('disabled', true);
        // disableInputs(); // disable input pada saat halaman pertama kali diload
        $('input[type=radio][name=identification]').change(function() {
            disableInputs(); // disable atau enable input saat radio button identification berubah
        });
    });

</script>
@endpush
