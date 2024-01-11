<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function create()
    {
        $countries = Country::get();
        $shippingCharges    =   ShippingCharge::select('countries.name','shipping_charges.*')->leftJoin('countries', 'countries.id', 'shipping_charges.country_id')->get();
        $data['countries']  =   $countries;
        $data['shippingCharges'] = $shippingCharges;
        
        return view('admin.shipping.create', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount'  => 'required|numeric'
        ]);
        if($validator->passes()){
            $count    = ShippingCharge::where('country_id',$request->country)->count();
            if($count > 0){
                Session()->flash('error', 'Shipping already add for this country');
                return response()->json([
                    'status' => true,
                ]);
            }
            $shipping = new ShippingCharge();
            $shipping->country_id = $request->country;
            $shipping->amount     = $request->amount;
            $shipping->save();
            Session()->flash('success', 'Shipping added successfully');
            return response()->json([
                'status' => true
            ]);

        }else{
            return response()->json([
                'status' => 'false',
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($id)
    {
        $shippingCharge = ShippingCharge::find($id);
        if($shippingCharge == ''){            
            return redirect()->route('shipping.create')->with('error', 'No record found');
        }      
       
        $countries      = Country::get();
        $data['shippingCharge'] = $shippingCharge;
        $data['countries'] = $countries;
        return view('admin.shipping.edit', $data);
    }

    public function update(Request $request , $id)
    {
        $shipping  = ShippingCharge::find($id);
        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount'  => 'required|numeric'
        ]);
        if($validator->passes()){
            
            
            if($shipping == null){
                session()->flash('error', 'Shipping couldnot found');
                return response()->json([
                    'status' => false,
                ]);
            }
            $shipping->country_id = $request->country;
            $shipping->amount     = $request->amount;
            $shipping->save();
            Session()->flash('success', 'Shipping updated successfully');
            return response()->json([
                'status' => true
            ]);

        }else{
            return response()->json([
                'status' => 'false',
                'errors' => $validator->errors()
            ]);
        }
    }

    public function delete($id)
    {
        $shippingCharge =   ShippingCharge::find($id);
        if($shippingCharge == null){
            session()->flash('error', 'Shipping couldnot found');
            return response()->json([
                'status' => false,
            ]);
        }
        $shippingCharge->delete();

        session()->flash('success', 'Shipping deleted successfully');
        return response()->json([
            'status' => true,
        ]);
    }
}
