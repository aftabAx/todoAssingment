<?php
namespace App\Http\Controllers;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return response()->json(['tasks' => Task::all()], 200);
    }

    public function store(Request $request)
    {
        $task = Task::create($request->all());
        return response()->json(['task' => $task], 201);
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        if ($task) {
            $task->delete();
            return response()->json(['message' => 'Task deleted'], 200);
        }
        return response()->json(['message' => 'Task not found'], 404);
    }
}
