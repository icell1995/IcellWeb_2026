<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Models\Peoples\AuthorizedSignatory;

class AuthorizedSignatoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = $this->getData();

        // AuthorizedSignatory::truncate();

        DB::beginTransaction();
        try{
            foreach($data as $item){
                // echo $item;
                // // $rank_id = $item['rank_id'];
                // $id = $item['id'];
                // $first_title = $item['first_title'];
                // $first_name = $item['first_name'];
                // $last_name = $item['last_name'];
                // $last_title = $item['last_title'];
                // // $register_number = $item['register_number'];
                // // $position_id = $item['position_id'];
                // $polres_id = $item['polres_id'];
            
                AuthorizedSignatory::create($item);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function getData()
    {
        return [
            // POLDA ACEH
            [
                // POLRES LANGSA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RITIAN',
                'last_name' => 'HANDAYANI',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '86101929',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0105', 
            ],
            [
                // POLRES ACEH JAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => "AS'ARI",
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '78061273',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0121', 
            ],
            [
                //POLRES ACEH UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FAISAL',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '74090577',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0116',
            ],
            [
                // POLRES LHOKSEUMAWE
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ADEK',
                'last_name' => 'TAUFIK',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '92030434',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0104',
            ],
            [
                // POLRES ACEH BESAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RINA BINTAR',
                'last_name' => 'HANDAYANI',
                'last_title' => 'S.I.K., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '92040413',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0102',
            ],
            [
                // POLRES ACEH SINGKIL
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RIDHO RIZKY',
                'last_name' => 'ANANDA',
                'last_title' => 'S.T.K.',
                'rank_id' => 'IPTU',
                'register_number' => '92100923',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0112',
            ],
            [
                // POLRES BENER MERIAH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'IAN',
                'last_name' => 'FITRAH',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '81080270',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0120',
            ],
            [
                // POLRES NAGAN RAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HANIFAH',
                'last_name' => 'ANAS',
                'last_title' => 'S.Tr.K., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '96101259',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0118',
            ],
            [
                // POLRES ACEH TAMIANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'IWAN',
                'last_name' => 'HAJI',
                'last_title' => 'S.Pd.I., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '73120186',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0114',
            ],
            [
                // POLRES GAYO LUES
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SYAFARUDDIN',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '80020858',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0115',
            ],
            [
                // POLRESTA BANDA ACEH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUKIRNO',
                'last_name' => '',
                'last_title' => 'S.E.',
                'rank_id' => 'KOMPOL',
                'register_number' => '71120214',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0101',
            ],
            [
                // POLRES BIREUEN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FACHRUL',
                'last_name' => 'RAZI',
                'last_title' => 'S.K.M., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '77050479',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0111',
            ],
            [
                // POLRES ACEH TENGGARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ABDULLAH',
                'last_name' => 'HUSIN',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '77100188',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0109',
            ],
            [
                // POLRES SIMEULUE
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'IRVAN EFFENDI',
                'last_name' => 'PASARIBU',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '79020007',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0113',
            ],
            [
                // POLRES PIDIE
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MAHRUZAR',
                'last_name' => 'HARIADI',
                'last_title' => '',
                'rank_id' => 'IPTU',
                'register_number' => '69010108',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0103',
            ],
            [
                // POLRES ACEH TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'KRISNA HADI',
                'last_name' => 'WIDYANTO',
                'last_title' => 'S.T.K., S.I.K., M.M.',
                'rank_id' => 'IPTU',
                'register_number' => '93091044',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0117',
            ],
            [
                // POLRES SABANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HARIYONO',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '68110232',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0110',
            ],
            [
                // POLRES ACEH SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ALFI',
                'last_name' => 'SYAHRIN',
                'last_title' => '',
                'rank_id' => 'IPTU',
                'register_number' => '80010221',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0107',
            ],
            [
                // POLRES ACEH TENGAH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'M. ARIE',
                'last_name' => 'SYAHPUTRA',
                'last_title' => 'S.A.P.',
                'rank_id' => 'IPTU',
                'register_number' => '82020022',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0108',
            ],
            [
                // POLRES PIDIE JAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SYAHRIL',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '76080360',
                'position_id' => 'PLT. KASAT LANTAS',
                'polres_id' => '0123',
            ],
            [
                // POLRES ACEH BARAT DAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TRI ANDI',
                'last_name' => 'DHARMA',
                'last_title' => 'S.Sos., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '77060027',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0119',
            ],
            [
                // POLRES SUBULUSSALAM
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HENDRA',
                'last_name' => 'SUKMANA',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '80100078',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0124',
            ],
            [
                // POLRES ACEH BARAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUH',
                'last_name' => 'BERNY',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPTU',
                'register_number' => '94051281',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0106',
            ],


            // POLDA SUMUT
            [
                // POLRESTABES MEDAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'M. RIKKI',
                'last_name' => 'RAMADHAN',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '82071409',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0201'
            ],
            [
                // POLRESTA DELI SERDANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'NASRUL',
                'last_name' => '',
                'last_title' => 'S.Kom., S.I.K.',
                'rank_id' => 'KOMPOL',
                'register_number' => '83051455',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0211'
            ],
            [
                // POLRES TEBING TINGGI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DHORARIA S.',
                'last_name' => 'SIMANJUNTAK',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '75090015',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0212'
            ],
            [
                // POLRES LANGKAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HOSEA',
                'last_name' => 'GINTING',
                'last_title' => 'S.H., M.Th.',
                'rank_id' => 'AKP',
                'register_number' => '72010088',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0209'
            ],
            [
                // POLRES BINJAI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BINSAR',
                'last_name' => 'NAIBAHO',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '66040345',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0206'
            ],
            [
                // POLRES TANAH KARO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BEVAN RAGA',
                'last_name' => 'UTAMA',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '91030233',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0216'
            ],
            [
                // POLRES SIMALUNGUN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'M. HARIS. S',
                'last_name' => '',
                'last_title' => 'S.E.',
                'rank_id' => 'AKP',
                'register_number' => '71060181',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0205'
            ],
            [
                // POLRES ASAHAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'GALIH RAMADHAN',
                'last_name' => 'HARIOMURSID',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '91030280',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0203'
            ],
            [
                // POLRES LABUHAN BATU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'M. AINUL',
                'last_name' => 'YAQIN',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '92100382',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0202'
            ],
            [
                // POLRES TAPANULI UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DAHNIAL',
                'last_name' => 'SARAGIH',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '75060581',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0218'
            ],
            [
                // POLRES DAIRI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HERLIANDRI',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '76040012',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0215'
            ],
            [
                // POLRES TAPANULI SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SOFYAN HELMI',
                'last_name' => 'NASUTION',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '75120513',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0210'
            ],
            [
                // POLRES MADINA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SYAMSUL ARIFIN',
                'last_name' => 'BATUBARA',
                'last_title' => 'S.E., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '87061705',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0217'
            ],
            [
                // POLRES TAPANULI TENGAH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUSA',
                'last_name' => 'SEMBIRING',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '72080603',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0214'
            ],
            [
                // POLRES NIAS
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MARIHOT PARDAMEAN',
                'last_name' => 'PARDEDE',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '73040051',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0208',
            ],
            [
                // POLRES PELABUHAN BELAWAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'PITTOR',
                'last_name' => 'GULTOM',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '73060050',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0204',
            ],
            [
                // POLRES SERDANG BEDAGAI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ANDITA',
                'last_name' => 'SITEPU',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '78100222',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0207',
            ],
            [
                // POLRES TANJUNG BALAI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RELAPANG',
                'last_name' => 'SITEPU',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '750804175',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0220',
            ],
            [
                // POLRES PEMATANG SIANTAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RELINA',
                'last_name' => 'LUMBANGAOL',
                'last_title' => 'S.Sos.',
                'rank_id' => 'AKP',
                'register_number' => '78010362',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0213',
            ],
            [
                // POLRES SIBOLGA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUPRIHANTO. P',
                'last_name' => '',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '65060576',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0221',
            ],
            [
                // POLRES PADANG SIDEMPUAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JUNAIDI',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '73070178',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0222',
            ],
            [
                // POLRES TOBA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'R.T.GUNAWAN',
                'last_name' => 'SIAHAAN',
                'last_title' => '',
                'rank_id' => 'IPTU',
                'register_number' => '66040498',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0219'
            ],
            [
                // POLRES HUMBANG HASUNDUTAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HENRY PALONA',
                'last_name' => 'BANGUN',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '76010042',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0225'
            ],
            [
                // POLRES SAMOSIR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'YUSWANTO',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '69040128',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0226'
            ],
            [
                // POLRES PAKPAK BHARAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUJIONO',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '74120398',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0223'
            ],
            [
                // POLRES NIAS SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AGUS ENTI',
                'last_name' => 'MANSYAH',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '66090419',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0224'
            ],
            [
                // POLRES BATU BARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'H. W. SIAHAAN',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '76030161',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0227'
            ],
            [
                // POLRES PADANG LAWAS
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ALFIAN',
                'last_name' => 'ARBI',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '76030073',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0228'
            ],
            /*[
                // POLRES LABUHAN BATU SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ERWIN',
                'last_name' => 'GULTOM',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '67030147',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0230',
            ],*/


            // POLDA SUMBAR
            [
                // POLRESTA PADANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ALFIN',
                'last_name' => '',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '88031155',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0301',
            ],
            [
                // POLRES PAYAKUMBUH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ANGGY',
                'last_name' => 'PRASETIYO',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '92070836',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0304',
            ],
            [
                // POLRES AGAM
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'APRIMAN',
                'last_name' => 'SURAL',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '69040072',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0310',
            ],
            [
                // POLRES PASAMAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'YULIARMAN',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '75070847',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0303',
            ],
            [
                // POLRES PADANG PARIAMAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ADI',
                'last_name' => 'SUHENDRA',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPTU',
                'register_number' => '92081029',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0306',
            ],
            [
                // POLRES SOLOK KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUHAMMAD',
                'last_name' => 'SUGINDO',
                'last_title' => 'S.I.K., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '93040364',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0313',
            ],
            [
                // POLRES BUKIT TINGGI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'GHANDA',
                'last_name' => 'NOVIDININGRAT G',
                'last_title' => 'SIK., MH.',
                'rank_id' => 'AKP',
                'register_number' => '91110424',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0305',
            ],
            [
                // POLRES SOLOK
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DIAN JUNES',
                'last_name' => 'PUTRA',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '78100525',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0309'
            ],
            [
                // POLRES SAWAH LUNTO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'IRAWADY',
                'last_name' => '',
                'last_title' => 'SH., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '78050902',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0317'
            ],
            [
                // POLRES SOLOK SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUGENG',
                'last_name' => 'RIADI',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '73010179',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0314'
            ],
            [
                // POLRES PESISIR SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RIWAL',
                'last_name' => 'MAULIDINATA',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '93081144',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0302'
            ],
            [
                // POLRES SIJUNJUNG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ZAMRINALDI',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '70121056',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0308'
            ],
            [
                // POLRES PADANG PANJANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ALDY',
                'last_name' => 'LAZZUARDY',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '93091050',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0315'
            ],
            [
                // POLRES PASAMAN BARAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'YULIADI',
                'last_name' => '',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '76070556',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0316'
            ],
            [
                // POLRES DHARMASRAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SAYYID MALIK',
                'last_name' => 'IBRAHIM',
                'last_title' => 'S.Tr.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '94061283',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0312'
            ],
            [
                // POLRES MENTAWAI
                'id' => Str::uuid(),
                'first_title' => 'Dr',
                'first_name' => 'FAHMI',
                'last_name' => 'REZA',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKBP',
                'register_number' => '76120895',
                'position_id' => 'KAPOLRES',
                'polres_id' => '0311'
            ],
            [
                // POLRES PARIAMAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AMELYA',
                'last_name' => '',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '87051876',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0319'
            ],
            [
                // POLRES TANAH DATAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JULISMAN',
                'last_name' => '',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '75010112',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0307'
            ],
            [
                // POLRES LIMA PULUH KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'OMRIZAL',
                'last_name' => '',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '77110431',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0318'
            ],


            // POLDA RIAU
            [
                // POLRESTA PEKANBARU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BIRGITTA ATVINA',
                'last_name' => 'WIJAYANTI',
                'last_title' => 'S.H., S.I.K.',
                'rank_id' => 'KOMPOL',
                'register_number' => '85032023',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0401'
            ],
            [
                // POLRES DUMAI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AKIRA',
                'last_name' => 'CERIA',
                'last_title' => 'S.I.K., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '87081663',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0403'
            ],
            [
                // POLRES INDRAGIRI HULU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ROCKI',
                'last_name' => 'JUNASMI',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '89060713',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0402'
            ],
            [
                // POLRES KAMPAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'R. SUDARYONO',
                'last_name' => '',
                'last_title' => 'S.H., S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '91070289',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0404'
            ],
            [
                // POLRES INDRAGIRI HILIR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ERI',
                'last_name' => 'ASMAN',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '76120248',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0405'
            ],
            [
                // POLRES PELALAWAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'LILY',
                'last_name' => 'SULFIANI',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '92050422',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0407'
            ],
            [
                // POLRES KUANTAN SINGINGI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SANDI HUMISAR',
                'last_name' => 'SIBARANI',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '91060342',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0411'
            ],
            [
                // POLRES ROKAN HILIR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TRY WIDYANTO',
                'last_name' => 'FAUZAL',
                'last_title' => 'S.I.K., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '90020298',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0408'
            ],
            [
                // POLRES ROKAN HULU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AKHMAD',
                'last_name' => 'RIVANDY N.',
                'last_title' => 'S.I.K., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '89090711',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0409'
            ],
            [
                // POLRES SIAK
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'VIOLA DWI',
                'last_name' => 'ANGGRENI',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '91080220',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0410'
            ],
            [
                // POLRES BENGKALIS
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'KALIMAN',
                'last_name' => 'SIREGAR',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '76120410',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0406'
            ],
            [
                // POLRES MERANTI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BOY',
                'last_name' => 'SETIAWAN',
                'last_title' => 'S.A.P., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '77030052',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0412'
            ],


            // POLDA BENGKULU
            [
                // POLRESTA BENGKULU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'PERDHANA',
                'last_name' => 'MAHARDHIKA',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '90080292',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0501'
            ],
            [
                // POLRES BENGKULU UTARA 
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'EKA HENDRA',
                'last_name' => 'ARDIANSYAH',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '92050632',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0502'
            ],
            [
                // POLRES BENGKULU SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BERLIN APUL',
                'last_name' => 'SWANDY SINAGA',
                'last_title' => 'S.Sos.',
                'rank_id' => 'AKP',
                'register_number' => '74090575',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0505'
            ],
            [
                // POLRES REJANG LEBONG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MELISA',
                'last_name' => '',
                'last_title' => 'S.Tr.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '92040576',
                'position_id' => 'PS. KASAT LANTAS',
                'polres_id' => '0506'
            ],
            [
                // POLRES SELUMA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AYU SEKAR',
                'last_name' => 'SARI KURAISIN',
                'last_title' => 'S.Tr.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '94061292',
                'position_id' => 'PS. KASAT LANTAS',
                'polres_id' => '0503'
            ],
            [
                // POLRES KAUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JANGKUNG',
                'last_name' => 'RIYANTO',
                'last_title' => 'S.I.Kom., M.M.',
                'rank_id' => 'IPTU',
                'register_number' => '78020658',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0507'
            ],
            [
                // POLRES KEPAHYANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BOLE',
                'last_name' => 'SUSANJA',
                'last_title' => 'M.Si.',
                'rank_id' => 'IPTU',
                'register_number' => '84040161',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0508',
            ],
            [
                // POLRES LEBONG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TEGUH',
                'last_name' => 'PRASETYO',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPTU',
                'register_number' => '94031206',
                'position_id' => 'PS. KASAT LANTAS',
                'polres_id' => '0509',
            ],
            [
                // POLRES MUKOMUKO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FERY OCTAVIARI',
                'last_name' => 'PRATAMA',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '91100463',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0504',
            ],
            [
                // POLRES BENGKULU TENGAH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'WIYANTO',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '78110579',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0511',
            ],
            

            // POLDA JAMBI
            [
                // POLRESTA JAMBI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AULIA',
                'last_name' => 'RAHMAD',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '85062123',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0601',
            ],
            [
                // POLRES MUARO JAMBI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ANGGA',
                'last_name' => 'LUVYANTO',
                'last_title' => 'M.H.',
                'rank_id' => 'AKP',
                'register_number' => '90060353',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0607',
            ],
            [
                // POLRES BATANGHARI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUDIHARSONO',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '81040391',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0602',
            ],
            [
                // POLRES TANJUNG JABUNG BARAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'IWAN',
                'last_name' => 'WAHYUDI',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '78120189',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0608',
            ],
            [
                // POLRES TANJUNG JABUNG TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AGUNG PRASETYO',
                'last_name' => 'SOEGIONO',
                'last_title' => 'S.Tr.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '94111200',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0609',
            ],
            [
                // POLRES TEBO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUHAMAD',
                'last_name' => 'TOHIR',
                'last_title' => 'S.Pd.',
                'rank_id' => 'AKP',
                'register_number' => '70120144',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0605'
            ],
            [
                // POLRES BUNGO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'STEFFAN THOMAS',
                'last_name' => 'LUMOWA',
                'last_title' => 'S.Tr.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '92090916',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0603'
            ],
            [
                // POLRES MERANGIN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BAMBANG',
                'last_name' => 'SOESETYO',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '72100103',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0610'
            ],
            [
                // POLRES SAROLANGGUN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MARSANI',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '68050556',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0606'
            ],
            [
                // POLRES KERINCI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ISNANDAR',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '72120362',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0604'
            ],


            // POLDA SUMSEL
            [
                // POLRES PRAMBUMULIH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUTHEMAINAH',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '79060032',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0710'
            ],
            [
                // POLRES OGAN KOMERING ULU TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'LASTARI',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '72010051',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0712'
            ],
            [
                // POLRES PAGAR ALAM
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TEGUH',
                'last_name' => 'HIDAYAT',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '77030673',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0709'
            ],
            [
                // POLRES LAHAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MURIYANTO',
                'last_name' => '',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '77090348',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0705'
            ],
            [
                // POLRES LUBUK LINGGAU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AGUS GUNAWAN',
                'last_name' => 'SETYAHADI',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '75080921',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0707'
            ],
            [
                // POLRES OGAN KOMERING ULU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ARIF',
                'last_name' => 'HARSONO',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKBP',
                'register_number' => '780713953',
                'position_id' => 'KAPOLRES',
                'polres_id' => '0706'
            ],
            [
                // POLRES OGAN KOMERING ILIR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'M. SADELI',
                'last_name' => '',
                'last_title' => 'S.H., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '76060159',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0703'
            ],
            [
                // POLRES MUAARA ENIM
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUWANDI',
                'last_name' => '',
                'last_title' => 'S.H., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '76110459',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0704'
            ],
            [
                // POLRES MUSI BANYUASIN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RICKY',
                'last_name' => 'MOZAM',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '88021064',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0702'
            ],
            [
                // POLRES EMPAT LAWANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DESI',
                'last_name' => 'AZHARI',
                'last_title' => 'S.H., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '78120281',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0715'
            ],
            [
                // POLRES OGAN KOMERING ULU SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JOKO EDY',
                'last_name' => 'SANTOSO',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '92030549',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0713'
            ],
            [
                // POLRES BANYU ASIN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'INDROWONO',
                'last_name' => '',
                'last_title' => 'S.H., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '70050237',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0708'
            ],
            [
                // POLRES MUSI RAWAS UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SAHARUDIN',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '72070336',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0718'
            ],
            [
                // POLRES MUSI RAWAS
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FITRI DEWI',
                'last_name' => 'UTAMI',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '91040252',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0711'
            ],
            [
                // POLRES OGAN ILIR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'PUTU EKA',
                'last_name' => 'DHENDA JAYANTI',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '90010376',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0714',
            ],
            [
                // POLRES PALI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'KUKUH',
                'last_name' => 'FEFRYANTO',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '80020466',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0717',
            ],
            [
                // POLRESTABES PALEMBANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RENDY SURYA',
                'last_name' => 'ADITAMA',
                'last_title' => 'S.H., S.I.K., M.H.',
                'rank_id' => 'AKBP',
                'register_number' => '82061381',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0701',
            ],


            // POLDA LAMPUNG
            [
                // POLRES TANGGAMUS
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AMSAR',
                'last_name' => '',
                'last_title' => 'S.Sos.',
                'rank_id' => 'AKP',
                'register_number' => '73010554',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0806'
            ],
            [
                // POLRES LAMPUNG SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JONNIFER',
                'last_name' => 'YOLANDRA',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '91010272',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0802'
            ],
            [
                // POLRES PESAWARAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MARTOYO',
                'last_name' => '',
                'last_title' => 'S.I.P., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '75030419',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0812'
            ],
            [
                // POLRES LAMPUNG UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JONI',
                'last_name' => 'CHARTER',
                'last_title' => 'S.I.P., M.M.',
                'rank_id' => 'IPTU',
                'register_number' => '83080909',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0808'
            ],
            [
                // POLRES LAMPUNG TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BIMA ALIEF',
                'last_name' => 'CAESAR GUMILANG',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '92020443',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0805'
            ],
            [
                // POLRES MESUJI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'WAHYU DWI',
                'last_name' => 'KRISTANTO',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '81100287',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0811'
            ],
            [
                // POLRES PRINGSEWU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'KHOIRUL',
                'last_name' => 'BAHRI',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '82050174',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0815'
            ],
            [
                // POLRES BANDAR LAMPUNG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'M. ROHMAWAN',
                'last_name' => '',
                'last_title' => 'S.H., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '76050706',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0801',
            ],
            [
                // POLRES TULANG BAWANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'GLEND',
                'last_name' => 'FELIX',
                'last_title' => 'S.Tr.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '93111127',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0807',
            ],
            [
                // POLRES LAMPUNG BARAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DAVID',
                'last_name' => 'PULNER',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '81070418',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0810',
            ],
            [
                // POLRES TULANG BAWANG BARAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SAMSI',
                'last_name' => 'RIZAL, AB',
                'last_title' => 'S.E., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '83060135',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0814',
            ],
            [
                // POLRES METRO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'REZKI',
                'last_name' => 'PARSINOVANDI',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '90060333',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0804',
            ],
            [
                // POLRES WAY KANAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ELVIS',
                'last_name' => 'YANI',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '76010218',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0809',
            ],
            [
                // POLRES LAMPUNG TENGAH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'IPRAN',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '75090919',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0803',
            ],
            // [
            //     // POLRES PESISIR BARAT
            //     'id' => Str::uuid(),
            //     'first_title' => '',
            //     'first_name' => 'RUDI APRIANSYAH',
            //     'last_name' => 'UNYI',
            //     'last_title' => 'S.H.',
            //     'rank_id' => 'IPTU',
            //     'register_number' => '80040229',
            //     'position_id' => 'KASAT LANTAS',
            //     'polres_id' => '0816',
            // ],


            // POLDA BABEL
            [
                // POLRES BELITUNG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'R.T.A.',
                'last_name' => 'SIANTURI',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '79040431',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0906',
            ],
            [
                // POLRES BANGKA SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'EDI',
                'last_name' => 'YUSUF',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '70030216',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0905',
            ],
            [
                // POLRES BANGKA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RIZKA SITI',
                'last_name' => 'AMALIA',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPTU',
                'register_number' => '96021069',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0902',
            ],
            [
                // POLRES PANGKAL PINANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ANDI EKO',
                'last_name' => 'WARDANA',
                'last_title' => 'S.E.',
                'rank_id' => 'AKP',
                'register_number' => '89100620',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '0901',
            ],
            [
                // POLRES BANGKA TENGAH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DWI BUDI',
                'last_name' => 'MURTIONO',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKBP',
                'register_number' => '80031093',
                'position_id' => 'KAPOLRES',
                'polres_id' => '0904',
            ],
            [
                // POLRES BELITUNG TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '0907',
            ],
            [
                // POLRES BANGKA BARAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'CATUR',
                'last_name' => 'PRASETIYO',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKBP',
                'register_number' => '77051078',
                'position_id' => 'KAPOLRES',
                'polres_id' => '0903',
            ],

            // POLDA KEPRI
            [
                // POLRESTA BALERANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'CUT PUTRI',
                'last_name' => 'AMELIA SARI',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '88110793',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1001'
            ],
            [
                // POLRES BINTAN
                'id' => Str::uuid(),
                'first_title' => 'KHAPANDI',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '74050387',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1004'
            ],
            [
                // POLRES NATUNA
                'id' => Str::uuid(),
                'first_title' => 'SOPAN',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '66120495',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1006'
            ],
            [
                // POLRES LINGGA
                'id' => Str::uuid(),
                'first_title' => 'AWANG RIMBA',
                'first_name' => 'BRIANTOKO',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '67100238',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1005'
            ],
            [
                // POLRESTA TANJUNGPINANG
                'id' => Str::uuid(),
                'first_title' => 'REZA ANUGRAH',
                'first_name' => 'ARIEF PERDANA',
                'last_name' => '',
                'last_title' => 'S.H., S.I.K.',
                'rank_id' => 'KOMPOL',
                'register_number' => '83071455',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1002'
            ],
            [
                // POLRES KARIMUN
                'id' => Str::uuid(),
                'first_title' => 'EKO',
                'first_name' => 'APRIANTO',
                'last_name' => '',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '90040419',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1003'
            ],
            [
                // POLRES ANAMBAS
                'id' => Str::uuid(),
                'first_title' => 'RIDWAN',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => 'IPTU',
                'register_number' => '68040631',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1007'
            ],


            // POLDA METRO JAYA
            [
                // POLRES JAKARTA PUSAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'PURWANTA',
                'last_name' => '',
                'last_title' => 'S.E., M.M.',
                'rank_id' => 'KOMPOL',
                'register_number' => '67070019',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1101'
            ],
            [
                // POLRES JAKARTA UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'EDY',
                'last_name' => 'PURWANTO',
                'last_title' => 'S.H., S.I.K., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '83101455',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1102'
            ],
            [
                // POLRES JAKARTA SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JOKO',
                'last_name' => 'SUTRIONO',
                'last_title' => 'S.H., M.M.',
                'rank_id' => 'KOMPOL',
                'register_number' => '68050334',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1104'
            ],
            [
                // POLRES JAKARTA TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'EDY',
                'last_name' => 'SURASA',
                'last_title' => 'S.H.',
                'rank_id' => 'AKBP',
                'register_number' => '68060864',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1105'
            ],
            [
                // POLRES JAKARTA BARAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MAULANA JALI',
                'last_name' => 'KAREPESINA',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '78020922',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1103'
            ],
            [
                // POLRES TANGERANG KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JOKO',
                'last_name' => 'SEMBODO',
                'last_title' => 'S.E., M.MTR.',
                'rank_id' => 'KOMPOL',
                'register_number' => '85082156',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1107'
            ],
            [
                // POLRES TANGERANG SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DICKY DWI PRIAMBUDI',
                'last_name' => 'ARIEF SUTARMAN',
                'last_title' => 'S.H., S.IK., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '89040781',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1115',
            ],
            [
                // POLRES BEKASI KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AGUNG',
                'last_name' => 'PITOYO',
                'last_title' => '',
                'rank_id' => 'AKBP',
                'register_number' => '66110476',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1108'
            ],
            [
                // POLRES BEKASI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HAPPY',
                'last_name' => 'SAPUTRA',
                'last_title' => 'S.Kom., S.I.K., M.PM.',
                'rank_id' => 'KOMPOL',
                'register_number' => '84071808',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1112'
            ],
            [
                // POLRES BANDARA SOEKARNO HATTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BAMBANG ASKAR',
                'last_name' => 'SODIQ',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '75081123',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1110'
            ],
            [
                // POLRES PELABUHAN TANJUNG PRIOK
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BACHTIAR',
                'last_name' => 'NOPRIANTO',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '80110127',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1106'
            ],
            [
                // POLRES DEPOK
                'id' => Str::uuid(),
                'first_title' => 'Dr.',
                'first_name' => 'BONIFACIUS',
                'last_name' => 'SURANO',
                'last_title' => 'S.H., M.Si.',
                'rank_id' => 'AKBP',
                'register_number' => '65060517',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1109'
            ],
            [
                // DITLANTAS POLDA METRO JAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JHONI EKA',
                'last_name' => 'PUTRA',
                'last_title' => 'S.H., S.I.K., M.M.',
                'rank_id' => 'AKBP',
                'register_number' => '80061250',
                'position_id' => 'KASUBDITGAKKUM',
                'polres_id' => '1114'
            ],


            // POLDA JABAR
            [
                // POLRESTABES BANDUNG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ARIEF SAEPUL',
                'last_name' => 'HARIS',
                'last_title' => 'S.T.',
                'rank_id' => 'AKP',
                'register_number' => '81030600',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1201',
            ],
            [
                // POLRESTA BANDUNG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ZAZID',
                'last_name' => 'ABDULLOH',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '74100244',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1211',
            ],
            [
                // POLRESTA BOGOR KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'F. EK. SUSILO',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '71100043',
                'position_id' => 'PS. KANIT GAKKUM',
                'polres_id' => '1202',
            ],
            [
                // POLRESTA CIREBON
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ENDANG',
                'last_name' => 'KUSNANDAR',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '77030483',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1219',
            ],
            [
                // POLRES CIMAHI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUDIRIANTO',
                'last_name' => '',
                'last_title' => 'S.H., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '74030589',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1210',
            ],
            [
                // POLRES CIMAHI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BAYU',
                'last_name' => 'SUBAKTI',
                'last_title' => 'S.H.',
                'rank_id' => 'IPDA',
                'register_number' => '85040665',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1210',
            ],
            [
                // POLRES SUMEDANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'KIKI HARTAKI',
                'last_name' => 'KADARUSTAMIM',
                'last_title' => 'S.Pd.',
                'rank_id' => 'AKP',
                'register_number' => '74110045',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1212',
            ],
            [
                // POLRES SUMEDANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TOTO',
                'last_name' => 'CASRIYANTO',
                'last_title' => 'S.H.',
                'rank_id' => 'IPDA',
                'register_number' => '78060123',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1212'
            ],
            [
                // POLRES BOGOR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DICKY ANGGY',
                'last_name' => 'PRANATA',
                'last_title' => 'S.I.K., M.Si., CPHR.',
                'rank_id' => 'AKP',
                'register_number' => '91110423',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1203'
            ],
            [
                // POLRES BOGOR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ANGGA NUGRAHA',
                'last_name' => 'FIRMANSYAH',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'IPDA',
                'register_number' => '86090140',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1203'
            ],
            [
                // POLRES CIANJUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUJANA AWIN',
                'last_name' => 'UMAR',
                'last_title' => 'S.M.',
                'rank_id' => 'AKP',
                'register_number' => '70110293',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1205'
            ],
            [
                // POLRES CIANJUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HADI',
                'last_name' => 'KURNIAWAN',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '80101057',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1205'
            ],
            [
                // POLRES SUKABUMI KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TEJO RENO',
                'last_name' => 'INDRATNO',
                'last_title' => 'S.I.K., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '90050368',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1204'
            ],
            [
                // POLRES SUKABUMI KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JAJAT',
                'last_name' => 'MUNAJAT',
                'last_title' => 'S.Ip.',
                'rank_id' => 'IPDA',
                'register_number' => '74020043',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1204',
            ],
            [
                // POLRES SUKABUMI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'M. YANUAR',
                'last_name' => 'FAJAR',
                'last_title' => 'S.H.',
                'rank_id' => 'IPDA',
                'register_number' => '84010157',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1206',
            ],
            [
                // POLRES KARAWANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ALI',
                'last_name' => 'IDRUS',
                'last_title' => 'S.H.',
                'rank_id' => 'IPDA',
                'register_number' => '86090140',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1208',
            ],
            [
                // POLRES SUBANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'LUKY',
                'last_name' => 'MARTONO',
                'last_title' => 'S.H., M.M., CHRA.',
                'rank_id' => 'AKP',
                'register_number' => '75030560',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1209',
            ],
            [
                // POLRES SUBANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ENDANG',
                'last_name' => 'SUDRAJAT',
                'last_title' => 'S.H.',
                'rank_id' => 'IPDA',
                'register_number' => '86011266',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1209',
            ],
            [
                // POLRES PURWAKARTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'H. WARJO',
                'last_name' => '',
                'last_title' => 'S.Pd., M.M., CHRA.',
                'rank_id' => 'AKP',
                'register_number' => '76040693',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1207',
            ],
            [
                // POLRES CIREBON KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TRIYONO',
                'last_name' => 'RAHARJA',
                'last_title' => 'S.I.K., M.H., CPHR.',
                'rank_id' => 'AKP',
                'register_number' => '90090298',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1218',
            ],
            [
                // POLRES MAJALENGKA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'NGADIMAN',
                'last_name' => '',
                'last_title' => 'S.Kom.',
                'rank_id' => 'AKP',
                'register_number' => '83051445',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1221',
            ],
            [
                // POLRES MAJALENGKA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ASENG',
                'last_name' => 'SUWANDA',
                'last_title' => 'S.H.',
                'rank_id' => 'IPDA',
                'register_number' => '85020037',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1221',
            ],
            [
                // POLRES INDRAMAYU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ANGGA',
                'last_name' => 'HANDIMAN',
                'last_title' => 'S.I.K., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '91040256',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1220',
            ],
            [
                // POLRES KUNINGAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'VINO',
                'last_name' => 'LESTARI',
                'last_title' => 'S.I.K., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '91030276',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1222',
            ],
            [
                // POLRES GARUT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'UNDANG SYARIF',
                'last_name' => 'HIDAYAT',
                'last_title' => 'S.H., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '78080847',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1213',
            ],
            [
                // POLRES GARUT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'PRIYO',
                'last_name' => 'SUMBODO',
                'last_title' => 'S.E.',
                'rank_id' => 'IPTU',
                'register_number' => '84060782',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1213',
            ],
            [
                // POLRES BANJAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HERMAWAN',
                'last_name' => '',
                'last_title' => 'S.Pd.I.',
                'rank_id' => 'IPDA',
                'register_number' => '7409047',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1217'
            ],
            [
                // POLRES CIAMIS
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ASEP IMAN',
                'last_name' => 'HERMAWAN',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '76010668',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1216'
            ],
            [
                // POLRES CIAMIS
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'P. ARI',
                'last_name' => 'PARANTORO',
                'last_title' => 'S.H.',
                'rank_id' => 'IPDA',
                'register_number' => '86020211',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1216'
            ],
            [
                // POLRES TASIKMALAYA KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ANAGA',
                'last_name' => 'BUDIHARSO',
                'last_title' => 'S.I.K., M,Si.',
                'rank_id' => 'AKP',
                'register_number' => '89050792',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1214'
            ],
            [
                // POLRES TASIKMALAYA KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'IPAN',
                'last_name' => 'FAISAL',
                'last_title' => 'S.Ip.',
                'rank_id' => 'IPDA',
                'register_number' => '83010930',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '1214'
            ],
            [
                // POLRES TASIKMALAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'YUDI',
                'last_name' => 'SADIKIN',
                'last_title' => 'S.I.P.',
                'rank_id' => 'AKP',
                'register_number' => '75040414',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1215'
            ],


            // POLDA JATENG
            [
                // POLRES KOTA BESAR SEMARANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SIGIT',
                'last_name' => '',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKBP',
                'register_number' => '77061168',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1301',
            ],
            [
                // POLRES KOTA SURAKARTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AGUNG',
                'last_name' => 'YUDIAWAN',
                'last_title' => 'S.H., S.I.K., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '84021523',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1302',
            ],
            [
                // POLRES KOTA BANYUMAS
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BOBBY',
                'last_name' => 'A RACHMAN',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '85111943',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1303',
            ],
            [
                // POLRES KOTA CILACAP
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'NUNUNG',
                'last_name' => 'FARMADI',
                'last_title' => 'S.Sos., S.I.K., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '84061810',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1304',
            ],
            [
                // POLRES KOTA MAGELANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AGUS',
                'last_name' => 'SANTOSO',
                'last_title' => 'S.E., S.I.K., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '81021099',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1331',
            ],
            [
                // POLRES KOTA PATI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ASFAURI',
                'last_name' => '',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '75070593',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1318',
            ],
            [
                // POLRES PURBALINGGA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MIA NOVRILA',
                'last_name' => 'SAVITRY',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '92110380',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1305',
            ],
            [
                // POLRES BANJARNEGARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'R. MANGGALA AGUNG',
                'last_name' => 'SRI MAHARDJO',
                'last_title' => 'S.I.K., M.H., CPHR.',
                'rank_id' => 'AKP',
                'register_number' => '92070392',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1306'
            ],
            [
                // POLRES PEKALONGAN KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'KOYIM',
                'last_name' => 'MATURROHMAN',
                'last_title' => 'S.Ds.',
                'rank_id' => 'AKP',
                'register_number' => '90090301',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1307'
            ],
            [
                // POLRES PEKALONGAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FITRIYANTO',
                'last_name' => '',
                'last_title' => 'S.H., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '75070115',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1308'
            ],
            [
                // POLRES BATANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AGUS PARDIYONO',
                'last_name' => 'MARINUS',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '76060425',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1309'
            ],
            [
                // POLRES PEMALANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ACHMAD RIEDWAN',
                'last_name' => 'PREVOOST',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '91090448',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1310'
            ],
            [
                // POLRES TEGAL KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUSTAKIM',
                'last_name' => '',
                'last_title' => 'S.H., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '68090476',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1311'
            ],
            [
                // POLRES TEGAL
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ERWIN',
                'last_name' => 'CHAN',
                'last_title' => 'S.H., S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '86101926',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1312'
            ],
            [
                // POLRES BREBES
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'EDI',
                'last_name' => 'SUKAMTO',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '77040206',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1313',
            ],
            [
                // POLRES SEMARANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DWI HIMAWAN',
                'last_name' => 'CHANDRA',
                'last_title' => 'S.I.K., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '87071799',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1314',
            ],
            [
                // POLRES SALATIGA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BETY',
                'last_name' => 'NUGROHO',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '74070016',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1315',
            ],
            [
                // POLRES KENDAL
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RIZKY WIDYO',
                'last_name' => 'PRATOMO',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '89080711',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1316',
            ],
            [
                // POLRES DEMAK
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUHAMAD GARGARIN',
                'last_name' => 'FRIYANDI',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '92080437',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1317',
            ],
            [
                // POLRES KUDUS
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'IVAN',
                'last_name' => 'PRABOWO',
                'last_title' => 'S.I.K., M.A.',
                'rank_id' => 'AKP',
                'register_number' => '92090405',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1319',
            ],
            [
                // POLRES JEPARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'R. ADE TRIKEN',
                'last_name' => 'DEAYOMI',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '91040310',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1320',
            ],
            [
                // POLRES REMBANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DWI PANJI',
                'last_name' => 'LESTARI',
                'last_title' => 'S.H., S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '88041119',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1321'
            ],
            [
                // POLRES BLORA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'NOACH',
                'last_name' => 'HENDRIK',
                'last_title' => 'S.I.K., M.A.',
                'rank_id' => 'AKP',
                'register_number' => '89110551',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1322'
            ],
            [
                // POLRES GROBOGAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DENI EKO',
                'last_name' => 'PRASETYO',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '91120462',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1323'
            ],
            [
                // POLRES SUKOHARJO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SOFIA',
                'last_name' => 'WURIANA',
                'last_title' => 'S.H., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '79050714',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1324'
            ],
            [
                // POLRES KLATEN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUGIYANTO',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '66090410',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1325'
            ],
            [
                // POLRES BOYOLALI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'M. HERDI',
                'last_name' => 'PRATAMA',
                'last_title' => 'S.I.K., M.H., M.Sc.',
                'rank_id' => 'AKP',
                'register_number' => '92070391',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1326'
            ],
            [
                // POLRES SRAGEN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ABIPRAYA GUNTUR',
                'last_name' => 'SULATIASTO',
                'last_title' => 'S.I.K., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '91110166',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1327'
            ],
            [
                // POLRES KARANGANYAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ALIET',
                'last_name' => 'ALPHARD',
                'last_title' => 'S.Sos.',
                'rank_id' => 'AKP',
                'register_number' => '88011068',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1328',
            ],
            [
                // POLRES WONOGIRI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MARYONO',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '71080148',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1329',
            ],
            [
                // POLRES MAGELANG KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AFIDITYA ARIEF',
                'last_name' => 'WIBOWO',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '89010765',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1330',
            ],
            [
                // POLRES PURWOREJO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ANDIKA',
                'last_name' => 'ALFATONI',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '90060338',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1332',
            ],
            [
                // POLRES KEBUMEN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TEJO',
                'last_name' => 'SUWONO',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '73090365',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1333',
            ],
            [
                // POLRES TEMANGGUNG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FELIK MU',
                'last_name' => '',
                'last_title' => 'S.E., M.Kom.',
                'rank_id' => 'AKP',
                'register_number' => '72110094',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1334',
            ],
            [
                // POLRES WONOSOBO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RAGIL',
                'last_name' => 'IRAWAN',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '77020557',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1335',
            ],


            // POLDA DIY
            [
                // POLRESTA YOGYAKARTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MARYANTO',
                'last_name' => '',
                'last_title' => 'S.H., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '70030322',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1401',
            ],
            [
                // POLRESTA SLEMAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'GUNAWAN',
                'last_name' => 'SETIYABUDI',
                'last_title' => 'S.H., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '76090112',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1405',
            ],
            [
                // POLRES BANTUL
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FIKRI',
                'last_name' => 'KURNIAWAN',
                'last_title' => 'S.Tr.K., S.I.K., M.M.',
                'rank_id' => 'IPTU',
                'register_number' => '94051257',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1402',
            ],
            [
                // POLRES GUNUNG KIDUL
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'A. PURWANTA',
                'last_name' => '',
                'last_title' => 'S.H., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '69020333',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1404',
            ],
            [
                // POLRES KULON PROGO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '1403',
            ],


            // POLDA JATIM
            [
                // POLRES BOJONEGORO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'I GUSTI',
                'last_name' => 'BAGUS KRISNA F.',
                'last_title' => 'S.I.K., M.A.P.',
                'rank_id' => 'AKP',
                'register_number' => '92090398',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1533'
            ],
            [
                // POLRESTA MALANG KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ARI GALANG',
                'last_name' => 'SAPUTRO',
                'last_title' => 'S.I.K., M.Si.',
                'rank_id' => 'KOMPOL',
                'register_number' => '85091999',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1507'
            ],
            [
                // POLRES KEDIRI KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'PRASTYA YANA',
                'last_name' => 'WISESA SUPRIYANTO',
                'last_title' => 'S.I.K., S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '92050417',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1519'
            ],
            [
                // POLRES PASURUAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'YUDHI ANUGRAH',
                'last_name' => 'PUTRA',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '91050348',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1511'
            ],
            [
                // POLRES MOJOKERTO KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HERU SUDJIO',
                'last_name' => 'BUDI SANTOSO',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '72050403',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1505'
            ],
            [
                // POLRES MAGETAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TRIFONIA',
                'last_name' => 'SITUMORANG',
                'last_title' => 'S.I.K., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '91100465',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1532'
            ],
            [
                // POLRES MADIUN KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'VISTA DWI',
                'last_name' => 'PUJININGSIH',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '92080438',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1527',
            ],
            [
                // POLRES KEDIRI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FIRDAUS CANGGIH',
                'last_name' => 'PAMUNGKAS',
                'last_title' => 'S.I.K., M.H., M.T.',
                'rank_id' => 'AKP',
                'register_number' => '90090291',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1520'
            ],
            [
                // POLRES BANGKALAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ANINDITA',
                'last_name' => 'HARCAHYANINGDYAH',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '89080703',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1539'
            ],
            [
                // POLRES JOMBANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RUDI',
                'last_name' => 'PURWANTO',
                'last_title' => 'S.H., M.A.P.',
                'rank_id' => 'AKP',
                'register_number' => '77040024',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1522'
            ],
            [
                // POLRES TRENGGALEK
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'YUDHIYONO',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '67030496',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1526'
            ],
            [
                // POLRES LAMONGAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'WIDYAGANA PUTRA',
                'last_name' => 'DHIROTSAHA',
                'last_title' => 'S.T.K., S.I.K., M.Si.',
                'rank_id' => 'IPTU',
                'register_number' => '92100924',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1535'
            ],
            [
                // POLRES BLITAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MURSID BUDI',
                'last_name' => 'HARTANTO',
                'last_title' => 'S.H., M.T.',
                'rank_id' => 'AKP',
                'register_number' => '75040176',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1525'
            ],
            [
                // POLRES PONOROGO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AFFAN PRIYO',
                'last_name' => 'WICAKSONO',
                'last_title' => 'S.E., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '85032038',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1531',
            ],
            [
                // POLRES BONDOWOSO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SURYONO',
                'last_name' => '',
                'last_title' => 'S.Sos.',
                'rank_id' => 'AKP',
                'register_number' => '74080825',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1518',
            ],
            [
                // POLRES PELABUHAN TANJUNG PERAK
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MOCHAMAD',
                'last_name' => "SU'UD",
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '71090219',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1502',
            ],
            [
                // POLRESTA SIDOARJO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'YANTO',
                'last_name' => 'MULYANTO P.',
                'last_title' => 'S.H., S.I.K., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '86052014',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1504',
            ],
            [
                // POLRES NGANJUK
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DINI ANNISA',
                'last_name' => 'RAHMAT',
                'last_title' => 'S.I.K., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '91070292',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1521',
            ],
            [
                // POLRES GRESIK
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AGUNG',
                'last_name' => 'FITRANSYAH',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '88051145',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1503',
            ],
            [
                // POLRESTA BANYUWANGI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RANDY',
                'last_name' => 'ASDAR',
                'last_title' => 'S.Kom., S.I.K.',
                'rank_id' => 'KOMPOL',
                'register_number' => '85122059',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1517',
            ],
            [
                // POLRES JEMBER
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ARUM',
                'last_name' => 'INAMBALA',
                'last_title' => 'S.I.K., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '92080432',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1514'
            ],
            [
                // POLRES LUMAJANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RADYATI PUTRI',
                'last_name' => 'PRADINI',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '92030424',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1515'
            ],
            [
                // POLRES SAMPANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TUTUT YUDHO',
                'last_name' => 'PRASTYAWAN',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '76120757',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1538'
            ],
            [
                // POLRES SITUBONDO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUWARNO',
                'last_name' => '',
                'last_title' => 'S.H., M.Hum.',
                'rank_id' => 'AKP',
                'register_number' => '70060041',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1516'
            ],
            [
                // POLRES BLITAR KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MULYA',
                'last_name' => 'SUGIHARTO',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '90110282',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1524'
            ],
            [
                // POLRES TULUNGAGUNG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RAHANDY GUSTI',
                'last_name' => 'PRADANA',
                'last_title' => 'S.I.K., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '90080298',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1523'
            ],
            [
                // POLRES PROBOLINGGO KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'PANDRI PRATAMA',
                'last_name' => 'PUTRA SIMBOLON',
                'last_title' => 'S.I.K., M.A.',
                'rank_id' => 'AKP',
                'register_number' => '91080452',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1512',
            ],
            [
                // POLRES PACITAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SISWOYO',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '75070261',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1530',
            ],
            [
                // POLRES MALANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AGNIS JUWITA',
                'last_name' => 'MANURUNG',
                'last_title' => 'S.I.K., M.M., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '92080444',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1508',
            ],
            [
                // POLRES TUBAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'KADEK ADITYA',
                'last_name' => 'YASA PUTRA',
                'last_title' => 'S.I.K., M.H., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '93040365',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1534',
            ],
            [
                // POLRES PASURUAN KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RIZAL NUGRA',
                'last_name' => 'WIJAYA',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '89010764',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1510',
            ],
            [
                // POLRES MOJOKERTO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUHAMMAD BAYU',
                'last_name' => 'AGUSTYAN',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '89080716',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1506',
            ],
            [
                // POLRES MADIUN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AGUS',
                'last_name' => 'SETYAWAN',
                'last_title' => 'S.I.K., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '92080442',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1528',
            ],
            [
                // POLRESTABES SURABAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ARIF',
                'last_name' => 'FAZLURRAHMAN',
                'last_title' => 'S.H., S.I.K., M.Si.',
                'rank_id' => 'AKBP',
                'register_number' => '84061728',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1501',
            ],
            [
                // POLRES NGAWI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ACHMAD FAHMI',
                'last_name' => 'ADIATMA',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '91020219',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1529',
            ],
            [
                // POLRES SUMENEP
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ALIMUDDIN',
                'last_name' => 'NASUTION',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '71050040',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1536',
            ],
            [
                // POLRES PROBOLINGGO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'M. SAPARI',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '73030428',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1513',
            ],
            [
                // POLRES PAMEKASAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MOKHAMAD',
                'last_name' => 'MUNIR',
                'last_title' => 'S.H., M.Hum.',
                'rank_id' => 'AKP',
                'register_number' => '77121065',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1537',
            ],
            [
                // POLRES BATU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'LYA',
                'last_name' => 'AMBARWATI',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '88021060',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1509',
            ],


            // POLDA BANTEN
            [
                // DITLANTAS POLDA BANTEN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SONNY',
                'last_name' => 'HARSONO',
                'last_title' => 'S.I.K., S.H., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '85081962',
                'position_id' => 'PS. KASUBDITGAKKUM',
                'polres_id' => '1605'
            ],
            [
                // POLRES CILEGON
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JEANY',
                'last_name' => 'VIADINIATI',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '91040259',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1604'
            ],
            [
                // POLRES LEBAK
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FIAT ARI',
                'last_name' => 'SUHADA',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '91010275',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1603'
            ],
            [
                // POLRES SERANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TIWI',
                'last_name' => 'AFRINA',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '90040412',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1601'
            ],
            [
                // POLRES SERANG KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TRY',
                'last_name' => 'WILARNO',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '87011558',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1606'
            ],
            [
                // POLRESTA TANGERANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FIKRY',
                'last_name' => 'ARDIANSYAH',
                'last_title' => 'S.H., S.I.K., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '86081906',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1611'
            ],
            [
                // POLRES PANDEGLANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ROBBY',
                'last_name' => 'RACHMAN',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '92060402',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1602'
            ],


            // POLDA BALI
            [
                // POLRES TABANAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'KANISIUS',
                'last_name' => 'FRANATA',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '91050280',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1703',
            ],
            [
                // POLRES BANGLI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'I KETUT',
                'last_name' => 'WIDIARTA',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '68090082',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1706',
            ],
            [
                // POLRES BADUNG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'NI PUTU',
                'last_name' => 'MEIPIN EKAYANTI',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '82050157',
                'position_id' => 'KASAT LANTAS',
                'identity_number' => '5104047105820002',
                'email' => 'Satlantasbadung@ymail.com',
                'phone' => '08123650582',
                'polres_id' => '1709',
            ],
            [
                // POLRES JEMBRANA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AAN',
                'last_name' => 'SAPUTRA R.A.',
                'last_title' => 'S.I.K., M.H., CPHR.',
                'rank_id' => 'AKP',
                'register_number' => '90040414',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1708',
            ],
            [
                // POLRESTA DENPASAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'NI PUTU',
                'last_name' => 'UTARIANI',
                'last_title' => 'S.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '69050033',
                'position_id' => 'KASAT LANTAS',
                'identity_number' => '5102066205690002',
                'email' => 'putuutariani@gmail.com',
                'phone' => '087861124583',
                'polres_id' => '1701',
            ],
            [
                // POLRES KLUNGKUNG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BACHTIAR',
                'last_name' => 'ARIFIN',
                'last_title' => 'S.T.K., S.I.K., M.Si.',
                'rank_id' => 'IPTU',
                'register_number' => '93101102',
                'position_id' => 'KASAT LANTAS',
                'identity_number' => '3275021110930012',
                'email' => 'bachtiarrifin@yahoo.com',
                'phone' => '082111576623',
                'polres_id' => '1705',
            ],
            [
                // POLRES KARANGASEM
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'WAHYU JOKO',
                'last_name' => 'NUGROHO',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '89070710',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1707',
            ],
            [
                // POLRES GIANYAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUHAMMAD BHAYANGKARA',
                'last_name' => 'PUTRA SEJATI',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '92030425',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1704'
            ],
            [
                // POLRES BULELENG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RIENA KUSMARLANDI',
                'last_name' => 'PUTRI',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '93041090',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1702'
            ],


            // POLDA NTB
            [
                // POLRES KOTA MATARAM
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BOWO TRI',
                'last_name' => 'HANDOKO',
                'last_title' => 'S.E., S.I.K.',
                'rank_id' => 'KOMPOL',
                'register_number' => '83111433',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1801'
            ],
            [
                // POLRES LOMBOK TENGAH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'PUTU GDE',
                'last_name' => 'CAKA P.R.',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '91030235',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1802'
            ],
            [
                // POLRES LOMBOK TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DONNY WIRA',
                'last_name' => 'SETIAWAN',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '90050358',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1803'
            ],
            [
                // POLRES BIMA KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ABDUL RACHMAN VIRGA',
                'last_name' => 'MAULIDHANY YUSUF',
                'last_title' => 'S.Tr.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '93081206',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1806'
            ],
            [
                // POLRES DOMPU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HERMANSYAH',
                'last_name' => '',
                'last_title' => 'S.Sos.',
                'rank_id' => 'AKP',
                'register_number' => '75100292',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1805'
            ],
            [
                // POLRES SUMBAWA BARAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'I MADE',
                'last_name' => 'SUGIARTA',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '78040188',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1809'
            ],
            [
                // POLRES SUMBAWA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SAMSUL',
                'last_name' => 'HILAL',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '79090740',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1804'
            ],
            [
                // POLRES BIMA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'NIKO',
                'last_name' => 'HERDIANTO',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '91090499',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1808',
            ],
            [
                // POLRES LOMBOK BARAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AGUS',
                'last_name' => 'RACHMAN',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '79080230',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1807',
            ],
            [
                // POLRES LOMBOK UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ASRI PUTRA',
                'last_name' => 'BAHARI',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPTU',
                'register_number' => '94091288',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1810',
            ],


            // POLDA NTT
            [
                // POLRES ALOR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ROBBY',
                'last_name' => "BU'U",
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '79070950',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1911'
            ],
            [
                // POLRES ENDE
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'GUSTI KOMANG',
                'last_name' => 'ASTINA',
                'last_title' => 'S.H.',
                'rank_id' => 'IPDA',
                'register_number' => '74020410',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1906'
            ],
            [
                // POLRES TIMOR TENGAH UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RAHMAT AGUS',
                'last_name' => 'IBRAHIM',
                'last_title' => 'S.E.',
                'rank_id' => 'IPTU',
                'register_number' => '81110413',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1902'
            ],
            [
                // POLRES BELU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BRIAN',
                'last_name' => 'WICAKSONO',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '92120944',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1903'
            ],
            [
                // POLRES KUPANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'YOHANA ENDAH',
                'last_name' => 'NENO',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '77120355',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1916'
            ],
            [
                // POLRES KUPANG KOTA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ANGGELINA IKUN',
                'last_name' => 'SALLY',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '85060505',
                'position_id' => 'KASUBNIT 1 GAKKUM',
                'polres_id' => '1915'
            ],
            [
                // POLRES NAGEKEO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MELKY DAVIDSON',
                'last_name' => 'NENOBAIS',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '78050440',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1922'
            ],
            [
                // POLRES SIKKA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FIRAMUDIN',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '75070432',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1907'
            ],
            [
                // POLRES MANGGARAI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MADE HENDRA',
                'last_name' => 'KUSUMANATA',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '91080223',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1910'
            ],
            [
                // POLRES FLORES TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '1908'
            ],
            [
                // POLRES LEMBATA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ABDUL',
                'last_name' => 'MALIK',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '73070170',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1912'
            ],
            [
                // POLRES SUMBA BARAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JAINUDIN',
                'last_name' => '',
                'last_title' => 'S.Sos., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '80060297',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1905'
            ],
            [
                // POLRES SUMBA TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'NANDA',
                'last_name' => 'GUSTIANA',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPTU',
                'register_number' => '93081226',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1904'
            ],
            [
                // POLRES SUMBA BARAT DAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'I WAYAN',
                'last_name' => 'SUARDIKA',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '70050214',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1920'
            ],
            [
                // POLRES ROTE NDAO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RALLY BASYE',
                'last_name' => 'LERRICK',
                'last_title' => 'S.Sos., M.A.P.',
                'rank_id' => 'IPTU',
                'register_number' => '70070440',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1914'
            ],
            [
                // POLRES TIMOR TENGAH SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ILHAM ADE',
                'last_name' => 'PUTRA',
                'last_title' => 'S.T.K.',
                'rank_id' => 'IPTU',
                'register_number' => '91020220',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '1901'
            ],
            [
                // POLRES MALAKA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RUDY JACOB',
                'last_name' => 'JUNUS LEDO',
                'last_title' => 'S.H., S.I.K.',
                'rank_id' => 'AKBP',
                'register_number' => '79061541',
                'position_id' => 'KAPOLRES',
                'polres_id' => '1918'
            ],
            [
                // POLRES MANGGARAI TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'I KETUT',
                'last_name' => 'WIDIARTA',
                'last_title' => 'S.H., S.I.K., M.Si.',
                'rank_id' => 'AKBP',
                'register_number' => '79071569',
                'position_id' => 'KAPOLRES',
                'polres_id' => '1921'
            ],
            [
                // POLRES NGADA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'PADMO',
                'last_name' => 'ARIYANTO',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKBP',
                'register_number' => '77091078',
                'position_id' => 'KAPOLRES',
                'polres_id' => '1909'
            ],
            [
                // POLRES MANGGARAI BARAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FELLI',
                'last_name' => 'HERMANTO',
                'last_title' => 'S.I.K., M.Si.',
                'rank_id' => 'AKBP',
                'register_number' => '77061135',
                'position_id' => 'KAPOLRES',
                'polres_id' => '1913'
            ],
            [
                // POLRES SABU RAIJUA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JAKOB',
                'last_name' => 'SEUBELAN',
                'last_title' => 'S.H.',
                'rank_id' => 'AKBP',
                'register_number' => '70020401',
                'position_id' => 'KAPOLRES',
                'polres_id' => '1919'
            ],


            // POLDA KALBAR
            [
                // POLRES BENGKAYANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SANGIDUN',
                'last_name' => '',
                'last_title' => 'S.Sos.',
                'rank_id' => 'IPTU',
                'register_number' => '73050228',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2010'
            ],
            [
                // POLRES MELAWI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'NANDA SULVY',
                'last_name' => 'PRATAMA',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPTU',
                'register_number' => '94041325',
                'position_id' => 'PS. KASAT LANTAS',
                'polres_id' => '2011'
            ],
            [
                // POLRESTA PONTIANAK
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AULIA',
                'last_name' => 'HADIPUTRA',
                'last_title' => 'S.H., S.I.K.',
                'rank_id' => 'KOMPOL',
                'register_number' => '85072091',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2001'
            ],
            [
                // POLRES LANDAK
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'I NYOMAN',
                'last_name' => 'BUDI ARTAWAN',
                'last_title' => 'S.H., S.I.K., M.M.',
                'rank_id' => 'AKBP',
                'register_number' => '83041250',
                'position_id' => 'KAPOLRES',
                'polres_id' => '2009'
            ],
            [
                // POLRES SINTANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BUNGA TRI',
                'last_name' => 'YULITASARI',
                'last_title' => 'S.Tr.K., M.',
                'rank_id' => 'IPTU',
                'register_number' => '93071046',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2006'
            ],
            [
                // POLRES SAMBAS
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ALFADA',
                'last_name' => 'IMANSYAH',
                'last_title' => 'S.Tr.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '94041304',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2004'
            ],
            [
                // POLRES SINGKAWANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ARWIN AMRIH',
                'last_name' => 'WIENTAMA',
                'last_title' => 'S.H., S.I.K., M.H.',
                'rank_id' => 'AKBP',
                'register_number' => '80061204',
                'position_id' => 'KAPOLRES',
                'polres_id' => '2003'
            ],
            [
                // POLRES KETAPANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ANGGA PRIBADI',
                'last_name' => 'AMSRIYANTO NAINGGOLAN',
                'last_title' => 'S.T.K., S.I.K., LL.M.',
                'rank_id' => 'AKP',
                'register_number' => '92030545',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2007'
            ],
            [
                // POLRES SEKADAU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUCH',
                'last_name' => 'SHOFIAN',
                'last_title' => 'S.AP, M.AP.',
                'rank_id' => 'AKP',
                'register_number' => '76020409',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2012'
            ],
            [
                // POLRES KAYONG UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BAGUS TRI',
                'last_name' => 'BASKORO',
                'last_title' => 'S.H., M.Si.',
                'rank_id' => 'IPTU',
                'register_number' => '82020282',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2013'
            ],
            [
                // POLRES KUBU RAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ARIEF',
                'last_name' => 'HIDAYAT',
                'last_title' => 'S.H., S.I.K.',
                'rank_id' => 'AKBP',
                'register_number' => '79011211',
                'position_id' => 'KAPOLRES',
                'polres_id' => '2015'
            ],
            [
                // POLRES MEMPAWAH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FAUZAN',
                'last_name' => 'SUKMAWANSYAH',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKBP',
                'register_number' => '79030875',
                'position_id' => 'KAPOLRES',
                'polres_id' => '2002'
            ],
            [
                // POLRES KAPUAS HULU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'USMAN',
                'last_name' => 'HASIBUAN',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '80030087',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2008'
            ],
            [
                // POLRES SANGGAU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'YUNITA',
                'last_name' => 'PUSPITA SARI',
                'last_title' => 'S.Tr.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '94061284',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2005'
            ],


            // POLDA KALTENG
            [
                // POLRES KATINGAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HARIYANTO',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '77090323',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2111',
            ],
            [
                // POLRES BARITO UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'WILDANIAR',
                'last_name' => 'KONDOWANGKO',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '93111038',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2102',
            ],
            [
                // POLRES KOTIM
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AZMI HALIM',
                'last_name' => 'PERMANA',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '89020643',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2105',
            ],
            [
                // POLRES KOBAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BAYU C.',
                'last_name' => 'TRI HARDIYANTO',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '92050623',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2104',
            ],
            [
                // POLRES KAPUAS
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUGENG',
                'last_name' => '',
                'last_title' => 'S.E., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '77010642',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2103',
            ],
            [
                // POLRES MURUNG RAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ANDRI',
                'last_name' => 'WICAKSONO',
                'last_title' => 'S.Sos.',
                'rank_id' => 'IPTU',
                'register_number' => '81120365',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2108',
            ],
            [
                // POLRES PALANGKA RAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FERIZA WINANDA',
                'last_name' => 'LUBIS',
                'last_title' => 'S.H., S.I.K.',
                'rank_id' => 'KOMPOL',
                'register_number' => '85021418',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2101',
            ],
            [
                // POLRES GUNUNG MAS
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DINDIN',
                'last_name' => 'MAHMUDIN',
                'last_title' => 'S.I.P.',
                'rank_id' => 'IPTU',
                'register_number' => '81080170',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2112',
            ],
            [
                // POLRES LAMANDAU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MOCH.',
                'last_name' => 'ROMADHON',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '74080354',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2113',
            ],
            [
                // POLRES BARITO TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'IRFAN MOCHAMMAD',
                'last_name' => 'NUR ALIREJA',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '90080300',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2107',
            ],
            [
                // POLRES SERUYAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'PRIO',
                'last_name' => 'AMBORO',
                'last_title' => 'S.T.',
                'rank_id' => 'IPTU',
                'register_number' => '80071414',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2110',
            ],
            [
                // POLRES BARITO SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'NOFIANA',
                'last_name' => 'RAHMY',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPTU',
                'register_number' => '93111142',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2106',
            ],
            [
                // POLRES PULANG PISAU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'KURNIAWAN',
                'last_name' => 'HARTONO',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKBP',
                'register_number' => '80100968',
                'position_id' => 'KAPOLRES',
                'polres_id' => '2109',
            ],
            [
                // POLRES SUKAMARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '2114',
            ],


            // POLDA KALSEL
            [
                // POLRESTA BANJARMASIN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'M.NOOR',
                'last_name' => 'CHAIDIR',
                'last_title' => 'S.H., S.I.K.',
                'rank_id' => 'KOMPOL',
                'register_number' => '84081962',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2201',
            ],
            [
                // POLRES BANJARBARU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'G.M.ANGGA',
                'last_name' => 'SATRYA WIBAWA',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '90120253',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2202',
            ],
            [
                // POLRES BANJAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'EKO',
                'last_name' => 'GUNTAR',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '93020884',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2203',
            ],
            [
                // POLRES TAPIN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'IMAM',
                'last_name' => 'SURYANA',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '75050359',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2204',
            ],
            [
                // POLRES HULU SUNGAI SELATAN (HSS)
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'OKI',
                'last_name' => 'HERMAWAN',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '80100759',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2205',
            ],
            [
                // POLRES HULU SUNGAI TENGAH (HST)
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '2206',
            ],
            [
                // POLRES HULU SUNGAI UTARA (HSU)
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TUGIYANA',
                'last_name' => '',
                'last_title' => 'S.A.P.',
                'rank_id' => 'AKP',
                'register_number' => '78010761',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2207',
            ],
            [
                // POLRES TABALONG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RIO ANGGA',
                'last_name' => 'PRASETYO',
                'last_title' => 'S.I.K., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '92020361',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2208'
            ],
            [
                // POLRES BALANGAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'H.TATAK',
                'last_name' => 'KUSDARYONO',
                'last_title' => 'S.M.',
                'rank_id' => 'IPTU',
                'register_number' => '69010482',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2209'
            ],
            [
                // POLRES KOTABARU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ADITYA',
                'last_name' => 'HADMANTO',
                'last_title' => 'S.Tr.K., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '96051253',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2210'
            ],
            [
                // POLRES TANAH BUMBU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'GUNTUR SETYO',
                'last_name' => 'PAMBUDI',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '92010425',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2211'
            ],
            [
                // POLRES TANAH LAUT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUPRIYATNO',
                'last_name' => '',
                'last_title' => 'S.Sos.',
                'rank_id' => 'AKP',
                'register_number' => '78040220',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2212'
            ],
            [
                // POLRES BATOLA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ROYKE NOLDY',
                'last_name' => 'DAREAN',
                'last_title' => 'S.Tr.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '94111205',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2213'
            ],


            // POLDA KALTIM
            [
                // POLRESTA SAMARINDA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'CREATO SONITEHE',
                'last_name' => 'GULO',
                'last_title' => 'S.H., S.I.K., M.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '87071768',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2302'
            ],
            [
                // POLRESTA BALIKPAPAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ROPIYANI',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '79040003',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2301'
            ],
            [
                // POLRES KUTAI KARTANEGARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'REZA PRATAMA',
                'last_name' => 'RAMDANI YUSUF',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '90030381',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2306'
            ],
            [
                // POLRES PENAJAM PASER UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'NING TYAS',
                'last_name' => 'WIDYAS MITA',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '92030433',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2313'
            ],
            [
                // POLRES BONTANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TONI JOKO',
                'last_name' => 'PURNOYO',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '71100351',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2308'
            ],
            [
                // POLRES BERAU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'EDO DAMARA',
                'last_name' => 'YUDHA',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '91120452',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2305'
            ],
            [
                // POLRES PASER
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HARI',
                'last_name' => 'PURNOYO',
                'last_title' => 'S.Sos., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '69120489',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2307'
            ],
            // [
            //     // POLRES MAHAKAM ULU
            //     'id' => Str::uuid(),
            //     'first_title' => '',
            //     'first_name' => 'BUDI',
            //     'last_name' => 'SANTOSO',
            //     'last_title' => 'S.H.',
            //     'rank_id' => 'IPDA',
            //     'register_number' => '79110708',
            //     'position_id' => 'PS. KASAT LANTAS',
            //     'polres_id' => '2315'
            // ],
            [
                // POLRES KUTAI TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ISNAN FATAH',
                'last_name' => "MA'RUF",
                'last_title' => 'S.H., M.AP.',
                'rank_id' => 'AKP',
                'register_number' => '76100226',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2311'
            ],
            [
                // POLRES KUTAI BARAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BUDI',
                'last_name' => 'WITIKNO',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '70050448',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2312'
            ],


            // POLDA SULUT
            [
                // POLRESTA MANADO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BENYAMIN NOLDIE',
                'last_name' => 'UNDAP',
                'last_title' => 'S.Sos.',
                'rank_id' => 'KOMPOL',
                'register_number' => '74110063',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2401'
            ],
            [
                // POLRES BOOLANG MONGONDOW UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HARIS',
                'last_name' => 'MOKODOMPIT',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '66010220',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2413'
            ],
            [
                // POLRES KEPULAUAN SITARO (SIAU DAN BIARO)
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MELDY',
                'last_name' => 'RORING',
                'last_title' => '',
                'rank_id' => 'IPTU',
                'register_number' => '70050066',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2415'
            ],
            [
                // POLRES BOOLANG MONGONDOW
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AGUS',
                'last_name' => 'JULIANTO',
                'last_title' => '',
                'rank_id' => 'IPTU',
                'register_number' => '69070041',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2414'
            ],
            [
                // POLRES BOOLANG MONGONDOW TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ALFRITS',
                'last_name' => 'WUNGKANA',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '66040246',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2411'
            ],
            [
                // POLRES BITUNG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AWALUDIN',
                'last_name' => 'PUHI',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '90110279',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2403'
            ],
            [
                // POLRES MINAHASA UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JULIO JAGRATARA',
                'last_name' => 'TAMPOI',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '90070343',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2407'
            ],
            [
                // POLRES MINAHASA SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BAYU DAMARA',
                'last_name' => 'HADI PUTRA',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '92120939',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2408'
            ],
            [
                // POLRES KOTAMOBAGU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SHIRLEY B. D.',
                'last_name' => 'MANGELEP',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '81080159',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2405'
            ],
            [
                // POLRES TOMOHON
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'NICKY N',
                'last_name' => 'PONDALOS',
                'last_title' => 'S.IP.',
                'rank_id' => 'IPTU',
                'register_number' => '80060183',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2409'
            ],
            [
                // POLRES KEPULAUAN TALAUD
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ODNIEL R.',
                'last_name' => 'NUSA',
                'last_title' => '',
                'rank_id' => 'IPTU',
                'register_number' => '71030295',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2406'
            ],
            [
                // POLRES KEPULAUN SANGIHE
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'M. ERZA TRI',
                'last_name' => 'SYAHPUTRA NASUTION',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPTU',
                'register_number' => '94051266',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2404'
            ],
            [
                // POLRES BOLAANG MONGONDOW SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RONA T. J.',
                'last_name' => 'MUKUAN',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '66070534',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2412'
            ],
            [
                // POLRES MINAHASA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RIYAN',
                'last_name' => 'WAHYUNINGTIYAS',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKP',
                'register_number' => '87111382',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2402'
            ],
            [
                // POLRES MINAHASA TENGGARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SOFYAN',
                'last_name' => 'MINIAGA',
                'last_title' => '',
                'rank_id' => 'IPTU',
                'register_number' => '68050282',
                'position_id' => 'PS. KASAT LANTAS',
                'polres_id' => '2416'
            ],


            // POLDA SULTENG
            [
                // POLRESTA PALU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'I DEWA',
                'last_name' => 'MADE ARDA',
                'last_title' => 'S.H., S.I.K.',
                'rank_id' => 'KOMPOL',
                'register_number' => '880411110',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2501'
            ],
            [
                // POLRES BANGGAI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'I MADE',
                'last_name' => 'BAGUS ADITYA M.',
                'last_title' => 'S.T.K., S.I.K., MAIC.',
                'rank_id' => 'AKP',
                'register_number' => '93051102',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2505'
            ],
            [
                // POLRES PARIMO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MURIYANTO',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '74030415',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2508'
            ],
            [
                // POLRES POSO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUHAMMAD',
                'last_name' => 'NAI',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '72050398',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2503'
            ],
            [
                // POLRES BUOL
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ADE IRFAN',
                'last_name' => 'RIVAI KURNIA',
                'last_title' => 'S.Tr.K., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '96031145',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2506'
            ],
            [
                // POLRES TOLITOLI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BUDI',
                'last_name' => 'PRASETYO',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '82050224',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2504'
            ],
            [
                // POLRES DONGGALA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ARIS',
                'last_name' => 'SUHENDAR',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '92030548',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2502'
            ],
            [
                // POLRES SIGI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HENDRIK',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '83070178',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2511',
            ],
            [
                // POLRES TOUNA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ASWAN',
                'last_name' => 'PRAYOGO',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPTU',
                'register_number' => '94081260',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2509',
            ],
            [
                // POLRES MOROWALI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ATMAJI SUGENG',
                'last_name' => 'WIBOWO',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '93031046',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2507',
            ],
            [
                // POLRES MOROWALI UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ARTA DWI',
                'last_name' => 'KUSUMA',
                'last_title' => 'S.T.K., S.I.K., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '91030284',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2513',
            ],
            [
                // POLRES BANGGAI KEPULAUAN (BANGKEP)
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'I KETUT',
                'last_name' => 'SIGIARTA',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '80060178',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2510',
            ],


            // POLDA SULSEL
            [
                // POLRES BARRU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ABDUL',
                'last_name' => 'MALIK',
                'last_title' => 'S.Sos.',
                'rank_id' => 'AKP',
                'register_number' => '76060343',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2611'
            ],
            [
                // POLRES LUWU UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ABANG',
                'last_name' => 'LAMUDDING',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '69050053',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2618'
            ],
            [
                // POLRES TAKALAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SYAHARUDDIN',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '68120349',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2603'
            ],
            [
                // POLRES JENEPONTO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUDIRMAN',
                'last_name' => '',
                'last_title' => 'S.Sos.',
                'rank_id' => 'IPTU',
                'register_number' => '78070399',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2607'
            ],
            [
                // POLRES PINRANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'NAWIR',
                'last_name' => 'EMING',
                'last_title' => 'S.E.',
                'rank_id' => 'AKP',
                'register_number' => '72120408',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2613'
            ],
            [
                // POLRES BANTAENG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUSMULYADI',
                'last_name' => '',
                'last_title' => 'S.Pd.I.',
                'rank_id' => 'IPTU',
                'register_number' => '70060093',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2606'
            ],
            [
                // POLRES SOPPENG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SAHARUNA',
                'last_name' => 'SAHAR',
                'last_title' => 'S.Sos., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '74120083',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2623'
            ],
            [
                // POLRES MAROS
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SYAMSIR',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '77070391',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '2604'
            ],
            [
                // POLRES SIDRAP
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MAHRUS',
                'last_name' => 'IBRAHIM',
                'last_title' => 'S.Sos.',
                'rank_id' => 'AKP',
                'register_number' => '74090293',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2612'
            ],
            [
                // POLRES KEPULAUAN SELAYAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUH.',
                'last_name' => 'MUAZ',
                'last_title' => 'S.Sos.',
                'rank_id' => 'IPTU',
                'register_number' => '78070315',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2609'
            ],
            [
                // POLRES LUWU TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SARIFUDDIN',
                'last_name' => '',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '78020265',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2629'
            ],
            [
                // POLRES PELABUHAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUH.',
                'last_name' => 'ALI',
                'last_title' => 'S.Sos.',
                'rank_id' => 'AKP',
                'register_number' => '73010075',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2630'
            ],
            [
                // POLRES BONE
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DESY AYU',
                'last_name' => 'DWI PUTRI',
                'last_title' => 'S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '91120457',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2621'
            ],
            [
                // POLRES TANA TORAJA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ADNAN',
                'last_name' => 'LEPPANG',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '82040028',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2619'
            ],
            [
                // POLRES LUWU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'H. M. NAWIR',
                'last_name' => '',
                'last_title' => 'S.Sos.',
                'rank_id' => 'AKP',
                'register_number' => '76040677',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2617'
            ],
            [
                // POLRES PANGKEP
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JUMADI',
                'last_name' => '',
                'last_title' => 'S.I.P.',
                'rank_id' => 'IPTU',
                'register_number' => '75010903',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2605'
            ],
            [
                // POLRES PARE - PARE
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUHAMAD',
                'last_name' => 'ARAFAH',
                'last_title' => 'S.I.P.',
                'rank_id' => 'AKP',
                'register_number' => '78070242',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2610'
            ],
            [
                // POLRES TORAJA UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'EKO',
                'last_name' => 'SUROSO',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKBP',
                'register_number' => '76010882',
                'position_id' => 'KAPOLRES',
                'polres_id' => '2632'
            ],
            [
                // POLRESTA GOWA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ANDI',
                'last_name' => 'MUH. YUSUF S.',
                'last_title' => 'S.Sos.',
                'rank_id' => 'IPTU',
                'register_number' => '79050860',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2602'
            ],
            [
                // POLRES SINJAI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUHAMMAD',
                'last_name' => 'IDRIS',
                'last_title' => 'S.Sos.',
                'rank_id' => 'IPTU',
                'register_number' => '74060552',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2624'
            ],
            [
                // POLRES WAJO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HARYANTO',
                'last_name' => '',
                'last_title' => 'S.Sos.',
                'rank_id' => 'AKP',
                'register_number' => '71090108',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2622'
            ],
            [
                // POLRES PALOPO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SISWAJI',
                'last_name' => '',
                'last_title' => 'S.Sos.',
                'rank_id' => 'IPTU',
                'register_number' => '79040028',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2628'
            ],
            [
                // POLRES ENREKANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'IBRAHIM',
                'last_name' => '',
                'last_title' => 'S.E.',
                'rank_id' => 'IPTU',
                'register_number' => '77110087',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2620'
            ],
            [
                // POLRES BULUKUMBA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ARDYANSYAH',
                'last_name' => '',
                'last_title' => 'S.I.K., M.Si.',
                'rank_id' => 'AKBP',
                'register_number' => '80111075',
                'position_id' => 'KAPOLRES',
                'polres_id' => '2608'
            ],
            [
                // POLRESTABES MAKASSAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ZULANDA',
                'last_name' => '',
                'last_title' => 'S.I.K., M.Si.',
                'rank_id' => 'AKBP',
                'register_number' => '81040854',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2601'
            ],


            // POLDA SULTRA
            [
                // POLRES BUTON UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'USMAN',
                'last_name' => 'LANNA',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '80100468',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2713',
            ],
            [
                // POLRES KOLAKA UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DEDA KRESNA',
                'last_name' => 'WIJAYA',
                'last_title' => 'S.Tr.K., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '94121349',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2707',
            ],
            [
                // POLRES BAUBAU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BANGGA PARNADIN',
                'last_name' => 'SIDAURUK',
                'last_title' => 'S.Tr.K., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '93071066',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2703',
            ],
            [
                // POLRES KOLAKA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'REZA',
                'last_name' => 'AMIRUDIN',
                'last_title' => 'S.T.K.',
                'rank_id' => 'IPTU',
                'register_number' => '91110489',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2702',
            ],
            [
                // POLRES BOMBANA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ANDI UDIN',
                'last_name' => 'PALISURI',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '77080278',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2709',
            ],
            [
                // POLRES WAKATOBI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AWALUDDIN',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '80010100',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2710',
            ],
            [
                // POLRES KONAWE SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ABDUL AZIS',
                'last_name' => 'HUSEIN LUBIS',
                'last_title' => 'S.T.K., S.I.K., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '93061048',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2706',
            ],
            /*[
                // POLRES BUTON TENGAH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SAMSUDDIN',
                'last_name' => '',
                'last_title' => 'S.Sos.',
                'rank_id' => 'IPTU',
                'register_number' => '69050388',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2715',
            ],*/
            [
                // POLRES BUTON
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUHERMIN',
                'last_name' => '',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '78090926',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2708',
            ],
            [
                // POLRES KONAWE
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RIDWAN',
                'last_name' => '',
                'last_title' => 'S.T.K., S.I.K., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '91040318',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2705',
            ],
            [
                // POLRES KONAWE UTARA
                'id' => Str::uuid(),        
                'first_title' => '',        
                'first_name' => 'ANSYAR. R',        
                'last_name' => '',        
                'last_title' => 'S.E.',        
                'rank_id' => 'IPTU',        
                'register_number' => '77030229',        
                'position_id' => 'KASAT LANTAS',        
                'polres_id' => '2712',    
            ],
            [        
                // POLRESTA KENDARI
                'id' => Str::uuid(),        
                'first_title' => '',        
                'first_name' => 'MUCHSIN',        
                'last_name' => '',        
                'last_title' => 'S.Si.',        
                'rank_id' => 'AKP',        
                'register_number' => '77050236',        
                'position_id' => 'KASAT LANTAS',        
                'polres_id' => '2701',    
            ],
            [        
                // POLRES MUNA
                'id' => Str::uuid(),        
                'first_title' => '',        
                'first_name' => 'YUSRAN YOYO',        
                'last_name' => '',        
                'last_title' => 'S.IP.',        
                'rank_id' => 'IPTU',        
                'register_number' => '79041075',        
                'position_id' => 'KASAT LANTAS',        
                'polres_id' => '2704',    
            ],
            /*[       
                // POLRES KOLAKA TIMUR 
                'id' => Str::uuid(),        
                'first_title' => '',        
                'first_name' => 'MUHLISI',        
                'last_name' => '',        
                'last_title' => 'S.H.',        
                'rank_id' => 'IPTU',        
                'register_number' => '79010423',        
                'position_id' => 'KASAT LANTAS',        
                'polres_id' => '2714',    
            ],*/


            // POLDA GORONTALO
            [
                // POLRESTA GORONTALO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUPOMO',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'AKP',
                'register_number' => '77080010',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2806',
            ],
            [
                // POLRES BONE BOLANGO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'IRAWAN',
                'last_name' => 'KUSUMO',
                'last_title' => 'S.Tr.K., S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '94031227',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2805',
            ],
            [
                // POLRES GORONTALO UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'NURMAYA',
                'last_name' => 'KASIM',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '82090542',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2808',
            ],
            [
                // POLRES BOALEMO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BRATA CITRA',
                'last_name' => 'SAKTI PURNOMO',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPDA',
                'register_number' => '96071211',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2804',
            ],
            [
                // POLRES GORONTALO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'GERRYLIYUS',
                'last_name' => 'FEBRERA',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPTU',
                'register_number' => '93020889',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2801',
            ],
            [
                // POLRES POHUWATO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'YUDHA BHARA',
                'last_name' => 'ANORAGA PUTRA',
                'last_title' => 'S.Tr.K., M.Sc(Eng).',
                'rank_id' => 'IPTU',
                'register_number' => '93061089',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2803',
            ],


            // POLDA MALUKU
            [
                // POLRESTA P.AMBON & P.P LEASE
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SENJA',
                'last_name' => 'PRATAMA',
                'last_title' => 'S.H., S.I.K.',
                'rank_id' => 'KOMPOL',
                'register_number' => '84091813',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2901',
            ],
            [
                // POLRES BURU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FRANS',
                'last_name' => 'NIOVALDO',
                'last_title' => 'S.T.K., S.I.K.',
                'rank_id' => 'IPTU',
                'register_number' => '92110880',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2904',
            ],
            /*[
                // POLRES MALRA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FRANS',
                'last_name' => 'DUMA',
                'last_title' => 'S.P.',
                'rank_id' => 'AKBP',
                'register_number' => '73030701',
                'position_id' => 'KAPOLRES',
                'polres_id' => '2912',
            ],*/
            [
                // POLRES SERAM BAGIAN TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'KADEK BOBY',
                'last_name' => 'HARTA SETIADI',
                'last_title' => '',
                'rank_id' => 'IPTU',
                'register_number' => '78120252',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2906',
            ],
            [
                // POLRES KEPULAUAN TANIMBAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'UMAR',
                'last_name' => 'WIJAYA',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKBP',
                'register_number' => '77030937',
                'position_id' => 'KAPOLRES',
                'polres_id' => '2905',
            ],
            /*[
                // POLRES BURU SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ORGENES A.',
                'last_name' => 'PEILOUW',
                'last_title' => '',
                'rank_id' => 'IPTU',
                'register_number' => '71100103',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2911',
            ],*/
            [
                // POLRES MALUKU TENGAH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MAULIDI P.',
                'last_name' => 'HUSIN',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPTU',
                'register_number' => '94081297',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2902',
            ],
            [
                // POLRES MALUKU BARAT DAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'KAFNES',
                'last_name' => 'MOLLSE',
                'last_title' => 'S.Sos.',
                'rank_id' => 'IPTU',
                'register_number' => '79090004',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2909',
            ],
            [
                // POLRES SERAM BAGIAN BARAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'LUKAS DANIEL',
                'last_name' => 'TAMAELA',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '67120592',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2907',
            ],
            [
                // POLRES KEPULAUAN ARU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'YUSTINUS',
                'last_name' => 'PINI',
                'last_title' => '',
                'rank_id' => 'IPTU',
                'register_number' => '68060056',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2908',
            ],
            [
                // POLRES TUAL
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RERIT',
                'last_name' => 'OKTAFIANDI',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPTU',
                'register_number' => '95101218',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '2903',
            ],


            // POLDA MALUT
            [
                // POLRES TERNATE
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'REZZA MUHAMMAD',
                'last_name' => 'FAJRIN',
                'last_title' => 'S.T.K.',
                'rank_id' => 'IPTU',
                'register_number' => '93091041',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3001',
            ],
            [
                // POLRES PULAU HALMAHERA UTARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'IBRAHIM',
                'last_name' => 'MAPPE',
                'last_title' => 'S.E.',
                'rank_id' => 'IPTU',
                'register_number' => '76050911',
                'position_id' => 'PS. KASAT LANTAS',
                'polres_id' => '3003',
            ],
            [
                // POLRESTA TIDORE
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'EDI',
                'last_name' => 'SUGIHARTO',
                'last_title' => 'S.E., M.H.',
                'rank_id' => 'AKBP',
                'register_number' => '75020664',
                'position_id' => 'WAKAPOLRES',
                'polres_id' => '3002',
            ],
            [
                // POLRES HALMAHERA BARAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RQCHMAD DS.',
                'last_name' => '',
                'last_title' => 'S.I.P., M.Si.',
                'rank_id' => 'KOMPOL',
                'register_number' => '66100463',
                'position_id' => 'WAKAPOLRES',
                'polres_id' => '3005',
            ],
            [
                // POLRES HALMAHERA TENGAH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'IRFAN MUZAFFAR',
                'last_name' => 'SONDANI',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPDA',
                'register_number' => '97090956',
                'position_id' => 'PS. KASAT LANTAS',
                'polres_id' => '3007',
            ],
            [
                // POLRES HALMAHERA TIMUR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'IKWAN',
                'last_name' => '',
                'last_title' => 'S.E.',
                'rank_id' => 'IPTU',
                'register_number' => '77050544',
                'position_id' => 'PS. KASAT LANTAS',
                'polres_id' => '3006',
            ],
            [
                // POLRES HALMAHERA SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MIRSAN',
                'last_name' => 'YASSIN',
                'last_title' => 'S.H.',
                'rank_id' => 'KOMPOL',
                'register_number' => '73020480',
                'position_id' => 'WAKAPOLRES',
                'polres_id' => '3008',
            ],
            [
                // POLRES KEP. SULA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'WALID',
                'last_name' => 'BUAMONA',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '79040535',
                'position_id' => 'PS. KASAT LANTAS',
                'polres_id' => '3004',
            ],
            [
                // POLRES PULAU MOROTAI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUWANDI',
                'last_name' => '',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '84080470',
                'position_id' => 'PS. KASAT LANTAS',
                'polres_id' => '3009',
            ],
            /*[
                // POLRES PULAU TALIABU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ABDULLAH',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '68090337',
                'position_id' => 'PS. KASAT LANTAS',
                'polres_id' => '3011',
            ],*/


            // POLDA PAPUA
            [
                // POLRES KOTA JAYAPURA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'PILLOMINA IDA',
                'last_name' => 'WAYMRAMRA',
                'last_title' => 'S.E., S.I.K.',
                'rank_id' => 'KOMPOL',
                'register_number' => '84061809',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3101'
            ],
            [
                // POLRES JAYAWIJAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'REZA HILMY',
                'last_name' => 'WIDI PUTRA',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPDA',
                'register_number' => '96121123',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3102'
            ],
            [
                // POLRES BIAK NUMFOR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BOBY',
                'last_name' => 'PRATAMA',
                'last_title' => 'S.T.K.',
                'rank_id' => 'IPTU',
                'register_number' => '91010288',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3103'
            ],
            [
                // POLRES MERAUKE
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'NOVINDRIANI',
                'last_name' => '',
                'last_title' => 'S.I.K., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '92110383',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3106'
            ],
            [
                // POLRES NABIRE
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JUSMAN',
                'last_name' => 'MORI',
                'last_title' => 'S.I.K., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '92020363',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3108'
            ],
            [
                // POLRES KEP YAPEN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUHAMMAD',
                'last_name' => 'ARIEF',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '67010408',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3109'
            ],
            [
                // POLRES MIMIKA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'DARWIS',
                'last_name' => '',
                'last_title' => 'S.Sos., M.M.',
                'rank_id' => 'AKP',
                'register_number' => '76090526',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3110'
            ],
            [
                // POLRES MIMIKA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUH. MARDANI',
                'last_name' => 'FAHACER',
                'last_title' => 'S.Tr.K., M.H.',
                'rank_id' => 'IPDA',
                'register_number' => '98030817',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '3110'
            ],
            [
                // POLRES JAYAPURA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'BAHARUDIN',
                'last_name' => 'BUTON',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '78040635',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3111'
            ],
            [
                // POLRES KEEROM
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUHAMMAD',
                'last_name' => 'AKBAR',
                'last_title' => 'S.Sos.',
                'rank_id' => 'AKP',
                'register_number' => '74010154',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3114'
            ],
            [
                // POLRES SARMI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'WARSITO',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '83010814',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3116'
            ],
            [
                // POLRES YAHUKIMO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '3119'
            ],
            [
                // POLRES BOVEN DIGOEL
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HARIYANTO',
                'last_name' => '',
                'last_title' => 'S.Sos.',
                'rank_id' => 'AKP',
                'register_number' => '72010349',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3120'
            ],
            [
                // POLRES WAROPEN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TAUFIK',
                'last_name' => 'HIDAYAT',
                'last_title' => 'S.H., M.H.',
                'rank_id' => 'IPDA',
                'register_number' => '85010862',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3122'
            ],
            [
                // POLRES MAPPI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'I NENGAH',
                'last_name' => 'SUKAYADNYA',
                'last_title' => '',
                'rank_id' => 'IPTU',
                'register_number' => '72090111',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3123',
            ],
            [
                // POLRES SUPIORI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AMELIA W.',
                'last_name' => 'RUMBIAK',
                'last_title' => 'S.I.P.',
                'rank_id' => 'AKP',
                'register_number' => '76060607',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3124',
            ],
            [
                // POLRES PUNCAK JAYA
                'id' => Str::uuid(),
                'first_title' => 'Dr.',
                'first_name' => 'HENDRIK R.',
                'last_name' => 'SIPAHUTAR',
                'last_title' => 'S.Sos., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '79070121',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3125',
            ],
            [
                // POLRES PEG BINTANG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '3127',
            ],
            [
                // POLRES TOLIKARA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MOCH SAIFUL',
                'last_name' => 'AMIN',
                'last_title' => 'S.H.',
                'rank_id' => 'IPDA',
                'register_number' => '84070459',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3128',
            ],
            [
                // POLRES PANIAI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUGIARTO',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => 'IPTU',
                'register_number' => '81070466',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3129',
            ],
            [
                // POLRES LANI JAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'LA ODE',
                'last_name' => 'ABDUL SYUKUR',
                'last_title' => '',
                'rank_id' => 'IPDA',
                'register_number' => '79061329',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3131',
            ],
            [
                // POLRES MAMBERAMO TENGAH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '3132'
            ],
            [
                // POLRES MAMBERAMO RAYA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SLAMET',
                'last_name' => 'SABARYANTO',
                'last_title' => 'S.Sos.',
                'rank_id' => 'IPTU',
                'register_number' => '72090109',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3133'
            ],
            [
                // POLRES YALIMO
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUH.',
                'last_name' => 'YUSUF',
                'last_title' => 'S.Sos.',
                'rank_id' => 'IPDA',
                'register_number' => '84011223',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3134'
            ],
            /*[
                // POLRES DOGIYAI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'WIDO',
                'last_name' => 'PURWANTO',
                'last_title' => '',
                'rank_id' => 'IPDA',
                'register_number' => '73080289',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3135'
            ],*/
            /*[
                // POLRES DEIYAI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '3136'
            ],*/
            // [
            //     // POLRES INTAN JAYA
            //     'id' => Str::uuid(),
            //     'first_title' => '',
            //     'first_name' => 'SERGIUS PAULUS',
            //     'last_name' => 'MUDUMI',
            //     'last_title' => '',
            //     'rank_id' => 'IPDA',
            //     'register_number' => '79071482',
            //     'position_id' => 'KASAT LANTAS',
            //     'polres_id' => '3137'
            // ],

            // POLDA SULBAR
            [
                // POLRES MAJENE
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'TONI',
                'last_name' => 'SUGADRI',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKBP',
                'register_number' => '77061156',
                'position_id' => 'KAPOLRES',
                'polres_id' => '3214'
            ],
            [
                // POLRES MAJENE
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MUHAMMAD',
                'last_name' => 'IRWAN',
                'last_title' => 'S.Sos.',
                'rank_id' => 'AKP',
                'register_number' => '70030417',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3214'
            ],
            [
                // POLRES PASANGKAYU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'CANDRA KURNIA',
                'last_name' => 'SETIAWAN',
                'last_title' => 'S.I.K.',
                'rank_id' => 'AKBP',
                'register_number' => '79101241',
                'position_id' => 'KAPOLRES',
                'polres_id' => '3227'
            ],
            [
                // POLRES PASANGKAYU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ABDUL AZIS',
                'last_name' => 'GANI',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '71120356',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3227'
            ],
            [
                // POLRES PASANGKAYU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'FIRMAN',
                'last_name' => 'SANUSI',
                'last_title' => 'S.H.',
                'rank_id' => 'IPTU',
                'register_number' => '860802742',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '3227'
            ],
            [
                // POLRES POLEWALI MANDAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AGUNG BUDI',
                'last_name' => 'LEKSONO',
                'last_title' => 'S.H., S.I.K., M.Pd.',
                'rank_id' => 'AKBP',
                'register_number' => '77081241',
                'position_id' => 'KAPOLRES',
                'polres_id' => '3215'
            ],
            [
                // POLRES POLEWALI MANDAR
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SETIYAJI',
                'last_name' => 'RAHMANSYAH, RA',
                'last_title' => 'S.Tr.K.',
                'rank_id' => 'IPTU',
                'register_number' => '94061295',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3215'
            ],
            [
                // POLRES MAMASA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HARRY',
                'last_name' => 'ANDREAS',
                'last_title' => 'S.I.K., M.M.',
                'rank_id' => 'AKBP',
                'register_number' => '79011209',
                'position_id' => 'KAPOLRES',
                'polres_id' => '3226'
            ],
            [
                // POLRES MAMASA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'HAERUDDIN',
                'last_name' => '',
                'last_title' => 'S.H.',
                'rank_id' => 'BRIGPOL',
                'register_number' => '92020252',
                'position_id' => 'KANIT GAKKUM',
                'polres_id' => '3226'
            ],
            [
                // POLRES MAMASA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'SUPRIADI',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => 'AKP',
                'register_number' => '74030381',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3226'
            ],
            [
                // POLRESTA MAMUJU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ISKANDAR',
                'last_name' => '',
                'last_title' => 'S.I.K., S.H., M.H.',
                'rank_id' => 'KOMBESPOL',
                'register_number' => '73060599',
                'position_id' => 'KAPOLRES',
                'polres_id' => '3216'
            ],
            [
                // POLRESTA MAMUJU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'JUNAID',
                'last_name' => '',
                'last_title' => 'S.Pd.',
                'rank_id' => 'IPTU',
                'register_number' => '74050173',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3216'
            ],
            [
                // POLRESTA MAMUJU TENGAH
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'MULHAM',
                'last_name' => '',
                'last_title' => 'S.E.',
                'rank_id' => 'IPTU',
                'register_number' => '80120307',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3229'
            ],


            // POLDA PAPUA BARAT
            [
                // POLRES MANOKWARI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '3304'
            ],
            [
                // POLRES SORONG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '3306'
            ],
            [
                // POLRES KAIMANA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '3307'
            ],
            [
                // POLRES BINTUNI
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '3308'
            ],
            [
                // POLRES TELUK WONDAMA
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '3309'
            ],
            [
                // POLRESTA SORONG
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '3310'
            ],
            [
                // POLRES SORONG SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '3311'
            ],
            [
                // POLRES RAJA AMPAT
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '3312'
            ],
            [
                // POLRES FAKFAK
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '3314'
            ],
            [
                // POLRES MANOKWARI SELATAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => '',
                'last_name' => '',
                'last_title' => '',
                'rank_id' => '',
                'register_number' => '',
                'position_id' => '',
                'polres_id' => '3315'
            ],
            

            // POLDA KALTARA
            [
                // POLRESTA BULUNGAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RADYAN KUNTO',
                'last_name' => 'WIBISONO',
                'last_title' => 'S.T.K., S.I.K., M.H.',
                'rank_id' => 'IPTU',
                'register_number' => '92110878',
                'position_id' => 'PS. KASAT LANTAS',
                'polres_id' => '3401'
            ],
            [
                // POLRES TARAKAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'RULLY ZULDH',
                'last_name' => 'FERMANA',
                'last_title' => 'S.I.K., M.Si.',
                'rank_id' => 'AKP',
                'register_number' => '93050368',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3402'
            ],
            [
                // POLRES MALINAU
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'ANGEL CHRISTY',
                'last_name' => 'H. G PONTOH',
                'last_title' => 'S.T.K., MSc.',
                'rank_id' => 'IPTU',
                'register_number' => '94011043',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3403'
            ],
            [
                // POLRES NUNUKAN
                'id' => Str::uuid(),
                'first_title' => '',
                'first_name' => 'AROFIEK APRILIAN',
                'last_name' => 'RISWANTO',
                'last_title' => 'S.H., S.I.K., M.H.',
                'rank_id' => 'AKP',
                'register_number' => '89040783',
                'position_id' => 'KASAT LANTAS',
                'polres_id' => '3404'
            ],
            // [
            //     // POLRES TANA TIDUNG
            //     'id' => Str::uuid(),
            //     'first_title' => '',
            //     'first_name' => 'FERRY AGUNG',
            //     'last_name' => 'APRIYANTO',
            //     'last_title' => 'S.E.',
            //     'rank_id' => 'IPDA',
            //     'register_number' => '83040993',
            //     'position_id' => 'PS. KASAT LANTAS',
            //     'polres_id' => '3406'
            // ],
        ];
    }
}
