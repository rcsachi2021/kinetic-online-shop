<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use File;

class ProductImageController extends Controller
{
    public function save($id, Request $request)
    {
        $image = $request->image;
        $sourcePath = $image -> getPathName();
        $extension = $image->getClientOriginalExtension();
        
        $productImage  = new ProductImage();
        $productImage -> product_id = $id;
        $productImage -> name = NULL;
        $productImage -> save();

        $newImageName = $id.'-'.$productImage->id.'-'.time().'.'.$extension;
        $productImage -> name =  $newImageName;
        $productImage -> save();

        $destinationPath = public_path().'/uploads/product/'.$newImageName;
        File::copy($sourcePath, $destinationPath);

        return response()->json([
            'status' => true,
            'image_id' => $productImage -> id,
            'image_path' => asset('uploads/product/'.$productImage->name),
            'message' => 'Image uploaded successfully'
        ]);
       // $sourcePath = $image -> get_file
    }

    public function destroy($id)
    {
        $productImage = ProductImage::find($id);
        if(empty($productImage))
        {
            return response()->json([
                'status' => false,
                'message' => 'No image found'
            ]);
        }
        $productImage -> delete();
        $imagePath = public_path().'/uploads/product/'.$productImage->name;
        File::delete($imagePath);
        return response()->json([
            'status' => true,
            'message' => 'Image deleted successfully'
        ]);
    }
}
