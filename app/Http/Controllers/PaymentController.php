<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{

    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PaymentResource::collection(Payment::with('user')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'type' => 'required',
            'paid' => 'required|numeric|between:0,1',
            'payment_date' => 'nullable',
            'value' => 'required|numeric'
        ]);

        if($validator->fails()){
            return $this->error('Data invalid', 422, $validator->errors());
        }

        try{
            $created = Payment::create($validator->validated());
            
            return $this->response('Successful data registration', 201, new PaymentResource($created->load('user')));
            
        }catch(\Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new PaymentResource(Payment::with('user')->where('id',$id)->first());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'type' => 'required',
            'paid' => 'required|numeric|between:0,1',
            'value' => 'required|numeric',
            'payment_date' => 'nullable|date_format:Y-m-d H:i:s'
        ]);

        if($validator->fails()){
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $validated = $validator->validated();

        try{
            $payment = Payment::findOrFail($id);
    
            $updated = $payment->update([
                'user_id' => $validated['user_id'],
                'type' =>  $validated['type'],
                'paid' => $validated['paid'],
                'value' => $validated['value'],
                'payment_date' => $validated['paid'] ? $validated['payment_date'] : null
            ]);
    
            if($updated){
                return  $this->response('Payment updated', 200, new PaymentResource($payment->load('user')));
            }
    
            return $this->error('Payment not updated', 400);
        }catch(\Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
