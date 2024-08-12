<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StackController extends Controller
{
    public function addToStack(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $stack = new Stack();
        $stack->value = $request->input('value');
        $stack->save();

        return response()->json(['message' => 'Value added to stack']);
    }

    public function getFromStack()
    {
        $stack = Stack::orderBy('id', 'desc')->first();

        if ($stack) {
            $value = $stack->value;
            $stack->delete();

            return response()->json(['value' => $value]);
        }

        return response()->json(['message' => 'Stack is empty'], 404);
    }
}
