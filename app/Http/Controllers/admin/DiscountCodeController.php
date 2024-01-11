<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class DiscountCodeController extends Controller
{
    public function index(Request $request)
    {
        $coupons = DiscountCoupon::latest();
        if(!empty($request->get('keyword')))
        {
            $coupons = $coupons->where('name', 'LIKE', '%'.$request->get('keyword').'%');
            $coupons = $coupons->orWhere('code', 'LIKE', '%'.$request->get('keyword').'%');
        }
        $coupons = $coupons->paginate(10);
        return view('admin.coupon.list', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupon.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required'
        ]);
        if($validator->passes()){
            //Start date must be greater than current date
            if(!empty($request->starts_at)){
                $now = Carbon::now();
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if($startAt->lte($now) == true){
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Start at cannot less than current date time']
                    ]);
                }
            }
            //Expiry date must be greater than start date
            if(!empty($request->starts_at) && !empty($request->expires_at))
            {
                $expiresAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->expires_at);
                $startsAt  = Carbon::createFromFormat('Y-m-d H:i:s', $request->starts_at);
                if($expiresAt->gt($startsAt) == false)
                {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expires at cannot less than start date time']
                    ]);
                }
            }
            $discountCode = new DiscountCoupon();
            $discountCode->code = $request->code;
            $discountCode->name = $request->name;
            $discountCode->description = $request->description;
            $discountCode->max_uses = $request->max_uses;
            $discountCode->max_uses_user = $request->max_uses_user;
            $discountCode->type = $request->type;
            $discountCode->discount_amount = $request->discount_amount;
            $discountCode->min_amount = $request->min_amount;
            $discountCode->status = $request->status;
            $discountCode->starts_at = $request->starts_at;
            $discountCode->expires_at = $request->expires_at;
            $discountCode->save();
            $message = 'Discount coupon added successfully';
            session()->flash('success', $message);
            return response()->json([
                'status' => true,
                'message' => $message
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit(Request $request, $id)
    {
        $coupon = DiscountCoupon::find($id);
        if($coupon == ''){
            return redirect()->back()->with('error', 'Discount coupon couldnot found');
        }
        return view('admin.coupon.edit', compact('coupon'));
    }

    public function update(Request $request, $id)
    {
        $discountCode = DiscountCoupon::find($id);        
        if($discountCode == null){
            session()->flash('error', 'Record couldnot find');
            return response()->json([
                'status' => 'false',
                'message' => 'Discount coupon couldnot find'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required'
        ]);
        if($validator->passes()){
            
            //Expiry date must be greater than start date
            if(!empty($request->starts_at) && !empty($request->expires_at))
            {
                $expiresAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->expires_at);
                $startsAt  = Carbon::createFromFormat('Y-m-d H:i:s', $request->starts_at);
                if($expiresAt->gt($startsAt) == false)
                {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expires at cannot less than start date time']
                    ]);
                }
            }
            
            $discountCode->code = $request->code;
            $discountCode->name = $request->name;
            $discountCode->description = $request->description;
            $discountCode->max_uses = $request->max_uses;
            $discountCode->max_uses_user = $request->max_uses_user;
            $discountCode->type = $request->type;
            $discountCode->discount_amount = $request->discount_amount;
            $discountCode->min_amount = $request->min_amount;
            $discountCode->status = $request->status;
            $discountCode->starts_at = $request->starts_at;
            $discountCode->expires_at = $request->expires_at;
            $discountCode->save();
            $message = 'Discount coupon updated successfully';
            session()->flash('success', $message);
            return response()->json([
                'status' => true,
                'message' => $message
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $discountCoupon = DiscountCoupon::find($id);
        if($discountCoupon == null){
            session()->flash('error', 'Discount coupon couldnot find');
            return response()->json([
                'status' => false,
                'message' => 'Discount coupon couldnot find'
            ]);
        }

        $discountCoupon->delete();
        session()->flash('success', 'Discount coupon deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Discount coupon deleted successfully'
        ]);
    }
}
