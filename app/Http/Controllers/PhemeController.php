<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Validator;
use App\Models\Category;
use App\Models\Thread;
use App\Models\Post;

class PhemeController extends Controller
{
	protected $paginate = 10;

    public function __construct(Request $request)
    {
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;
        $this->paginate = $user->getPaginate();
    }

    public function getCategory(Request $request)
    {
    	/*if ($request->has('parent_id')) {
            $categories = CategoryAccess::getFilteredDescendantsFor($request->user(), $request->query('parent_id'));
        } else {
            $categories = CategoryAccess::getFilteredTreeFor($request->user());
        }

        return $this->resourceClass::collection($categories);*/

        $category = Category::orderBy('title', 'asc')->paginate($this->paginate, ['*'], 'page', $request->page);

        return response()
            ->json($category);
    }
    public function showCategory(Category $category)
    {
    	/*$category = $request->route('category');
        if (!$category->isAccessibleTo($request->user())) {
            return $this->notFoundResponse();
        }

        return new $this->resourceClass($category);*/

        return $category;
    }
    public function setCategory(Request $request, Category $category = NULL)
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

            $category = Category::create(array_merge($request->all()));

            return response()->json($category, 201);
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

            return response()->json($category, 200);
        }
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

            $thread = Thread::create(array_merge('categories_id' => $category->id, $request->all()));

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
        $thread = Thread::recent()->get();
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

        return response()->json($category, 200);
    }
    public function unreadThreat(Request $request)
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
