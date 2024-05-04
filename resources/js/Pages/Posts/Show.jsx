import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.jsx";
import {Head, usePage} from "@inertiajs/react";
import ReplyForm from "@/Pages/Posts/Partials/ReplyForm.jsx";
import {Post, PostActions, PostAvatar, PostBody, PostContent, PostHeader} from "@/Components/Post.jsx";

export default function Show({Post, auth}){
    return(
        <AuthenticatedLayout user={auth.user}>
            <Head title={`Post by ${Post.user.name}: ${Post.message}`}/>
            <main className='max-w-2xl mx-auto p-4 sm:p-6 lg:p-8'>
                <div className='p-4 bg-white shadow-sm rounded-lg '>
                    <Post Post={Post}>
                        <PostAvatar />
                        <PostContent>
                            <PostHeader />
                            {Post.image_path && <img src={`/storage/${Post.image_path}`} alt="Post Image" className="mt-2 rounded-md" style={{ width: '350px', height: '450px'}} />}
                            <PostBody />
                            
                            <PostActions/>
                        </PostContent>
                    </Post>
                    <div className='md:ms-12 my-4'>
                        <ReplyForm Post={Post}/>
                    </div>

                    <div className='flex flex-col border-l ms-12'>
                        {Post.replies.map(reply=>(
                            <Post key={reply.id} Post={reply}>
                                <PostAvatar />
                                <PostContent>
                                    <PostHeader />
                                    <PostBody />
                                    <PostActions />
                                </PostContent>
                            </Post>
                        ))}
                    </div>

                </div>
            </main>
        </AuthenticatedLayout>

    )
}
