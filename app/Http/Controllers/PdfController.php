<?php

namespace App\Http\Controllers;

use App\Models\BeritaAcaraBlokirRekeningBank;
use Illuminate\Http\Request;
use Auth;
use App\Models\Accident;
use Carbon\Carbon;
//kategori 1
use App\Models\SuratTugas;
use App\Models\SuratPenyelidikan;
use App\Models\SuratPenyidikan;
use App\Models\SuratSpdp;
use App\Models\LaporanPolisi;
use App\Models\BA_penangkapan_tkp;
use App\Models\BAPemotretan;
use App\Models\BeritaAcaraPengambilanDarah;
use App\Models\LaporanHasilPenyelidikan;
use App\Models\BeritaAcaraIntrogasi;
use App\Models\SpdpUpload;

//kategori 2
use App\Models\BeritaAcaraMembawaSaksi;
use App\Models\BeritaAcaraPenyumpahanSaksi;
use App\Models\SuratPerintahMembawaSaksi;
use App\Models\BeritaAcaraPemeriksaanSaksi;
use App\Models\BeritaAcaraPemeriksaanAhli;

//kategori 3
use App\Models\SuratPanggilanTersangka;
// use App\Models\SuratPerintahPenangkapan;
use App\Models\BeritaAcaraPemeriksaanTersangka;
use App\Models\BeritaAcaraKonfrontasi;
use App\Models\BeritaAcaraPembukaanBlokirRekeningBank;
use App\Models\BeritaAcaraRekonstruksi;
use App\Models\SketTkp;
use App\Models\SuratBantuanPenangkapan;
use App\Models\BeritaPenyerahanTersangka;
// use App\Models\BeritaPelepasanTersangka;

//kategori 4
use App\Models\SuratPerintahPenahanan;
use App\Models\BeritaAcaraPenahanan;
use App\Models\PermintaanPerpanjanganPenahanan;
use App\Models\SuratPerpanjanganPenahanan;
use App\Models\BeritaPengeluaranPenahanan;
use App\Models\SuratPembatalanPenahanan;
use App\Models\SuratPencabutanPembatalanPenahanan;
use App\Models\BeritaPencabutanPembatalanPenahanan;
use App\Models\SuratPenahananLanjutan;
use App\Models\BeritaPenahananLanjutan;

//kategori 5
use App\Models\SuratIzinPenggeledahan;
use App\Models\SuratPerintahPenggeledahan;
use App\Models\SuratPersetujuanPenggeledahan;
use App\Models\BeritaAcaraPenggeledahan;

//kategori 6
use App\Models\SuratIzinPenyitaan;
use App\Models\SuratPersetujuanPenyitaan;
use App\Models\BeritaAcaraPenyitaan;
use App\Models\SuratPengirimanBerkasPerkara;
use App\Models\TandaTerimaBerkasPerkara;
use App\Models\SuratPengirimanTersangkaBarangBukti;
use App\Models\BeritaAcaraSerahTerimaTersangka;
use App\Models\SuratBantuanPenyelidikan;
use App\Models\SuratPerintahPenitipanBarangBukti;
use App\Models\SuratPerintahPengembalianBendaSitaan;

//kategori 7
use App\Models\SuratPersetujuanPenyegelan;
use App\Models\BeritaAcaraPenyegelan;
use App\Models\DaftarBarangBukti;
use App\Models\SuratBlokirRekeningBank;
use App\Models\SuratPembukaanBlokirRekeningBank;
use App\Models\SuratPencabutanBarang;
use App\Models\SuratPencabutanTersangka;
use App\Models\SuratPenyitaan;
use App\Models\SuratPenyegelan;

//kategori 8
use App\Models\SuratPermintaanBantuanLabfor;
use App\Models\SuratHasilPemeriksaanLabfor;
use App\Models\SuratPermintaanBantuanIdentifikasi;
use App\Models\SuratHasilPemeriksaanIdentifikasi;
use App\Models\KetetapanIjinKhususPemeriksaanSurat;
use App\Models\SuratPerintahPemeriksaanSurat;
use App\Models\BeritaAcaraPemeriksaanSurat;

//kategori 11
use App\Models\BeritaAcaraPenghentianPenyelidikan;
use App\Models\BeritaAcaraPenghentianPenyidikan;
use App\Models\SuratKesepakatanPerdamaian;
use App\Models\SuratKetetapanPenghentianPenyelidikan;
use App\Models\SuratKetetapanPenghentianPenyidikan;
use App\Models\SuratPencabutanPenghentianPenyelidikan;
use App\Models\SuratPencabutanPenghentianPenyidikan;
use App\Models\SuratPerintahPenghentianPenyelidikan;
use App\Models\SuratPerintahPenghentianPenyidikan;
use App\Models\SuratPerintahPenyelidikanLanjutan;
use App\Models\SuratPerintahPenyidikanLanjutan;
use App\Models\SuratPernyataan;
use App\Models\PersetujuanPejabatYangBerwenang;
use App\Models\PutusanPraPeradilan;
use App\Models\UploadSuratKetetapan;

//kategori 12
use App\Models\SuratKetetapanPenetapanTersangka;
use App\Models\SuratPerintahPenangkapan;
use App\Models\SuratMembawaMenghadapkan;
use App\Models\SuratPerintahPelepasanTersangka;
use App\Models\BeritaAcaraPenangkapan;
use App\Models\BeritaAcaraPengembalianBendaSitaan;
use App\Models\BeritaAcaraPenitipanBarangBukti;
use App\Models\BeritaAcaraPenyerahanBerkasPerkara;
use App\Models\BeritaPelepasanTersangka;
use App\Models\KetetapanIjinPenyitaan;
use App\Models\KetetapanPersetujuanPenyitaan;
use App\Models\LaporanHasilGelarPerkara;
use App\Models\LaporanHasilGelarPerkaraKhusus;
use App\Models\SP3;
use App\Models\SprintGas;
use App\Models\SuratPengantar;
use App\Models\SuratSP3;
use App\Models\SuratTandaPenerimaan;

//kategori 13
use App\Models\SuratP21\SuratP21Tahap1;
use App\Models\SuratP21\SuratP21Tahap2;

class PdfController extends Controller
{
    public function grp($route){
        $data=explode("/",$route);
        $name=$data[0];
        switch($name){
            //kategori 1
            case "laporan_polisi":
                $grp['path']=public_path('file/tugas/laporan_polisi');
            break;
            case "BA_Penangkapan":
                $grp['path']=public_path('file/tugas/BA_Penangkapan');
            break;
            case "BA_Pemotretan":
                $grp['path']=public_path('file/tugas/BA_Pemotretan');
            break;
            case "BA-pengambilan-darah":
                $grp['path']=public_path('file/tugas/BA-pengambilan-darah');
            break;
            case "laporan-hasil-penyelidikan":
                $grp['path']=public_path('file/tugas/laporan-hasil-penyelidikan');
            break;
            case "BA-introgasi":
                $grp['path']=public_path('file/tugas/BA-introgasi');
            break;
            case "SPDP-Upload":
                $grp['path']=public_path('file/tugas/SPDP-Upload');
            break;

            //kategori 2
            case "surat-perintah-membawa-saksi":
                $grp['path']=public_path('file/saksi/surat-perintah-membawa-saksi');
            break;
            case "berita-acara-membawa-saksi":
                $grp['path']=public_path('file/saksi/berita-acara-membawa-saksi');
            break;
            case "berita-acara-penyumpahan-saksi":
                $grp['path']=public_path('file/saksi/berita-acara-penyumpahan-saksi');
            break;
            case "berita-pemeriksaan-saksi":
                $grp['path']=public_path('file/saksi/berita-pemeriksaan-saksi');
            break;
            case "berita-pemeriksaan-ahli":
                $grp['path']=public_path('file/saksi/berita-pemeriksaan-ahli');
            break;

            //kategori 3
            case "surat-panggilan-tersangka":
                $grp['path']=public_path('file/tersangka/surat-panggilan-tersangka');
                break;
            // case "surat-perintah-penangkapan":
            // $grp['path']=public_path('file/tersangka/surat-perintah-penangkapan');
            //     break;
            case "berita-pemeriksaan-tersangka":
                $grp['path']=public_path('file/tersangka/berita-pemeriksaan-tersangka');
                break;
            case "berita-acara-konfrontasi":
                $grp['path']=public_path('file/tersangka/berita-acara-konfrontasi');
                break;
            case "berita-acara-rekonstruksi":
                $grp['path']=public_path('file/tersangka/berita-acara-rekonstruksi');
                break;
            case "sket-tkp":
                $grp['path']=public_path('file/tersangka/sket-tkp');
                break;
            case "surat-bantuan-penangkapan":
                $grp['path']=public_path('file/tersangka/surat-bantuan-penangkapan');
                break;
            case "penyerahan-tersangka":
                $grp['path']=public_path('file/tersangka/penyerahan-tersangka');
                break;
            // case "pelepasan-tersangka":
            //     $grp['path']=public_path('file/tersangka/pelepasan-tersangka');
            //     break;

            //kategori 4
            // case "surat-perintah-penahanan":
            //     $grp['path']=public_path('file/penahanan/surat-perintah-penahanan');
            //     break;
            case "berita-acara-penahanan":
                $grp['path']=public_path('file/penahanan/berita-acara-penahanan');
                break;
            case "perpanjangan-penahanan-hakim":
                $grp['path']=public_path('file/penahanan/perpanjangan-penahanan-hakim');
                break;
            case "surat-perpanjangan-penahanan":
                $grp['path']=public_path('file/penahanan/surat-perpanjangan-penahanan');
                break;
            case "berita-pengeluaran-penahanan":
                $grp['path']=public_path('file/penahanan/berita-pengeluaran-penahanan');
                break;
            case "permintaan-perpanjangan-penahanan":
                $grp['path']=public_path('file/penahanan/permintaan-perpanjangan-penahanan');
                break;
            case "surat-pembatalan-penahanan":
                $grp['path']=public_path('file/penahanan/surat-pembatalan-penahanan');
                break;
            case "pencabutan-pembatalan-penahanan":
                $grp['path']=public_path('file/penahanan/pencabutan-pembatalan-penahanan');
                break;
            case "berita-pembatalan-penahanan":
                $grp['path']=public_path('file/penahanan/berita-acara-pencabutan-pembatalan-penahanan');
                break;
            case "penahanan-lanjutan":
                $grp['path']=public_path('file/penahanan/surat-perintah-penahanan-lanjutan');
                break;
            case "berita-penahanan-lanjutan":
                $grp['path']=public_path('file/penahanan/berita-acara-penahanan-lanjutan');
                break;

            //kategori 5
            case "permintaan-izin-penggeledahan":
                $grp['path']=public_path('file/penggeledahan/surat-permintaan-izin-penggeledahan');
            break;
            case "perintah-penggeledahan":
                $grp['path']=public_path('file/penggeledahan/perintah-penggeledahan');
            break;
            case "persetujuan-penggeledahan":
                $grp['path']=public_path('file/penggeledahan/surat-persetujuan-penggeledahan');
            break;
            case "berita-penggeledahan":
                $grp['path']=public_path('file/penggeledahan/berita-acara-penggeledahan');
            break;

            //kategori 6
            case "surat-izin-penyitaan":
                $grp['path']=public_path('file/penyitaan/surat-izin-penyitaan');
            break;
            case "surat-persetujuan-penyitaan":
                $grp['path']=public_path('file/penyitaan/surat-persetujuan-penyitaan');
            break;
            case "berita-acara-penyitaan":
                $grp['path']=public_path('file/penyitaan/berita-acara-penyitaan');
            break;
            case "surat-pengiriman-berkas-perkara":
                $grp['path']=public_path('file/penyitaan/surat-pengiriman-berkas-perkara');
            break;
            case "tanda-terima-berkas-perkara":
                $grp['path']=public_path('file/penyitaan/tanda-terima-berkas-perkara');
            break;
            case "pengiriman-barang-bukti":
                $grp['path']=public_path('file/penyitaan/pengiriman-barang-bukti');
            break;
            case "berita-acara-terima-tersangka":
                $grp['path']=public_path('file/penyitaan/berita-acara-terima-tersangka');
            break;
            case "surat-bantuan-penyelidikan":
                $grp['path']=public_path('file/penyitaan/surat-bantuan-penyelidikan');
            break;
            case "surat-pentitipan-barang":
                $grp['path']=public_path('file/penyitaan/surat-pentitipan-barang');
            break;
            case "surat-pengembalian-sitaan":
                $grp['path']=public_path('file/penyitaan/surat-pengembalian-sitaan');
            break;
            case "berita-penitipan-barang":
                $grp['path']=public_path('file/penyitaan/berita-penitipan-barang');
            break;
            case "berita-pengembalian-sitaan":
                $grp['path']=public_path('file/penyitaan/berita-pengembalian-sitaan');
            break;
            case "ketetapan-ijin-penyitaan":
                $grp['path']=public_path('file/penyitaan/ketetapan-ijin-penyitaan');
            break;
            case "ketetapan-persetujuan-penyitaan":
                $grp['path']=public_path('file/penyitaan/ketetapan-persetujuan-penyitaan');
            break;
            case "surat-tanda-penerimaan":
                $grp['path']=public_path('file/penyitaan/surat-tanda-penerimaan');
            break;
            case "surat-pengantar":
                $grp['path']=public_path('file/penyitaan/surat-pengantar');
            break;
            case "berita-penyerahan-berkas":
                $grp['path']=public_path('file/penyitaan/berita-penyerahan-berkas');
            break;
            case "laporan-gelar-perkara":
                $grp['path']=public_path('file/penyitaan/laporan-gelar-perkara');
            break;
            case "laporan-perkara-khusus":
                $grp['path']=public_path('file/penyitaan/laporan-perkara-khusus');
            break;

            //kategori 7
            case "surat-persetujuan-penyegelan":
                $grp['path']=public_path('file/penyegelan/surat-persetujuan-penyegelan');
            break;
            case "berita-acara-penyegelan":
                $grp['path']=public_path('file/penyegelan/berita-acara-penyegelan');
            break;

            //kategori 8
            case "surat-permintaan-bantuan-labfor":
                $grp['path']=public_path('file/labfor/surat-permintaan-bantuan-labfor');
            break;
            case "surat-hasil-pemeriksaan-labfor":
                $grp['path']=public_path('file/labfor/surat-hasil-pemeriksaan-labfor');
            break;
            case "surat-bantuan-identifikasi":
                $grp['path']=public_path('file/labfor/surat-permintaan-bantuan-identifikasi');
            break;
            case "surat-pemeriksaan-identifikasi":
                $grp['path']=public_path('file/labfor/surat-hasil-pemeriksaan-identifikasi');
            break;
            case "ketetapan-khusus-surat":
                $grp['path']=public_path('file/labfor/ketetapan-khusus-surat');
            break;
            case "perintah-pemeriksaan-surat":
                $grp['path']=public_path('file/labfor/perintah-pemeriksaan-surat');
            break;
            case "berita-pemeriksaan-surat":
                $grp['path']=public_path('file/labfor/berita-pemeriksaan-surat');
            break;

            //kategori 9
            case "surat-blokir-rekening-bank":
                $grp['path']=public_path('file/rekening-bank/surat-blokir-rekening-bank');
            break;
            case "berita-acara-blokir":
                $grp['path']=public_path('file/rekening-bank/berita-acara-blokir');
            break;
            case "surat-pembukaan-blokir":
                $grp['path']=public_path('file/rekening-bank/surat-pembukaan-blokir');
            break;
            case "berita-acara-pembukaan-blokir":
                $grp['path']=public_path('file/rekening-bank/berita-acara-pembukaan-blokir');
            break;

            //kategori 10
            case "surat-pencabutan-tersangka":
                $grp['path']=public_path('file/dpo-dpb/surat-pencabutan-tersangka');
            break;
            case "surat-pencabutan-barang":
                $grp['path']=public_path('file/dpo-dpb/surat-pencabutan-barang');
            break;

            //kategori 11
            case "surat-perintah-penyelidikan":
                $grp['path']=public_path('file/penghentian/surat-perintah-penyelidikan');
            break;
            case "surat-ketetapan-penyelidikan":
                $grp['path']=public_path('file/penghentian/surat-ketetapan-penyelidikan');
            break;
            case "surat-pencabutan-penyelidikan":
                $grp['path']=public_path('file/penghentian/surat-pencabutan-penyelidikan');
            break;
            case "surat-penyelidikan-lanjutan":
                $grp['path']=public_path('file/penghentian/surat-penyelidikan-lanjutan');
            break;
            case "berita-penghentian-penyelidikan":
                $grp['path']=public_path('file/penghentian/berita-penghentian-penyelidikan');
            break;
            case "persetujuan-pejabat-berwenang":
                $grp['path']=public_path('file/penghentian/persetujuan-pejabat-berwenang');
            break;
            case "surat-perintah-penyidikan":
                $grp['path']=public_path('file/penghentian/surat-perintah-penyidikan');
            break;
            case "surat-ketetapan-penyidikan":
                $grp['path']=public_path('file/penghentian/surat-ketetapan-penyidikan');
            break;
            case "putusan-pra-peradilan":
                $grp['path']=public_path('file/penghentian/putusan-pra-peradilan');
            break;
            case "surat-pencabutan-penyidikan":
                $grp['path']=public_path('file/penghentian/surat-pencabutan-penyidikan');
            break;
            case "surat-penyidikan-lanjutan":
                $grp['path']=public_path('file/penghentian/surat-penyidikan-lanjutan');
            break;
            case "berita-penghentian-penyidikan":
                $grp['path']=public_path('file/penghentian/berita-penghentian-penyidikan');
            break;
            case "surat-pernyataan":
                $grp['path']=public_path('file/penghentian/surat-pernyataan');
            break;
            case "surat-kesepakatan-perdamaian":
                $grp['path']=public_path('file/penghentian/surat-kesepakatan-perdamaian');
            break;
            case "upload-surat-ketetapan":
                $grp['path']=public_path('file/penghentian/upload-surat-ketetapan');
            break;

            //kategori 12
            case "surat-penetapan-tersangka":
                $grp['path']=public_path('file/penangkapan/surat-penetapan-tersangka');
            break;
            case "surat-perintah-penangkapan":
                $grp['path']=public_path('file/penangkapan/surat-perintah-penangkapan');
            break;
            case "surat-membawa-menghadapkan":
                $grp['path']=public_path('file/penangkapan/surat-membawa-menghadapkan');
            break;
            case "surat-pelepasan-tersangka":
                $grp['path']=public_path('file/penangkapan/surat-pelepasan-tersangka');
            break;
            case "berita-acara-penangkapan":
                $grp['path']=public_path('file/penangkapan/berita-acara-penangkapan');
            break;
            case "pelepasan-tersangka":
                $grp['path']=public_path('file/penangkapan/pelepasan-tersangka');
            break;

            //kategori 13
            case "surat-p21-tahap-1":
                $grp['path']=public_path('file/p21/surat-p21-tahap-1');
            break;
            case "surat-p21-tahap-2":
                $grp['path']=public_path('file/p21/surat-p21-tahap-2');
            break;
        }
        $grp['name']=$name;
        // dd($grp);
        return $grp;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline'
        ];
        $grp=$this->grp($request->route()->uri);
        $content_types='application/pdf';
        // dd($id);
        $get=$grp['name'];
        switch($get){
            //kategori 1
            case"laporan_polisi":
                $find_data = LaporanPolisi::where("accident_id",$id)->orderBy("created_at","desc")->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case"BA_Penangkapan":
                $find_data = BA_penangkapan_tkp::where("accident_id",$id)->orderBy("created_at","desc")->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case"BA_Pemotretan":
                $find_data = BAPemotretan::where("accident_id",$id)->orderBy("created_at","desc")->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case"BA-pengambilan-darah":
                $find_data = BeritaAcaraPengambilanDarah::where("accident_id",$id)->orderBy("created_at","desc")->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case"laporan-hasil-penyelidikan":
                $find_data = LaporanHasilPenyelidikan::where("accident_id",$id)->orderBy("created_at","desc")->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case"BA-introgasi":
                $find_data = BeritaAcaraIntrogasi::where("accident_id",$id)->orderBy("created_at","desc")->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case"SPDP-Upload":
                $find_data = SpdpUpload::where("accident_id",$id)->orderBy("created_at","desc")->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;

            //kategori 2
            case "surat-perintah-membawa-saksi":
                $find_data = SuratPerintahMembawaSaksi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
                // dd($grp['path']);
            break;
            case "berita-acara-membawa-saksi":
                $find_data = BeritaAcaraMembawaSaksi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-acara-penyumpahan-saksi":
                $find_data = BeritaAcaraPenyumpahanSaksi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-pemeriksaan-saksi":
                $find_data = BeritaAcaraPemeriksaanSaksi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-pemeriksaan-ahli":
                $find_data = BeritaAcaraPemeriksaanAhli::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;

            //kategori 3
            case "surat-panggilan-tersangka":
                $find_data = SuratPanggilanTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            // case "surat-perintah-penangkapan":
            //     $find_data = SuratPerintahPenangkapan::where('accident_id',$id)->orderBy('created_at','desc')->first();
            //     $data=$grp['path'] .'/'.$find_data->name;
            // break;
            case "berita-pemeriksaan-tersangka":
                $find_data = BeritaAcaraPemeriksaanTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-acara-konfrontasi":
                $find_data = BeritaAcaraKonfrontasi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-acara-rekonstruksi":
                $find_data = BeritaAcaraRekonstruksi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "sket-tkp":
                $find_data = SketTkp::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-bantuan-penangkapan":
                $find_data = SuratBantuanPenangkapan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "penyerahan-tersangka":
                $find_data = BeritaPenyerahanTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            // case "pelepasan-tersangka":
            //     $find_data = BeritaPelepasanTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
            //     $data=$grp['path'] .'/'.$find_data->name;
            // break;

            // kategori 4
            // case "surat-perintah-penahanan":
            //     $find_data = SuratPerintahPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
            //     $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-acara-penahanan":
                $find_data = BeritaAcaraPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "perpanjangan-penahanan-hakim":
                $find_data = PermintaanPerpanjanganPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-perpanjangan-penahanan":
                $find_data = SuratPerpanjanganPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-pengeluaran-penahanan":
                $find_data = BeritaPengeluaranPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-pembatalan-penahanan":
                $find_data = SuratPembatalanPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "pencabutan-pembatalan-penahanan":
                $find_data = SuratPencabutanPembatalanPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-pembatalan-penahanan":
                $find_data = BeritaPencabutanPembatalanPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "penahanan-lanjutan":
                $find_data = SuratPenahananLanjutan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-penahanan-lanjutan":
                $find_data = BeritaPenahananLanjutan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;

            //kategori 5
            case "permintaan-izin-penggeledahan":
                $find_data = SuratIzinPenggeledahan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "perintah-penggeledahan":
                $find_data = SuratPerintahPenggeledahan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "persetujuan-penggeledahan":
                $find_data = SuratPersetujuanPenggeledahan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-penggeledahan":
                $find_data = BeritaAcaraPenggeledahan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;

            //kategori 6
            case "surat-izin-penyitaan":
                $find_data = SuratIzinPenyitaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-persetujuan-penyitaan":
                $find_data = SuratPersetujuanPenyitaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "daftar-barang-bukti":
                $find_data = DaftarBarangBukti::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            // case "surat-penyitaan":
            //     $find_data = SuratPenyitaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
            //     $data=$grp['path'] .'/'.$find_data->name;
            // break;
            case "berita-acara-penyitaan":
                $find_data = BeritaAcaraPenyitaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-pengiriman-berkas-perkara":
                $find_data = SuratPengirimanBerkasPerkara::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "tanda-terima-berkas-perkara":
                $find_data = TandaTerimaBerkasPerkara::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "pengiriman-barang-bukti":
                $find_data = SuratPengirimanTersangkaBarangBukti::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-acara-terima-tersangka":
                $find_data = BeritaAcaraSerahTerimaTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-bantuan-penyelidikan":
                $find_data = SuratBantuanPenyelidikan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-pentitipan-barang":
                $find_data = SuratPerintahPenitipanBarangBukti::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-pengembalian-sitaan":
                $find_data = SuratPerintahPengembalianBendaSitaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-penitipan-barang":
                $find_data = BeritaAcaraPenitipanBarangBukti::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-pengembalian-sitaan":
                $find_data = BeritaAcaraPengembalianBendaSitaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "ketetapan-ijin-penyitaan":
                $find_data = KetetapanIjinPenyitaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "ketetapan-persetujuan-penyitaan":
                $find_data = KetetapanPersetujuanPenyitaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-tanda-penerimaan":
                $find_data = SuratTandaPenerimaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-pengantar":
                $find_data = SuratPengantar::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-penyerahan-berkas":
                $find_data = BeritaAcaraPenyerahanBerkasPerkara::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "laporan-gelar-perkara":
                $find_data = LaporanHasilGelarPerkara::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "laporan-perkara-khusus":
                $find_data = LaporanHasilGelarPerkaraKhusus::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;

            //kategori 7
            case "surat-persetujuan-penyegelan":
                $find_data = SuratPersetujuanPenyegelan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-acara-penyegelan":
                $find_data = BeritaAcaraPenyegelan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;

            //kategori 8
            case "surat-permintaan-bantuan-labfor":
                $find_data = SuratPermintaanBantuanLabfor::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-hasil-pemeriksaan-labfor":
                $find_data = SuratHasilPemeriksaanLabfor::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-bantuan-identifikasi":
                $find_data = SuratPermintaanBantuanIdentifikasi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-pemeriksaan-identifikasi":
                $find_data = SuratHasilPemeriksaanIdentifikasi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "ketetapan-khusus-surat":
                $find_data = KetetapanIjinKhususPemeriksaanSurat::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "perintah-pemeriksaan-surat":
                $find_data = SuratPerintahPemeriksaanSurat::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-pemeriksaan-surat":
                $find_data = BeritaAcaraPemeriksaanSurat::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;

            //kategori 9
            case "surat-blokir-rekening-bank":
                $find_data = SuratBlokirRekeningBank::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-acara-blokir":
                $find_data = BeritaAcaraBlokirRekeningBank::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-pembukaan-blokir":
                $find_data = SuratPembukaanBlokirRekeningBank::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-acara-pembukaan-blokir":
                $find_data = BeritaAcaraPembukaanBlokirRekeningBank::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;

            //kategori 10
            case "surat-pencabutan-tersangka":
                $find_data = SuratPencabutanTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-pencabutan-barang":
                $find_data = SuratPencabutanBarang::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;

            //kategori 11
            case "surat-perintah-penyelidikan":
                $find_data = SuratPerintahPenghentianPenyelidikan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-ketetapan-penyelidikan":
                $find_data = SuratKetetapanPenghentianPenyelidikan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-pencabutan-penyelidikan":
                $find_data = SuratPencabutanPenghentianPenyelidikan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-penyelidikan-lanjutan":
                $find_data = SuratPerintahPenyelidikanLanjutan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-penghentian-penyelidikan":
                $find_data = BeritaAcaraPenghentianPenyelidikan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "persetujuan-pejabat-berwenang":
                $find_data = PersetujuanPejabatYangBerwenang::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-perintah-penyidikan":
                $find_data = SuratPerintahPenghentianPenyidikan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-ketetapan-penyidikan":
                $find_data = SuratKetetapanPenghentianPenyidikan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "putusan-pra-peradilan":
                $find_data = PutusanPraPeradilan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-pencabutan-penyidikan":
                $find_data = SuratPencabutanPenghentianPenyidikan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-penyidikan-lanjutan":
                $find_data = SuratPerintahPenyidikanLanjutan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-penghentian-penyidikan":
                $find_data = BeritaAcaraPenghentianPenyidikan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-pernyataan":
                $find_data = SuratPernyataan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-kesepakatan-perdamaian":
                $find_data = SuratKesepakatanPerdamaian::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "upload-surat-ketetapan":
                $find_data = UploadSuratKetetapan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;

            //kategori 12
            case "surat-penetapan-tersangka":
                $find_data = SuratKetetapanPenetapanTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-perintah-penangkapan":
                $find_data = SuratPerintahPenangkapan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-membawa-menghadapkan":
                $find_data = SuratMembawaMenghadapkan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-pelepasan-tersangka":
                $find_data = SuratPerintahPelepasanTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "berita-acara-penangkapan":
                $find_data = BeritaAcaraPenangkapan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "pelepasan-tersangka":
                $find_data = BeritaPelepasanTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;

            //kategori 13
            case "surat-p21-tahap-1":
                $find_data = SuratP21Tahap1::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
            case "surat-p21-tahap-2":
                $find_data = SuratP21Tahap2::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $data=$grp['path'] .'/'.$find_data->name;
            break;
        }

        return response()->file($data);


        // return response()->file(storage_path('public/file/tersangka/berita-acara-pemeriksaan/1621635896.pdf'));

        // return response() -> json($data, 200, ['Content-type'=> 'application/json; charset=utf-8']);
        // return response(file($data),200);
        // $response->header('Content-Type', ['application/pdf','UTF-8']);
        // ->header('Content-Type', 'application/json;charset=utf8');
        // dd($find_data);
        // awal return response()->file($data,$headers);

        // $find_data['data']=$find_data;
        // $find_data['direct']=$test;
        // $find_data['dokumen']=$data;
        // return view('test',$find_data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline'
        ];
        // dd($id);
        $grp=$this->grp($request->route()->uri);
        $get=$grp['name'];
        switch($get){
            //kategori 1
            // case "surat-tugas":
            //     SuratTugas::where('accident_id', $id)->delete();
            //     Accident::where('id', $id)
            //     ->update([
            //         'last_update' => Carbon::now(),
            //         'category' =>'D010101',
            //         'tipe_update' => 'HAPUS'
            //     ]);
            // break;
            case "springas":
                SprintGas::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010101',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-penyelidikan":
                SuratPenyelidikan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010102',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-penyidikan":
                SuratPenyidikan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010103',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            // case "surat-spdp":
            case "spdp":
                SuratSpdp::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010104',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "laporan_polisi":
                $find_data = LaporanPolisi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/tugas/'.$get.'/'.$name;
                unlink($path);
                LaporanPolisi::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010105',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "BA_Penangkapan":
                $find_data = BA_penangkapan_tkp::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/tugas/'.$get.'/'.$name;
                unlink($path);
                BA_penangkapan_tkp::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010106',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "BA_Pemotretan":
                $find_data = BAPemotretan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path()."/file/tugas/".$get.'/'.$name;
                unlink($path);

                BAPemotretan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010107',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "BA-pengambilan-darah":
                $find_data = BeritaAcaraPengambilanDarah::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path()."/file/tugas/".$get.'/'.$name;
                unlink($path);

                BeritaAcaraPengambilanDarah::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010108',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "laporan-hasil-penyelidikan":
                $find_data = LaporanHasilPenyelidikan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path()."/file/tugas/".$get.'/'.$name;
                unlink($path);

                LaporanHasilPenyelidikan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010109',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "BA-introgasi":
                $find_data = BeritaAcaraIntrogasi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path()."/file/tugas/".$get.'/'.$name;
                unlink($path);

                BeritaAcaraIntrogasi::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010110',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "SPDP-Upload":
                $find_data = SpdpUpload::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path()."/file/tugas/".$get.'/'.$name;
                unlink($path);

                SpdpUpload::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010112',
                    'tipe_update' => 'HAPUS'
                ]);
            break;

            //kategori 2
            case "surat-perintah-membawa-saksi":
                $find_data = SuratPerintahMembawaSaksi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/saksi/'.$get.'/'.$name;
                unlink($path);
                SuratPerintahMembawaSaksi::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D020101',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-acara-membawa-saksi":
                $find_data = BeritaAcaraMembawaSaksi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/saksi/'.$get.'/'.$name;
                unlink($path);
                BeritaAcaraMembawaSaksi::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D020102',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-acara-penyumpahan-saksi":
                $find_data = BeritaAcaraPenyumpahanSaksi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/saksi/'.$get.'/'.$name;
                unlink($path);
                BeritaAcaraPenyumpahanSaksi::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D020103',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-pemeriksaan-saksi":
                $find_data = BeritaAcaraPemeriksaanSaksi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/saksi/'.$get.'/'.$name;
                unlink($path);
                BeritaAcaraPemeriksaanSaksi::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D020104',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-pemeriksaan-ahli":
                $find_data = BeritaAcaraPemeriksaanAhli::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/saksi/'.$get.'/'.$name;
                unlink($path);
                BeritaAcaraPemeriksaanAhli::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D020105',
                    'tipe_update' => 'HAPUS'
                ]);
            break;

            //kategori 3
            case "surat-panggilan-tersangka":
                $find_data = SuratPanggilanTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/tersangka/'.$get.'/'.$name;
                unlink($path);
                SuratPanggilanTersangka::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D030101',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            // case "surat-perintah-penangkapan":
            //     $find_data = SuratPerintahPenangkapan::where('accident_id',$id)->orderBy('created_at','desc')->first();
            //     $name = $find_data->name;
            //     $path = public_path().'/file/tersangka/'.$get.'/'.$name;
            //     unlink($path);
            //     SuratPerintahPenangkapan::where('accident_id', $id)->delete();
            //     Accident::where('id', $id)
            //     ->update([
            //         'last_update' => Carbon::now(),
            //         'category' =>'D030102',
            //         'tipe_update' => 'HAPUS'
            //     ]);
            // break;
            case "berita-pemeriksaan-tersangka":
                $find_data = BeritaAcaraPemeriksaanTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/tersangka/'.$get.'/'.$name;
                unlink($path);
                BeritaAcaraPemeriksaanTersangka::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D030103',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-acara-konfrontasi":
                $find_data = BeritaAcaraKonfrontasi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/tersangka/'.$get.'/'.$name;
                unlink($path);
                BeritaAcaraKonfrontasi::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D030104',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-acara-rekonstruksi":
                $find_data = BeritaAcaraRekonstruksi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/tersangka/'.$get.'/'.$name;
                unlink($path);
                BeritaAcaraRekonstruksi::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D030105',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "sket-tkp":
                $find_data = SketTkp::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/tersangka/'.$get.'/'.$name;
                unlink($path);
                SketTkp::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D030106',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-bantuan-penangkapan":
                $find_data = SuratBantuanPenangkapan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/tersangka/'.$get.'/'.$name;
                unlink($path);
                SuratBantuanPenangkapan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D030107',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "penyerahan-tersangka":
                $find_data = BeritaPenyerahanTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/tersangka/'.$get.'/'.$name;
                unlink($path);
                BeritaPenyerahanTersangka::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D030108',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            // case "pelepasan-tersangka":
            //     $find_data = BeritaPelepasanTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
            //     $name = $find_data->name;
            //     $path = public_path().'/file/tersangka/'.$get.'/'.$name;
            //     unlink($path);
            //     BeritaPelepasanTersangka::where('accident_id', $id)->delete();
            //     Accident::where('id', $id)
            //     ->update([
            //         'last_update' => Carbon::now(),
            //         'category' =>'D030109',
            //         'tipe_update' => 'HAPUS'
            //     ]);
            // break;

            //kategori 4
            // case "surat-perintah-penahanan":
            //     $find_data = SuratPerintahPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
            //     $name = $find_data->name;
            //     $path = public_path().'/file/penahanan/'.$get.'/'.$name;
            //     unlink($path);
            //     SuratPerintahPenahanan::where('accident_id', $id)->delete();
            //     Accident::where('id', $id)
            //     ->update([
            //         'last_update' => Carbon::now(),
            //         'category' =>'D040101',
            //         'tipe_update' => 'HAPUS'
            //     ]);
            // break;
            case "berita-acara-penahanan":
                $find_data = BeritaAcaraPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/penahanan/'.$get.'/'.$name;
                unlink($path);
                BeritaAcaraPenahanan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040102',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "perpanjangan-penahanan-hakim":
                $find_data = PermintaanPerpanjanganPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/penahanan/'.$get.'/'.$name;
                unlink($path);
                PermintaanPerpanjanganPenahanan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040103',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-perpanjangan-penahanan":
                $find_data = SuratPerpanjanganPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/penahanan/'.$get.'/'.$name;
                unlink($path);
                SuratPerpanjanganPenahanan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040104',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-pengeluaran-penahanan":
                $find_data = BeritaPengeluaranPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/penahanan/'.$get.'/'.$name;
                unlink($path);
                BeritaPengeluaranPenahanan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040105',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-pembatalan-penahanan":
                $find_data = SuratPembatalanPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/penahanan/'.$get.'/'.$name;
                unlink($path);
                SuratPembatalanPenahanan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040106',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "pencabutan-pembatalan-penahanan":
                $find_data = SuratPencabutanPembatalanPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/penahanan/'.$get.'/'.$name;
                unlink($path);
                SuratPencabutanPembatalanPenahanan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040107',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-pembatalan-penahanan":
                $find_data = BeritaPencabutanPembatalanPenahanan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                BeritaPencabutanPembatalanPenahanan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040108',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "penahanan-lanjutan":
                $find_data = SuratPenahananLanjutan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratPenahananLanjutan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040109',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-penahanan-lanjutan":
                $find_data = BeritaPenahananLanjutan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                BeritaPenahananLanjutan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040110',
                    'tipe_update' => 'HAPUS'
                ]);
            break;

            //kategori 5
            case "permintaan-izin-penggeledahan":
                $find_data = SuratIzinPenggeledahan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratIzinPenggeledahan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D050101',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "perintah-penggeledahan":
                $find_data = SuratPerintahPenggeledahan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratPerintahPenggeledahan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D050102',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "persetujuan-penggeledahan":
                $find_data = SuratPersetujuanPenggeledahan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratPersetujuanPenggeledahan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D050103',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-penggeledahan":
                $find_data = BeritaAcaraPenggeledahan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                BeritaAcaraPenggeledahan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D050104',
                    'tipe_update' => 'HAPUS'
                ]);
            break;

            //kategori 6
            case "surat-izin-penyitaan":
                $find_data = SuratIzinPenyitaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratIzinPenyitaan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060101',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-persetujuan-penyitaan":
                $find_data = SuratPersetujuanPenyitaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratPersetujuanPenyitaan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060102',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-penyitaan":
                SuratPenyitaan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060104',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-acara-penyitaan":
                $find_data = BeritaAcaraPenyitaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                BeritaAcaraPenyitaan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060105',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-pengiriman-berkas-perkara":
                $find_data = SuratPengirimanBerkasPerkara::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratPengirimanBerkasPerkara::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060106',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "tanda-terima-berkas-perkara":
                $find_data = TandaTerimaBerkasPerkara::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                TandaTerimaBerkasPerkara::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060107',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "pengiriman-barang-bukti":
                $find_data = SuratPengirimanTersangkaBarangBukti::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratPengirimanTersangkaBarangBukti::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060108',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-acara-terima-tersangka":
                $find_data = BeritaAcaraSerahTerimaTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                BeritaAcaraSerahTerimaTersangka::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060109',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-bantuan-penyelidikan":
                $find_data = SuratBantuanPenyelidikan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratBantuanPenyelidikan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060110',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-pentitipan-barang":
                $find_data = SuratPerintahPenitipanBarangBukti::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratPerintahPenitipanBarangBukti::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060111',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-pengembalian-sitaan":
                $find_data = SuratPerintahPengembalianBendaSitaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratPerintahPengembalianBendaSitaan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060112',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-penitipan-barang":
                $find_data = BeritaAcaraPenitipanBarangBukti::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                BeritaAcaraPenitipanBarangBukti::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D06013',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-pengembalian-sitaan":
                $find_data = BeritaAcaraPengembalianBendaSitaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                BeritaAcaraPengembalianBendaSitaan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D06014',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "ketetapan-ijin-penyitaan":
                $find_data = KetetapanIjinPenyitaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                KetetapanIjinPenyitaan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D06015',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "ketetapan-persetujuan-penyitaan":
                $find_data = KetetapanPersetujuanPenyitaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                KetetapanPersetujuanPenyitaan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D06016',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-tanda-penerimaan":
                $find_data = SuratTandaPenerimaan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratTandaPenerimaan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D06017',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-pengantar":
                $find_data = SuratPengantar::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratPengantar::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D06018',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-penyerahan-berkas":
                $find_data = BeritaAcaraPenyerahanBerkasPerkara::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                BeritaAcaraPenyerahanBerkasPerkara::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D06019',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "laporan-gelar-perkara":
                $find_data = LaporanHasilGelarPerkara::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                LaporanHasilGelarPerkara::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060120',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "laporan-perkara-khusus":
                $find_data = LaporanHasilGelarPerkaraKhusus::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                LaporanHasilGelarPerkaraKhusus::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060121',
                    'tipe_update' => 'HAPUS'
                ]);
            break;

            //kategori 7
            case "surat-persetujuan-penyegelan":
                $find_data = SuratPersetujuanPenyegelan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratPersetujuanPenyegelan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D070101',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-acara-penyegelan":
                $find_data = BeritaAcaraPenyegelan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                BeritaAcaraPenyegelan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D070102',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-penyegelan":
                SuratPenyegelan::where('accident_id', $id)->delete();
            break;

           //kategori 8
            case "surat-permintaan-bantuan-labfor":
                $find_data = SuratPermintaanBantuanLabfor::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratPermintaanBantuanLabfor::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D080101',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-hasil-pemeriksaan-labfor":
                $find_data = SuratHasilPemeriksaanLabfor::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratHasilPemeriksaanLabfor::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D080102',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-bantuan-identifikasi":
                $find_data = SuratPermintaanBantuanIdentifikasi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratPermintaanBantuanIdentifikasi::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D080103',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-pemeriksaan-identifikasi":
                $find_data = SuratHasilPemeriksaanIdentifikasi::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = $grp['path'].'/'.$name;
                unlink($path);
                SuratHasilPemeriksaanIdentifikasi::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D080104',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "ketetapan-khusus-surat":
                $find_data = KetetapanIjinKhususPemeriksaanSurat::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                    $path = $grp['path'].'/'.$name;
                    unlink($path);
                    KetetapanIjinKhususPemeriksaanSurat::where('accident_id', $id)->delete();
                    Accident::where('id', $id)
                    ->update([
                        'last_update' => Carbon::now(),
                        'category' =>'D080105',
                        'tipe_update' => 'HAPUS'
                    ]);
            break;
            case "perintah-pemeriksaan-surat":
                $find_data = SuratPerintahPemeriksaanSurat::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                    $path = $grp['path'].'/'.$name;
                    unlink($path);
                    SuratPerintahPemeriksaanSurat::where('accident_id', $id)->delete();
                    Accident::where('id', $id)
                    ->update([
                        'last_update' => Carbon::now(),
                        'category' =>'D080106',
                        'tipe_update' => 'HAPUS'
                    ]);
            break;
            case "berita-pemeriksaan-surat":
                $find_data = BeritaAcaraPemeriksaanSurat::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                    $path = $grp['path'].'/'.$name;
                    unlink($path);
                    BeritaAcaraPemeriksaanSurat::where('accident_id', $id)->delete();
                    Accident::where('id', $id)
                    ->update([
                        'last_update' => Carbon::now(),
                        'category' =>'D080107',
                        'tipe_update' => 'HAPUS'
                    ]);
            break;

            //kategori 9
            case "surat-blokir-rekening-bank":
                SuratBlokirRekeningBank::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D090101',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-acara-blokir":
                BeritaAcaraBlokirRekeningBank::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D090102',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-pembukaan-blokir":
                SuratPembukaanBlokirRekeningBank::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D090103',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-acara-pembukaan-blokir":
                BeritaAcaraPembukaanBlokirRekeningBank::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D090104',
                    'tipe_update' => 'HAPUS'
                ]);
            break;

            //kategori 10
            case "surat-pencabutan-terangka":
                SuratPencabutanTersangka::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D100101',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-pencabutan-barang":
                SuratPencabutanBarang::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D100102',
                    'tipe_update' => 'HAPUS'
                ]);
            break;

            //kategori 11
            case "surat-perintah-penyelidikan":
                SuratPerintahPenghentianPenyelidikan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D110101',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-ketetapan-penyelidikan":
                SuratKetetapanPenghentianPenyelidikan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D110102',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-pencabutan-penyelidikan":
                SuratPencabutanPenghentianPenyelidikan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D110103',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-penyelidikan-lanjutan":
                SuratPerintahPenyelidikanLanjutan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D110104',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-penghentian-penyelidikan":
                BeritaAcaraPenghentianPenyelidikan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D110105',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "persetujuan-pejabat-berwenang":
                PersetujuanPejabatYangBerwenang::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D110106',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-perintah-penyidikan":
                SP3::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D110107',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-ketetapan-penyidikan":
                SuratKetetapanPenghentianPenyidikan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D110108',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "putusan-pra-peradilan":
                PutusanPraPeradilan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D110109',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-pencabutan-penyidikan":
                SuratPencabutanPenghentianPenyidikan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D110110',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-penyidikan-lanjutan":
                SuratPerintahPenyidikanLanjutan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D110111',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-penghentian-penyidikan":
                BeritaAcaraPenghentianPenyidikan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D110112',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-pernyataan":
                SuratPernyataan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D110113',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-kesepakatan-perdamaian":
                SuratKesepakatanPerdamaian::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D110114',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "upload-surat-ketetapan":
                UploadSuratKetetapan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D110115',
                    'tipe_update' => 'HAPUS'
                ]);
            break;

            //kategori 12
            // case "surat-penetapan-tersangka":
            //     SuratKetetapanPenetapanTersangka::where('accident_id', $id)->delete();
            //     Accident::where('id', $id)
            //     ->update([
            //         'last_update' => Carbon::now(),
            //         'category' => 'D120101',
            //         'tipe_update' => 'HAPUS'
            //     ]);
            // break;
            case "surat-penetapan-tersangka":
                $find_data = SuratKetetapanPenetapanTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/penangkapan/'.$get.'/'.$name;
                unlink($path);
                SuratKetetapanPenetapanTersangka::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D120101',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-perintah-penangkapan":
                $find_data = SuratPerintahPenangkapan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/penangkapan/'.$get.'/'.$name;
                unlink($path);
                SuratPerintahPenangkapan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D120102',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-membawa-menghadapkan":
                $find_data = SuratMembawaMenghadapkan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/penangkapan/'.$get.'/'.$name;
                unlink($path);
                SuratMembawaMenghadapkan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D120103',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-pelepasan-tersangka":
                $find_data = SuratPerintahPelepasanTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/penangkapan/'.$get.'/'.$name;
                unlink($path);
                SuratPerintahPelepasanTersangka::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D120104',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "berita-acara-penangkapan":
                $find_data = BeritaAcaraPenangkapan::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/penangkapan/'.$get.'/'.$name;
                unlink($path);
                BeritaAcaraPenangkapan::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D120105',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "pelepasan-tersangka":
                $find_data = BeritaPelepasanTersangka::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                $path = public_path().'/file/penangkapan/'.$get.'/'.$name;
                unlink($path);
                BeritaPelepasanTersangka::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D120106',
                    'tipe_update' => 'HAPUS'
                ]);
            break;

            //kategori 13
            case "surat-p21-tahap-1":
                $find_data = SuratP21Tahap1::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                // $path = public_path().'/file/p21/'.$get.'/'.$name;
                // unlink($path);
                SuratP21Tahap1::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D120101',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
            case "surat-p21-tahap-2":
                $find_data = SuratP21Tahap2::where('accident_id',$id)->orderBy('created_at','desc')->first();
                $name = $find_data->name;
                // $path = public_path().'/file/p21/'.$get.'/'.$name;
                // unlink($path);
                SuratP21Tahap2::where('accident_id', $id)->delete();
                Accident::where('id', $id)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D120102',
                    'tipe_update' => 'HAPUS'
                ]);
            break;
        }
        return back();
    }
}
