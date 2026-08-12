<?php

namespace App\Http\Controllers;

use App\Models\File;
use Auth;
use Illuminate\Http\Request;
use App\Models\Accident;
use Carbon\Carbon;

use App\Models\SuratPerintahMembawaSaksi;
use App\Models\LaporanPolisi;
use App\Models\BA_penangkapan_tkp;
use App\Models\BAPemotretan;
use App\Models\BeritaAcaraMembawaSaksi;
use App\Models\BeritaAcaraPenyumpahanSaksi;
use App\Models\BeritaAcaraPemeriksaanTersangka;
use App\Models\BeritaAcaraKonfrontasi;
use App\Models\BeritaAcaraRekonstruksi;
use App\Models\SketTkp;
use App\Models\SuratBantuanPenangkapan;
// use App\Models\BeritaPelepasanTersangka;
use App\Models\BeritaPenyerahanTersangka;
use App\Models\BeritaAcaraPengambilanDarah;
use App\Models\LaporanHasilPenyelidikan;
use App\Models\BeritaAcaraIntrogasi;
use App\Models\SpdpUpload;

use App\Models\BeritaPenahananLanjutan;
use App\Models\BeritaPencabutanPembatalanPenahanan;
use App\Models\BeritaPengeluaranPenahanan;
use App\Models\SuratPembatalanPenahanan;
use App\Models\SuratPenahananLanjutan;
use App\Models\SuratPencabutanPembatalanPenahanan;
use App\Models\SuratPerpanjanganPenahanan;
Use App\Models\BeritaAcaraPemeriksaanSaksi;
Use App\Models\BeritaAcaraPemeriksaanAhli;

use App\Models\SuratIzinPenggeledahan;
use App\Models\SuratPerintahPenggeledahan;
use App\Models\SuratPersetujuanPenggeledahan;
use App\Models\BeritaAcaraPenggeledahan;

use App\Models\SuratIzinPenyitaan;
use App\Models\SuratPersetujuanPenyitaan;
use App\Models\BeritaAcaraPenyitaan;

use App\Models\SuratPersetujuanPenyegelan;
use App\Models\BeritaAcaraPenyegelan;

use App\Models\SuratPermintaanBantuanLabfor;
use App\Models\SuratHasilPemeriksaanLabfor;
use App\Models\SuratPermintaanBantuanIdentifikasi;
use App\Models\SuratHasilPemeriksaanIdentifikasi;
use App\Models\SuratBantuanPenyelidikan;
use App\Models\KetetapanIjinKhususPemeriksaanSurat;
use App\Models\SuratPerintahPemeriksaanSurat;
use App\Models\BeritaAcaraPemeriksaanSurat;

use App\Models\SuratBlokirRekeningBank;
use App\Models\BeritaAcaraBlokirRekeningBank;
use App\Models\SuratPembukaanBlokirRekeningBank;
use App\Models\BeritaAcaraPembukaanBlokirRekeningBank;
use App\Models\BeritaAcaraPenahanan;
use App\Models\BeritaAcaraSerahTerimaTersangka;
use App\Models\DaftarBarangBukti;
use App\Models\PermintaanPerpanjanganPenahanan;
use App\Models\SuratPanggilanTersangka;
use App\Models\SuratPencabutanBarang;
use App\Models\SuratPencabutanTersangka;
use App\Models\SuratPengirimanBerkasPerkara;
use App\Models\SuratPengirimanTersangkaBarangBukti;
use App\Models\SuratPenyitaan;
use App\Models\SuratPerintahPenahanan;
use App\Models\TandaTerimaBerkasPerkara;

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

use App\Models\SuratKetetapanPenetapanTersangka;
use App\Models\SuratPerintahPenangkapan;
use App\Models\SuratMembawaMenghadapkan;
use App\Models\SuratPerintahPelepasanTersangka;
use App\Models\BeritaAcaraPenangkapan;
use App\Models\BeritaAcaraPenitipanBarangBukti;
use App\Models\BeritaPelepasanTersangka;
use App\Models\SuratPerintahPengembalianBendaSitaan;
use App\Models\SuratPerintahPenitipanBarangBukti;
use App\Models\BeritaAcaraPengembalianBendaSitaan;
use App\Models\BeritaAcaraPenyerahanBerkasPerkara;
use App\Models\KetetapanIjinPenyitaan;
use App\Models\KetetapanPersetujuanPenyitaan;
use App\Models\LaporanHasilGelarPerkara;
use App\Models\LaporanHasilGelarPerkaraKhusus;
use App\Models\SuratPengantar;
use App\Models\SuratTandaPenerimaan;
use App\Models\UploadSuratKetetapan;

use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function fileUploadPost(Request $request)
    {
        $user=Auth::getUser()->username;
        $request->validate([
            // 'file' => 'required|file|mimes:jpg,jpeg,bmp,png,doc,docx,csv,rtf,xlsx,xls,txt,pdf',
            'file' => 'required|file|max:30000|mimes:doc,docx,txt,pdf',
        ]);

        $selra=$request->update_selra;
        $upload=$request->form_id;
        $accident=$request->accident_id;
        // dd($upload);

        $fileName = null;

        switch($upload){
            //start tugas ketegori 1

            case 'laporan_polisi':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/tugas/laporan_polisi') ,$fileName);
                /* Store $fileName name in DATABASE from HERE */
                LaporanPolisi::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D010105','initial'=>'laporan-polisi', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010105',
                    'tipe_update' => 'UPLOAD'
                ]);
            break;
            case 'BA_Penangkapan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/tugas/BA_Penangkapan') ,$fileName);
                /* Store $fileName name in DATABASE from HERE */
                BA_penangkapan_tkp::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D010106','initial'=>'Berita-acara-penangkapan-tkp', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010106',
                    'tipe_update' => 'UPLOAD'
                ]);
            break;
            case 'BA_Pemotretan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/tugas/BA_Pemotretan') ,$fileName);
                /* Store $fileName name in DATABASE from HERE */
                BAPemotretan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D010107','initial'=>'Berita-acara-pemotretan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010107',
                    'tipe_update' => 'UPLOAD'
                ]);
            break;
            case 'BA-pengambilan-darah':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/tugas/BA-pengambilan-darah') ,$fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraPengambilanDarah::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D010108','initial'=>'Berita-acara-pengambilan-darah', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010108',
                    'tipe_update' => 'UPLOAD'
                ]);
            break;
            case 'laporan-hasil-penyelidikan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/tugas/laporan-hasil-penyelidikan') ,$fileName);
                /* Store $fileName name in DATABASE from HERE */
                LaporanHasilPenyelidikan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D010109','initial'=>'Laporan-hasil-penyelidikan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010109',
                    'tipe_update' => 'UPLOAD'
                ]);
            break;
            case 'BA-introgasi':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/tugas/BA-introgasi') ,$fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraIntrogasi::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D010110','initial'=>'Berita-acara-introgasi', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010110',
                    'tipe_update' => 'UPLOAD'
                ]);
            break;
            case 'SPDP-Upload':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/tugas/SPDP-Upload') ,$fileName);
                /* Store $fileName name in DATABASE from HERE */
                SpdpUpload::create(['id'=>Str::uuid(),'accident_id'=>$accident,'name' => $fileName,'category' => 'D010112','initial'=>'SPDP-Upload', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D010112',
                    'tipe_update' => 'UPLOAD'
                ]);
            break;

            //end tugas kategori 1

            //start saksi kategori 2
            case 'form_perintah_membawa_saksi':
                // $fileName = time().'.'.$request->file->extension();
                // $fileName = $request->file->getClientOriginalName().'.'.$request->file->extension();
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/saksi/surat-perintah-membawa-saksi') ,$fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPerintahMembawaSaksi::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D020101','initial'=>'surat-perintah-membawa-saksi', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D020101',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_acara_membawa_saksi':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/saksi/berita-acara-membawa-saksi'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraMembawaSaksi::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D020102','initial'=>'berita-acara-membawa-saksi', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D020102',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_acara_penyumpahan_saksi':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/saksi/berita-acara-penyumpahan-saksi'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraPenyumpahanSaksi::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D020103','initial'=>'berita-acara-penyumpahan-saksi', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D020103',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_pemeriksaan_saksi':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/saksi/berita-pemeriksaan-saksi'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraPemeriksaanSaksi::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D020104','initial'=>'berita-pemeriksaan-saksi', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D020104',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_pemeriksaan_ahli':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/saksi/berita-pemeriksaan-ahli'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraPemeriksaanAhli::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D020105','initial'=>'berita-pemeriksaan-ahli', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D020105',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            //end saksi

            //start tersangka kategori 3
            case 'surat_panggilan_tersangka':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/tersangka/surat-panggilan-tersangka'), $fileName);
                SuratPanggilanTersangka::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D030101', 'initial'=>'surat-panggilan-tersangka', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D030101',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            // case 'surat_perintah_penangkapan':
            //     $fileName = uniqid($accident). $request->file->getClientOriginalName();
            //     $request->file->move(public_path('file/tersangka/surat-perintah-penangkapan'), $fileName);
            //     SuratPerintahPenangkapan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D030102', 'initial'=>'surat-perintah-penangkapan', 'created_by'=>$user]);
            //     Accident::where('id', $accident)
            //     ->update([
            //         'last_update' => Carbon::now(),
            //         'category' =>'D030102',
            //         'tipe_update' => 'UPLOAD'
            //     ]);
            //     break;
            case 'berita_acara_pemeriksaan_tersangka':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/tersangka/berita-pemeriksaan-tersangka'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraPemeriksaanTersangka::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D030103', 'initial'=>'berita-pemeriksaan-tersangka', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D030103',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_acara_konfrontasi':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/tersangka/berita-acara-konfrontasi'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraKonfrontasi::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D030104', 'initial'=>'berita-acara-konfrontasi', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D030104',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_acara_rekonstruksi':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/tersangka/berita-acara-rekonstruksi'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraRekonstruksi::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D030105', 'initial'=>'berita-acara-rekonstruksi', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D030105',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'sket_tkp':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/tersangka/sket-tkp'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SketTkp::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D030106', 'initial'=>'sket-tkp', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D030106',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_bantuan_penangkapan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/tersangka/surat-bantuan-penangkapan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratBantuanPenangkapan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D030107', 'initial'=>'surat-bantuan-penangkapan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D030107',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'penyerahan_tersangka':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/tersangka/penyerahan-tersangka'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaPenyerahanTersangka::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D030108', 'initial'=>'penyerahan-tersangka', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D030108',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            // case 'pelepasan_tersangka':
            //     $fileName = uniqid($accident). $request->file->getClientOriginalName();
            //     $request->file->move(public_path('file/tersangka/pelepasan-tersangka'), $fileName);
            //     /* Store $fileName name in DATABASE from HERE */
            //     BeritaPelepasanTersangka::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D030109', 'initial'=>'pelepasan-tersangka', 'created_by'=>$user]);
            //     Accident::where('id', $accident)
            //     ->update([
            //         'last_update' => Carbon::now(),
            //         'category' =>'D030109',
            //         'tipe_update' => 'UPLOAD'
            //     ]);
            //     break;
            //end tersangka

            //start penahanan kategori 4
            // case 'surat_perintah_penahanan':
            //     $fileName = uniqid($accident). $request->file->getClientOriginalName();
            //     $request->file->move(public_path('file/penahanan/surat-perintah-penahanan'), $fileName);
            //     SuratPerintahPenahanan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D040101', 'initial'=>'surat-perintah-penahanan', 'created_by'=>$user]);
            //     Accident::where('id', $accident)
            //     ->update([
            //         'last_update' => Carbon::now(),
            //         'category' =>'D040101',
            //         'tipe_update' => 'UPLOAD'
            //     ]);
            //     break;
            case 'berita_acara_penahanan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penahanan/berita-acara-penahanan'), $fileName);
                BeritaAcaraPenahanan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D040102', 'initial'=>'berita-acara-penahanan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040102',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'permintaan_perpanjangan_penahanan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penahanan/perpanjangan-penahanan-hakim'), $fileName);
                PermintaanPerpanjanganPenahanan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D040103', 'initial'=>'perpanjangan-penahanan-hakim', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040103',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_perintah_perpanjangan_penahanan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penahanan/surat-perpanjangan-penahanan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPerpanjanganPenahanan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D040104', 'initial'=>'surat-perpanjangan-penahanan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040104',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_acara_pengeluaran_penahanan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penahanan/berita-pengeluaran-penahanan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaPengeluaranPenahanan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D040105', 'initial'=>'berita-pengeluaran-penahanan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040105',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_pembatalan_penahanan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penahanan/surat-pembatalan-penahanan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPembatalanPenahanan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D040106', 'initial'=>'surat-pembatalan-penahanan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040106',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_pencabutan_pembatalan_penahanan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penahanan/pencabutan-pembatalan-penahanan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPencabutanPembatalanPenahanan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D040107', 'initial'=>'pencabutan-pembatalan-penahanan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040107',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_acara_pencabutan_pembatalan_penahanan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penahanan/berita-acara-pencabutan-pembatalan-penahanan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaPencabutanPembatalanPenahanan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D040108', 'initial'=>'berita-pembatalan-penahanan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040108',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_perintah_penahanan_lanjutan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penahanan/surat-perintah-penahanan-lanjutan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPenahananLanjutan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D040109', 'initial'=>'penahanan-lanjutan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040109',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_acara_penahanan_lanjutan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penahanan/berita-acara-penahanan-lanjutan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaPenahananLanjutan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D040110', 'initial'=>'berita-penahanan-lanjutan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D040110',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            //end tersangka

            //start penggeledahan kategori 5
            case 'surat_perintah_izin_penggeledahan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penggeledahan/surat-permintaan-izin-penggeledahan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratIzinPenggeledahan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D050101', 'initial'=>'permintaan-izin-penggeledahan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D050101',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_perintah_penggeledahan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penggeledahan/perintah-penggeledahan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPerintahPenggeledahan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D050102', 'initial'=>'perintah-penggeledahan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D050102',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_persetujuan_penggeledahan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penggeledahan/surat-persetujuan-penggeledahan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPersetujuanPenggeledahan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D050103', 'initial'=>'persetujuan-penggeledahan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D050103',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_acara_penggeledahan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penggeledahan/berita-acara-penggeledahan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraPenggeledahan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D050104', 'initial'=>'berita-penggeledahan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D050104',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            //end penggeledahan

            //start penyitaan kategori 6
            case 'surat_izin_penyitaan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/surat-izin-penyitaan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratIzinPenyitaan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060101', 'initial'=>'surat-izin-penyitaan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060101',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_persetujuan_penyitaan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/surat-persetujuan-penyitaan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPersetujuanPenyitaan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060102', 'initial'=>'surat-persetujuan-penyitaan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060102',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'daftar_barang_bukti':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/daftar-barang-bukti'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                DaftarBarangBukti::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060103', 'initial'=>'daftar-barang-bukti', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060103',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_penyitaan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/surat-penyitaan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPenyitaan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060104', 'initial'=>'surat-penyitaan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060104',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_acara_penyitaan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/berita-acara-penyitaan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraPenyitaan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060105', 'initial'=>'berita-acara-penyitaan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060105',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_pengiriman_berkas_perkara':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/surat-pengiriman-berkas-perkara'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPengirimanBerkasPerkara::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060106', 'initial'=>'surat-pengiriman-berkas-perkara', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060106',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'tanda_terima_berkas_perkara':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/tanda-terima-berkas-perkara'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                TandaTerimaBerkasPerkara::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060107', 'initial'=>'tanda-terima-berkas-perkara', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060107',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_pengiriman_tersangka_barang_bukti':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/pengiriman-barang-bukti'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPengirimanTersangkaBarangBukti::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060108', 'initial'=>'pengiriman-barang-bukti', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060108',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_acara_serah_terima_tersangka':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/berita-acara-terima-tersangka'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraSerahTerimaTersangka::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060109', 'initial'=>'berita-acara-terima-tersangka', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060109',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_bantuan_penyelidikan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/surat-bantuan-penyelidikan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratBantuanPenyelidikan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060110', 'initial'=>'surat-bantuan-penyelidikan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060110',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_pentitipan_barang':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/surat-pentitipan-barang'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPerintahPenitipanBarangBukti::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060111', 'initial'=>'surat-pentitipan-barang', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060111',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_pengembalian_sitaan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/surat-pengembalian-sitaan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPerintahPengembalianBendaSitaan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060112', 'initial'=>'surat-pengembalian-sitaan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060112',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_penitipan_barang':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/berita-penitipan-barang'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraPenitipanBarangBukti::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060113', 'initial'=>'berita-penitipan-barang', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060113',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_pengembalian_sitaan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/berita-pengembalian-sitaan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraPengembalianBendaSitaan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060114', 'initial'=>'berita-pengembalian-sitaan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060114',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'ketetapan_ijin_penyitaan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/ketetapan-ijin-penyitaan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                KetetapanIjinPenyitaan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060115', 'initial'=>'ketetapan-ijin-penyitaan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060115',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'ketetapan_persetujuan_penyitaan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/ketetapan-persetujuan-penyitaan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                KetetapanPersetujuanPenyitaan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060116', 'initial'=>'ketetapan-persetujuan-penyitaan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060116',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_tanda_penerimaan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/surat-tanda-penerimaan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratTandaPenerimaan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060117', 'initial'=>'surat-tanda-penerimaan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060117',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_pengantar':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/surat-pengantar'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPengantar::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060118', 'initial'=>'surat-pengantar', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060118',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_penyerahan_berkas':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/berita-penyerahan-berkas'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraPenyerahanBerkasPerkara::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060119', 'initial'=>'berita-penyerahan-berkas', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060119',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'laporan_gelar_perkara':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/laporan-gelar-perkara'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                LaporanHasilGelarPerkara::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060120', 'initial'=>'laporan-gelar-perkara', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060120',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'laporan_perkara_khusus':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyitaan/laporan-perkara-khusus'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                LaporanHasilGelarPerkaraKhusus::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D060121', 'initial'=>'laporan-perkara-khusus', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D060121',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;

            //end penyitaan kategori 6

            //start penyegelan kategori 7
            case 'surat_persetujuan_penyegelan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyegelan/surat-persetujuan-penyegelan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPersetujuanPenyegelan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D070101', 'initial'=>'surat-persetujuan-penyegelan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D070101',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_acara_penyegelan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penyegelan/berita-acara-penyegelan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraPenyegelan::create(['accident_id'=>$accident,'name' => $fileName, 'category' => 'D070102', 'initial'=>'berita-acara-penyegelan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D070102',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            //end penyegelan kategori 7


            //start labfor kategori 8
            case 'surat_permintaan_bantuan_labfor':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/labfor/surat-permintaan-bantuan-labfor'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPermintaanBantuanLabfor::create(['accident_id'=>$accident,'name' => $fileName ,'category' => 'D080101','initial'=>'surat-permintaan-bantuan-labfor', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D080101',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_hasil_pemeriksaan_labfor':
                   $fileName = uniqid($accident). $request->file->getClientOriginalName();
                    $request->file->move(public_path('file/labfor/surat-hasil-pemeriksaan-labfor'), $fileName);
                    /* Store $fileName name in DATABASE from HERE */
                SuratHasilPemeriksaanLabfor::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D080102','initial'=>'surat-hasil-pemeriksaan-labfor', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D080102',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_permintaan_bantuan_identifikasi':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/labfor/surat-permintaan-bantuan-identifikasi'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPermintaanBantuanIdentifikasi::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D080103','initial'=>'surat-bantuan-identifikasi', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D080103',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_hasil_pemeriksaan_identifikasi':
                   $fileName = uniqid($accident). $request->file->getClientOriginalName();
                    $request->file->move(public_path('file/labfor/surat-hasil-pemeriksaan-identifikasi'), $fileName);
                    /* Store $fileName name in DATABASE from HERE */
                SuratHasilPemeriksaanIdentifikasi::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D080104','initial'=>'surat-pemeriksaan-identifikasi', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D080104',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'ketetapan_khusus_surat':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/labfor/ketetapan-khusus-surat'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
            KetetapanIjinKhususPemeriksaanSurat::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D080105','initial'=>'ketetapan-khusus-surat', 'created_by'=>$user]);
            Accident::where('id', $accident)
            ->update([
                'last_update' => Carbon::now(),
                'category' =>'D080105',
                'tipe_update' => 'UPLOAD'
            ]);
            break;
            case 'perintah_pemeriksaan_surat':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/labfor/perintah-pemeriksaan-surat'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
            SuratPerintahPemeriksaanSurat::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D080106','initial'=>'perintah-pemeriksaan-surat', 'created_by'=>$user]);
            Accident::where('id', $accident)
            ->update([
                'last_update' => Carbon::now(),
                'category' =>'D080106',
                'tipe_update' => 'UPLOAD'
            ]);
            break;
            case 'berita_pemeriksaan_surat':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/labfor/berita-pemeriksaan-surat'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
            BeritaAcaraPemeriksaanSurat::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D080107','initial'=>'berita-pemeriksaan-surat', 'created_by'=>$user]);
            Accident::where('id', $accident)
            ->update([
                'last_update' => Carbon::now(),
                'category' =>'D080107',
                'tipe_update' => 'UPLOAD'
            ]);
            break;
            //end labfor kategori 8

            //start rekening bank kategori 9
            case 'surat_blokir_rekening_bank':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/rekening-bank/surat-blokir-rekening-bank'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratBlokirRekeningBank::create(['accident_id'=>$accident,'name' => $fileName ,'category' => 'D090101','initial'=>'surat-blokir-rekening-bank', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D090101',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_acara_blokir_rekening_bank':
                   $fileName = uniqid($accident). $request->file->getClientOriginalName();
                    $request->file->move(public_path('file/rekening-bank/berita-acara-blokir'), $fileName);
                    /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraBlokirRekeningBank::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D090102','initial'=>'berita-acara-blokir', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D090102',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_pembukaan_blokir_rekening_bank':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/rekening-bank/surat-pembukaan-blokir'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPembukaanBlokirRekeningBank::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D090103','initial'=>'surat-pembukaan-blokir', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D090103',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_acara_pembukaan_blokir_rekening_bank':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/rekening-bank/berita-acara-pembukaan-blokir'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraPembukaanBlokirRekeningBank::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D090104','initial'=>'berita-acara-pembukaan-blokir', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D090104',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            //end rekening bank kategori 9

            //start dpo-dpb kategori 10
            case 'surat_pencabutan_tersangka':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/dpo-dpb/surat-pencabutan-tersangka'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPencabutanTersangka::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D100101','initial'=>'surat-pencabutan-tersangka', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D100101',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_pencabutan_barang':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/dpo-dpb/surat-pencabutan-barang'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPencabutanBarang::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D100102','initial'=>'surat-pencabutan-barang', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D100102',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            //end dpo-dpb kategori 10

            //start penghentian kategori 11
            case 'surat_perintah_penyelidikan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penghentian/surat-perintah-penyelidikan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPerintahPenghentianPenyelidikan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D110101','initial'=>'surat-perintah-penyidikan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D110101',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_ketetapan_penyelidikan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penghentian/surat-ketetapan-penyelidikan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratKetetapanPenghentianPenyelidikan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D110102','initial'=>'surat-ketetapan-penyelidikan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D110102',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_pencabutan_penyelidikan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penghentian/surat-pencabutan-penyelidikan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPencabutanPenghentianPenyelidikan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D110103','initial'=>'surat-pencabutan-penyelidikan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D110103',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_penyelidikan_lanjutan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penghentian/surat-penyelidikan-lanjutan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPerintahPenyelidikanLanjutan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D110104','initial'=>'surat-penyelidikan-lanjutan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D110104',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_penghentian_penyelidikan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penghentian/berita-penghentian-penyelidikan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraPenghentianPenyelidikan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D110105','initial'=>'berita-penghentian-penyelidikan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D110105',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'persetujuan_pejabat_berwenang':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penghentian/persetujuan-pejabat-berwenang'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                PersetujuanPejabatYangBerwenang::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D110106','initial'=>'persetujuan-pejabat-berwenang', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D110106',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_perintah_penyidikan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penghentian/surat-perintah-penyidikan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPerintahPenghentianPenyidikan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D110107','initial'=>'surat-perintah-penyidikan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D110107',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_ketetapan_penyidikan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penghentian/surat-ketetapan-penyidikan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratKetetapanPenghentianPenyidikan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D110108','initial'=>'surat-ketetapan-penyidikan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D110108',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'putusan_pra_peradilan':
               $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penghentian/putusan-pra-peradilan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                PutusanPraPeradilan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D110109','initial'=>'putusan-pra-peradilan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D110109',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_pencabutan_penyidikan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penghentian/surat-pencabutan-penyidikan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPencabutanPenghentianPenyidikan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D110110','initial'=>'surat-pencabutan-penyidikan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D110110',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_penyidikan_lanjutan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penghentian/surat-penyidikan-lanjutan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPerintahPenyidikanLanjutan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D110111','initial'=>'surat-penyidikan-lanjutan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D110111',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_penghentian_penyidikan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penghentian/berita-penghentian-penyidikan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaAcaraPenghentianPenyidikan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D110112','initial'=>'berita-penghentian-penyidikan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D110112',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_pernyataan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penghentian/surat-pernyataan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratPernyataan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D110113','initial'=>'surat-pernyataan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D110113',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_kesepakatan_perdamaian':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penghentian/surat-kesepakatan-perdamaian'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratKesepakatanPerdamaian::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D110114','initial'=>'surat-kesepakatan-perdamaian', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D110114',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'upload_surat_ketetapan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penghentian/upload-surat-ketetapan'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                UploadSuratKetetapan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D110115','initial'=>'upload-surat-ketetapan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D110115',
                    'tipe_update' => 'UPLOAD',
                    'selra_flag' => $selra,
                ]);
                break;
            //end penghentian kategori 11

            //start penangkapan kategori 12
            case 'surat_penetapan_tersangka':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penangkapan/surat-penetapan-tersangka'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                SuratKetetapanPenetapanTersangka::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D120101','initial'=>'surat-penetapan-tersangka', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D120101',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_perintah_penangkapan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penangkapan/surat-perintah-penangkapan'), $fileName);
                SuratPerintahPenangkapan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D120102', 'initial'=>'surat-perintah-penangkapan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D120102',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_membawa_menghadapkan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penangkapan/surat-membawa-menghadapkan'), $fileName);
                SuratMembawaMenghadapkan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D120103', 'initial'=>'surat-membawa-menghadapkan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D120103',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'surat_pelepasan_tersangka':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penangkapan/surat-pelepasan-tersangka'), $fileName);
                SuratPerintahPelepasanTersangka::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D120104', 'initial'=>'surat-pelepasan-tersangka', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D120104',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'berita_acara_penangkapan':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penangkapan/berita-acara-penangkapan'), $fileName);
                BeritaAcaraPenangkapan::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D120105', 'initial'=>'berita-acara-penangkapan', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D120105',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            case 'pelepasan_tersangka':
                $fileName = uniqid($accident). $request->file->getClientOriginalName();
                $request->file->move(public_path('file/penangkapan/pelepasan-tersangka'), $fileName);
                /* Store $fileName name in DATABASE from HERE */
                BeritaPelepasanTersangka::create(['accident_id'=>$accident,'name' => $fileName,'category' => 'D120106', 'initial'=>'pelepasan-tersangka', 'created_by'=>$user]);
                Accident::where('id', $accident)
                ->update([
                    'last_update' => Carbon::now(),
                    'category' =>'D120106',
                    'tipe_update' => 'UPLOAD'
                ]);
                break;
            //end penangkapan kategori 12

        }

        return back()
            ->with('success','Berhasi upload berkas')
            ->with('file',$fileName);
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
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        //
    }
}
