<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Polda;

class RegionPolicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regionPolices = collect([
            [
                "id" => "01",
                "spptti_id" => "2",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.02",

                "name" => "ACEH",
                "full_name" => "ACEH",

                "timezone" => "+7",
                "address" => "Jl. Teuku Nyak Arief",
                "province_name" => "ACEH",
            ],
            [
                "id" => "02",
                "spptti_id" => "2",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.03",

                "name" => "SUMUT",
                "full_name" => "SUMATRA UTARA",

                "timezone" => "+7",
                "address" => "Jl. Tanjung Morawa Km. 10.5",
                "province_name" => "SUMATERA UTARA",
            ],
            [
                "id" => "03",
                "spptti_id" => "4",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.04",

                "name" => "SUMBAR",
                "full_name" => "SUMATRA BARAT",

                "timezone" => "+7",
                "address" => "",
                "province_name" => "SUMATERA BARAT",
            ],
            [
                "id" => "04",
                "spptti_id" => "28",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.05",

                "name" => "RIAU",
                "full_name" => "RIAU",

                "timezone" => "+7",
                "address" => "Jl. Jenderal Sudirman No. 235",
                "province_name" => "RIAU",
            ],
            [
                "id" => "05",
                "spptti_id" => "16",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.09",

                "name" => "BENGKULU",
                "full_name" => "BENGKULU",

                "timezone" => "+7",
                "address" => "Jalan Adam Malik Km 9",
                "province_name" => "BENGKULU",
            ],
            [
                "id" => "06",
                "spptti_id" => "19",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.10",

                "name" => "JAMBI",
                "full_name" => "JAMBI",

                "timezone" => "+7",
                "address" => "Jln.jendral Sudirman No.45",
                "province_name" => "JAMBI",
            ],
            [
                "id" => "07",
                "spptti_id" => "22",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.07",

                "name" => "SUMSEL",
                "full_name" => "SUMATRA SELATAN",

                "timezone" => "+7",
                "address" => "Jl. Jendral Sudirman Km 4,5, Pahlawan",
                "province_name" => "SUMATERA SELATAN",
            ],
            [
                "id" => "08",
                "spptti_id" => "6",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.11",

                "name" => "LAMPUNG",
                "full_name" => "LAMPUNG",

                "timezone" => "+7",
                "address" => "",
                "province_name" => "LAMPUNG",
            ],
            [
                "id" => "09",
                "spptti_id" => "5",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.08",

                "name" => "BABEL",
                "full_name" => "KEPULAUAN BANGKA BELITUNG",

                "timezone" => "+7",
                "address" => "",
                "province_name" => "KEPULAUAN BANGKA BELITUNG",
            ],
            [
                "id" => "10",
                "spptti_id" => "26",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.06",

                "name" => "KEPRI",
                "full_name" => "KEPULAUAN RIAU",

                "timezone" => "+7",
                "address" => "Jl. Hang Jebat 81 Batu Besar",
                "province_name" => "KEPULAUAN RIAU",
            ],
            [
                "id" => "11",
                "spptti_id" => "21",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.12",

                "name" => "METRO JAYA",
                "full_name" => "METRO JAYA",

                "timezone" => "+7",
                "address" => "Jl. Jenderal Sudirman No. 55, Rt.5/rw.3",
                "province_name" => "DKI JAKARTA",
            ],
            [
                "id" => "12",
                "spptti_id" => "18",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.13",

                "name" => "JABAR",
                "full_name" => "JAWA BARAT",

                "timezone" => "+7",
                "address" => "Jl Soekarno Hatta No.748",
                "province_name" => "JAWA BARAT",
            ],
            [
                "id" => "13",
                "spptti_id" => "32",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.15",

                "name" => "JATENG",
                "full_name" => "JAWA TENGAH",

                "timezone" => "+7",
                "address" => "Jl. Pahlawan No.1",
                "province_name" => "JAWA TENGAH",
            ],
            [
                "id" => "14",
                "spptti_id" => "7",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.16",

                "name" => "DIY",
                "full_name" => "D.I YOGYAKARTA",

                "timezone" => "+7",
                "address" => "",
                "province_name" => "DI YOGYAKARTA",
            ],
            [
                "id" => "15",
                "spptti_id" => "17",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.17",

                "name" => "JATIM",
                "full_name" => "JAWA TIMUR",

                "timezone" => "+7",
                "address" => "Jl. Ahmad Yani 116",
                "province_name" => "JAWA TIMUR",
            ],
            [
                "id" => "16",
                "spptti_id" => "14",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.14",

                "name" => "BANTEN",
                "full_name" => "BANTEN",

                "timezone" => "+7",
                "address" => "Jl. Syech Moh Nawawi Albantani No 76",
                "province_name" => "BANTEN",
            ],
            [
                "id" => "17",
                "spptti_id" => "15",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.22",

                "name" => "BALI",
                "full_name" => "BALI",

                "timezone" => "+8",
                "address" => "Jl. Wr.supratman No 7 Denpasar",
                "province_name" => "BALI",
            ],
            [
                "id" => "18",
                "spptti_id" => "10",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.23",

                "name" => "NTB",
                "full_name" => "NUSA TENGGARA BARAT",

                "timezone" => "+8",
                "address" => "Jl. Langko No. 77 Mataram",
                "province_name" => "NUSA TENGGARA BARAT",
            ],
            [
                "id" => "19",
                "spptti_id" => "23",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.24",

                "name" => "NTT",
                "full_name" => "NUSA TENGGARA TIMUR",

                "timezone" => "+8",
                "address" => "Jl. Sueharto Nomor 3",
                "province_name" => "NUSA TENGGARA TIMUR",
            ],
            [
                "id" => "20",
                "spptti_id" => "30",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.18",

                "name" => "KALBAR",
                "full_name" => "KALIMANTAN BARAT",

                "timezone" => "+7",
                "address" => "Jl. Ahmad Yani No 1 Pontianak",
                "province_name" => "KALIMANTAN BARAT",
            ],
            [
                "id" => "21",
                "spptti_id" => "27",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.20",

                "name" => "KALTENG",
                "full_name" => "KALIMANTAN TENGAH",

                "timezone" => "+7",
                "address" => "Jl. Tjilik Riwut Km. 1",
                "province_name" => "KALIMANTAN TENGAH",
            ],
            [
                "id" => "22",
                "spptti_id" => "12",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.19",

                "name" => "KALSEL",
                "full_name" => "KALIMANTAN SELATAN",

                "timezone" => "+8",
                "address" => "Jl. S. Parman No. 16",
                "province_name" => "KALIMANTAN SELATAN",
            ],
            [
                "id" => "23",
                "spptti_id" => "11",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.21",

                "name" => "KALTIM",
                "full_name" => "KALIMANTAN TIMUR",

                "timezone" => "+8",
                "address" => "Jl. Syarifuddin Yoes No.99",
                "province_name" => "KALIMANTAN TIMUR",
            ],
            [
                "id" => "24",
                "spptti_id" => "31",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.28",

                "name" => "SULUT",
                "full_name" => "SULAWESI UTARA",

                "timezone" => "+8",
                "address" => "l.bethesda No.62",
                "province_name" => "SULAWESI UTARA",
            ],
            [
                "id" => "25",
                "spptti_id" => "8",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.27",

                "name" => "SULTENG",
                "full_name" => "SULAWESI TENGAH",

                "timezone" => "+8",
                "address" => "",
                "province_name" => "SULAWESI TENGAH",
            ],
            [
                "id" => "26",
                "spptti_id" => "13",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.25",

                "name" => "SULSEL",
                "full_name" => "SULAWESI SELATAN",

                "timezone" => "+8",
                "address" => "Jl. Perintis Kemerdekaan Km 16",
                "province_name" => "SULAWESI SELATAN",
            ],
            [
                "id" => "27",
                "spptti_id" => "29",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.26",

                "name" => "SULTRA",
                "full_name" => "SULAWESI TENGGARA",

                "timezone" => "+8",
                "address" => "Jl. Haluoleo No. 1",
                "province_name" => "SULAWESI TENGGARA",
            ],
            [
                "id" => "28",
                "spptti_id" => "34",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.29",

                "name" => "GORONTALO",
                "full_name" => "GORONTALO",

                "timezone" => "+8",
                "address" => "Jl. Ahmad A Wahab",
                "province_name" => "GORONTALO",
            ],
            [
                "id" => "29",
                "spptti_id" => "33",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.30",

                "name" => "MALUKU",
                "full_name" => "MALUKU",

                "timezone" => "+9",
                "address" => "Jl. Rijali Nomor 1",
                "province_name" => "MALUKU",
            ],
            [
                "id" => "30",
                "spptti_id" => "9",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.31",

                "name" => "MALUT",
                "full_name" => "MALUKU UTARA",

                "timezone" => "+9",
                "address" => "Jl. Kapitan Pattimura",
                "province_name" => "MALUKU UTARA",
            ],
            [
                "id" => "31",
                "spptti_id" => "20",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.32",

                "name" => "PAPUA",
                "full_name" => "PAPUA",

                "timezone" => "+9",
                "address" => "Jl. Dr. Sam Ratulangi No 8",
                "province_name" => "PAPUA",
            ],
            [
                "id" => "32",
                "spptti_id" => "24",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.34",

                "name" => "SULBAR",
                "full_name" => "SULAWESI BARAT",

                "timezone" => "+8",
                "address" => "Jl. Aiptu Nurman 1",
                "province_name" => "SULAWESI BARAT",
            ],
            [
                "id" => "33",
                "spptti_id" => "25",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.33",

                "name" => "PAPUA BARAT",
                "full_name" => "PAPUA BARAT",

                "timezone" => "+9",
                "address" => "Jl. Pahlawan No 1 Manokwari",
                "province_name" => "PAPUA BARAT",
            ],
            [
                "id" => "34",
                "spptti_id" => "28990",
                "work_unit_code" => "0",
                "puskarda_code" => "060.01.35",

                "name" => "KALTARA",
                "full_name" => "KALIMANTAN UTARA",

                "timezone" => "+8",
                "address" => "Jl. Agatis Tanjung Selor",
                "province_name" => "KALIMANTAN UTARA",
            ],
        ]);

        DB::beginTransaction();
        try{
            // update to polda table
            foreach($regionPolices as $regionPolice) {
                Polda::where('id', $regionPolice['id'])->update([
                    'spptti_id' => $regionPolice['spptti_id'],
                    'work_unit_code' => $regionPolice['work_unit_code'],
                    'puskarda_code' => $regionPolice['puskarda_code'],
                    'name' => $regionPolice['name'],
                    'full_name' => $regionPolice['full_name'],
                    'timezone' => $regionPolice['timezone'],
                    'address' => $regionPolice['address'],
                    'province_name' => $regionPolice['province_name'],
                ]);
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
