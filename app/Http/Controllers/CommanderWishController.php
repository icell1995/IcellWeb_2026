<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Fill;

use App\Models\Lib\Police;
use App\Models\Polda;

class CommanderWishController extends Controller
{
    protected $presentationTitle = 'Laporan Commander Wish';

    public function index(Request $request)
    {
        $startAccidentDate = $request->query('startAccidentDate');
        $endAccidentDate = $request->query('endAccidentDate');
        $regionalPoliceId = $request->query('regionalPolice');
        $resortPoliceId = $request->query('resortPolice');

        $performances = [];
        if(!empty($startAccidentDate) && !empty($endAccidentDate)){
           
            $performances = DB::table('polda')
                ->selectRaw('polda.name AS nama_polda')
                ->selectRaw('MAX(COALESCE(total_p21.total, 0)) AS p21')
                ->selectRaw('MAX(COALESCE(total_sp3.total, 0)) AS sp3')
                ->selectRaw('MAX(COALESCE(total_diversi.total, 0)) AS diversi')
                ->selectRaw('MAX(COALESCE(total_pom_tni.total, 0)) AS pom_tni')
                ->selectRaw('MAX(COALESCE(total_sp2lid.total, 0)) AS sp2lid')
                ->selectRaw("ROUND((
                    CASE WHEN MAX(splidik.document_number) IS NOT NULL AND MAX(splidik.rejected_at) IS NULL AND MAX(splidik.status_id) = '86' THEN 1 ELSE 0 END +
                    CASE WHEN MAX(spsidik.document_number) IS NOT NULL AND MAX(spsidik.rejected_at) IS NULL AND MAX(spsidik.status_id) = '86' THEN 1 ELSE 0 END +
                    CASE WHEN MAX(sptugas.document_number) IS NOT NULL AND MAX(sptugas.rejected_at) IS NULL AND MAX(sptugas.status_id) = '86' THEN 1 ELSE 0 END +
                    CASE WHEN MAX(lhgp.case_degree_invite_reference) IS NOT NULL AND MAX(lhgp.rejected_at) IS NULL AND MAX(lhgp.status_id) = '86' THEN 1 ELSE 0 END +
                    CASE WHEN MAX(staptsk.document_number) IS NOT NULL AND MAX(staptsk.rejected_at) IS NULL AND MAX(staptsk.status_id) = '86' THEN 1 ELSE 0 END +
                    CASE WHEN MAX(spdp.document_number) IS NOT NULL AND MAX(spdp.rejected_at) IS NULL AND MAX(spdp.status_id) = '86' THEN 1 ELSE 0 END
                ) / 6.0 * 100, 2) AS persentase_keberhasilan")
                ->leftJoin('polres', 'polda.id', '=', 'polres.polda_id')
                ->leftJoin('accidents', 'polres.id', '=', 'accidents.polres_id')
                ->leftJoin('doc.surat_perintah_penyelidikan_documents as splidik', 'accidents.id', '=', 'splidik.accident_id')
                ->leftJoin('doc.surat_perintah_penyidikan_documents as spsidik', 'accidents.id', '=', 'spsidik.accident_id')
                ->leftJoin('doc.surat_perintah_tugas_documents as sptugas', 'accidents.id', '=', 'sptugas.accident_id')
                ->leftJoin('doc.laporan_hasil_gelar_perkara_documents as lhgp', 'accidents.id', '=', 'lhgp.accident_id')
                ->leftJoin('doc.surat_ketetapan_tentang_penetapan_tersangka_documents as staptsk', 'accidents.id', '=', 'staptsk.accident_id')
                ->leftJoin('doc.surat_pemberitahuan_dimulainya_penyidikan_documents as spdp', 'accidents.id', '=', 'spdp.accident_id')
                // Tambahkan LEFT JOIN dan subquery untuk menghitung total_p21, total_sp3, dst.
                // Sesuaikan subquery dengan selera_flag yang sesuai
                ->leftJoin(DB::raw("(SELECT polres.id AS id, COUNT(accidents.id) AS total
                    FROM polres
                    JOIN accidents ON polres.id = accidents.polres_id
                    WHERE accident_date BETWEEN '" . $startAccidentDate . "' AND '" . $endAccidentDate . "'
                    AND selra_flag = 'S0101'
                    AND polres.state <> 0
                    AND accidents.state <> 0
                    GROUP BY 1) AS total_p21"), 'polres.id', '=', 'total_p21.id')
                ->leftJoin(DB::raw("(SELECT polres.id AS id, COUNT(accidents.id) AS total
                    FROM polres
                    JOIN accidents ON polres.id = accidents.polres_id
                    WHERE accident_date BETWEEN '" . $startAccidentDate . "' AND '" . $endAccidentDate . "'
                    AND selra_flag = 'S0102'
                    AND polres.state <> 0
                    AND accidents.state <> 0
                    GROUP BY 1) AS total_sp3"), 'polres.id', '=', 'total_sp3.id')
                // Lanjutkan dengan LEFT JOIN dan subquery untuk total_diversi, total_pom_tni, total_sp2lid
                ->leftJoin(DB::raw("(SELECT polres.id AS id, COUNT(accidents.id) AS total
                    FROM polres
                    JOIN accidents ON polres.id = accidents.polres_id
                    WHERE accident_date BETWEEN '" . $startAccidentDate . "' AND '" . $endAccidentDate . "'
                    AND selra_flag = 'S0103'
                    AND polres.state <> 0
                    AND accidents.state <> 0
                    GROUP BY 1) AS total_diversi"), 'polres.id', '=', 'total_diversi.id')
                ->leftJoin(DB::raw("(SELECT polres.id AS id, COUNT(accidents.id) AS total
                    FROM polres
                    JOIN accidents ON polres.id = accidents.polres_id
                    WHERE accident_date BETWEEN '" . $startAccidentDate . "' AND '" . $endAccidentDate . "'
                    AND selra_flag = 'S0104'
                    AND polres.state <> 0
                    AND accidents.state <> 0
                    GROUP BY 1) AS total_pom_tni"), 'polres.id', '=', 'total_pom_tni.id')
                ->leftJoin(DB::raw("(SELECT polres.id AS id, COUNT(accidents.id) AS total
                    FROM polres
                    JOIN accidents ON polres.id = accidents.polres_id
                    WHERE accident_date BETWEEN '" . $startAccidentDate . "' AND '" . $endAccidentDate . "'
                    AND selra_flag = 'S0108'
                    AND polres.state <> 0
                    AND accidents.state <> 0
                    GROUP BY 1) AS total_sp2lid"), 'polres.id', '=', 'total_sp2lid.id')
                ->where('polda.state', '<>', 0)
                ->where('polres.state', '<>', 0)
                ->whereNotIn('polda.id', ['90', '99'])
                ->whereNotIn('accidents.selra_flag', ['S0107', 'S0108'])
                ->groupBy('polda.name')
                ->orderBy('polda.name', 'ASC');

            if(!empty($regionalPoliceId)){
                $performances = $performances->where('polda.id', $regionalPoliceId);
            }

            $performances = $performances->get();
        }
        
        $regionalPolices = Police::where('class', 'DAERAH')
        ->where('is_active', true)
        ->whereNotIn('id', ['90', '99', '80'])
        ->orderBy('sort', 'asc')
        ->get();

        $urlParameters = [
            'startAccidentDate' => $startAccidentDate,
            'endAccidentDate' => $endAccidentDate,
            'regionalPoliceId' => $regionalPoliceId,
            'resortPoliceId' => $resortPoliceId
        ];

        $viewData = [
            'performances' => $performances,
            'regionalPolices' => $regionalPolices,
            'urlParameters' => $urlParameters
        ];

        return view('commander-wish.index', $viewData);
    }

    public function getResortPolices(Request $request)
    {
        $regionalPoliceId = $request->regionalPoliceId;
        $resortPolices = Police::where('parent_id', $regionalPoliceId)
            ->where('class', 'RESOR')
            ->where('is_active', true)
            ->orderBy('sort', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $resortPolices
        ], 200);
    }

  /*  public function generatePresentation(Request $request){
        $presentation = new PhpPresentation();

        // Create slide
        $currentSlide = $presentation->getActiveSlide();

        // Create a shape (drawing)
        $shape = $currentSlide->createDrawingShape();
        $shape->setName('PHPPresentation logo')
                ->setDescription('PHPPresentation logo')
                ->setPath(public_path('images/korlantas.png'))
                ->setHeight(36)
                ->setOffsetX(10)
                ->setOffsetY(10);
        $shape->getShadow()->setVisible(true)
                            ->setDirection(45)
                            ->setDistance(10);

        // Create a shape (text)
        $shape = $currentSlide->createRichTextShape()
                ->setHeight(300)
                ->setWidth(600)
                ->setOffsetX(170)
                ->setOffsetY(180);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $shape->createTextRun('Thank you for using PHPPresentation!');
        $textRun->getFont()->setBold(true)
                            ->setSize(60)
                            ->setColor(new Color('FFE06B20'));

        $writerPPTX = IOFactory::createWriter($presentation, 'PowerPoint2007');
        $writerPPTX->save(public_path('generate/korlantas.pptx'));
    }*/

    public function generatePresentation(Request $request)
    {
        $startAccidentDate = $request->query('startAccidentDate');
        $endAccidentDate = $request->query('endAccidentDate');
        $regionalPoliceId = $request->query('regionalPolice');
        $resortPoliceId = $request->query('resortPolice');

        echo "Generate Presentation, startAccidentDate: $startAccidentDate, endAccidentDate: $endAccidentDate, regionalPoliceId: $regionalPoliceId, resortPoliceId: $resortPoliceId";

        // $poldas = DB::select("select name from polda where id = '$polda'");
        // if ($poldas <> null) {
        //     $name = $poldas[0]->name;
        // } else {
        //     $name = 'SEMUA POLDA';
        // }

        $presentation = new PhpPresentation();
        $presentation->removeSlideByIndex(0);

        $this->generatePresentationFirstPage($presentation);
        $this->generatePresentationSecondPage($presentation, $startAccidentDate, $endAccidentDate, $regionalPoliceId, $resortPoliceId);
        $this->generatePresentationThirdPage($presentation);

        // Save the presentation to a file using the writer class
        $date = Carbon::now()->format('Y-m-d');

        $path = public_path('generate/commander-wish/LAPORAN COMMANDER WISH' . '.pptx');
        $writerType = 'PowerPoint2007'; // You can change this based on your needs

        $writer = IOFactory::createWriter($presentation, $writerType);
        $writer->save($path);

        //   $download = storage_path('app/public/report/LAPORAN MINGGUAN ' . '-' . $polda . ' ' . $dari . '-' . 'Sd' . '-' . $sampai . '.pptx'); // Change the file path accordingly

        return response()->download($path, 'LAPORAN COMMANDER WISH' . '.pptx', [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment'
        ])->deleteFileAfterSend(true);
    }

    private function generatePresentationFirstPage($presentation){
        // if ($poldas <> null) {
        //     $name = $poldas[0]->name;
        // } else {
        //     $name = 'SEMUA POLDA';
        // }

        // $formatDate = Carbon::parse($dari);
        // $formatDateto = Carbon::parse($sampai);

        // // Format objek Carbon sesuai kebutuhan
        // $formattedDate = $formatDate->formatLocalized('%d %B');
        // $formattedDateto = $formatDateto->formatLocalized('%d %B %Y');

        $slide = $presentation->createSlide();
        $slideWidth = 960;
        $slideHeight = 340;

        // Set the dimensions of the table
        $tableWidth = 700;
        $tableHeight = 400;

        // Calculate the offsets to center the table
        $offsetX = ($slideWidth - $tableWidth) / 2;
        $offsetY = ($slideHeight - $tableHeight) / 2;

        // Adjust this based on your desired slide height

        // Set the dimensions of the logo

        $bd = $slide->createDrawingShape();
        $bd->setPath(public_path('images/generate-presentation/commander-wish/cover.png'))
        ->setHeight(10000)
        ->setWidth(960)
        ->setOffsetX(0)
        ->setOffsetY(50);

    }

    private function generatePresentationSecondPage($presentation, $startAccidentDate = null, $endAccidentDate = null, $regionalPoliceId = null, $resortPoliceId = null){
        $slide = $presentation->createSlide();
        $slideWidth = 960;
        $slideHeight = 800;


        // Set the dimensions of the table
        $tableWidth = 900;
        $tableHeight = 700;

        // Calculate the offsets to center the table
        $offsetX = ($slideWidth - $tableWidth) / 2;
        $offsetY = ($slideHeight - $tableHeight) / 2;

        // Adjust this based on your desired slide height

        // Set the dimensions of the logo

        $bd = $slide->createDrawingShape();
        $bd->setPath(public_path('images/generate-presentation/commander-wish/content-body.png'))
            ->setHeight(10000)
            ->setWidth(960)
            ->setOffsetX(0)
            ->setOffsetY(50);

        $table = $slide->createTableShape(8);
        // $table->setHeight($tableHeight);
        // $table->setWidth($tableWidth);
        // $table->setOffsetX($offsetX);
        // $table->setOffsetY($offsetY);
        $table->setHeight(500);
        $table->setWidth(900);
        $table->setOffsetX(100);
        $table->setOffsetY(40);

        $row = $table->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('ffffff'))
            ->setEndColor(new Color('ffffff'));

        $cell = $row->nextCell();
        $cell->setWidth(30);
        $cell->createTextRun('N0')->getFont()->setBold(true)->setSize(10);
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $cell = $row->nextCell();
        $cell->setWidth(270);
        $cell->createTextRun('Satker')->getFont()->setBold(true)->setSize(10);
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $cell = $row->nextCell();
        $cell->setWidth(60);
        $cell->createTextRun('P21')->getFont()->setBold(true)->setSize(10);
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $cell = $row->nextCell();
        $cell->setWidth(60);
        $cell->createTextRun('SP3')->getFont()->setBold(true)->setSize(9);
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $cell = $row->nextCell();
        $cell->setWidth(60);
        $cell->createTextRun('Diversi')->getFont()->setBold(true)->setSize(9);
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $cell = $row->nextCell();
        $cell->setWidth(60);
        $cell->createTextRun('POM/TNI')->getFont()->setBold(true)->setSize(9);
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $cell = $row->nextCell();
        $cell->setWidth(60);
        $cell->createTextRun('SP2LID')->getFont()->setBold(true)->setSize(9);
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $cell = $row->nextCell();
        $cell->setWidth(100);
        $cell->createTextRun('Presentase Kelengkapan Doc')->getFont()->setBold(true)->setSize(9);
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);


        // Create rows and cells in the table
        // $dari = '2023-10-10';
        // $sampai = '2023-10-14';
        // $polda = '10';
   
        $performances = DB::table('polda')
        ->selectRaw('ROW_NUMBER() OVER (ORDER BY polda.name) AS row_number')
        ->selectRaw('polda.name AS nama_polda')
        ->selectRaw('MAX(COALESCE(total_p21.total, 0)) AS p21')
        ->selectRaw('MAX(COALESCE(total_sp3.total, 0)) AS sp3')
        ->selectRaw('MAX(COALESCE(total_diversi.total, 0)) AS diversi')
        ->selectRaw('MAX(COALESCE(total_pom_tni.total, 0)) AS pom_tni')
        ->selectRaw('MAX(COALESCE(total_sp2lid.total, 0)) AS sp2lid')
        ->selectRaw("CONCAT(ROUND((
            CASE WHEN MAX(splidik.document_number) IS NOT NULL AND MAX(splidik.rejected_at) IS NULL AND MAX(splidik.status_id) = '86' THEN 1 ELSE 0 END +
            CASE WHEN MAX(spsidik.document_number) IS NOT NULL AND MAX(spsidik.rejected_at) IS NULL AND MAX(spsidik.status_id) = '86' THEN 1 ELSE 0 END +
            CASE WHEN MAX(sptugas.document_number) IS NOT NULL AND MAX(sptugas.rejected_at) IS NULL AND MAX(sptugas.status_id) = '86' THEN 1 ELSE 0 END +
            CASE WHEN MAX(lhgp.case_degree_invite_reference) IS NOT NULL AND MAX(lhgp.rejected_at) IS NULL AND MAX(lhgp.status_id) = '86' THEN 1 ELSE 0 END +
            CASE WHEN MAX(staptsk.document_number) IS NOT NULL AND MAX(staptsk.rejected_at) IS NULL AND MAX(staptsk.status_id) = '86' THEN 1 ELSE 0 END +
            CASE WHEN MAX(spdp.document_number) IS NOT NULL AND MAX(spdp.rejected_at) IS NULL AND MAX(spdp.status_id) = '86' THEN 1 ELSE 0 END
        ) / 6.0 * 100, 2), '%') AS persentase_keberhasilan")
        ->leftJoin('polres', 'polda.id', '=', 'polres.polda_id')
        ->leftJoin('accidents', 'polres.id', '=', 'accidents.polres_id')
        ->leftJoin('doc.surat_perintah_penyelidikan_documents as splidik', 'accidents.id', '=', 'splidik.accident_id')
        ->leftJoin('doc.surat_perintah_penyidikan_documents as spsidik', 'accidents.id', '=', 'spsidik.accident_id')
        ->leftJoin('doc.surat_perintah_tugas_documents as sptugas', 'accidents.id', '=', 'sptugas.accident_id')
        ->leftJoin('doc.laporan_hasil_gelar_perkara_documents as lhgp', 'accidents.id', '=', 'lhgp.accident_id')
        ->leftJoin('doc.surat_ketetapan_tentang_penetapan_tersangka_documents as staptsk', 'accidents.id', '=', 'staptsk.accident_id')
        ->leftJoin('doc.surat_pemberitahuan_dimulainya_penyidikan_documents as spdp', 'accidents.id', '=', 'spdp.accident_id')
        // Tambahkan LEFT JOIN dan subquery untuk menghitung total_p21, total_sp3, dst.
        // Sesuaikan subquery dengan selera_flag yang sesuai
        ->leftJoin(DB::raw("(SELECT polres.id AS id, COUNT(accidents.id) AS total
            FROM polres
            JOIN accidents ON polres.id = accidents.polres_id
            WHERE accident_date BETWEEN '" . $startAccidentDate . "' AND '" . $endAccidentDate . "'
            AND selra_flag = 'S0101'
            AND polres.state <> 0
            AND accidents.state <> 0
            GROUP BY 1) AS total_p21"), 'polres.id', '=', 'total_p21.id')
        ->leftJoin(DB::raw("(SELECT polres.id AS id, COUNT(accidents.id) AS total
            FROM polres
            JOIN accidents ON polres.id = accidents.polres_id
            WHERE accident_date BETWEEN '" . $startAccidentDate . "' AND '" . $endAccidentDate . "'
            AND selra_flag = 'S0102'
            AND polres.state <> 0
            AND accidents.state <> 0
            GROUP BY 1) AS total_sp3"), 'polres.id', '=', 'total_sp3.id')
        // Lanjutkan dengan LEFT JOIN dan subquery untuk total_diversi, total_pom_tni, total_sp2lid
        ->leftJoin(DB::raw("(SELECT polres.id AS id, COUNT(accidents.id) AS total
            FROM polres
            JOIN accidents ON polres.id = accidents.polres_id
            WHERE accident_date BETWEEN '" . $startAccidentDate . "' AND '" . $endAccidentDate . "'
            AND selra_flag = 'S0103'
            AND polres.state <> 0
            AND accidents.state <> 0
            GROUP BY 1) AS total_diversi"), 'polres.id', '=', 'total_diversi.id')
        ->leftJoin(DB::raw("(SELECT polres.id AS id, COUNT(accidents.id) AS total
            FROM polres
            JOIN accidents ON polres.id = accidents.polres_id
            WHERE accident_date BETWEEN '" . $startAccidentDate . "' AND '" . $endAccidentDate . "'
            AND selra_flag = 'S0104'
            AND polres.state <> 0
            AND accidents.state <> 0
            GROUP BY 1) AS total_pom_tni"), 'polres.id', '=', 'total_pom_tni.id')
        ->leftJoin(DB::raw("(SELECT polres.id AS id, COUNT(accidents.id) AS total
            FROM polres
            JOIN accidents ON polres.id = accidents.polres_id
            WHERE accident_date BETWEEN '" . $startAccidentDate . "' AND '" . $endAccidentDate . "'
            AND selra_flag = 'S0108'
            AND polres.state <> 0
            AND accidents.state <> 0
            GROUP BY 1) AS total_sp2lid"), 'polres.id', '=', 'total_sp2lid.id')
        ->where('polda.state', '<>', 0)
        ->where('polres.state', '<>', 0)
        ->whereNotIn('polda.id', ['90', '99'])
        ->whereNotIn('accidents.selra_flag', ['S0107', 'S0108'])
        ->groupBy('polda.name')
        ->orderBy('polda.name', 'ASC');
        
        if(!empty($regionalPoliceId)){
            $performances = $performances->where('polda.id', $regionalPoliceId);
        }

        $performances = $performances->get();

        foreach ($performances as $rowData) {
            $row = $table->createRow();
            $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('ffffff'))
            ->setEndColor(new Color('ffffff'));
            $row->setHeight(18);

            foreach ($rowData as $colIndex => $cellData) {
                $cell = $row->nextCell();
                if ($colIndex === 0) {
                    $cell->setWidth(400); // Set the width of the first column to 20
                } else {
                    $cell->setWidth(100); // Set the width of other columns to 200
                } // Set the width of the cell

                $cell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                // $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $cell->createTextRun($cellData)->getFont()->setBold(false)->setSize(10);
            }

            foreach ($row->getCells() as $cell) {
                $cell->getBorders()->getTop()->setLineWidth(2);
            }
        }
    }
    
    private function generatePresentationThirdPage($presentation){
        $slide = $presentation->createSlide();
        $slideWidth = 960;
        $slideHeight = 340;

        // Set the dimensions of the table
        $tableWidth = 700;
        $tableHeight = 400;

        // Calculate the offsets to center the table
        $offsetX = ($slideWidth - $tableWidth) / 2;
        $offsetY = ($slideHeight - $tableHeight) / 2;

        // Adjust this based on your desired slide height

        // Set the dimensions of the logo

        $bd = $slide->createDrawingShape();
        $bd->setPath(public_path('images/generate-presentation/commander-wish/ending.png'))
            ->setHeight(10000)
            ->setWidth(960)
            ->setOffsetX(0)
            ->setOffsetY(50);
    }
}


/*
    function __construct(Polda $_poldaModel){
        $this->_poldaModel    = $_poldaModel->where('state','<>',0);
        view()->share('_title', $this->_title);
    }
    
    public function index(){
        $roleData  = Sentinel::getUser()->role_id;

        if ($roleData > 2) {
            // Sentinel::logout();
            return '<script type="text/javascript">
                    alert("Tidak punya akses");
                    window.location.href = "/login";
                </script>';
        }
        $optPolda = [''=>'Semua Polda'];
        switch ($roleData) {
            case '2':
                // dd('level2');
                $poldaId  = Sentinel::getUser()->polda_id;

                $qPolda    = $this->_poldaModel->where('id', $poldaId)->where('state','<>',0);
                $optPolda  = $qPolda->pluck('name', 'id')->toArray();
                break;
            
            default:
            $optPolda  = $this->_poldaModel->orderBy('id', 'ASC')->pluck('name', 'id')->prepend('Semua Polda', '')->toArray();
        }

        $data['optPolda']  = $optPolda;
        return view('commander.index',$data);
    }

    public function pptExport(Request $request){

        $dari = date('Y-m-d',strtotime($request->input('date_from')));;
        $sampai = date('Y-m-d',strtotime($request->input('date_to')));
        $polda = $request->input('polda');

       
    }

    

    public function commanderWishBody($presentation, $dari, $sampai, $poldas,$polda)
    {
        if ($poldas <> null) {
            $name = $poldas[0]->name;
        } else {
            $name = 'SEMUA POLDA';
        }

        $formatDate = Carbon::parse($dari);
        $formatDateto = Carbon::parse($sampai);

        // Format objek Carbon sesuai kebutuhan
        $formattedDate = $formatDate->formatLocalized('%d %B');
        $formattedDateto = $formatDateto->formatLocalized('%d %B %Y');



        $slide = $presentation->createSlide();
        $slideWidth = 960;
        $slideHeight = 340;

        // Set the dimensions of the table
        $tableWidth = 700;
        $tableHeight = 400;

        // Calculate the offsets to center the table
        $offsetX = ($slideWidth - $tableWidth) / 2;
        $offsetY = ($slideHeight - $tableHeight) / 2;

        // Adjust this based on your desired slide height

        // Set the dimensions of the logo

        $bd = $slide->createDrawingShape();
        $bd->setPath('./images/bd-body-cw.png')
        ->setHeight(1300)
            ->setWidth(960)
            ->setOffsetX(0)
            ->setOffsetY(0);

        $table = $slide->createTableShape(6);
        // $table->setHeight($tableHeight);
        // $table->setWidth($tableWidth);
        // $table->setOffsetX($offsetX);
        // $table->setOffsetY($offsetY);
        $table->setHeight(400);
        $table->setWidth(800);
        $table->setOffsetX(160);
        $table->setOffsetY(180);

        $row = $table->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('4d0000'))
            ->setEndColor(new Color('4d0000'));
        $cell = $row->nextCell();
        $cell->setWidth(30);
        $cell->createTextRun('N0')->getFont()->setBold(true)->setSize(10)->setColor(new Color('ffffff'));
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $cell = $row->nextCell();
        $cell->setWidth(270);
        $cell->createTextRun('POLRES')->getFont()->setBold(true)->setSize(10)->setColor(new Color('ffffff'));
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $cell = $row->nextCell();
        $cell->setWidth(80);
        $cell->createTextRun('JML LP')->getFont()->setBold(true)->setSize(10)->setColor(new Color('ffffff'));
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $cell = $row->nextCell();
        $cell->setWidth(60);
        $cell->createTextRun('ON TIME')->getFont()->setBold(true)->setSize(9)->setColor(new Color('ffffff'));
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $cell = $row->nextCell();
        $cell->setWidth(60);
        $cell->createTextRun('DELAY')->getFont()->setBold(true)->setSize(9)->setColor(new Color('ffffff'));
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $cell = $row->nextCell();
        $cell->setWidth(100);
        $cell->createTextRun('PERSENTASE DATA ON TIME')->getFont()->setBold(true)->setSize(9)->setColor(new Color('ffffff'));
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
       // $cell = $row->nextCell();
        // $cell->setWidth(100);
        // $cell->createTextRun('PERSENTASE TDK LENGKAP')->getFont()->setBold(true)->setSize(12)->setColor(new Color('ffffff'));
        // $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);


        // Create rows and cells in the table
        // $dari = '2023-10-10';
        // $sampai = '2023-10-14';
        // $polda = '10';
   

        $dt = DB::select(
            "SELECT
            ROW_NUMBER() OVER (ORDER BY polres.id) AS row_number,
            polres.name,
            COALESCE(x.total, 0) AS total,
            coalesce(x.on_time, 0) as on_time,
            COALESCE(x.delay, 0) AS delay,
			CONCAT(COALESCE(ROUND((on_time::FLOAT/total::FLOAT) * 100),0),' ','%') as persentase_on_time
		--	CONCAT(COALESCE(ROUND(( (total::FLOAT - on_time::FLOAT) /total::FLOAT) * 100),0),'','%') as persentase_delay
        	FROM
            polres
            join polda on polres.polda_id = polda.id
        	LEFT JOIN (
            	SELECT
                        polres.id,
                        count(*) AS total,
                        count(case when dors_laka.state = 4 then 1 end) as btl,
                        SUM(CASE WHEN DATE_PART('day', dors_laka.created_at - dors_laka.accident_date) = 0 THEN 1 ELSE 0 END) AS on_time,
                		SUM(CASE WHEN DATE_PART('day', dors_laka.created_at - dors_laka.accident_date) <> 0 THEN 1 ELSE 0 END) AS delay
                    FROM
                        dors_laka
                    JOIN polres ON dors_laka.polres_id = polres.id
                    join polda on polres.polda_id = polda.id
                    WHERE
                        dors_laka.created_at::date BETWEEN '$dari' AND '$sampai'
                        and case when '$polda' <> '-' then polda.id = '$polda' else true end
                         and dors_laka.state <> 0
                    AND POLRES.STATE = 1
                    GROUP BY
                        polres.id
                ) AS x ON polres.id = x.id
                where polda.id = '$polda'
                AND POLRES.STATE = 1
				order by polres.id
                "
        );


        foreach ($dt as $rowData) {
            $row = $table->createRow();
            $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('4d0026'))
            ->setEndColor(new Color('4d0026'));
            $row->setHeight(18);

            foreach ($rowData as $colIndex => $cellData) {
                $cell = $row->nextCell();
                if ($colIndex === 0) {
                    $cell->setWidth(400); // Set the width of the first column to 20
                } else {
                    $cell->setWidth(100); // Set the width of other columns to 200
                } // Set the width of the cell

                $cell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                // $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $cell->createTextRun($cellData)->getFont()->setBold(false)->setSize(10)->setColor(new Color('ffffff'));
            }

            foreach ($row->getCells() as $cell) {
                $cell->getBorders()->getTop()->setLineWidth(2);
            }
        }
    
    }

    public function commanderWisHKelengkapan($presentation, $dari, $sampai, $poldas, $polda)
    {
        if ($poldas <> null) {
            $name = $poldas[0]->name;
        } else {
            $name = 'SEMUA POLDA';
        }

        $formatDate = Carbon::parse($dari);
        $formatDateto = Carbon::parse($sampai);

        // Format objek Carbon sesuai kebutuhan
        $formattedDate = $formatDate->formatLocalized('%d %B');
        $formattedDateto = $formatDateto->formatLocalized('%d %B %Y');



        $slide = $presentation->createSlide();
        $slideWidth = 960;
        $slideHeight = 340;

        // Set the dimensions of the table
        $tableWidth = 700;
        $tableHeight = 400;

        // Calculate the offsets to center the table
        $offsetX = ($slideWidth - $tableWidth) / 2;
        $offsetY = ($slideHeight - $tableHeight) / 2;

        // Adjust this based on your desired slide height

        // Set the dimensions of the logo

        $bd = $slide->createDrawingShape();
        $bd->setPath('./images/bg-body-kel.png')
        ->setHeight(1300)
            ->setWidth(960)
            ->setOffsetX(0)
            ->setOffsetY(0);

        $table = $slide->createTableShape(6);
        // $table->setHeight($tableHeight);
        // $table->setWidth($tableWidth);
        // $table->setOffsetX($offsetX);
        // $table->setOffsetY($offsetY);
        $table->setHeight(400);
        $table->setWidth(800);
        $table->setOffsetX(160);
        $table->setOffsetY(180);

        $row = $table->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('4d0026'))
            ->setEndColor(new Color('4d0026'));
        $cell = $row->nextCell();
        $cell->setWidth(30);
        $cell->createTextRun('NO')->getFont()->setBold(true)->setSize(9)->setColor(new Color('ffffff'));
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $cell = $row->nextCell();
        $cell->setWidth(270);
        $cell->createTextRun('POLRES')->getFont()->setBold(true)->setSize(9)->setColor(new Color('ffffff'));
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $cell = $row->nextCell();
        $cell->setWidth(80);
        $cell->createTextRun('JML LP')->getFont()->setBold(true)->setSize(9)->setColor(new Color('ffffff'));
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $cell = $row->nextCell();
        $cell->setWidth(60);
        $cell->createTextRun('LENGKAP')->getFont()->setBold(true)->setSize(9)->setColor(new Color('ffffff'));
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $cell = $row->nextCell();
        $cell->setWidth(60);
        $cell->createTextRun('TDK LENGKAP')->getFont()->setBold(true)->setSize(9)->setColor(new Color('ffffff'));
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $cell = $row->nextCell();
        $cell->setWidth(100);
        $cell->createTextRun('PERSENTASE KELENGKAPAN DATA')->getFont()->setBold(true)->setSize(9)->setColor(new Color('ffffff'));
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
      
      
        // Create rows and cells in the table
        // $dari = '2023-10-10';
        // $sampai = '2023-10-14';
        // $polda = '10';
        $row = DB::SELECT(
            "
         SELECT 
        ROW_NUMBER() OVER (ORDER BY polres.id) AS row_number,
         polres.name,
         coalesce(acc.jml_laka,0) as jml_laka, 
         coalesce(lengkap.jml_lengkap,0) as jml_lengkap,
         coalesce((jml_laka - jml_lengkap),0) as jml_tdk_lengkap,
         CONCAT(COALESCE(ROUND((jml_lengkap::FLOAT/jml_laka::FLOAT) * 100),0),' ','%') as persentase_lengkap
     ---    CONCAT(COALESCE(ROUND(( (jml_laka::FLOAT - jml_lengkap::FLOAT) /jml_laka::FLOAT) * 100),0),'','%') as persentase_tidak_lengkap
         from polda left join polres on polda.id = polres.polda_id
        left join (
		SELECT polda.id as polda,polres.id as polres, count(*) as jml_lengkap FROM POLDA 
		LEFT JOIN POLRES ON POLDA.ID = POLRES.POLDA_ID 
		LEFT JOIN ACCIDENT ON POLRES.ID = ACCIDENT.POLRES_ID
		where 
		(light_cond_id is not null or light_cond_id <> 'A0800') and (weather_cond_id is not null or weather_cond_id <> 'A0900')
		AND (ref_spot_id is not null) AND (road_function_id is not null) AND (road_class_id is not null) AND (road_type_id is not null)
		AND (road_geometry_id is not null) AND (road_surface_id is not null) AND (speed_limit_id is not null OR speed_limit_id <> 'R0700')
		AND (road_slope_id is not null) AND (road_state_id is not null OR road_state_id <> 'R1000') AND (penyebab is not null)
		AND POLDA.STATE = 1
		AND POLRES.STATE = 1
		AND ACCIDENT.STATE > 1
		AND date(accident.created_at) BETWEEN '$dari' AND '$sampai'
		AND FLAG_LP = 9
		AND NO_LP IS NOT NULL
		AND POLDA.ID = '$polda'
		GROUP BY 1,2
	    ) as lengkap on lengkap.polres = polres.id
        left join (
		SELECT polda.id as polda,polres.id as polres, COUNT(*) AS JML_LAKA FROM POLDA 
		LEFT JOIN POLRES ON POLDA.ID = POLRES.POLDA_ID 
		LEFT JOIN DORS_LAKA ON POLRES.ID = DORS_LAKA.POLRES_ID
		where 
		POLDA.STATE = 1
		AND POLRES.STATE = 1
		AND DORS_LAKA.STATE <> 0
		AND date(dors_laka.created_at) BETWEEN '$dari' AND '$sampai'
		AND NO_LP IS NOT NULL
		AND POLDA.ID = '$polda'
		GROUP BY 1,2
	    ) as ACC on ACC.polres = polres.id
        where polda.id = '$polda'
        and polres.state = 1
        ");


        foreach ($row as $rowData) {
            $row = $table->createRow();
            $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('4d0026'))
            ->setEndColor(new Color('4d0026'));
            $row->setHeight(18);

            foreach ($rowData as $colIndex => $cellData) {
                $cell = $row->nextCell();
                if ($colIndex === 0) {
                    $cell->setWidth(400); // Set the width of the first column to 20
                } else {
                    $cell->setWidth(100); // Set the width of other columns to 200
                } // Set the width of the cell

                $cell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $cell->createTextRun($cellData)->getFont()->setBold(false)->setSize(10)->setColor(new Color('ffffff'));
            }

            foreach ($row->getCells() as $cell) {
                $cell->getBorders()->getTop()->setLineWidth(2);
            }
        }
    }

    public function commanderWishEnd($presentation, $dari, $sampai, $poldas)
    {
        if ($poldas <> null) {
            $name = $poldas[0]->name;
        } else {
            $name = 'SEMUA POLDA';
        }

        $formatDate = Carbon::parse($dari);
        $formatDateto = Carbon::parse($sampai);

        // Format objek Carbon sesuai kebutuhan
        $formattedDate = $formatDate->formatLocalized('%d %B');
        $formattedDateto = $formatDateto->formatLocalized('%d %B %Y');



        $slide = $presentation->createSlide();
        $slideWidth = 960;
        $slideHeight = 340;

        // Set the dimensions of the table
        $tableWidth = 700;
        $tableHeight = 400;

        // Calculate the offsets to center the table
        $offsetX = ($slideWidth - $tableWidth) / 2;
        $offsetY = ($slideHeight - $tableHeight) / 2;

        // Adjust this based on your desired slide height

        // Set the dimensions of the logo

        $bd = $slide->createDrawingShape();
        $bd->setPath('./images/bg-end-cw.png')
        ->setHeight(1300)
            ->setWidth(960)
            ->setOffsetX(0)
            ->setOffsetY(0);
    }
*/