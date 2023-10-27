<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use PDF;
use View;

class PDFController extends Controller
{
    //
    public function createPDFTemplate(Request $request)
    {
        //dd($request->contador_label);
        $elements = $request->get("elements");
        $type_elements = $request->get("type_elements");
        
        $files_image = [];
        foreach($elements as $index => $element)
        {
            if($type_elements[$index] == 1)
            {
                list($type, $data) = explode(';', $element);
                list(, $data)      = explode(',', $element);
                $data = base64_decode($data);
                
                $filename = md5(microtime()).".jpg";
                $temp_name = public_path("images/tmp_plots/".$filename);
                file_put_contents($temp_name, $data);
                
                $files_image[] = $temp_name;
                
                $elements[$index] = public_path("images/tmp_plots/".$filename);
            }
            else if($type_elements[$index] == 2)
            {
                $element = base64_decode($element);
                $element = str_replace("table-responsive", "", $element);
                $elements[$index] = $element;
            }
        }
        
        $view = View::make("pdf.pdfcontent", compact("elements", "type_elements"));
        $contents = (string) $view;
        $contents = $view->render();

        $pdf = PDF::loadView('pdf.pdfcontent', ['elements' => $elements, 'type_elements' => $type_elements]);
        
        $filename = md5(microtime()).".html";
        $html_name = sys_get_temp_dir()."/".$filename;
        file_put_contents($html_name, $contents);
        
        
        $file_name_pdf = sys_get_temp_dir()."/".md5(microtime()).".pdf";
        $process_command = "cat ".$html_name." | wkhtmltopdf - " . $file_name_pdf;       
        $process = Process::fromShellCommandline($process_command);
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);

            try
            {
                $message_process = $process->getOutput();
                $data = json_decode($message_process);
                $msg_error = "Command: ". $process_command." \nResponse: ".$data;
                Log::info($msg_error);
                dd($msg_error);
            }
            catch(Exception $error)
            {
                
            }
            
        }
        
        foreach($files_image as $image)
        {
            if(file_exists($image) && !is_dir($image))
            {
                unlink($image);
            }
        }
        
        $nombreArchivoPdf = $request->titulo."_".$request->contador_label."_".$request->date_from."_".$request->date_to.".pdf";
        unlink($html_name);
        // return $pdf->stream($nombreArchivoPdf);
        return response()->download($file_name_pdf, $nombreArchivoPdf)->deleteFileAfterSend(true);
        
        //return view("pdf.pdfcontent", compact("elements", "type_elements"));
        /*$pdf = PDF::loadView("pdf.pdfcontent", compact("elements", "type_elements"));
        $output = $pdf->output();        
        $temp_name = tempnam(sys_get_temp_dir(), '').".pdf";
        file_put_contents($temp_name, $output);
        
        foreach($files_image as $image)
        {
            if(file_exists($image) && !is_dir($image))
            {
                unlink($image);
            }
        }
        
        return response()->download($temp_name, "exportacion.pdf")->deleteFileAfterSend(true);*/
    }
}
