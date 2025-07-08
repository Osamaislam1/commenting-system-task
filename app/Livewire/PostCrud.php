<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;

class PostCrud extends Component
{
    use WithPagination;

    public $title = '';
    public $content = '';
    public $postId = null;
    public $isEdit = false;
    public $showForm = false;
    public $deleteId = null;
    public $showDeleteModal = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
    ];

    public function showCreateForm()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEdit = false;
    }

    public function showEditForm($id)
    {
        $post = Post::findOrFail($id);
        $this->postId = $post->id;
        $this->title = $post->title;
        $this->content = $post->content;
        $this->showForm = true;
        $this->isEdit = true;
    }

    public function createPost()
    {
        $this->validate();
        Post::create([
            'title' => $this->title,
            'content' => $this->content,
        ]);
        $this->resetForm();
        session()->flash('success', 'Post created successfully!');
    }

    public function updatePost()
    {
        $this->validate();
        $post = Post::findOrFail($this->postId);
        $post->update([
            'title' => $this->title,
            'content' => $this->content,
        ]);
        $this->resetForm();
        session()->flash('success', 'Post updated successfully!');
    }

    public function deletePost($id)
    {
        try {
            $post = Post::find($id);

            if (!$post) {
                session()->flash('error', 'Post not found.');
                return;
            }

            $post->delete();
            session()->flash('success', 'Post deleted successfully!');

            $this->resetPage();

        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting post: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->deleteId = null;
        $this->showDeleteModal = false;
    }

    public function deleteConfirmed()
    {
        if ($this->deleteId) {
            $this->deletePost($this->deleteId);
        }
        $this->cancelDelete();
    }

    protected $listeners = ['deleteConfirmed'];

    public function resetForm()
    {
        $this->title = '';
        $this->content = '';
        $this->postId = null;
        $this->isEdit = false;
        $this->showForm = false;
        $this->resetValidation();
    }

    public function render()
    {
        $posts = Post::latest()->paginate(10);
        return view('livewire.post-crud', compact('posts'));
    }
}
