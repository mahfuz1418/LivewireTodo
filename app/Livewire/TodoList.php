<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Validate('required|min:2|max:50')]
    public $name;

    public $search;

    public $editTodoID;

    #[Validate('required|min:2|max:50')]
    public $editTodoName;

    public function createTodo()
    {
        $validate = $this->validateOnly('name');
        Todo::create($validate);

        $this->reset('name');
        session()->flash('success', 'Todo Created Successfully');
        $this->resetPage();
    }

    public function toggle($todoID)
    {
        $check = Todo::find($todoID);
        $check->completed = !$check->completed;
        $check->save();

    }

    public function edit($todoID)
    {
        $this->editTodoID = $todoID;
        $this->editTodoName = Todo::find($todoID)->name;
    }

    public function cancel()
    {
        $this->reset('editTodoID', 'editTodoName');
    }

    public function delete($todoId)
    {
        Todo::findOrFail($todoId)->delete();
    }

    public function update()
    {
        $validate = $this->validateOnly('editTodoName');
        Todo::find($this->editTodoID)->update([
            'name' => $this->editTodoName,
        ]);

        $this->cancel();


    }


    public function render()
    {
        return view('livewire.todo-list',[
            'todos' => Todo::latest()->where('name', 'LIKE', "%{$this->search}%")->paginate(5),
        ]);
    }
}
