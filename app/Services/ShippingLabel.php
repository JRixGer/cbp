<?php 

namespace cbp\Services; 

// use App;
use Auth;
// use PDF;
use Response;
use Knp\Snappy\Pdf;
use SnappyPdf;
use Illuminate\Support\Facades\Storage;
use \cbp\Shipment; 
use \cbp\CBPAddress;
use \cbp\RecipientAddress;
use \cbp\Recipient;
use \cbp\Sender;

class ShippingLabel 
{

    public function generate($shipment) 
    {
        
        $shipment = $shipment instanceof Shipment ? $shipment : Shipment::find($shipment);
        //dd($shipment);
        $address_from = CBPAddress::find($shipment->cbp_address_id);
        $recipient_address = RecipientAddress::where("recipient_id",$shipment->recipient_id)->first();
        $recipient = Recipient::find($shipment->recipient_id);
        //dd($recipient_address);
        $sender = Sender::find($shipment->sender_id);


        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode_png = base64_encode($generator->getBarcode($shipment->shipment_code, $generator::TYPE_CODE_93));
        
        $filePDF = "public/labels/in_".$shipment->shipment_code.".pdf";
        $fileTXT = "postage_labels/in_".$shipment->shipment_code.".txt";

        if(file_exists(storage_path("app/$filePDF"))){
            Storage::delete($filePDF);
        }
        // NOTE:
        // this is to support local development, otherwise it will take forever to load until your php timeouts
        // $logo = file_get_contents(public_path('/images/label_logo.png')); 
        // $logo = base64_encode($logo);

        $snappy = SnappyPdf::loadView('labels.shipping_label', compact('shipment','address_from','recipient','recipient_address', 'sender' ,'barcode_png'));
        $snappy->setOption('image-quality', 94);
        $snappy->setOption('orientation', 'Portrait');
        $snappy->setOption('page-width', '101.6');
        $snappy->setOption('page-height', '152.4');
        $snappy->setOption('margin-bottom', 10);
        $snappy->setOption('margin-top', 10);
        $snappy->setOption('margin-left', 10);
        $snappy->setOption('margin-right', 10);

         // $snappy->setOption('page-width', '50.6');
        // $snappy->setOption('page-height', '65.24');
        // $snappy->save(public_path().'/test.pdf');
        // dd(public_path().'/test.pdf');
        $snappy->save(storage_path("app/$filePDF"));
        // dd(storage_path("app/$filePDF")); 


        $b64Doc = chunk_split(base64_encode(file_get_contents(storage_path("app/$filePDF"))));
        Storage::disk('local')->put($fileTXT, $b64Doc );
        
        // remove generated PDF
        // Storage::delete($filePDF);

        // $model =\cbp\Shipment::find($shipment->id);
        // $model->print_type = "pdf";
        // $model->save();
        
        return Storage::get($fileTXT);
    }


    public function generateFromImage($label, $packageID) 
    {
        

        // dd($shipment);


        $filePDF = "public/labels/".$packageID.".pdf";
        $fileTXT = "postage_labels/".$packageID.".txt";

        if(file_exists(storage_path("app/$filePDF"))){
            Storage::delete($filePDF);
        }
        // NOTE:
        // this is to support local development, otherwise it will take forever to load until your php timeouts
        // $label = file_get_contents(storage_path("app/$filePNG")); 

        $snappy = SnappyPdf::loadView('labels.shipping_label_DHL', compact('label'));
        $snappy->setOption('image-quality', 94);
        $snappy->setOption('orientation', 'Portrait');
        $snappy->setOption('page-width', '101.6');
        $snappy->setOption('page-height', '152.4');
        $snappy->setOption('margin-bottom', 0);
        $snappy->setOption('margin-top', 0);
        $snappy->setOption('margin-left', 0);
        $snappy->setOption('margin-right', 0);

         // $snappy->setOption('page-width', '50.6');
        // $snappy->setOption('page-height', '65.24');
        // $snappy->save(public_path().'/test.pdf');
        // dd(public_path().'/test.pdf');
        $snappy->save(storage_path("app/$filePDF"));
        // dd(storage_path("app/$filePDF")); 


        $b64Doc = chunk_split(base64_encode(file_get_contents(storage_path("app/$filePDF"))));
        Storage::disk('local')->put($fileTXT, $b64Doc );
        
        // remove generated PDF
        // Storage::delete($filePDF);

        // $model =\cbp\Shipment::find($shipment->id);
        // $model->print_type = "pdf";
        // $model->save();
        
        return Storage::get($fileTXT);
    }

}