<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Polda;
use App\Models\Polres;
use App\Models\Geography\Province;
use App\Models\Geography\Regency;
use App\Models\Geography\District;
use App\Models\Geography\Village;
use App\Models\Kejaksaan;
use App\Models\Officer;
use App\Models\Peoples\AuthorizedSignatory;

class FormsController extends Controller
{
    public $ranks;
    public $positions;
    public $whitelistKanitGakkum;
    public $invalidIdentityNumbers;

    // construct 
    public function __construct(){
        $rankList = [    ['id' => 'BRIPTU', 'name' => 'Brigadir Polisi Satu'],
            ['id' => 'AIPTU', 'name' => 'Ajun Inspektur Polisi Satu'],
            ['id' => 'BRIGADIR', 'name' => 'Brigadir'],
            ['id' => 'AIPDA', 'name' => 'Ajun Inspektur Polisi Dua'],
            ['id' => 'BRIPDA', 'name' => 'Brigadir Polisi Dua'],
            ['id' => 'BRIPKA', 'name' => 'Brigadir Polisi Kepala'],
            ['id' => 'IPDA', 'name' => 'Inspektur Polisi Dua'],
            ['id' => 'AKP', 'name' => 'Ajun Komisaris Polisi'],
            ['id' => 'AKBP', 'name' => 'Ajun Komisaris Besar Polisi'],
            ['id' => 'IPTU', 'name' => 'Inspektur Polisi Satu'],
            ['id' => 'KOMBESPOL', 'name' => 'Komisaris Besar Polisi'],
            ['id' => 'KOMJENPOL', 'name' => 'Komisaris Jendral'],
            ['id' => 'KOMPOL', 'name' => 'Komisaris Polisi'],
            ['id' => 'BRIGPOL', 'name' => 'Brigadir Polisi'],
        ];
       
        $positionList = [    
            ['id' => 'KASAT LANTAS', 'name' => 'Kasat Lantas'],
            ['id' => 'PS. KASAT LANTAS', 'name' => 'PS. Kasat Lantas'],
            ['id' => 'PLT. KASAT LANTAS', 'name' => 'PLT. Kasat Lantas'],
            ['id' => 'KAPOLRES', 'name' => 'Kapolres'],
            ['id' => 'WAKAPOLRES', 'name' => 'Wakapolres'],
            ['id' => 'KASUBDITGAKKUM', 'name' => 'Kasubditgakkum'],
            ['id' => 'PS. KASUBDITGAKKUM', 'name' => 'PS. Kasubditgakkum'],
            ['id' => 'KANIT GAKKUM', 'name' => 'Kanit Gakkum'],
            ['id' => 'PS. KANIT GAKKUM', 'name' => 'PS. Kanit Gakkum'],
        ];

        $whitelistsKanitGakkum = collect([
            [
                'polres_id' => '2608',
                'name' => 'POLRES BULUKUMBA', //NOTED
            ],
            // [
            //     'polres_id' => '0230',
            //     'name' => 'POLRES LABUHAN BATU SELATAN',
            // ],
            // [
            //     'polres_id' => '2911',
            //     'name' => 'POLRES BURU SELATAN',
            // ],
            // [
            //     'polres_id' => '0712',
            //     'name' => 'POLRES OGAN KOMERING ULU TIMUR',
            // ],
            // [
            //     'polres_id' => '1004',
            //     'name' => 'POLRES BINTAN',
            // ],
            // [
            //     'polres_id' => '2509',
            //     'name' => 'POLRES TOJO UNA-UNA',
            // ],
            // [
            //     'polres_id' => '2206',
            //     'name' => 'POLRES HULU SUNGAI TENGAH',
            // ],

        ]);

        $invalidIdentityNumberList = collect([
            [
                'polres_id' => '0410',
                'name' => 'POLRES SIAK',
                'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 21 dan tahun 51</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 08 tahun 91</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            ],
            [
                'polres_id' => '0709',
                'name' => 'POLRES PAGAR ALAM',
                'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 10 dan tahun 01</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 03 tahun 77</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            ],
            // [
            //     'polres_id' => '0714',
            //     'name' => 'POLRES OGAN ILIR',
            //     'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 40 dan tahun 19</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 01 tahun 90</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            // ],
            [
                'polres_id' => '1108',
                'name' => 'POLRES METRO BEKASI KOTA',
                'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 11 dan tahun 69</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 11 tahun 66</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            ],
            [
                'polres_id' => '1201',
                'name' => 'POLRESTABES BANDUNG',
                'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 03 dan tahun 80</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 03 tahun 81</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            ],
            [
                'polres_id' => '1220',
                'name' => 'POLRES INDRAMAYU',
                'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 09 dan tahun 19</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 04 tahun 91</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            ],
            // [
            //     'polres_id' => '1320',
            //     'name' => 'POLRES JEPARA',
            //     'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 10 dan tahun 90</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 04 tahun 91</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            // ],
            [
                'polres_id' => '1506',
                'name' => 'POLRES MOJOKERTO',
                'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 08 dan tahun 07</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 08 tahun 89</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            ],
            [
                'polres_id' => '1516',
                'name' => 'POLRES SITUBONDO',
                'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 08 dan tahun 70</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 06 tahun 70</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            ],
            // [
            //     'polres_id' => '1520',
            //     'name' => 'POLRES KEDIRI',
            //     'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 04 dan tahun 73</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 09 tahun 90</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            // ],
            [
                'polres_id' => '1603',
                'name' => 'POLRES LEBAK',
                'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 01 dan tahun 81</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 01 tahun 91</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            ],
            [
                'polres_id' => '1906',
                'name' => 'POLRES ENDE',
                'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 12 dan tahun 74</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 02 tahun 74</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            ],
            [
                'polres_id' => '2617',
                'name' => 'POLRES LUWU',
                'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 09 dan tahun 74</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 04 tahun 76</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            ],
            [
                'polres_id' => '2702',
                'name' => 'POLRES KOLAKA',
                'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 09 dan tahun 70</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 11 tahun 91</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            ],
            [
                'polres_id' => '1510',
                'name' => 'POLRES PASURUAN KOTA',
                'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 01 dan tahun 81</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 01 tahun 89</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            ],
            [
                'polres_id' => '2613',
                'name' => 'POLRES PINRANG',
                'message' => 'Mohon konfirmasi ulang Nomor Induk Kependudukan (NIK) yang telah diinput karena terdapat ketidaksesuaian data pada sistem kami. Setelah dilakukan review oleh tim kami, ditemukan bahwa <b>NIK</b> yang tertera mencantumkan <b>bulan 08 dan tahun 69</b>, sedangkan pada <b>NRP</b> tercantum <b>bulan 12 tahun 72</b>. Mohon untuk memeriksa kembali NIK yang telah diinput. Bila sudah yakin silahkan klik tombol Submit.',
            ],
        ]);

        $this->ranks = $rankList;
        $this->positions = $positionList;
        $this->whitelistKanitGakkum = $whitelistsKanitGakkum;
        $this->invalidIdentityNumbers = $invalidIdentityNumberList;
    }

    public function formCollect()
    {
        // get url parameter
        $mode = request()->query('mode');

        $user = Auth::getUser();
      
        $polda = Polda::where('id', $user->polda_id)->first();
        $polres = Polres::where('id', $user->polres_id)->first();
        
        $getPolda = Polda::orderBy('name', 'ASC')->get();
        $getPolres = Polres::orderBy('name', 'ASC')->get();

        $getUpdatedPolres = Polres::join('polda', 'polda.id', '=', 'polres.polda_id')
            ->select('polres.*', 'polda.name as polda_name')
            ->where('polres.address', '!=', NULL)
            ->orderBy('polres.name', 'ASC')
            ->get();

        $poldaId = $user->polda_id;
        $polresId = $user->polres_id;
        
        if($polres == NULL && $user->role_id != 1){
            return view('icell_forms.form_failed');
        }

        if($polres->address != NULL && $polres->kejaksaan_name != NULL && $mode != 'edit'){
            $polresAddress = $polres->address;
            $polresProvince = $polres->polres_province;
            $polresRegency = $polres->polres_regency;
            $polresDistrict = $polres->polres_district;
            $polresVillage = $polres->polres_village;
            $polresZipcode = $polres->polres_zipcode;

            $kejaksaan = $polres->kejaksaan_name;
            $kejaksaanAddress = $polres->kejaksaan_address;
            $kejaksaanProvince = $polres->kejaksaan_province;
            $kejaksaanRegency = $polres->kejaksaan_regency;
            $kejaksaanDistrict = $polres->kejaksaan_district;
            $kejaksaanVillage = $polres->kejaksaan_village;
           
            $kejaksaan2 = $polres->kejaksaan2_name;
            $kejaksaan2Address = $polres->kejaksaan2_address;
            $kejaksaan2Province = $polres->kejaksaan2_province;
            $kejaksaan2Regency = $polres->kejaksaan2_regency;
            $kejaksaan2District = $polres->kejaksaan2_district;
            $kejaksaan2Village = $polres->kejaksaan2_village;

            $kejaksaan3 = $polres->kejaksaan3_name;
            $kejaksaan3Address = $polres->kejaksaan3_address;
            $kejaksaan3Province = $polres->kejaksaan3_province;
            $kejaksaan3Regency = $polres->kejaksaan3_regency;
            $kejaksaan3District = $polres->kejaksaan3_district;
            $kejaksaan3Village = $polres->kejaksaan3_village;

            return view('icell_forms.form_success', [
                'polresName' => $polres->name, 
                'poldaName' => $polda->name,
                'polresAddress' => $polresAddress,
                'polresProvince' => $polresProvince,
                'polresRegency' => $polresRegency,
                'polresDistrict' => $polresDistrict,
                'polresVillage' => $polresVillage,
                'polresZipcode' => $polresZipcode,

                'kejaksaan' => $kejaksaan,
                'kejaksaanAddress' => $kejaksaanAddress,
                'kejaksaanProvince' => $kejaksaanProvince,
                'kejaksaanRegency' => $kejaksaanRegency,
                'kejaksaanDistrict' => $kejaksaanDistrict,
                'kejaksaanVillage' => $kejaksaanVillage,

                'kejaksaan2' => $kejaksaan2,
                'kejaksaan2Address' => $kejaksaan2Address,
                'kejaksaan2Province' => $kejaksaan2Province,
                'kejaksaan2Regency' => $kejaksaan2Regency,
                'kejaksaan2District' => $kejaksaan2District,
                'kejaksaan2Village' => $kejaksaan2Village,

                'kejaksaan3' => $kejaksaan3,
                'kejaksaan3Address' => $kejaksaan3Address,
                'kejaksaan3Province' => $kejaksaan3Province,
                'kejaksaan3Regency' => $kejaksaan3Regency,
                'kejaksaan3District' => $kejaksaan3District,
                'kejaksaan3Village' => $kejaksaan3Village,
            ]);
        }

        $provinces = Province::orderBy('name', 'asc')->get();
        $kejaksaan = Kejaksaan::orderBy('id', 'asc')
            ->get();

        $viewData = [
            'poldaName' => ($polda) ? $polda->name : '',
            'polresName' => ($polres) ? $polres->name : '',
            'poldaId' => $poldaId,
            'polresId' => $polresId,
            'provinces' => $provinces,
            'kejaksaanGet' => $kejaksaan,
            'getPolda' => $getPolda,
            'getPolres' => $getPolres,
            'getUpdatedPolres' => $getUpdatedPolres,
            'polres' => $polres,
            'mode' => $mode,
        ];

        return view('icell_forms.form_collect', $viewData);
    }

    public function formStore(Request $request)
    {
        // Validate the form data
        $request->validate([
            'poldaId' => 'required',
            'polresId' => 'required',
            'polresAddress' => 'required | max:255',
            'polresProvinceId' => 'required',
            'polresRegencyId' => 'required',
            'polresDistrictId' => 'required',
            'polresVillageId' => 'required',
            'polresZipcode' => 'required | max:5',
            'kejaksaan' => 'required',
            'kejaksaanAddress' => 'required | max:255',
            'kejaksaanProvinceId' => 'required',
            'kejaksaanRegencyId' => 'required',
            'kejaksaanDistrictId' => 'required',
            'kejaksaanVillageId' => 'required',
        ],[
            'poldaId.required' => 'Polda tidak boleh kosong',
            'polresId.required' => 'Polres tidak boleh kosong',
            'polresAddress.required' => 'Alamat Polres tidak boleh kosong',
            'polresAddress.max' => 'Alamat Polres tidak boleh lebih dari 255 karakter',
            'polresProvinceId.required' => 'Provinsi Polres tidak boleh kosong',
            'polresRegencyId.required' => 'Kabupaten/Kota Polres tidak boleh kosong',
            'polresDistrictId.required' => 'Kecamatan Polres tidak boleh kosong',
            'polresVillageId.required' => 'Kelurahan Polres tidak boleh kosong',
            'polresZipcode.required' => 'Kode Pos Polres tidak boleh kosong',
            'polresZipcode.max' => 'Kode Pos Polres tidak boleh lebih dari 5 karakter',
            'kejaksaan.required' => 'Kejaksaan tidak boleh kosong',
            'kejaksaanAddress.required' => 'Alamat Kejaksaan tidak boleh kosong',
            'kejaksaanAddress.max' => 'Alamat Kejaksaan tidak boleh lebih dari 255 karakter',
            'kejaksaanProvinceId.required' => 'Provinsi Kejaksaan tidak boleh kosong',
            'kejaksaanRegencyId.required' => 'Kabupaten/Kota Kejaksaan tidak boleh kosong',
            'kejaksaanDistrictId.required' => 'Kecamatan Kejaksaan tidak boleh kosong',
            'kejaksaanVillageId.required' => 'Kelurahan Kejaksaan tidak boleh kosong',
        ]);

        if($request->kejaksaan2){
            $request->validate([
                'kejaksaan2' => 'required',
                'kejaksaan2Address' => 'required | max:255',
                'kejaksaan2ProvinceId' => 'required',
                'kejaksaan2RegencyId' => 'required',
                'kejaksaan2DistrictId' => 'required',
                'kejaksaan2VillageId' => 'required',
            ],[
                'kejaksaan2.required' => 'Kejaksaan 2 tidak boleh kosong',
                'kejaksaan2Address.required' => 'Alamat Kejaksaan 2 tidak boleh kosong',
                'kejaksaan2Address.max' => 'Alamat Kejaksaan 2 tidak boleh lebih dari 255 karakter',
                'kejaksaan2ProvinceId.required' => 'Provinsi Kejaksaan 2 tidak boleh kosong',
                'kejaksaan2RegencyId.required' => 'Kabupaten/Kota Kejaksaan 2 tidak boleh kosong',
                'kejaksaan2DistrictId.required' => 'Kecamatan Kejaksaan 2 tidak boleh kosong',
                'kejaksaan2VillageId.required' => 'Kelurahan Kejaksaan 2 tidak boleh kosong',
            ]);
        }
        
        if($request->kejaksaan3){
            $request->validate([
                'kejaksaan3' => 'required',
                'kejaksaan3Address' => 'required | max:255',
                'kejaksaan3ProvinceId' => 'required',
                'kejaksaan3RegencyId' => 'required',
                'kejaksaan3DistrictId' => 'required',
                'kejaksaan3VillageId' => 'required',
            ],[
                'kejaksaan3.required' => 'Kejaksaan 3 tidak boleh kosong',
                'kejaksaan3Address.required' => 'Alamat Kejaksaan 3 tidak boleh kosong',
                'kejaksaan3Address.max' => 'Alamat Kejaksaan 3 tidak boleh lebih dari 255 karakter',
                'kejaksaan3ProvinceId.required' => 'Provinsi Kejaksaan 3 tidak boleh kosong',
                'kejaksaan3RegencyId.required' => 'Kabupaten/Kota Kejaksaan 3 tidak boleh kosong',
                'kejaksaan3DistrictId.required' => 'Kecamatan Kejaksaan 3 tidak boleh kosong',
                'kejaksaan3VillageId.required' => 'Kelurahan Kejaksaan 3 tidak boleh kosong',
            ]);
        }

        $user = Auth::getUser();
        $polda = Polda::where('id', $request->poldaId)->first();
        $polres = Polres::where('id', $request->polresId)->first();
        $polresAddress = htmlspecialchars($request->polresAddress);
        $polresProvince = Province::where('id', $request->polresProvinceId)->first()->name;
        $polresRegency = Regency::where('id', $request->polresRegencyId)->first()->name;
        $polresDistrict = District::where('id', $request->polresDistrictId)->first()->name;
        $polresVillage = Village::where('id', $request->polresVillageId)->first()->name;
        $polresZipcode = htmlspecialchars($request->polresZipcode);
        
        $kejaksaan = Kejaksaan::where('id', $request->kejaksaan)->first()->name;
        $kejaksaanAddress = htmlspecialchars($request->kejaksaanAddress);
        $kejaksaanProvince = Province::where('id', $request->kejaksaanProvinceId)->first()->name;
        $kejaksaanRegency = Regency::where('id', $request->kejaksaanRegencyId)->first()->name;
        $kejaksaanDistrict = District::where('id', $request->kejaksaanDistrictId)->first()->name;
        $kejaksaanVillage = Village::where('id', $request->kejaksaanVillageId)->first()->name;
        
        $kejaksaan2 = ($request->kejaksaan2) ? Kejaksaan::where('id', $request->kejaksaan2)->first()->name : "";
        $kejaksaan2Address = htmlspecialchars($request->kejaksaan2Address);
        $kejaksaan2Province = ($request->kejaksaan2ProvinceId) ? Province::where('id', $request->kejaksaan2ProvinceId)->first()->name : "";
        $kejaksaan2Regency = ($request->kejaksaan2RegencyId) ? Regency::where('id', $request->kejaksaan2RegencyId)->first()->name : "";
        $kejaksaan2District = ($request->kejaksaan2DistrictId) ? District::where('id', $request->kejaksaan2DistrictId)->first()->name : "";
        $kejaksaan2Village = ($request->kejaksaan2VillageId) ? Village::where('id', $request->kejaksaan2VillageId)->first()->name : "";

        $kejaksaan3 = ($request->kejaksaan3) ? Kejaksaan::where('id', $request->kejaksaan3)->first()->name : "";
        $kejaksaan3Address = htmlspecialchars($request->kejaksaan3Address);
        $kejaksaan3Province = ($request->kejaksaan3ProvinceId) ? Province::where('id', $request->kejaksaan3ProvinceId)->first()->name : "";
        $kejaksaan3Regency = ($request->kejaksaan3RegencyId) ? Regency::where('id', $request->kejaksaan3RegencyId)->first()->name : "";
        $kejaksaan3District = ($request->kejaksaan3DistrictId) ? District::where('id', $request->kejaksaan3DistrictId)->first()->name : "";
        $kejaksaan3Village = ($request->kejaksaan3VillageId) ? Village::where('id', $request->kejaksaan3VillageId)->first()->name : "";

        // Update to Polres table
        $polres = Polres::where('id', $request->polresId)->first();

        if($polres == NULL && $user->role_id != 1){
            return view('icell_forms.form_failed');
        }
    
        $polres->update([
            'address' => $polresAddress,
            'polres_province' => $polresProvince,
            'polres_regency' => $polresRegency,
            'polres_district' => $polresDistrict,
            'polres_village' => $polresVillage,
            'polres_zipcode' => $polresZipcode,

            'kejaksaan_name' => $kejaksaan,
            'kejaksaan_address' => $kejaksaanAddress,
            'kejaksaan_province' => $kejaksaanProvince,
            'kejaksaan_regency' => $kejaksaanRegency,
            'kejaksaan_district' => $kejaksaanDistrict,
            'kejaksaan_village' => $kejaksaanVillage,

            'kejaksaan2_name' => $kejaksaan2,
            'kejaksaan2_address' => $kejaksaan2Address,
            'kejaksaan2_province' => $kejaksaan2Province,
            'kejaksaan2_regency' => $kejaksaan2Regency,
            'kejaksaan2_district' => $kejaksaan2District,
            'kejaksaan2_village' => $kejaksaan2Village,

            'kejaksaan3_name' => $kejaksaan3,
            'kejaksaan3_address' => $kejaksaan3Address,
            'kejaksaan3_province' => $kejaksaan3Province,
            'kejaksaan3_regency' => $kejaksaan3Regency,
            'kejaksaan3_district' => $kejaksaan3District,
            'kejaksaan3_village' => $kejaksaan3Village,
        ]);

        // Redirect to the form collect page
        return view('icell_forms.form_success', [
            'poldaName' => $polda->name, 
            'polresName' => $polres->name,
            'polresAddress' => $polresAddress,
            'polresProvince' => $polresProvince,
            'polresRegency' => $polresRegency,
            'polresDistrict' => $polresDistrict,
            'polresVillage' => $polresVillage,
            'polresZipcode' => $polresZipcode,

            'kejaksaan' => $kejaksaan,
            'kejaksaanAddress' => $kejaksaanAddress,
            'kejaksaanProvince' => $kejaksaanProvince,
            'kejaksaanRegency' => $kejaksaanRegency,
            'kejaksaanDistrict' => $kejaksaanDistrict,
            'kejaksaanVillage' => $kejaksaanVillage,

            'kejaksaan2' => $kejaksaan2,
            'kejaksaan2Address' => $kejaksaan2Address,
            'kejaksaan2Province' => $kejaksaan2Province,
            'kejaksaan2Regency' => $kejaksaan2Regency,
            'kejaksaan2District' => $kejaksaan2District,
            'kejaksaan2Village' => $kejaksaan2Village,

            'kejaksaan3' => $kejaksaan3,
            'kejaksaan3Address' => $kejaksaan3Address,
            'kejaksaan3Province' => $kejaksaan3Province,
            'kejaksaan3Regency' => $kejaksaan3Regency,
            'kejaksaan3District' => $kejaksaan3District,
            'kejaksaan3Village' => $kejaksaan3Village,
        ]);
    }

    public function formConfirmation(){
        $user = Auth::getUser();
    
        $polda = Polda::where('id', $user->polda_id)->first();
        $polres = Polres::where('id', $user->polres_id)->first();
        $poldaId = $user->polda_id;
        $polresId = $user->polres_id;
    
        $provinces = Province::orderBy('name', 'asc')->get();
        $currentProvince = $provinces->where('name', $polres->polres_province)->first();
   
        $regencies = Regency::where('province_id', $currentProvince->id)->orderBy('name', 'asc')->get();
        $currentRegency = $regencies->where('name', $polres->polres_regency)->first();

        $districts = District::where('regency_id', $currentRegency->id)->orderBy('name', 'asc')->get();
        $currentDistrict = $districts->where('name', $polres->polres_district)->first();

        $villages = Village::where('district_id', $currentDistrict->id)->orderBy('name', 'asc')->get();
        $currentVillage = $villages->where('name', $polres->polres_village)->first();

        $authorizedSignatories = AuthorizedSignatory::where('polres_id', $user->polres_id)->get();

        $isOpenKanitGakkum = $this->whitelistKanitGakkum->where('polres_id', $user->polres_id)->count();
        $invalidIdentityNumber = $this->invalidIdentityNumbers->where('polres_id', $user->polres_id)->first();
        $isInvalidIdentityNumber = ($invalidIdentityNumber) ? true : false;
     
        $viewData = [
            'polda' => $polda,
            'polres' => $polres,
            'poldaName' => ($polda) ? $polda->name : '',
            'polresName' => ($polres) ? $polres->name : '',
            'poldaId' => $poldaId,
            'polresId' => $polresId,
            'provinces' => $provinces,
            'currentProvince' => $currentProvince,
            'regencies' => $regencies,
            'currentRegency' => $currentRegency,
            'districts' => $districts,
            'currentDistrict' => $currentDistrict,
            'villages' => $villages,
            'currentVillage' => $currentVillage,
            'authorizedSignatories' => $authorizedSignatories,
            'ranks' => $this->ranks,
            'positions' => $this->positions,
            'isOpenKanitGakkum' => ($isOpenKanitGakkum > 0) ? true : false,
            'isInvalidIdentityNumber' => $isInvalidIdentityNumber,
            'invalidIdentityNumber' => $invalidIdentityNumber,
        ];
  
        return view('icell_forms.form_confirmation', $viewData);
    }

    public function formConfirmationStore(Request $request){
        // Validate the request...
        $request->validate([
            'poldaId' => 'required',
            'polresId' => 'required',
            'polresAddress' => 'required | max:255',
            'polresProvinceId' => 'required',
            'polresRegencyId' => 'required',
            'polresDistrictId' => 'required',
            'polresVillageId' => 'required',
            'polresZipcode' => 'required | max:5',
            
            'idAuthorizedSignatory.*' => 'required | max:255',
            'firstTitleAuthorizedSignatory.*' => 'required_if:lastTitleAuthorizedSignatory.*,null|required_if:lastTitleAuthorizedSignatory.*,|max:255',
            'firstNameAuthorizedSignatory.*' => 'required | max:255',
            'lastNameAuthorizedSignatory.*' => 'max:255',
            'lastTitleAuthorizedSignatory.*' => 'required_if:firstTitleAuthorizedSignatory.*,null|required_if:firstTitleAuthorizedSignatory.*,|max:255',
    
            'rankAuthorizedSignatory.*' => 'required | max:255',
            'registerNumberAuthorizedSignatory.*' => 'required | max:8',
            'positionAuthorizedSignatory.*' => 'required | max:255',
            'identityNumberAuthorizedSignatory.*' => 'required | digits:16',
            'emailAuthorizedSignatory.*' => 'max:255',
            'phoneAuthorizedSignatory.*' => 'required | max:16',
        ],[
            'poldaId.required'=>'POLDA ID tidak boleh kosong',
            'polresId.required'=>'POLRES ID tidak boleh kosong',

            'polresAddress.required'=>'Alamat POLRES tidak boleh kosong',
            'polresAddress.max'=>'Alamat POLRES tidak boleh lebih dari 255 karakter',

            'polresProvinceId.required'=>'Provinsi POLRES tidak boleh kosong',
            'polresRegencyId.required'=>'Kabupaten/Kota POLRES tidak boleh kosong',
            'polresDistrictId.required'=>'Kecamatan POLRES tidak boleh kosong',
            'polresVillageId.required'=>'Desa/Kelurahan POLRES tidak boleh kosong',

            'polresZipcode.required'=>'Kode Pos POLRES tidak boleh kosong',
            'polresZipcode.max'=>'Kode Pos POLRES tidak boleh lebih dari 5 karakter',


            'idAuthorizedSignatory.*.required'=>'ID Pejabat Tanda Tangan tidak boleh kosong',
            'idAuthorizedSignatory.*.max'=>'ID Pejabat Tanda Tangan tidak boleh lebih dari 255 karakter',

            'firstNameAuthorizedSignatory.*.required'=>'Nama Depan Pejabat Tanda Tangan tidak boleh kosong',
            'firstNameAuthorizedSignatory.*.max'=>'Nama Depan Pejabat Tanda Tangan tidak boleh lebih dari 255 karakter',
            'lastNameAuthorizedSignatory.*.max'=>'Nama Belakang Pejabat Tanda Tangan tidak boleh lebih dari 255 karakter',

            'firstTitleAuthorizedSignatory.*.required_if'=>'Gelar Pejabat Tanda Tangan tidak boleh kosong',
            'firstTitleAuthorizedSignatory.*.max'=>'Gelar Depan Pejabat Tanda Tangan tidak boleh lebih dari 255 karakter',
            'lastTitleAuthorizedSignatory.*.required_if'=>'Gelar Pejabat Tanda Tangan tidak boleh kosong',
            'lastTitleAuthorizedSignatory.*.max'=>'Gelar Belakang Pejabat Tanda Tangan tidak boleh lebih dari 255 karakter',

            'rankAuthorizedSignatory.*.required'=>'Pangkat Pejabat Tanda Tangan tidak boleh kosong',
            'rankAuthorizedSignatory.*.max'=>'Pangkat Pejabat Tanda Tangan tidak boleh lebih dari 255 karakter',

            'registerNumberAuthorizedSignatory.*.required'=>'Nomor Registrasi Pejabat Tanda Tangan tidak boleh kosong',
            'registerNumberAuthorizedSignatory.*.max'=>'Nomor Registrasi Pejabat Tanda Tangan tidak boleh lebih dari 8 digit',

            'positionAuthorizedSignatory.*.required'=>'Jabatan Pejabat Tanda Tangan tidak boleh kosong',
            'positionAuthorizedSignatory.*.max'=>'Jabatan Pejabat Tanda Tangan tidak boleh lebih dari 255 karakter',

            'identityNumberAuthorizedSignatory.*.required'=>'Nomor Identitas Pejabat Tanda Tangan tidak boleh kosong',
            'identityNumberAuthorizedSignatory.*.digits'=>'Nomor Identitas Pejabat Tanda Tangan harus 16 digit',

            'emailAuthorizedSignatory.*.max'=>'Email Pejabat Tanda Tangan tidak boleh lebih dari 255 karakter',
            'emailAuthorizedSignatory.*.email'=>'Format Email Pejabat Tanda Tangan tidak valid',

            'phoneAuthorizedSignatory.*.required'=>'Nomor Telepon Pejabat Tanda Tangan tidak boleh kosong',
            'phoneAuthorizedSignatory.*.max'=>'Nomor Telepon Pejabat Tanda Tangan tidak boleh lebih dari 255 karakter',
        ]);

        $user = Auth::getUser();

        $polda = Polda::where('id', $user->polda_id)->first();
        $polres = Polres::where('id', $user->polres_id)->first();
        $poldaId = $user->polda_id;
        $polresId = $user->polres_id;

        $polresAddress = htmlspecialchars($request->polresAddress);
        $polresProvince = Province::where('id', $request->polresProvinceId)->first();
        $polresRegency = Regency::where('id', $request->polresRegencyId)->first();
        $polresDistrict = District::where('id', $request->polresDistrictId)->first();
        $polresVillage = Village::where('id', $request->polresVillageId)->first();
        $polresZipcode = htmlspecialchars($request->polresZipcode);
        
        Polres::where('id', $polresId)
            ->update([
                'address' => $polresAddress,
                'polres_province' => ($polresProvince->name) ? strtoupper($polresProvince->name) : '',
                'province_id' => $polresProvince->id,
                'polres_regency' => ($polresRegency->name) ? strtoupper($polresRegency->name) : '',
                'regency_id' => $polresRegency->id,
                'polres_district' => ($polresDistrict->name) ? strtoupper($polresDistrict->name) : '',
                'district_id' => $polresDistrict->id,
                'polres_village' => ($polresVillage->name) ? strtoupper($polresVillage->name) : '',
                'village_id' => $polresVillage->id,
                'polres_zipcode' => $polresZipcode,
            ]);

        $authorizedSignatories = $request->idAuthorizedSignatory;
        foreach($authorizedSignatories as $key => $authorizedSignatory){
            $idAuthorizedSignatory = htmlspecialchars($request->idAuthorizedSignatory[$key]);
            $firstNameAuthorizedSignatory = htmlspecialchars($request->firstNameAuthorizedSignatory[$key]);
            $lastNameAuthorizedSignatory = htmlspecialchars($request->lastNameAuthorizedSignatory[$key]);
            $firstTitleAuthorizedSignatory = htmlspecialchars($request->firstTitleAuthorizedSignatory[$key]);
            $lastTitleAuthorizedSignatory = htmlspecialchars($request->lastTitleAuthorizedSignatory[$key]);
            $rankAuthorizedSignatory = htmlspecialchars($request->rankAuthorizedSignatory[$key]);
            $registerNumberAuthorizedSignatory = htmlspecialchars($request->registerNumberAuthorizedSignatory[$key]);
            $positionAuthorizedSignatory = htmlspecialchars($request->positionAuthorizedSignatory[$key]);
            $identityNumberAuthorizedSignatory = htmlspecialchars($request->identityNumberAuthorizedSignatory[$key]);
            $emailAuthorizedSignatory = htmlspecialchars($request->emailAuthorizedSignatory[$key]);
            $phoneAuthorizedSignatory = htmlspecialchars($request->phoneAuthorizedSignatory[$key]);

            $rankId = NULL;
            $rankName = NULL;
            $positionId = NULL;
            $positionName = NULL;

            foreach ($this->ranks as $rank) {
                if ($rank['id'] == $rankAuthorizedSignatory) {
                    // Match found
                    $rankId = $rank['id'];
                    $rankName = $rank['name'];

                    break;
                }
            }
            if ($rankId == NULL) {
                // Back to previous page with error message
                return redirect()->back()->with('error', 'Pangkat Pejabat Tanda Tangan tidak valid, tidak ada dalam opsi');
            }
            
            foreach ($this->positions as $position) {
                if ($position['id'] == $positionAuthorizedSignatory) {
                    // Match found
                    $positionId = $position['id'];
                    $positionName = $position['name'];

                    break;
                }
            }
            if ($positionId == NULL) {
                // Back to previous page with error message
                return redirect()->back()->with('error', 'Jabatan Pejabat Tanda Tangan tidak valid, tidak ada dalam opsi');
            }

            AuthorizedSignatory::where('id', $idAuthorizedSignatory)
                ->update([
                    'first_name' => strtoupper($firstNameAuthorizedSignatory),
                    'last_name' => ($lastNameAuthorizedSignatory) ? strtoupper($lastNameAuthorizedSignatory) : '',
                    'first_title' => $firstTitleAuthorizedSignatory,
                    'last_title' => $lastTitleAuthorizedSignatory,
                    'rank_id' => $rankId,
                    'rank' => $rankName,
                    'register_number' => $registerNumberAuthorizedSignatory,
                    'position_id' => $positionId,
                    'position' => $positionName,
                    'identity_number' => $identityNumberAuthorizedSignatory,
                    'email' => $emailAuthorizedSignatory,
                    'phone' => $phoneAuthorizedSignatory,
                ]);

             
        }
            
            Polres::where('id', $polresId)
            ->update([
                'is_complete' => true,
            ]);

        return redirect()->route('home');
    }


    // Form Input Data Pejabat Tanda Tangan
    public function formSignatoryInput(){
        $user = Auth::getUser();

        $poldaId = $user->polda_id;
        $polresId = $user->polres_id;

        if($user->role_id != 1){
            return redirect()->route('home');
        }
    
        $polda = Polda::with(['polres'])->orderBy('name', 'asc')->get();

        $authorizedSignatories = AuthorizedSignatory::select('authorized_signatories.*', 'polres.name as polres_name', 'polda.name as polda_name')
            ->join('polres', 'polres.id', '=', 'authorized_signatories.polres_id')
            ->join('polda', 'polda.id', '=', 'polres.polda_id')
            ->orderBy('authorized_signatories.id', 'asc')
            ->groupBy('authorized_signatories.polres_id')
            ->groupBy('authorized_signatories.id')
            ->groupBy('polres.name')
            ->groupBy('polda.name')
            ->get();

        $viewData = [
            'polda' => $polda,
            'poldaId' => $poldaId,
            'polresId' => $polresId,
            'ranks' => $this->ranks,
            'positions' => $this->positions,
            'authorizedSignatories' => $authorizedSignatories,
        ];
  
        return view('icell_forms.form_signatory_input', $viewData);
    }
   
    public function formSignatoryInputStore(Request $request){
        // Validate the request...
        $request->validate([ 
            'polresAuthorizedSignatory' => 'required',

            'firstTitleAuthorizedSignatory' => 'required_if:lastTitleAuthorizedSignatory,null|required_if:lastTitleAuthorizedSignatory,|max:255',
            'firstNameAuthorizedSignatory' => 'required | max:255',
            'lastNameAuthorizedSignatory' => 'max:255',
            'lastTitleAuthorizedSignatory' => 'required_if:firstTitleAuthorizedSignatory,null|required_if:firstTitleAuthorizedSignatory,|max:255',

            'rankAuthorizedSignatory' => 'required | max:255',
            'registerNumberAuthorizedSignatory' => 'required | max:255',
            'positionAuthorizedSignatory' => 'required | max:255',
            'identityNumberAuthorizedSignatory' => 'max:16',
            'emailAuthorizedSignatory' => 'max:255',
            'phoneAuthorizedSignatory' => 'max:16',
        ],[
            'polresAuthorizedSignatory.required' => 'Polres harus diisi',

            'firstTitleAuthorizedSignatory.required_if' => 'Gelar depan harus diisi',
            'firstNameAuthorizedSignatory.required' => 'Nama depan harus diisi',
            'lastNameAuthorizedSignatory.required' => 'Nama belakang harus diisi',
            'lastTitleAuthorizedSignatory.required_if' => 'Gelar belakang harus diisi',

            'rankAuthorizedSignatory.required' => 'Pangkat harus diisi',
            'registerNumberAuthorizedSignatory.required' => 'Nomor register harus diisi',
            'positionAuthorizedSignatory.required' => 'Jabatan harus diisi',
            'identityNumberAuthorizedSignatory.required' => 'Nomor identitas harus diisi',
            'emailAuthorizedSignatory.required' => 'Email harus diisi',
            'phoneAuthorizedSignatory.required' => 'Nomor telepon harus diisi',
        ]);

        $polresId = htmlspecialchars($request->polresAuthorizedSignatory);

        $firstTitleAuthorizedSignatory = htmlspecialchars($request->firstTitleAuthorizedSignatory);
        $firstNameAuthorizedSignatory = htmlspecialchars($request->firstNameAuthorizedSignatory);
        $lastNameAuthorizedSignatory = htmlspecialchars($request->lastNameAuthorizedSignatory);
        $lastTitleAuthorizedSignatory = htmlspecialchars($request->lastTitleAuthorizedSignatory);
        
        $rankAuthorizedSignatory = htmlspecialchars($request->rankAuthorizedSignatory);
        $registerNumberAuthorizedSignatory = htmlspecialchars($request->registerNumberAuthorizedSignatory);
        $positionAuthorizedSignatory = htmlspecialchars($request->positionAuthorizedSignatory);
        $identityNumberAuthorizedSignatory = htmlspecialchars($request->identityNumberAuthorizedSignatory);
        $emailAuthorizedSignatory = htmlspecialchars($request->emailAuthorizedSignatory);
        $phoneAuthorizedSignatory = htmlspecialchars($request->phoneAuthorizedSignatory);

        $rankId = NULL;
        $rankName = NULL;
        $positionId = NULL;
        $positionName = NULL;

        foreach ($this->ranks as $rank) {
            if ($rank['id'] == $rankAuthorizedSignatory) {
                // Match found
                $rankId = $rank['id'];
                $rankName = $rank['name'];

                break;
            }
        }
        
        foreach ($this->positions as $position) {
            if ($position['id'] == $positionAuthorizedSignatory) {
                // Match found
                $positionId = $position['id'];
                $positionName = $position['name'];

                break;
            }
        }

        AuthorizedSignatory::create([
            'id' => Str::uuid(),

            'polres_id' => $polresId,

            'first_title' => $firstTitleAuthorizedSignatory,
            'first_name' => strtoupper($firstNameAuthorizedSignatory),
            'last_name' => ($lastNameAuthorizedSignatory) ? strtoupper($lastNameAuthorizedSignatory) : '',
            'last_title' => $lastTitleAuthorizedSignatory,

            'rank_id' => $rankId,
            'rank' => $rankName,
            'register_number' => $registerNumberAuthorizedSignatory,
            'position_id' => $positionId,
            'position' => $positionName,
            'identity_number' => $identityNumberAuthorizedSignatory,
            'email' => $emailAuthorizedSignatory,
            'phone' => $phoneAuthorizedSignatory,
        ]);

        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    public function formSignatoryInputDelete($id){
        $authorizedSignatory = AuthorizedSignatory::where('id', $id)->first();
        $authorizedSignatory->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }



    // AJAX REQUEST API
    public function getRegency(Request $request)
    {
        $regencies = Regency::where('province_id', $request->provinceId)->orderBy('name', 'asc')->get();
        return response()->json($regencies);
    }

    public function getDistrict(Request $request)
    {
        $districts = District::where('regency_id', $request->regencyId)->orderBy('name', 'asc')->get();
        return response()->json($districts);
    }

    public function getVillage(Request $request)
    {
        $villages = Village::where('district_id', $request->districtId)->orderBy('name', 'asc')->get();
        return response()->json($villages);
    }
    
    public function getPolres(Request $request)
    {
        $polres = Polres::where('polda_id', $request->poldaId)->orderBy('name', 'asc')->get();
        return response()->json($polres);
    }
}
