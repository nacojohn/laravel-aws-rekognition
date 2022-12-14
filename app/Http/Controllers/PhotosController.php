<?php

namespace App\Http\Controllers;

use Aws\Rekognition\RekognitionClient;
use Exception;
use Illuminate\Http\Request;

class PhotosController extends Controller
{
    public function showForm()
    {
        return view('welcome');
    }
    
    public function submitForm(Request $request)
    {
        $validated = $request->validate([
            'photo1' => 'required|mimes:jpeg,png|max:3000',
            'photo2' => 'required|string',
        ]);

        try {
            $client = new RekognitionClient([
                'region'    => config('aws.region'),
                'version'   => 'latest'
            ]);

            $image1 = fopen($request->file('photo1')->getPathname(), 'r');
            $bytes1 = fread($image1, $request->file('photo1')->getSize());

            
            $file = $this->uploadFile($request->photo2);
            $image2 = fopen($file, 'r');
            $bytes2 = fread($image2, filesize($file));

            $params = [
                // 'QualityFilter' => 'NONE|AUTO|LOW|MEDIUM|HIGH',
                'SimilarityThreshold' => 80,
                'SourceImage' => [
                    'Bytes' => $bytes1,
                    // 'S3Object' => [
                    //     'Bucket' => '<string>',
                    //     'Name' => '<string>',
                    //     'Version' => '<string>',
                    // ],
                ],
                'TargetImage' => [ // REQUIRED
                    'Bytes' => $bytes2,
                    // 'S3Object' => [
                    //     'Bucket' => '<string>',
                    //     'Name' => '<string>',
                    //     'Version' => '<string>',
                    // ],
                ],
            ];

            $results = $client->compareFaces($params);
            fclose($image1);
            fclose($image2);
            unlink($file);

            // dd($results);

            if (count($results['UnmatchedFaces']) > 0)
                return back()
                    ->with('error','We could not verify. There was ' . count($results['UnmatchedFaces']) . ' face(s) that did not match');

            if (count($results['FaceMatches']) > 0 and $results['FaceMatches'][0]['Similarity'] >= 90) {
                return back()
                    ->with('success','You have successfully verified your data at ' . round($results['FaceMatches'][0]['Similarity'], 1) . '%')
                    ->with('image1', $bytes1) 
                    ->with('image2', $bytes2);
            } else {
                return back()
                    ->with('error','We could not verify your data');
            }

        } catch (Exception $error) {
            return back()
                ->with('error','Request failed - ' . $error->getMessage());
        }
    }

    protected function uploadFile($base64_file)
    {
        // create folder if it does not exist
        if (!file_exists(public_path('webcam/'))) mkdir(public_path('webcam/'), 0775, true);
        
        // processing image 2 from webcam
        $image_parts = explode(";base64,", $base64_file);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $filePath = public_path('webcam/' . uniqid() . '.' . $image_type);
        file_put_contents($filePath, $image_base64);

        return $filePath;
    }
}
