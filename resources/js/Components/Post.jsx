import {createContext, useContext, useState} from "react";
import dayjs from "dayjs";
import relativeTime from 'dayjs/plugin/relativeTime';
import {Link, useForm, usePage,router} from "@inertiajs/react";
import Dropdown from "@/Components/Dropdown.jsx";
import InputError from "@/Components/InputError.jsx";
import PrimaryButton from "@/Components/PrimaryButton.jsx";
import IconChatBubble, {IconPost, IconRePost, IconReply} from "@/Components/Icons.jsx";
import {cn} from "@/lib/utils.js";

dayjs.extend(relativeTime);

const PostContext = createContext();

export function Post({Post, classname, children}) {
    const  [editing, setEditing] = useState(false);

    return (
        <PostContext.Provider value={{Post, editing, setEditing}}>
            <div className={cn("p-3 sm:p-6 flex space-x-4 hover:bg-gray-50 transition-all", classname)}>
                {children}
            </div>
        </PostContext.Provider>
    )
}

export function PostContent({children}){
    return <div className="flex-1">
        {children}
    </div>
}

const usePost = ()=>useContext(PostContext);

export function PostAvatar(){
    const {Post} = usePost();

    return(
        <>
            {Post.replying_to !== null
            ? <Link href={route('posts.show', {Post: Post.replying_to})}>
                <IconReply/>
            </Link>
            :
            Post.rePosting !== null ? <IconRePost/>
                : <IconPost/>}
        </>

    )

}

export function PostHeader() {
    const {auth} = usePage().props
    const {Post, setEditing} = usePost();

    return (
        <>
            <div className="flex justify-between items-baseline">
                <div className='flex items-center gap-2'>
                    {Post.rePosting
                        ?
                        <>
                            <Link
                                href={route('users.show', {user: Post.rePosted_Post.Poster.id})}
                                className="text-gray-800 hover:underline line-clamp-1"
                            >
                                {Post.rePosted_Post.user.name}
                            </Link>
                            <span className={'text-gray-500'}>via</span>

                        </>
                        : null}
                    <Link
                        href={route('users.show', {user: Post.user.id})}
                        className="text-gray-800 hover:underline line-clamp-1"
                    >
                        {Post.Poster.name}
                    </Link>


                    <small className="ml-2 text-xs text-gray-500 line-clamp-1">{dayjs(Post.created_at).fromNow()}</small>

                    {Post.in_reply_to
                        ?
                        <Link
                            href={route('posts.show', {Post: Post.replying_to})}
                            className="ml-2 text-xs text-blue-800 hover:underline"
                        >
                            {`replying to ${Post.in_reply_to.user.name}`}
                        </Link>
                        : null
                    }
                    {Post.created_at !== Post.updated_at &&
                        <small className="text-xs text-gray-700">&middot; edited</small>}
                </div>

                {Post.user.id === auth.user.id &&
                    <Dropdown>
                        <Dropdown.Trigger>
                            <button>
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 text-gray-400"
                                     viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
                                </svg>
                            </button>
                        </Dropdown.Trigger>
                        <Dropdown.Content>
                            <Dropdown.Link as="button" href={route('posts.destroy', Post.id)} method="delete">
                                Delete
                            </Dropdown.Link>
                        </Dropdown.Content>
                    </Dropdown>
                }
            </div>
        </>
    )

}

export function PostBody(){
    const {Post, editing , setEditing} = usePost();



    const {data, setData, patch, clearErrors, reset, errors} = useForm({
        message: Post.message,
    });
    const submit = (e) => {
        e.preventDefault();
        patch(route('posts.update', Post), {
            onSuccess: () => {
                setEditing(false);
            }
        });
    }

    return (
        <>
            {editing
                ? <form onSubmit={submit}>
                    <textarea value={data.message} onChange={e => setData('message', e.target.value)}
                              className="mt-4 w-full text-gray-900 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"></textarea>
                    <InputError message={errors.message} className="mt-2"/>
                    <div className="space-x-2">
                        <PrimaryButton className="mt-4">Save</PrimaryButton>
                        <button className="mt-4" onClick={() => {
                            setEditing(false);
                            reset();
                            clearErrors();
                        }}>Cancel
                        </button>
                    </div>
                </form>
                :
                <>
                    <Link
                        href={route('posts.show', [Post.rePosting||Post.id])}
                        className="mt-3 test-gray-900 flex flex-col gap-2"
                    >
                        {Post.message}

                    </Link>
                </>
            }
        </>
    )


}


export function PostActions() {

    const {Post, editing} = usePost();
    const [isLike, setIsLike] = useState(Post.is_like);
    const [likes, setLikes] = useState(Post.likes_count);

    const [isRePosted, setIsRePosted] = useState(Post.is_rePost);
    const [rePosts, setRePosts] = useState(Post.rePosts_count);

    const onToggleLike = () => {
        router.patch(route('posts.toggle-like', {Post: Post.id}),
            {},
            {
                preserveScroll: true,
            })
        setLikes(prev => isLike ? prev - 1 : prev + 1)
        setIsLike(prev => !prev)
    }

    function getRePostPrams() {
        if (Post.rePosting) {
            return {Post: Post.rePosting}
        }
        return {Post: Post.id}
    }


    const onToggleRePost = () => {
        router.post(route(isRePosted ? 'posts.undo_rePost' : 'posts.rePost', getRePostPrams()),
            {},
            {
                preserveScroll: true,
                preserveState: true,
            })
        setRePosts(prev => isRePosted ? prev - 1 : prev + 1)
        setIsRePosted(prev => !prev)
    }
    if(editing) return null
    return (
        <div className={'flex gap-8 items-center mt-4'}>
            <Link
                href={route('posts.show', [Post.id])}
                className="tex-xs text-gray-400  hover:scale-105 transition flex gap-2 items-center"
            >
                <IconChatBubble className={`w-6 h-6 `}/> <span
                className='text-sm'>{Post.replies_count}</span>
            </Link>

            <button onClick={(e) => {
                e.preventDefault();
                onToggleLike()
            }} className="tex-xs text-gray-400 hover:scale-105 transition">
                {isLike ? "❤️" : "🤍"} <span className='text-sm'>{likes}</span>
            </button>

            <button onClick={(e) => {
                e.preventDefault();
                onToggleRePost()
            }} className="tex-xs text-gray-400  hover:scale-105 transition flex gap-2">
                <IconRePost className={`${isRePosted ? 'fill-green-600' : 'fill-gray-500'} `}/> <span
                className={cn('text-sm', {
                    'text-green-500': isRePosted
                })}>{rePosts}</span>
            </button>

        </div>
    )

}


