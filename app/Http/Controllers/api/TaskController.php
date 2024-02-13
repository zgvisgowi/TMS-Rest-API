<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TaskResource;


class TaskController extends Controller
{
    public function index(){
        $tasks=Task::where('user_id',auth()->id())->get();
        if ($tasks->isEmpty()) {
            return response()->json(['message' => "you haven't tasks"], 200);}
        else {
            return response()->json(['message' => 'this is your to do list', 'tasks' => $tasks], 200);
        }
    }


    public function create(Request $request){
        $validated=Validator::make($request->all(),[
            'name'=>'required',
            'description'=>'nullable|string',
            'task_id'=>'nullable|exists:tasks,id'
        ]);
        if($validated->fails()){
            return response()->json(['success'=>false,'message'=>$validated->errors()->first()]);
        }
             $task=Task::create([
            'name'=>$request->name,
            'description'=>$request->description,
            'user_id'=>Auth::id(),
            'task_id'=>$request->task_id]);
            return response()->json(['success'=>true,'message'=>'task createdsuccessfully','task'=>new TaskResource($task)],200);
    }
    public function read($taskId){
        $task=Task::find($taskId);
        $this->authorize('view', $task);
        if(!$task){
            return response()->json(['success'=>false,'message'=>"task can't be founded"]);
        }
        return response()->json(['success'=>true,'message'=>'this is your task','task'=>new TaskResource($task)],200);
    }



    public function update($taskId,Request $request){
        $task=Task::find($taskId);
        $this->authorize('update', $task);
        if(!$task){
            return response()->json(['success'=>false,'message'=>'Task not found.'],404);
        }
        $validated=Validator::make($request->all(),[
            'name'=>'required|string|min:2',
            'description'=>'nullable|string',
            'task_id'=>'required|array|same:'.$taskId
        ]);
        if ($validated->fails()) {
                return response()->json(['success'=>false,'errors' => $validated->errors()->first()], 422);
        }
        $task->update([
            'name' => $request->name,
            'description' => $request->description,
            'task_id' => $request->task_id
            ]);
        return response()->json(['success'=>true,'message' => "task updated succesfuly", 'task' => new TaskResource($task)], 200);
    }



    public function delete($taskId)
    {
        $task = Task::find($taskId);
        $this->authorize('delete', $task);
        if (!$task) {
            return response()->json(['success'=>false,'message' => 'Task not found.'], 404);
        }
        $task->delete();
        return response()->json(['success'=>true,'message' => 'task destroyed successfully'], 200);
    }

}
