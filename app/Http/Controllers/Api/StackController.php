<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StackController extends Controller
{
    /**
     * Add a value to the stack.
     *
     * This endpoint adds a new value to the stack. The stack operates on a Last-In-First-Out (LIFO) principle.
     * Values are pushed to the top of the stack and can later be retrieved or removed. This endpoint requires
     * a single parameter, `value`, which is the value to be added to the stack.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

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

    /**
     * Retrieve and remove the top value from the stack.
     *
     * This endpoint retrieves the top value from the stack, which operates on a Last-In-First-Out (LIFO) principle.
     * After retrieving the value, it is removed from the stack. If the stack is empty, a 404 status code with 
     * a message indicating that the stack is empty is returned.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
