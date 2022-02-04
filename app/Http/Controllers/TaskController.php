<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::all();

        if(isset($_GET["search"]) || isset($_GET["completed"]))
        {
            return $this->search();
        }

        return response($tasks, 200);

    }


    public function store(TaskRequest $request)
    {
        $data = $request->validated();

        $completed = ($data["completed"]) ? 1 : 0;
        $parent_id = $data["parent_id"] ?? null;

        $task = Task::create([
            "title" => $data["title"],
            "completed" => $completed,
            "parent_id" => $parent_id
        ]);

        return response($task, 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);

        return response($task, 200);
    }


    public function update(TaskRequest $request, $id)
    {
        $data = $request->validated();
        $task = Task::findOrFail($id);

        $completed = ($data["completed"]) ? 1 : 0;
        $parent_id = $data["parent_id"] ?? null;

        $task->update([
            "title" => $data["title"],
            "completed" => $completed,
            "parent_id" => $parent_id
        ]);

        return response($task, 202);



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::find($id);

        if(!$task)
        {
            return response([
                "message" => "Task with id:$id not exists"
            ], 403);
        }
        $task->delete($id);

        return response(1, 200);

    }

    public function search()
    {

        // BullShit way to do this
        //But idk how I can do this with another way
        if(isset($_GET["search"]) && isset($_GET["completed"]))
        {
            $tasks = Task::where("title", "LIKE", "%".$_GET["search"]."%")->where("completed", $_GET["completed"])->get();

            return response($tasks, 200);
        }

        if(isset($_GET["search"]))
        {
            $tasks = Task::where("title", "LIKE", "%".$_GET["search"]."%")->get();

            return response($tasks, 200);
        }

        if(isset($_GET["completed"]))
        {
            $tasks = Task::where("completed", $_GET["completed"])->get();

            return response($tasks, 200);
        }
    }
}
