<?php

namespace App\Console\Commands\Testing;

use Illuminate\Console\Command;
use ZipArchive;

class ReadQRCodeInWordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qrcode:readinword';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        \PhpOffice\PhpWord\Element\Table::class;
        $phpWord = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');
        $path = public_path('documents/attachments/EHVBci4DaSzgKsVq57dTFpduj3xr3yKx5QzcWKMG.docx');
        $document = $phpWord->load($path);

        foreach ($document->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                // echo 'Image data or object content:' . PHP_EOL;
                // Check for embedded images or OLE objects
                if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                    foreach ($element->getRows() as $rows) {
                        foreach ($rows->getCells() as $cells) {
                            foreach ($cells->getElements() as $element) {
                                if ($element instanceof \PhpOffice\PhpWord\Element\Image || $element instanceof \PhpOffice\PhpWord\Element\OLEObject) {
                                    echo 'Image data or object content:' . PHP_EOL;
                                    // $imageData = $element->getImageData();
                                    // Extract image data or object content
                                    // dump($imageData);
                                    // Further processing to identify barcode image based on data format (might require additional libraries)
                                }
                                }
                            }
                        }
                    }
                }
                if ($element instanceof \PhpOffice\PhpWord\Element\Image || $element instanceof \PhpOffice\PhpWord\Element\OLEObject) {
                    // $imageData = $element->getImageData();
                    // Extract image data or object content
                    // dump($imageData);
                    // Further processing to identify barcode image based on data format (might require additional libraries)
                }
            }
        }

        // /*Create a new ZIP archive object*/
        //     $zip = new ZipArchive;

        //     /*Open the received archive file*/
        //     if (true === $zip->open($path)) {
        //         for ($i=0; $i<$zip->numFiles;$i++) {


        // /*Loop via all the files to check for image files*/
        //             $zip_element = $zip->statIndex($i);


        // /*Check for images*/
        //             if(preg_match("([^\s]+(\.(?i)(jpg|jpeg|png|gif|bmp))$)",$zip_element['name'])) {


        // /*Display images if present by using display.php*/
        //                 echo "<image src='display.php?filename=".$path."&index=".$i."' /><hr />";
        //                 echo $zip_element['name'];
        //             }
        //         }
        //     }

    }

