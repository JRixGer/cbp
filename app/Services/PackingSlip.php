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
use \cbp\ShipmentItem;
use \cbp\SenderBusiness;

class PackingSlip 
{

    public function generate($shipment, $shipmentId) 
    {
        
        $shipment = $shipment instanceof Shipment ? $shipment : Shipment::find($shipmentId);
        
        $address_from = CBPAddress::find($shipment->cbp_address_id);

        $recipient_address = RecipientAddress::where("id",$shipment->recipient_address_id)->where('is_active','=',1)->first();
        
        $recipient = Recipient::find($shipment->recipient_id);
        
        $sender = Sender::find($shipment->sender_id);

        $sender_business = SenderBusiness::where("sender_id",$shipment->sender_id)->first();

        $items = ShipmentItem::where('shipment_id','=',$shipment->id)->get();

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode_png = base64_encode($generator->getBarcode($shipment->shipment_code, $generator::TYPE_CODE_93));
        
        $filePDF = "public/packing_slip/ps-{$shipment->shipment_code}.pdf";
        $fileTXT = "packing_slip_txt/ps-{$shipment->shipment_code}.txt";

        if(file_exists(storage_path("app/$filePDF"))){
            Storage::delete($filePDF);
        }
        // NOTE:
        // this is to support local development, otherwise it will take forever to load until your php timeouts
        // $logo = file_get_contents(public_path('/images/label_logo.png')); 
        // $logo = base64_encode($logo);

        $snappy = SnappyPdf::loadView('reports.packing_slip', compact('shipment','sender_business','address_from','recipient','recipient_address', 'sender', 'items' ,'barcode_png'));
        $snappy->setOption('image-quality', 94);
        $snappy->setOption('orientation', 'Portrait');
        $snappy->setOption('page-width', '210');
        $snappy->setOption('page-height', '297');
        $snappy->setOption('margin-bottom', 10);
        $snappy->setOption('margin-top', 10);
        $snappy->setOption('margin-left', 10);
        $snappy->setOption('margin-right', 10);

        // $snappy->setOption('image-quality', 94);
        // $snappy->setOption('orientation', 'Portrait');
        // $snappy->setOption('page-size','Letter');
        // $snappy->setOption('encoding', 'utf-8');
        // $snappy->setOption('margin-bottom', 5);
        // $snappy->setOption('margin-top', 5);
        // $snappy->setOption('margin-left', 6);
        // $snappy->setOption('margin-right', 6);


         // $snappy->setOption('page-width', '50.6');
        // $snappy->setOption('page-height', '65.24');
        // $snappy->save(public_path().'/test.pdf');
        // dd(public_path().'/test.pdf');
        $snappy->save(storage_path("app/$filePDF"));
        // dd(storage_path("app/$filePDF")); 


        $b64Doc = chunk_split(base64_encode(file_get_contents(storage_path("app/$filePDF"))));
        Storage::disk('local')->put($fileTXT, $b64Doc);
        
        // remove generated PDF
        // Storage::delete($filePDF);

        // $model =\cbp\Shipment::find($shipment->id);
        // $model->print_type = "pdf";
        // $model->save();
        
        return Storage::get($fileTXT);
    }
}