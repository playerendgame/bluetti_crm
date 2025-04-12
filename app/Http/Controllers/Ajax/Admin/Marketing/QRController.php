<?php

namespace App\Http\Controllers\Ajax\Admin\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QRGenerators;
use Carbon\Carbon;
use App\Services\QrGeneratorService;

class QRController extends Controller
{

    public function list(Request $request){
        
        $from_date = Carbon::parse($request->input('from_date'))->startOfDay();
        $to_date = Carbon::parse($request->input('to_date'))->endOfDay();

        $links = QRGenerators::whereBetween('created_at', [$from_date, $to_date])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($links);

    }


/////////
    private $qrGeneratorService;

    public function __construct(QrGeneratorService $qrGeneratorService){
        $this->qrGeneratorService = $qrGeneratorService;
    }
    public function create(Request $request)
    {
        $request->validate([
            'link' => 'required',
        ]);

        $links = new QRGenerators();
        
        if ($links) {
            $links->link = $request->input('link');

            $qrCode = $this->qrGeneratorService->generateQRCode($links->link);
            $qrCodePath = public_path('qr_codes/') . uniqid() . '.png';
            file_put_contents($qrCodePath, $qrCode);

            $links->qr_code_path = $qrCodePath;

            $links->save();

            $qrCodeBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($qrCodePath));
            return response()->json(['success' => true, 'message' => 'QR Link Generated Successfully', 'qrCode' => $qrCodeBase64]);

        } else {
            return response()->json(['error' => 'Error adding link'], 404);
        }
    }

///////////


    public function fetchQrPerId($id = null){
        
        $qr_links = QRGenerators::findOrFail($id);

        $qrCodePath = $qr_links->qr_code_path;
        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($qrCodePath));

        return response()->json(['qr_links' => $qr_links, 'qrCode' => $qrCodePath, 'qrCodeBase64' => $qrCodeBase64]);

    }


}
