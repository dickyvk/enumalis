<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Thread;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhemeController extends Controller
{
    /**
     * Display a listing of categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexCategories()
    {
        $categories = Category::all();
        return response()->json(['categories' => $categories], 200);
    }

    /**
     * Store a newly created category.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($request->only('name', 'description'));
        return response()->json(['message' => 'Category created successfully', 'data' => $category], 201);
    }

    /**
     * Update the specified category.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->only('name', 'description'));

        return response()->json(['message' => 'Category updated successfully', 'data' => $category], 200);
    }

    /**
     * Remove the specified category.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }

    /**
     * Display a listing of threads for a given category.
     *
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexThreads($categoryId)
    {
        $threads = Thread::where('category_id', $categoryId)->with('profile')->get();
        return response()->json(['threads' => $threads], 200);
    }

    /**
     * Store a newly created thread.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeThread(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $thread = Thread::create([
            'title' => $request->title,
            'body' => $request->body,
            'profiles_id' => Auth::user()->id, // Assuming user authentication is handled
            'category_id' => $request->category_id,
        ]);

        return response()->json(['message' => 'Thread created successfully', 'data' => $thread], 201);
    }

    /**
     * Update the specified thread.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateThread(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'body' => 'sometimes|required|string',
        ]);

        $thread = Thread::findOrFail($id);
        $thread->update($request->only('title', 'body'));

        return response()->json(['message' => 'Thread updated successfully', 'data' => $thread], 200);
    }

    /**
     * Remove the specified thread.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyThread($id)
    {
        $thread = Thread::findOrFail($id);
        $thread->delete();

        return response()->json(['message' => 'Thread deleted successfully'], 200);
    }

    /**
     * Display a listing of posts for a given thread.
     *
     * @param int $threadId
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexPosts($threadId)
    {
        $posts = Post::where('thread_id', $threadId)->with('profile')->get();
        return response()->json(['posts' => $posts], 200);
    }

    /**
     * Store a newly created post.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storePost(Request $request)
    {
        $request->validate([
            'body' => 'required|string',
            'thread_id' => 'required|exists:threads,id',
        ]);

        $post = Post::create([
            'body' => $request->body,
            'profiles_id' => Auth::user()->id, // Assuming user authentication is handled
            'thread_id' => $request->thread_id,
        ]);

        return response()->json(['message' => 'Post created successfully', 'data' => $post], 201);
    }

    /**
     * Update the specified post.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePost(Request $request, $id)
    {
        $request->validate([
            'body' => 'sometimes|required|string',
        ]);

        $post = Post::findOrFail($id);
        $post->update($request->only('body'));

        return response()->json(['message' => 'Post updated successfully', 'data' => $post], 200);
    }

    /**
     * Remove the specified post.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyPost($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}






/*
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Validator;
use App\Models\Profile;
use App\Models\Category;
use App\Models\Thread;
use App\Models\Post;

class PhemeController extends Controller
{
    protected $user_type;
	protected $paginate = 10;

    public function __construct(Request $request)
    {
        if($user = auth()->user())
        {
            $this->user_type = $user->type;
            $this->paginate = $user->getPaginate();
        }
    }

    public function getCategory(Request $request, Profile $profile)
    {
        if (!$profile->ownedBy(auth()->user()))
        {
            return $this->unauthorizedResponse();
        }
        if($user = auth()->user())
        {
            $this->user_type = $user->type;
            $this->paginate = $user->getPaginate();
        }
        switch($this->user_type)
        {
            case 'master':
                $category = Category::orderBy('title', 'asc')->paginate($this->paginate, ['*'], 'page', $request->page);
                break;
            case 'admin':
            case 'user':
                $category = Category::whereIn('id', $profile->getAccessCategoriesId())->orderBy('title', 'asc')->paginate($this->paginate, ['*'], 'page', $request->page);
                break;
            default:
                $category = Category::whereIn('id', [1,2,3])->orderBy('title', 'asc')->paginate($this->paginate, ['*'], 'page', $request->page);
                break;
        }

        return response()
            ->json($category);
    }
    public function showCategory(Profile $profile, Category $category)
    {
        if (!$profile->ownedBy(auth()->user())
            || !$category->isAccessibleTo($profile))
        {
            return $this->unauthorizedResponse();
        }

        return $category;
    }
    public function setCategory(Request $request, Profile $profile, Category $category = NULL)
    {
        if(!$category)
        {
            $validator = Validator::make($request->all(),[
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'accepts_threads' => 'required|integer|between:0,1',
                'is_private' => 'nullable|integer|between:0,1',
            ]);

            if($validator->fails()){
                return response()->json(['message' => $validator->errors()->first()], 400);
            }

            Category::create(array_merge($request->all()));

            return response()->json(['message' => 'Category created successfully'], 201);
        }
        else
        {
            $validator = Validator::make($request->all(),[
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'accepts_threads' => 'nullable|integer|between:0,1',
                'is_private' => 'nullable|integer|between:0,1',
            ]);

            if($validator->fails()){
                return response()->json(['message' => $validator->errors()->first()], 400);
            }
            
            $category->update($request->all());

            return response()->json(['message' => 'Category updated successfully'], 200);
        }
    }
    public function grantAccess(Request $request)
    {
        //
    }
    public function deleteCategory(Category $category)
    {
        $category->delete();

        return response()->json(null, 204);
    }
    public function indexByCategory(Request $request, Category $category)
    {
        /*$category = $request->route('category');
        if (!$category->isAccessibleTo($request->user())) {
            return $this->notFoundResponse();
        }*/

        $thread = Thread::orderBy('created_at')->where('categories_id', $category->id);

        $createdAfter = $request->query('created_after');
        $createdBefore = $request->query('created_before');
        $updatedAfter = $request->query('updated_after');
        $updatedBefore = $request->query('updated_before');

        if ($createdAfter !== null) {
            $thread = $thread->where('created_at', '>', Carbon::parse($createdAfter)->toDateString());
        }
        if ($createdBefore !== null) {
            $thread = $thread->where('created_at', '<', Carbon::parse($createdBefore)->toDateString());
        }
        if ($updatedAfter !== null) {
            $thread = $thread->where('updated_at', '>', Carbon::parse($updatedAfter)->toDateString());
        }
        if ($updatedBefore !== null) {
            $thread = $thread->where('updated_at', '<', Carbon::parse($updatedBefore)->toDateString());
        }

        $thread = $thread->paginate($this->paginate, ['*'], 'page', $request->page);

        /*if ($category->is_private) {
            $threads->setCollection($threads->getCollection()->filter(function ($thread) use ($request) {
                return $request->user() && $request->user()->can('view', $thread);
            }));
        }

        return $this->resourceClass::collection($threads);*/

        return response()->json($thread, 200);
    }
    public function setThread(Request $request, Category $category, Thread $thread = NULL)
    {
        if(!$thread)
        {
            $validator = Validator::make($request->all(),[
                'profiles_id' => 'required|integer',
                'title' => 'required|string|max:255',
                'pinned' => 'nullable|integer|between:0,1',
                'locked' => 'nullable|integer|between:0,1',
            ]);

            if($validator->fails()){
                return response()->json(['message' => $validator->errors()->first()], 400);
            }

            $thread = Thread::create(array_merge(array('categories_id' => $category->id), $request->all()));

            return response()->json($thread, 201);
        }
        else
        {
            $validator = Validator::make($request->all(),[
                'profiles_id' => 'nullable|integer',
                'categories_id' => 'nullable|integer',
                'title' => 'nullable|string|max:255',
                'pinned' => 'nullable|integer|between:0,1',
                'locked' => 'nullable|integer|between:0,1',
            ]);

            if($validator->fails()){
                return response()->json(['message' => $validator->errors()->first()], 400);
            }
            
            $thread->update($request->all());

            return response()->json($thread, 200);
        }
    }
    
    public function recentThread(Request $request, bool $unreadOnly = false)
    {
        $thread = Thread::latest();
        $thread = $thread->paginate($this->paginate, ['*'], 'page', $request->page);
        /*    ->filter(function ($thread) use ($request, $unreadOnly) {
                return $thread->category->isAccessibleTo($request->user())
                    && (!$unreadOnly || $thread->userReadStatus !== null)
                    && (
                        !$thread->category->is_private
                        || $request->user()
                        && $request->user()->can('view', $thread)
                    );
            });

        return $this->resourceClass::collection($threads);*/

        return response()->json($thread, 200);
    }
    public function unreadThread(Request $request)
    {
        return $this->recentThread($request, true);
    }
    public function markAsRead(MarkThreadsAsRead $request): Response //belum dirubah
    {
        $category = $request->fulfill();

        return new Response(['success' => true]);
    }
    public function showThread(Thread $thread)
    {
        return $thread;
    }
    public function deleteThread(Thread $thread)
    {
        $thread->delete();

        return response()->json(null, 204);
    }
    public function restoreThread(Thread $thread)
    {
        $thread->restore();

        return response()->json($thread, 200);
    }

    public function indexByThread(Request $request)
    {
        $post = Post::orderBy('created_at', 'desc')->paginate(10, ['*'], 'page', $request->page);

        return response()
            ->json($post);
    }
    public function setPost(Request $request, Post $post = NULL)
    {
        if(!$post)
        {
            $validator = Validator::make($request->all(),[
                'profiles_id' => 'required|integer',
                'threads_id' => 'required|integer',
                'content' => 'required|string',
                'sequence' => 'nullable|integer',
            ]);

            if($validator->fails()){
                return response()->json(['message' => $validator->errors()->first()], 400);
            }

            $post = Post::create(array_merge($request->all()));

            return response()->json($post, 201);
        }
        else
        {
            $validator = Validator::make($request->all(),[
                'profiles_id' => 'required|integer',
                'threads_id' => 'required|integer',
                'content' => 'required|string',
                'sequence' => 'nullable|integer',
            ]);

            if($validator->fails()){
                return response()->json(['message' => $validator->errors()->first()], 400);
            }
            
            $post->update($request->all());

            return response()->json($post, 200);
        }
    }
    public function search(SearchPosts $request) //belum dirubah
    {
        $posts = $request->fulfill();

        return $this->resourceClass::collection($posts);
    }
    public function recentPost(Request $request, bool $unreadOnly = false)
    {
        $post = Post::recent()->get();
        return response()->json($post, 200);
    }
    public function unreadPost(Request $request)
    {
        return $this->recentPost($request, true);
    }
    public function showPost(Post $post)
    {
        return $post;
    }
    public function deletePost(Post $post)
    {
        $post->delete();

        return response()->json(null, 204);
    }
    public function restorePost(Post $post)
    {
        $post->restore();

        return response()->json($post, 200);
    }
}
